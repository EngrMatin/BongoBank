<?php
session_start();
require '../vendor/autoload.php';

use BongoBank\Admin;
use BongoBank\Database;

if ($_SERVER['REQUEST_METHOD'] === 'POST') 
{
    $email    = $_POST['email'];
    $password = $_POST['password'];

    $config   = include '../config.php';

    if ($config['storage'] === 'file') 
    {
        $usersFile = '../storage/users.json';
        $users = json_decode(file_get_contents($usersFile), true);

        foreach ($users as $user) 
        {
            if ($user['email'] === $email && password_verify($password, $user['password']) && $user['type'] === 'admin') 
            {
                $_SESSION['user'] = $user;
                header('Location: dashboard.php');
                exit;
            }
        }
    } 
    elseif ($config['storage'] === 'database') 
    {
        $dbConfig = $config['database'];
        $db = Database::getInstance($dbConfig)->getConnection();

        $query = "SELECT * FROM users WHERE email = :email AND type = 'admin'";
        $stmt = $db->prepare($query);
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) 
        {
            $_SESSION['user'] = $user;
            header('Location: dashboard.php');
            exit;
        }
    } 
    else 
    {
        throw new \Exception("Invalid storage option.");
    }

    $error_message = "Invalid email or password";
}
?>


<!DOCTYPE html>
<html class="h-full bg-white" lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" />

    <style>
        * {
            font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont,
            'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans',
            'Helvetica Neue', sans-serif;
        }
    </style>

    <title>Sign-In To Your Account</title>
</head>
<body class="h-full bg-slate-100">
    <div class="flex flex-col justify-center min-h-full py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <h2 class="mt-6 text-2xl font-bold leading-9 tracking-tight text-center text-gray-900"> Sign In To Your Account </h2>
        </div>

        <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-[480px]">
            <div class="px-6 py-12 bg-white shadow sm:rounded-lg sm:px-12">
                <form class="space-y-6" action="" method="POST">
                    <div>
                        <label for="email" class="block text-sm font-medium leading-6 text-gray-900">Email address</label>
                        <div class="mt-2">
                            <input type="email" name="email" id="email" autocomplete="email" required
                                   class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-emerald-600 p-2 sm:text-sm sm:leading-6" />
                        </div>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium leading-6 text-gray-900">Password</label>
                        <div class="mt-2">
                            <input type="password" name="password" id="password" autocomplete="current-password" 
                                   class="block w-full p-2 text-gray-900 border-0 rounded-md shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-emerald-600 sm:text-sm sm:leading-6" />
                        </div>
                    </div>

                    <?php if (isset($error_message)): ?>
                        <div class="text-red-600 text-sm">
                            <?php echo $error_message; ?>
                        </div>
                    <?php endif; ?>

                    <div>
                        <button type="submit"
                                class="flex w-full justify-center rounded-md bg-emerald-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-emerald-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-600">
                            Sign in
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>    
</html>
