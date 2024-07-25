<?php

namespace BongoBank;

class Transaction
{
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
        if (!file_exists($transactionsFile)) 
        {
            return [];
        }

        $fileContents = file_get_contents($transactionsFile);
        if (empty($fileContents)) 
        {
            return [];
        }

        $transactions = json_decode($fileContents, true);
        if (!is_array($transactions)) 
        {
            return [];
        }

        return $transactions;
    }

    private static function getAllTransactionsDatabase($dbConfig)
    {
        $db = Database::getInstance($dbConfig)->getConnection();
        $query = "SELECT * FROM transactions";
        $stmt = $db->query($query);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
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
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function getTotalTransactions()
    {
        $config = include '../config.php';
        if ($config['storage'] === 'file') 
        {
            return self::getTotalTransactionsFile($config['file']['transactions']);
        } 
        elseif ($config['storage'] === 'database') 
        {
            return self::getTotalTransactionsDatabase($config['database']);
        } 
        else 
        {
            throw new \Exception("Invalid storage option.");
        }
    }

    private static function getTotalTransactionsFile($transactionsFile)
    {
        $transactions = self::getAllTransactionsFile($transactionsFile);
        return count($transactions);
    }

    private static function getTotalTransactionsDatabase($dbConfig)
    {
        $db = Database::getInstance($dbConfig)->getConnection();
        $query = "SELECT COUNT(*) FROM transactions";
        $stmt = $db->query($query);
        return $stmt->fetchColumn();
    }

    public static function getTotalTransactionAmount()
    {
        $config = include '../config.php';
        if ($config['storage'] === 'file') 
        {
            return self::getTotalTransactionAmountFile($config['file']['transactions']);
        } 
        elseif ($config['storage'] === 'database') 
        {
            return self::getTotalTransactionAmountDatabase($config['database']);
        } 
        else 
        {
            throw new \Exception("Invalid storage option.");
        }
    }

    private static function getTotalTransactionAmountFile($transactionsFile)
    {
        $transactions = self::getAllTransactionsFile($transactionsFile);
        $totalAmount = array_reduce($transactions, function($sum, $transaction) {
            return $sum + $transaction['amount'];
        }, 0);
        return $totalAmount;
    }

    private static function getTotalTransactionAmountDatabase($dbConfig)
    {
        $db = Database::getInstance($dbConfig)->getConnection();
        $query = "SELECT SUM(amount) FROM transactions";
        $stmt = $db->query($query);
        return $stmt->fetchColumn();
    }

    public static function getTransactionsInLastMonth()
    {
        $config = include '../config.php';
        if ($config['storage'] === 'file') 
        {
            return self::getTransactionsInLastMonthFile($config['file']['transactions']);
        } 
        elseif ($config['storage'] === 'database') 
        {
            return self::getTransactionsInLastMonthDatabase($config['database']);
        } 
        else 
        {
            throw new \Exception("Invalid storage option.");
        }
    }

    private static function getTransactionsInLastMonthFile($transactionsFile)
    {
        $transactions = self::getAllTransactionsFile($transactionsFile);
        $lastMonth = strtotime("-1 month");

        $transactionsInLastMonth = array_filter($transactions, function($transaction) use ($lastMonth) {
            return strtotime($transaction['date']) >= $lastMonth;
        });
        return count($transactionsInLastMonth);
    }

    private static function getTransactionsInLastMonthDatabase($dbConfig)
    {
        $db = Database::getInstance($dbConfig)->getConnection();
        $query = "SELECT COUNT(*) FROM transactions WHERE date >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
        $stmt = $db->query($query);
        return $stmt->fetchColumn();
    }

    public static function getTransactionAmountInLastMonth()
    {
        $config = include '../config.php';
        if ($config['storage'] === 'file') 
        {
            return self::getTransactionAmountInLastMonthFile($config['file']['transactions']);
        } 
        elseif ($config['storage'] === 'database') 
        {
            return self::getTransactionAmountInLastMonthDatabase($config['database']);
        } 
        else 
        {
            throw new \Exception("Invalid storage option.");
        }
    }

    private static function getTransactionAmountInLastMonthFile($transactionsFile)
    {
        $transactions = self::getAllTransactionsFile($transactionsFile);
        $lastMonth = strtotime("-1 month");

        $totalAmount = array_reduce($transactions, function($sum, $transaction) use ($lastMonth) {
            if (strtotime($transaction['date']) >= $lastMonth) 
            {
                return $sum + $transaction['amount'];
            }
            return $sum;
        }, 0);
        return $totalAmount;
    }

    private static function getTransactionAmountInLastMonthDatabase($dbConfig)
    {
        $db = Database::getInstance($dbConfig)->getConnection();
        $query = "SELECT SUM(amount) FROM transactions WHERE date >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
        $stmt = $db->query($query);
        return $stmt->fetchColumn();
    }

    public static function getTotalDepositAmount()
    {
        $config = include '../config.php';
        if ($config['storage'] === 'file') 
        {
            return self::getTotalDepositAmountFile($config['file']['transactions']);
        } 
        elseif ($config['storage'] === 'database') 
        {
            return self::getTotalDepositAmountDatabase($config['database']);
        } 
        else 
        {
            throw new \Exception("Invalid storage option.");
        }
    }

    private static function getTotalDepositAmountFile($transactionsFile)
    {
        $transactions = self::getAllTransactionsFile($transactionsFile);
        $totalDeposit = array_reduce($transactions, function($sum, $transaction) {
            return $transaction['type'] === 'deposit' ? $sum + $transaction['amount'] : $sum;
        }, 0);
        return $totalDeposit;
    }

    private static function getTotalDepositAmountDatabase($dbConfig)
    {
        $db = Database::getInstance($dbConfig)->getConnection();
        $query = "SELECT SUM(amount) FROM transactions WHERE type = 'deposit'";
        $stmt = $db->query($query);
        return $stmt->fetchColumn();
    }

    public static function getTotalWithdrawAmount()
    {
        $config = include '../config.php';
        if ($config['storage'] === 'file') 
        {
            return self::getTotalWithdrawAmountFile($config['file']['transactions']);
        } 
        elseif ($config['storage'] === 'database') 
        {
            return self::getTotalWithdrawAmountDatabase($config['database']);
        } 
        else 
        {
            throw new \Exception("Invalid storage option.");
        }
    }

    private static function getTotalWithdrawAmountFile($transactionsFile)
    {
        $transactions = self::getAllTransactionsFile($transactionsFile);
        $totalWithdraw = array_reduce($transactions, function($sum, $transaction) {
            return $transaction['type'] === 'withdraw' ? $sum + $transaction['amount'] : $sum;
        }, 0);
        return $totalWithdraw;
    }

    private static function getTotalWithdrawAmountDatabase($dbConfig)
    {
        $db = Database::getInstance($dbConfig)->getConnection();
        $query = "SELECT SUM(amount) FROM transactions WHERE type = 'withdraw'";
        $stmt = $db->query($query);
        return $stmt->fetchColumn();
    }

    public static function getTotalBalanceAmount()
    {
        $config = include '../config.php';
        if ($config['storage'] === 'file') 
        {
            return self::getTotalBalanceAmountFile($config['file']['users']);
        } 
        elseif ($config['storage'] === 'database') 
        {
            return self::getTotalBalanceAmountDatabase($config['database']);
        } 
        else 
        {
            throw new \Exception("Invalid storage option.");
        }
    }

    private static function getTotalBalanceAmountFile($usersFile)
    {
        $users = json_decode(file_get_contents($usersFile), true) ?? [];
        $totalBalance = array_reduce($users, function($sum, $user) {
            return $user['type'] === 'customer' ? $sum + $user['balance'] : $sum;
        }, 0);
        return $totalBalance;
    }

    private static function getTotalBalanceAmountDatabase($dbConfig)
    {
        $db = Database::getInstance($dbConfig)->getConnection();
        $query = "SELECT SUM(balance) FROM customers";
        $stmt = $db->query($query);
        return $stmt->fetchColumn();
    }

    public static function getDepositAmountInLastMonth()
    {
        $config = include '../config.php';
        if ($config['storage'] === 'file') 
        {
            return self::getDepositAmountInLastMonthFile($config['file']['transactions']);
        } 
        elseif ($config['storage'] === 'database') 
        {
            return self::getDepositAmountInLastMonthDatabase($config['database']);
        } 
        else 
        {
            throw new \Exception("Invalid storage option.");
        }
    }

    private static function getDepositAmountInLastMonthFile($transactionsFile)
    {
        $transactions = self::getAllTransactionsFile($transactionsFile);
        $lastMonth = strtotime("-1 month");

        $totalDeposit = array_reduce($transactions, function($sum, $transaction) use ($lastMonth) {
            return $transaction['type'] === 'deposit' && strtotime($transaction['date']) >= $lastMonth
                ? $sum + $transaction['amount']
                : $sum;
        }, 0);
        return $totalDeposit;
    }

    private static function getDepositAmountInLastMonthDatabase($dbConfig)
    {
        $db = Database::getInstance($dbConfig)->getConnection();
        $query = "SELECT SUM(amount) FROM transactions WHERE type = 'deposit' AND date >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
        $stmt = $db->query($query);
        return $stmt->fetchColumn();
    }

    public static function getWithdrawAmountInLastMonth()
    {
        $config = include '../config.php';
        if ($config['storage'] === 'file') 
        {
            return self::getWithdrawAmountInLastMonthFile($config['file']['transactions']);
        } 
        elseif ($config['storage'] === 'database') 
        {
            return self::getWithdrawAmountInLastMonthDatabase($config['database']);
        } 
        else 
        {
            throw new \Exception("Invalid storage option.");
        }
    }

    private static function getWithdrawAmountInLastMonthFile($transactionsFile)
    {
        $transactions = self::getAllTransactionsFile($transactionsFile);
        $lastMonth = strtotime("-1 month");

        $totalWithdraw = array_reduce($transactions, function($sum, $transaction) use ($lastMonth) {
            return $transaction['type'] === 'withdraw' && strtotime($transaction['date']) >= $lastMonth
                ? $sum + $transaction['amount']
                : $sum;
        }, 0);
        return $totalWithdraw;
    }

    private static function getWithdrawAmountInLastMonthDatabase($dbConfig)
    {
        $db = Database::getInstance($dbConfig)->getConnection();
        $query = "SELECT SUM(amount) FROM transactions WHERE type = 'withdraw' AND date >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
        $stmt = $db->query($query);
        return $stmt->fetchColumn();
    }

    public static function getBalanceAmountInLastMonth()
    {
        $config = include '../config.php';
        if ($config['storage'] === 'file') 
        {
            return self::getBalanceAmountInLastMonthFile($config['file']['users']);
        } 
        elseif ($config['storage'] === 'database') 
        {
            return self::getBalanceAmountInLastMonthDatabase($config['database']);
        } 
        else 
        {
            throw new \Exception("Invalid storage option.");
        }
    }

    private static function getBalanceAmountInLastMonthFile($usersFile)
    {
        $users = json_decode(file_get_contents($usersFile), true) ?? [];
        $lastMonth = strtotime("-1 month");

        $totalBalance = array_reduce($users, function($sum, $user) use ($lastMonth) {
            return $user['type'] === 'customer' && strtotime($user['created_at']) >= $lastMonth
                ? $sum + $user['balance']
                : $sum;
        }, 0);
        return $totalBalance;
    }

    private static function getBalanceAmountInLastMonthDatabase($dbConfig)
    {
        $db = Database::getInstance($dbConfig)->getConnection();
        $query = "SELECT SUM(balance) FROM customers WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)";
        $stmt = $db->query($query);
        return $stmt->fetchColumn();
    }
}

?>

