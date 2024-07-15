<?php

namespace BongoBank;

class Admin extends User 
{
    public static function getAllTransactions() 
    {
        $transactions = json_decode(file_get_contents('../storage/transactions.json'), true);
        return $transactions;
    }

    public static function getTransactionsByEmail($email) 
    {
        $transactions = self::getAllTransactions();
        return array_filter($transactions, function($transaction) use ($email) {
            return $transaction['email'] == $email;
        });
    }

    public static function getAllCustomers() 
    {
        $users = json_decode(file_get_contents('../storage/users.json'), true);
        return array_filter($users, function($user) {
            return $user['type'] == 'customer';
        });
    }

    public static function createAdmin($name, $mobile, $email, $password) 
    {
        $usersFile = '../storage/users.json';
        $users = json_decode(file_get_contents($usersFile), true);

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
        $usersFile = '../storage/users.json';
        $users = json_decode(file_get_contents($usersFile), true);

        foreach ($users as &$user) 
        {
            if ($user['email'] === $email && $user['type'] === 'admin') 
            {
                $user['name']   = $name;
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
    
}

?>








<?php

// namespace BongoBank;

// class Admin extends User 
// {
//     public static function getAllTransactions() 
//     {
//         $transactions = json_decode(file_get_contents('storage/transactions.json'), true);
//         return $transactions;
//     }

//     public static function getTransactionsByEmail($email) 
//     {
//         $transactions = self::getAllTransactions();
//         return array_filter($transactions, function($transaction) use ($email) {
//             return $transaction['email'] == $email;
//         });
//     }

//     public static function getAllCustomers() 
//     {
//         $users = json_decode(file_get_contents('storage/users.json'), true);
//         return array_filter($users, function($user) {
//             return $user['type'] == 'customer';
//         });
//     }
// }

?>
