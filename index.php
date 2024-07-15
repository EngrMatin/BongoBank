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

    <title>BongoBank</title>
</head>
<body class="h-full bg-slate-100">
    <div class="flex flex-col justify-center min-h-full py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <h2 class="mt-6 text-2xl font-bold leading-9 tracking-tight text-center text-gray-900"> Welcome to BongoBank </h2>
        </div>

        <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-[480px]">
            <div class="px-6 py-12 bg-white shadow sm:rounded-lg sm:px-12">
                <div class="space-y-6">
                    <a href="admin/login.php" class="block w-full text-center text-white bg-blue-500 hover:bg-blue-600 rounded-md px-4 py-2">
                        Admin Login
                    </a>
                    <a href="customer/login.php" class="block w-full text-center text-white bg-green-500 hover:bg-green-600 rounded-md px-4 py-2">
                        Customer Login
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
