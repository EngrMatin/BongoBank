<?php

return [
    // 'storage' => 'file', // Change to 'database' to use MySQL storage
    'storage' => 'database', // Change to 'file' to use file storage
    
    'file' => [
        'users' => '../storage/users.json',
        'transactions' => '../storage/transactions.json',
    ],
    
    'database' => [
        'host' => 'localhost',
        'username' => 'root',
        'password' => '',
        'dbname' => 'bongobank',
        
    ],


    // 'database' => [
    //     'host' => 'your_db_host',
    //     'username' => 'your_db_username',
    //     'password' => 'your_db_password',
    //     'dbname' => 'your_db_name',
    //     'options' => [
    //         PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    //         PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    //     ]
    // ],
    
];

?>


