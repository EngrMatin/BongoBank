<?php

require 'vendor/autoload.php';

use BongoBank\Admin;
use BongoBank\Database;

$config = include 'config.php';

if ($config['storage'] === 'file') 
{
    $usersFile = 'storage/users.json';

    if (!file_exists($usersFile)) 
    {
        file_put_contents($usersFile, json_encode([]));
    }

    $users = json_decode(file_get_contents($usersFile), true);

    if (empty($users)) 
    {
        $initialAdmin = new Admin('System Admin', '01715529212', 'system_admin@gmail.com', password_hash('defaultpassword', PASSWORD_DEFAULT));
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
} 
elseif ($config['storage'] === 'database') 
{
    $dbConfig = $config['database'];
    $db = Database::getInstance($dbConfig)->getConnection();

    $query = "SELECT COUNT(*) as count FROM users WHERE type = 'admin'";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result['count'] == 0) 
    {
        $initialAdmin = new Admin('System Admin', '01715529212', 'system_admin@gmail.com', 'defaultpassword');

        $insertQuery = "INSERT INTO users (name, mobile, email, password, type) VALUES (:name, :mobile, :email, :password, 'admin')";
        $stmt = $db->prepare($insertQuery);
        $stmt->execute([
            ':name' => $initialAdmin->getName(),
            ':mobile' => $initialAdmin->getMobile(),
            ':email' => $initialAdmin->getEmail(),
            ':password' => $initialAdmin->getPassword()
        ]);
        echo "Initial admin user created. Email: system_admin@gmail.com, Password: defaultpassword\n";
    } 
    else 
    {
        echo "Admin user already exists.\n";
    }
} 
else 
{
    throw new \Exception("Invalid storage option.");
}

?>
