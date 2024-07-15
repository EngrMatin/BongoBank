<?php

require 'vendor/autoload.php';

use BongoBank\Admin;

$usersFile = 'storage/users.json';

if (!file_exists($usersFile)) 
{
    file_put_contents($usersFile, json_encode([]));
}

$users = json_decode(file_get_contents($usersFile), true);


if (empty($users)) 
{
    $initialAdmin = new Admin('System Admin', '01715529212', 'system_admin@gmail.com', 'defaultpassword');
    $users[] = [
        'name' => $initialAdmin->getName(),
        'mobile' => $initialAdmin->getMobile(),
        'email' => $initialAdmin->getEmail(),
        'password' => $initialAdmin->getPassword(),
        'type' => 'admin'
    ];
    file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT));
    echo "Initial admin user created. Email: system_admin@gmail.com, Password: defaultpassword\n";
} 
else 
{
    echo "Admin user already exists.\n";
}

?>
