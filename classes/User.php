<?php

namespace BongoBank;

abstract class User 
{
    protected $name;
    protected $mobile;
    protected $email;
    protected $password;
    
    public function __construct($name, $mobile, $email, $password) 
    {
        $this->name = $name;
        $this->mobile = $mobile;
        $this->email = $email;
        $this->password = password_hash($password, PASSWORD_DEFAULT);
    }

    public function getEmail() 
    {
        return $this->email;
    }

    public function checkPassword($password) 
    {
        return password_verify($password, $this->password);
    }
}

?>
