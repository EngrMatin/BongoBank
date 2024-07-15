<?php

namespace BongoBank;

class Customer extends User 
{
    private $balance;

    public function __construct($name, $mobile, $email, $password, $balance=0.0) 
    {
        parent::__construct($name, $mobile, $email, $password, $balance);
        $this->balance = $balance;
    }

    public function getBalance() 
    {
        return $this->balance;
    }

    public function setBalance($balance) 
    {
        $this->balance = $balance;
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
        $transactionsFile = '../storage/transactions.json';
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

    public static function findCustomerByEmail($email) 
    {
        $usersFile = '../storage/users.json';
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

    public static function createCustomer($name, $mobile, $email, $password, $balance=0.0) 
    {
        $usersFile = '../storage/users.json';
        $users = json_decode(file_get_contents($usersFile), true) ?? [];

        $newCustomer = new Customer($name, $mobile, $email, $password, $balance);
        $users[] = [
            'name' => $newCustomer->getName(),
            'mobile' => $newCustomer->getMobile(),
            'email' => $newCustomer->getEmail(),
            'password' => $newCustomer->getPassword(),
            'type' => 'customer',
            'created_at' => date('Y-m-d H:i:s'),
            'balance' => $newCustomer->getBalance(),
        ];

        file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT));
    }

    public function save()
    {
        $usersFile = '../storage/users.json';
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
        $usersFile = '../storage/users.json';
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

    public static function getTotalCustomers()
    {
        $usersFile = '../storage/users.json';
        $users = json_decode(file_get_contents($usersFile), true) ?? [];
        $customers = array_filter($users, function($user) {
            return $user['type'] === 'customer';
        });
        return count($customers);
    }

    public static function getCustomersAddedInLastMonth()
    {
        $usersFile = '../storage/users.json';
        $users = json_decode(file_get_contents($usersFile), true) ?? [];
        $lastMonth = strtotime("-1 month");

        $customers = array_filter($users, function($user) use ($lastMonth) {
            return $user['type'] === 'customer' && strtotime($user['created_at']) >= $lastMonth;
        });
        return count($customers);
    }
}

?>
