<?php

namespace BongoBank;

use PDO;

class Admin extends User
{
    public static function createAdmin($name, $mobile, $email, $password)
    {
        $config = include '../config.php';
        if ($config['storage'] === 'file') 
        {
            self::createAdminFile($name, $mobile, $email, $password, $config['file']['users']);
        } 
        elseif ($config['storage'] === 'database') 
        {
            self::createAdminDatabase($name, $mobile, $email, $password, $config['database']);
        } 
        else 
        {
            throw new \Exception("Invalid storage option.");
        }
    }

    private static function createAdminFile($name, $mobile, $email, $password, $usersFile)
    {
        $users = json_decode(file_get_contents($usersFile), true) ?? [];

        $newAdmin = new Admin($name, $mobile, $email, $password);
        $users[] = [
            'name' => $newAdmin->getName(),
            'mobile' => $newAdmin->getMobile(),
            'email' => $newAdmin->getEmail(),
            'password' => $newAdmin->getPassword(),
            'type' => 'admin'
        ];

        file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT));
    }

    private static function createAdminDatabase($name, $mobile, $email, $password, $dbConfig)
    {
        $db = Database::getInstance($dbConfig)->getConnection();
        $query = "INSERT INTO users (name, mobile, email, password, type) VALUES (:name, :mobile, :email, :password, 'admin')";
        $stmt = $db->prepare($query);
        $stmt->execute([
            ':name' => $name,
            ':mobile' => $mobile,
            ':email' => $email,
            ':password' => password_hash($password, PASSWORD_DEFAULT)
        ]);
    }

    public function getName()
    {
        return $this->name;
    }

    public function getMobile()
    {
        return $this->mobile;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public static function updateProfile($email, $name, $mobile, $password = null)
    {
        $config = include '../config.php';
        if ($config['storage'] === 'file') 
        {
            return self::updateProfileFile($email, $name, $mobile, $password, $config['file']['users']);
        } 
        elseif ($config['storage'] === 'database') 
        {
            return self::updateProfileDatabase($email, $name, $mobile, $password, $config['database']);
        } 
        else 
        {
            throw new \Exception("Invalid storage option.");
        }
    }

    private static function updateProfileFile($email, $name, $mobile, $password, $usersFile)
    {
        $users = json_decode(file_get_contents($usersFile), true) ?? [];

        foreach ($users as &$user) 
        {
            if ($user['email'] === $email && $user['type'] === 'admin') 
            {
                $user['name'] = $name;
                $user['mobile'] = $mobile;
                if ($password) 
                {
                    $user['password'] = password_hash($password, PASSWORD_DEFAULT);
                }
                file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT));
                return true;
            }
        }
        return false;
    }

    private static function updateProfileDatabase($email, $name, $mobile, $password, $dbConfig)
    {
        $db = Database::getInstance($dbConfig)->getConnection();
        $query = "UPDATE users SET name = :name, mobile = :mobile" . ($password ? ", password = :password" : "") . " WHERE email = :email AND type = 'admin'";
        $stmt = $db->prepare($query);
        $params = [
            ':name' => $name,
            ':mobile' => $mobile,
            ':email' => $email
        ];
        if ($password) 
        {
            $params[':password'] = password_hash($password, PASSWORD_DEFAULT);
        }
        return $stmt->execute($params);
    }

    public static function getAllCustomers()
    {
        $config = include '../config.php';
        if ($config['storage'] === 'file') 
        {
            return self::getAllCustomersFile($config['file']['users']);
        } 
        elseif ($config['storage'] === 'database') 
        {
            return self::getAllCustomersDatabase($config['database']);
        } 
        else 
        {
            throw new \Exception("Invalid storage option.");
        }
    }

    private static function getAllCustomersFile($usersFile)
    {
        $users = json_decode(file_get_contents($usersFile), true) ?? [];
        return array_filter($users, function($user) {
            return $user['type'] == 'customer';
        });
    }

    private static function getAllCustomersDatabase($dbConfig)
    {
        $db = Database::getInstance($dbConfig)->getConnection();
        $query = "SELECT * FROM customers ";
        $stmt = $db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getAllTransactions()
    {
        $config = include '../config.php';
        if ($config['storage'] === 'file') 
        {
            return self::getAllTransactionsFile($config['file']['transactions']);
        } 
        elseif ($config['storage'] === 'database') 
        {
            return self::getAllTransactionsDatabase($config['database']);
        } 
        else 
        {
            throw new \Exception("Invalid storage option.");
        }
    }

    private static function getAllTransactionsFile($transactionsFile)
    {
        return json_decode(file_get_contents($transactionsFile), true) ?? [];
    }

    private static function getAllTransactionsDatabase($dbConfig)
    {
        $db = Database::getInstance($dbConfig)->getConnection();
        $query = "SELECT * FROM transactions";
        $stmt = $db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getTransactionsByEmail($email)
    {
        $config = include '../config.php';
        if ($config['storage'] === 'file') 
        {
            return self::getTransactionsByEmailFile($email, $config['file']['transactions']);
        } 
        elseif ($config['storage'] === 'database') 
        {
            return self::getTransactionsByEmailDatabase($email, $config['database']);
        } 
        else 
        {
            throw new \Exception("Invalid storage option.");
        }
    }

    private static function getTransactionsByEmailFile($email, $transactionsFile)
    {
        $transactions = self::getAllTransactionsFile($transactionsFile);
        return array_filter($transactions, function($transaction) use ($email) {
            return $transaction['email'] == $email;
        });
    }

    private static function getTransactionsByEmailDatabase($email, $dbConfig)
    {
        $db = Database::getInstance($dbConfig)->getConnection();
        $query = "SELECT * FROM transactions WHERE email = :email";
        $stmt = $db->prepare($query);
        $stmt->execute([':email' => $email]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>



