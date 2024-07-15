<?php

namespace BongoBank;

class Transaction 
{
    public static function getAllTransactions() 
    {
        $transactionsFile = '../storage/transactions.json';
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

    public static function getTransactionsByEmail($email) 
    {
        $transactions = self::getAllTransactions();
        return array_filter($transactions, function($transaction) use ($email) {
            return $transaction['email'] == $email;
        });
    }

    public static function getTotalTransactions()
    {
        $transactionsFile = '../storage/transactions.json';
        $transactions = json_decode(file_get_contents($transactionsFile), true) ?? [];
        return count($transactions);
    }

    public static function getTotalTransactionAmount()
    {
        $transactionsFile = '../storage/transactions.json';
        $transactions = json_decode(file_get_contents($transactionsFile), true) ?? [];
        $totalAmount = array_reduce($transactions, function($sum, $transaction) {
            return $sum + $transaction['amount'];
        }, 0);
        return $totalAmount;
    }

    public static function getTransactionsInLastMonth()
    {
        $transactionsFile = '../storage/transactions.json';
        $transactions = json_decode(file_get_contents($transactionsFile), true) ?? [];
        $lastMonth = strtotime("-1 month");

        $transactionsInLastMonth = array_filter($transactions, function($transaction) use ($lastMonth) {
            return strtotime($transaction['date']) >= $lastMonth;
        });
        return count($transactionsInLastMonth);
    }

    public static function getTransactionAmountInLastMonth()
    {
        $transactionsFile = '../storage/transactions.json';
        $transactions = json_decode(file_get_contents($transactionsFile), true) ?? [];
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

    public static function getTotalDepositAmount()
    {
        $transactionsFile = '../storage/transactions.json';
        $transactions = json_decode(file_get_contents($transactionsFile), true) ?? [];
        $totalDeposit = array_reduce($transactions, function($sum, $transaction) {
            return $transaction['type'] === 'deposit' ? $sum + $transaction['amount'] : $sum;
        }, 0);
        return $totalDeposit;
    }

    public static function getTotalWithdrawAmount()
    {
        $transactionsFile = '../storage/transactions.json';
        $transactions = json_decode(file_get_contents($transactionsFile), true) ?? [];
        $totalWithdraw = array_reduce($transactions, function($sum, $transaction) {
            return $transaction['type'] === 'withdraw' ? $sum + $transaction['amount'] : $sum;
        }, 0);
        return $totalWithdraw;
    }

    public static function getTotalBalanceAmount()
    {
        $usersFile = '../storage/users.json';
        $users = json_decode(file_get_contents($usersFile), true) ?? [];
        $totalBalance = array_reduce($users, function($sum, $user) {
            return $user['type'] === 'customer' ? $sum + $user['balance'] : $sum;
        }, 0);
        return $totalBalance;
    }

    public static function getDepositAmountInLastMonth()
    {
        $transactionsFile = '../storage/transactions.json';
        $transactions = json_decode(file_get_contents($transactionsFile), true) ?? [];
        $lastMonth = strtotime("-1 month");

        $totalDeposit = array_reduce($transactions, function($sum, $transaction) use ($lastMonth) {
            return $transaction['type'] === 'deposit' && strtotime($transaction['date']) >= $lastMonth
                ? $sum + $transaction['amount']
                : $sum;
        }, 0);
        return $totalDeposit;
    }

    public static function getWithdrawAmountInLastMonth()
    {
        $transactionsFile = '../storage/transactions.json';
        $transactions = json_decode(file_get_contents($transactionsFile), true) ?? [];
        $lastMonth = strtotime("-1 month");

        $totalWithdraw = array_reduce($transactions, function($sum, $transaction) use ($lastMonth) {
            return $transaction['type'] === 'withdraw' && strtotime($transaction['date']) >= $lastMonth
                ? $sum + $transaction['amount']
                : $sum;
        }, 0);
        return $totalWithdraw;
    }

    public static function getBalanceAmountInLastMonth()
    {
        $usersFile = '../storage/users.json';
        $users = json_decode(file_get_contents($usersFile), true) ?? [];
        $lastMonth = strtotime("-1 month");

        $totalBalance = array_reduce($users, function($sum, $user) use ($lastMonth) {
            return $user['type'] === 'customer' && strtotime($user['created_at']) >= $lastMonth
                ? $sum + $user['balance']
                : $sum;
        }, 0);
        return $totalBalance;
    }

    // public static function getBalanceAmountInLastMonth()
    // {
    //     $transactionsFile = '../storage/transactions.json';
    //     $transactions = json_decode(file_get_contents($transactionsFile), true) ?? [];
    //     $lastMonth = strtotime("-1 month");

    //     $totalBalanceLastMonth = 0;

    //     foreach ($transactions as $transaction) 
    //     {
    //         $transactionDate = strtotime($transaction['date']);
    //         if ($transactionDate >= $lastMonth) 
    //         {
    //             if ($transaction['type'] === 'deposit') 
    //             {
    //                 $totalBalanceLastMonth += $transaction['amount'];
    //             } 
    //             elseif ($transaction['type'] === 'withdraw') 
    //             {
    //                 $totalBalanceLastMonth -= $transaction['amount'];
    //             }
    //         }
    //     }

    //     return $totalBalanceLastMonth;
    // }

}

?>
