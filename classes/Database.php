<?php

namespace BongoBank;

use PDO;
use PDOException;

class Database
{
    private static $instance = null;
    private $connection;

    private function __construct($config)
    {
        try 
        {
            $dsn = "mysql:host={$config['host']};dbname={$config['dbname']}";
            $this->connection = new PDO($dsn, $config['username'], $config['password']);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } 
        catch (PDOException $e) 
        {
            throw new \Exception('Database connection error: ' . $e->getMessage());
        }
    }

    // private function __construct($dbConfig)
    // {
    //     $this->connection = new \PDO(
    //         $dbConfig['dsn'],
    //         $dbConfig['username'],
    //         $dbConfig['password'],
    //         $dbConfig['options']
    //     );
    // }

    public static function getInstance($config)
    {
        if (self::$instance === null) 
        {
            self::$instance = new Database($config);
        }
        return self::$instance;
    }

    public function getConnection()
    {
        return $this->connection;
    }
}

?>