<?php
/**
 * Created by PhpStorm.
 * User: mirza
 * Date: 6/28/18
 * Time: 8:51 AM
 */

namespace Model\Service;


use Model\Entity\Admin;
use Model\Entity\ResponseBootstrap;
use Model\Entity\Shared;
use Model\Mapper\AuthMapper;

class AuthService
{

    private $authMapper;
    private $configuration;

    public function __construct(AuthMapper $authMapper)
    {
        $this->authMapper = $authMapper;
        $this->configuration = $authMapper->getConfiguration();
    }
    
    
    public function callback($code)
    {
        try{
            //Setup google auth
            $client = new \Google_Client();
            $client->setAuthConfig(__DIR__ . '/../../../resources/google/client_secret.json');
            $client->setScopes( [ 'email', 'profile' ] );
            $response = $client->fetchAccessTokenWithAuthCode($code);
            
            // Check if access token exsists
            if(@$response['access_token']){
                
                // Make request on google page with access token to obtain user data
                $authorization = "Authorization: Bearer ".$response['access_token'];
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL,'https://www.googleapis.com/oauth2/v2/userinfo');
                curl_setopt($ch, CURLOPT_TIMEOUT, 30); //timeout after 30 seconds
                curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
                curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization ));
                $result=curl_exec ($ch);
                $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);   //get status code
                curl_close ($ch);
                $result = json_decode($result,true);

                // check if email exsists in client id

                $response = $this->authMapper->getClientInfo($result['email']);

                $tempUserResult = $result;
                
                // if user exsists
                if(!empty($response)){
                    
                    // make request on
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $this->configuration['admin_url'].'/auth?response_type=code&client_id='.$response['client_id'].'&state='.md5(uniqid(rand(), true)));
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    $result = curl_exec($ch);
                    curl_close ($ch);
                    $result = json_decode($result ,true);
          die(print_r($result));
                    if(!empty($result['access_token'])){
                        $admin = new Admin();
                        
                        // if first time login than fill up data to user
                        if(!empty($tempUserResult['id']) || !empty($tempUserResult['email'])){
                            $shared = new Shared();
                            
                            $admin->setFirstName($tempUserResult['given_name']);
                            $admin->setLastName($tempUserResult['family_name']);
                            $admin->setEmailVerified($tempUserResult['verified_email']);
                            $admin->setImage($tempUserResult['picture']);
                            $admin->setUsername($response['username']);
                            
                            $this->authMapper->firstLogin($shared, $admin);
                        }
                        
                        // return access token and refresh token
                        header('Location:' . $this->configuration['admin_console'].
                            '?access_token='.$result['access_token'].
                            '&refresh_token='.$result['refresh_token'].
                            '&name='.$admin->getFirstName().
                            '&surname='.$admin->getLastName().
                            '&email='.$tempUserResult['email'].
                            '&image='.$admin->getImage());

//                        return new RedirectWrapper(
//                            $this->configuration['admin_console'].
//                            '?access_token='.$result['access_token'].
//                            '&refresh_token='.$result['refresh_token'].
//                            '&name='.$admin->getFirstName().
//                            '&surname='.$admin->getLastName().
//                            '&email='.$tempUserResult['email'].
//                            '&image='.$admin->getImage()
//                            );
                    }else{
                        // user doest not exsist
                        header('Location:' . $this->configuration['admin_console'].'?error=auth-user');
                        //return new RedirectWrapper($this->configuration['admin_console'].'?error=auth-user');
                    }
                    
                }
                else{
                    // user doest not exsist
                    header('Location:' . $this->configuration['admin_console'].'?error=no-user');
                    //return new RedirectWrapper($this->configuration['admin_console'].'?error=no-user');
                }
            }
        }catch(\Exception $e){
            die(print_r($e->getMessage()));
        }
    }

    
    public function loginInfo()
    {
        try{
            $client = new \Google_Client();
            $client->setAuthConfig(__DIR__ . '/../../../resources/google/client_secret.json');
            $client->setAccessType("offline");
            $client->setIncludeGrantedScopes(true);
            $client->addScope(\Google_Service_Plus::USERINFO_EMAIL);
            $auth_url = $client->createAuthUrl();
            
            header('Location:' . $auth_url);
            
            return [
                'status' => 200,
                'data' => [
                    'login' => $auth_url
                ]
            ];
        }catch(\Exception $e){
            die(print_r($e));
            //$this->monolog->addError("loginInfo IN AuthService".$e->getMessage());
            return [
                'status' => 410
            ];
        }
    }

}