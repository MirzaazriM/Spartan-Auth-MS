<?php
/**
 * Created by PhpStorm.
 * User: mirza
 * Date: 6/28/18
 * Time: 8:51 AM
 */

namespace Model\Mapper;

use PDO;
use Component\DataMapper;
use Model\Entity\Admin;
use Model\Entity\Shared;

class AuthMapper extends DataMapper
{

    public function getConfiguration()
    {
        return $this->configuration;
    }

    public function getClientInfo(string $email)
    {
        try{
            $sql = "SELECT * 
                    FROM oauth_clients AS oc
                    INNER JOIN oauth_users AS ou ON ou.username = oc.user_id
                    WHERE ou.email = ? ";
            $statement = $this->connection->prepare($sql);
            $statement->execute(
                [
                    $email
                ]
            );
            $response = $statement->fetch(PDO::FETCH_ASSOC);

        }catch (\Exception $e){

            $response = $e->getMessage();
        }

        return $response;
    }


    public function firstLogin(Shared $shared, Admin $admin) //TODO
    {
        try{
            $this->connection->beginTransaction();

            $sql = "UPDATE oauth_users 
            SET first_name = ?, last_name = ?,email_verified = ?,  image = ? WHERE username = ?";
            $statement = $this->connection->prepare($sql);
            $statement->execute(
                [
                    $admin->getFirstName(),
                    $admin->getLastName(),
                    $admin->getEmailVerified(),
                    $admin->getImage(),
                    $admin->getUsername()
                ]
            );

            $response = [
                'status' => 200
            ];

            $this->connection->commit();
        }catch(\Exception $e){
            $this->connection->rollBack();

            // log error
            $this->monolog->addWarning("Admin Mapper firstLogin:".$e->getMessage());

            $response = [
                'status' => 404,
                'message' => $e->getMessage()
            ];
        }
        $shared->setResponse($response);
    }
}