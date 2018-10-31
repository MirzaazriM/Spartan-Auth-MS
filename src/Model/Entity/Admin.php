<?php
/**
 * Created by PhpStorm.
 * User: mirza
 * Date: 8/10/18
 * Time: 4:06 PM
 */

namespace Model\Entity;


class Admin
{

    private $firstName;
    private $lastName;
    private $emailVerified;
    private $image;
    private $username;

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param mixed $firstName
     */
    public function setFirstName($firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param mixed $lastName
     */
    public function setLastName($lastName): void
    {
        $this->lastName = $lastName;
    }

    /**
     * @return mixed
     */
    public function getEmailVerified()
    {
        return $this->emailVerified;
    }

    /**
     * @param mixed $emailVerified
     */
    public function setEmailVerified($emailVerified): void
    {
        $this->emailVerified = $emailVerified;
    }

    /**
     * @return mixed
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param mixed $image
     */
    public function setImage($image): void
    {
        $this->image = $image;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username): void
    {
        $this->username = $username;
    }

}