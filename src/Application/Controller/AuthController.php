<?php
/**
 * Created by PhpStorm.
 * User: mirza
 * Date: 6/28/18
 * Time: 8:51 AM
 */

namespace Application\Controller;


use Model\Entity\ResponseBootstrap;
use Model\Service\AuthService;
use Symfony\Component\HttpFoundation\Request;

class AuthController
{

    private $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }
    
    
    /**
     * Google Login Callback
     * 
     * @param Request $request
     * @return ResponseBootstrap
     */
    public function getCallback(Request $request):ResponseBootstrap
    {
        $code = $request->get('code');
 
        return $this->authService->callback($code);
    }
    
    
    public function getLogin(Request $request):ResponseBootstrap
    {
        $this->authService->loginInfo();
    }
    

    /*
    public function get(Request $request):ResponseBootstrap {
        die("get");
    }
    public function post(Request $request):ResponseBootstrap {
        die("post");
    }
    public function put(Request $request):ResponseBootstrap {
        die("put");
    }
    public function delete(Request $request):ResponseBootstrap {
        die("delete");
    }
    */
}