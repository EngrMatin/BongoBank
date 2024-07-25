<?php

namespace BongoBank;

use PDO;

class Customer extends User 
{
    private $balance;
    private $createdAt;

    public function __construct($name, $mobile, $email, $password, $balance = 0.0, $createdAt = null) 
    {
        parent::__construct($name, $mobile, $email, $password, $balance);
        $this->balance = $balance;
        $this->createdAt = $createdAt ?? date('d-m-Y H:i:s');
    }

    public function getBalance() 
    {
        return $this->balance;
    }

    public function setBalance($balance) 
    {
        $this->balance = $balance;
    }

    public static function createCustomer($name, $mobile, $email, $password, $balance = 0.0)
    {
        $config = include '../config.php';
        if ($config['storage'] === 'file') 
        {
            self::createCustomerFile($name, $mobile, $email, $password, $balance);
        } 
        elseif ($config['storage'] === 'database') 
        {
            self::createCustomerDatabase($name, $mobile, $email, $password, $balance, $config['database']);
        } 
        else 
        {
            throw new \Exception("Invalid storage option.");
        }
    }

    private static function createCustomerFile($name, $mobile, $email, $password, $balance)
    {
        $config = include '../config.php';
        $usersFile = $config['file']['users'];
        $users = json_decode(file_get_contents($usersFile), true) ?? [];

        $newCustomer = new Customer($name, $mobile, $email, $password, $balance);
        $users[] = [
            'name' => $newCustomer->getName(),
            'mobile' => $newCustomer->getMobile(),
            'email' => $newCustomer->getEmail(),
            'password' => $newCustomer->getPassword(),
            'type' => 'customer',
            'created_at' => $newCustomer->createdAt,
            'balance' => $newCustomer->getBalance(),
        ];

        if (file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT)) === false) 
        {
            throw new \Exception("Failed to write to users file.");
        }
    }

    private static function createCustomerDatabase($name, $mobile, $email, $password, $balance, $dbConfig)
    {
        $db = Database::getInstance($dbConfig)->getConnection();
        $query = "INSERT INTO customers (name, mobile, email, password, balance, created_at) VALUES (:name, :mobile, :email, :password, :balance, :created_at)";
        $stmt = $db->prepare($query);
        $stmt->execute([
            ':name' => $name,
            ':mobile' => $mobile,
            ':email' => $email,
            ':password' => password_hash($password, PASSWORD_DEFAULT),
            ':balance' => $balance,
            ':created_at' => date('Y-m-d H:i:s'),
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

    public static function getNameByEmail($email)
    {
        $config = include '../config.php';
        if ($config['storage'] === 'file') 
        {
            return self::getNameByEmailFile($email, $config['file']['users']);
        } 
        elseif ($config['storage'] === 'database') 
        {
            return self::getNameByEmailDatabase($email, $config['database']);
        } 
        else 
        {
            throw new \Exception("Invalid storage option.");
        }
    }

    private static function getNameByEmailFile($email, $usersFile)
    {
        $users = json_decode(file_get_contents($usersFile), true) ?? [];
        foreach ($users as $user) 
        {
            if ($user['email'] == $email) 
            {
                return $user['name'];
            }
        }
        return null;
    }

    private static function getNameByEmailDatabase($email, $dbConfig)
    {
        $db = Database::getInstance($dbConfig)->getConnection();
        $query = "SELECT name FROM customers WHERE email = :email";
        $stmt = $db->prepare($query);
        $stmt->execute([':email' => $email]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['name'] : null;
    }


    public function deposit($amount) 
    {
        $this->balance += $amount;
        $this->logTransaction('deposit', $amount);
        $this->save();
    }

    public function withdraw($amount) 
    {
        if ($this->balance >= $amount) 
        {
            $this->balance -= $amount;
            $this->logTransaction('withdraw', $amount);
            $this->save();
            return true;
        }
        return false;
    }

    public function transfer($amount, $recipientEmail) 
    {
        if ($this->balance >= $amount) 
        {
            $recipient = self::findCustomerByEmail($recipientEmail);
            if ($recipient) 
            {
                $this->withdraw($amount);
                $recipient->deposit($amount);
                $this->logTransaction('transfer', $amount, $recipientEmail);
                $recipient->save();
                return true;
            }
        }
        return false;
    }

    private function logTransaction($type, $amount, $recipientEmail = null)
    {
        $config = include '../config.php';
        if ($config['storage'] === 'file') 
        {
            $this->logTransactionFile($type, $amount, $recipientEmail, $config['file']['transactions']);
        } 
        elseif ($config['storage'] === 'database') 
        {
            $this->logTransactionDatabase($type, $amount, $recipientEmail, $config['database']);
        } 
        else 
        {
            throw new \Exception("Invalid storage option.");
        }
    }

    private function logTransactionFile($type, $amount, $recipientEmail, $transactionsFile)
    {
        $transactions = json_decode(file_get_contents($transactionsFile), true);
        $transactions[] = [
            'email' => $this->email,
            'type' => $type,
            'amount' => $amount,
            'recipientEmail' => $recipientEmail,
            'date' => date('Y-m-d H:i:s')
        ];
        file_put_contents($transactionsFile, json_encode($transactions, JSON_PRETTY_PRINT));
    }

    private function logTransactionDatabase($type, $amount, $recipientEmail, $dbConfig)
    {
        $db = Database::getInstance($dbConfig)->getConnection();
        $query = "INSERT INTO transactions (email, type, amount, recipientEmail, date) VALUES (:email, :type, :amount, :recipientEmail, :date)";
        $stmt = $db->prepare($query);
        $stmt->execute([
            ':email' => $this->email,
            ':type' => $type,
            ':amount' => $amount,
            ':recipientEmail' => $recipientEmail,
            ':date' => date('Y-m-d H:i:s')
        ]);
    }

    public function save()
    {
        $config = include '../config.php';
        if ($config['storage'] === 'file') 
        {
            $this->saveFile($config['file']['users']);
        } 
        elseif ($config['storage'] === 'database') 
        {
            $this->saveDatabase($config['database']);
        } 
        else 
        {
            throw new \Exception("Invalid storage option.");
        }
    }

    private function saveFile($usersFile)
    {
        $users = json_decode(file_get_contents($usersFile), true) ?? [];
        foreach ($users as &$storedUser) 
        {
            if ($storedUser['email'] === $this->getEmail()) 
            {
                $storedUser['balance'] = $this->getBalance();
                break;
            }
        }
        file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT));
    }

    private function saveDatabase($dbConfig)
    {
        $db = Database::getInstance($dbConfig)->getConnection();
        $query = "UPDATE customers SET balance = :balance WHERE email = :email";
        $stmt = $db->prepare($query);
        $stmt->execute([
            ':balance' => $this->balance,
            ':email' => $this->getEmail()
        ]);
    }

    public static function findCustomerByEmail($email)
    {
        $config = include '../config.php';
        if ($config['storage'] === 'file') 
        {
            return self::findCustomerByEmailFile($email, $config['file']['users']);
        } 
        elseif ($config['storage'] === 'database') 
        {
            return self::findCustomerByEmailDatabase($email, $config['database']);
        } 
        else 
        {
            throw new \Exception("Invalid storage option.");
        }
    }

    private static function findCustomerByEmailFile($email, $usersFile)
    {
        $users = json_decode(file_get_contents($usersFile), true);
        foreach ($users as $user) 
        {
            if ($user['email'] == $email && $user['type'] == 'customer') 
            {
                return new Customer($user['name'], $user['mobile'], $user['email'], $user['password'], $user['balance']);
            }
        }
        return null;
    }

    private static function findCustomerByEmailDatabase($email, $dbConfig)
    {
        $db = Database::getInstance($dbConfig)->getConnection();
        $query = "SELECT * FROM customers WHERE email = :email";
        $stmt = $db->prepare($query);
        $stmt->execute([':email' => $email]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? new Customer($result['name'], $result['mobile'], $result['email'], $result['password'], $result['balance'], $result['created_at']) : null;
    }


    public static function getTotalCustomers()
    {
        $config = include '../config.php';
        if ($config['storage'] === 'file') 
        {
            return self::getTotalCustomersFile($config['file']['users']);
        } 
        elseif ($config['storage'] === 'database') 
        {
            return self::getTotalCustomersDatabase($config['database']);
        } 
        else 
        {
            throw new \Exception("Invalid storage option.");
        }
    }

    private static function getTotalCustomersFile($usersFile)
    {
        $users = json_decode(file_get_contents($usersFile), true) ?? [];
        $customers = array_filter($users, function ($user) {
            return $user['type'] === 'customer';
        });
        return count($customers);
    }

    private static function getTotalCustomersDatabase($dbConfig)
    {
        $db = Database::getInstance($dbConfig)->getConnection();
        $query = "SELECT COUNT(*) AS total FROM customers";
        $stmt = $db->query($query);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    public static function getCustomersAddedInLastMonth()
    {
        $config = include '../config.php';
        if ($config['storage'] === 'file') 
        {
            return self::getCustomersAddedInLastMonthFile($config['file']['users']);
        } 
        elseif ($config['storage'] === 'database') 
        {
            return self::getCustomersAddedInLastMonthDatabase($config['database']);
        } 
        else 
        {
            throw new \Exception("Invalid storage option.");
        }
    }

    private static function getCustomersAddedInLastMonthFile($usersFile)
    {
        $users = json_decode(file_get_contents($usersFile), true) ?? [];
        $lastMonth = strtotime("-1 month");

        $customers = array_filter($users, function ($user) use ($lastMonth) {
            return $user['type'] === 'customer' && strtotime($user['created_at']) >= $lastMonth;
        });
        return count($customers);
    }

    private static function getCustomersAddedInLastMonthDatabase($dbConfig)
    {
        $db = Database::getInstance($dbConfig)->getConnection();
        $lastMonth = date('Y-m-d H:i:s', strtotime("-1 month"));
        $query = "SELECT COUNT(*) AS total FROM customers WHERE created_at >= :last_month";
        $stmt = $db->prepare($query);
        $stmt->execute([':last_month' => $lastMonth]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }
}

?>

