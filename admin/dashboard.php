<?php

session_start();
require '../vendor/autoload.php';

use BongoBank\Admin;
use BongoBank\Customer;
use BongoBank\Transaction;

if (!isset($_SESSION['user']) || $_SESSION['user']['type'] !== 'admin') 
{
    header('Location: login.php');
    exit;
}

function getInitials($name) 
{
  $words = explode(' ', $name);
  $initials = '';
  foreach ($words as $word) 
  {
      if (strlen($initials) < 3) 
      {
          $initials .= strtoupper(substr($word, 0, 1));
      } 
      else 
      {
          break;
      }
  }
  return $initials;
}

$currentUser = $_SESSION['user'];   
$customers = Admin::getAllCustomers();

$todaysDate = date('d-m-Y');
$totalCustomers = Customer::getTotalCustomers();
$totalTransactions = Transaction::getTotalTransactions();
$totalTransactionAmount = Transaction::getTotalTransactionAmount();

$totalDepositAmount = Transaction::getTotalDepositAmount();
$totalWithdrawAmount = Transaction::getTotalWithdrawAmount();
$totalBalanceAmount = Transaction::getTotalBalanceAmount();

$customersAddedLastMonth = Customer::getCustomersAddedInLastMonth();
$transactionsLastMonth = Transaction::getTransactionsInLastMonth();
$transactionAmountLastMonth = Transaction::getTransactionAmountInLastMonth();

$depositAmountLastMonth = Transaction::getDepositAmountInLastMonth();
$withdrawAmountLastMonth = Transaction::getWithdrawAmountInLastMonth();
$balanceAmountLastMonth = Transaction::getBalanceAmountInLastMonth();

?>


<!DOCTYPE html>
<html
  class="h-full bg-gray-100"
  lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0" />

    <!-- Tailwindcss CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- AlpineJS CDN -->
    <script
      defer
      src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Inter Font -->
    <link
      rel="preconnect"
      href="https://fonts.googleapis.com" />
    <link
      rel="preconnect"
      href="https://fonts.gstatic.com"
      crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
      rel="stylesheet" />
    <style>
      * {
        font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont,
          'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans',
          'Helvetica Neue', sans-serif;
      }
    </style>

    <title>Admin Dashboard</title>
  </head>
  <body class="h-full">
    <div class="min-h-full">
      <div class="pb-32 bg-sky-600">
        <!-- Navigation -->
        <nav
          class="border-b border-opacity-25 border-sky-300 bg-sky-600"
          x-data="{ mobileMenuOpen: false, userMenuOpen: false }">
          <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
              <div class="flex items-center px-2 lg:px-0">
                <div class="hidden sm:block">
                  <div class="flex space-x-4">
                    <!-- Current: "bg-sky-700 text-white", Default: "text-white hover:bg-sky-500 hover:bg-opacity-75" -->
                    <a href="./dashboard.php"
                        class="block px-3 py-2 text-base font-medium text-white rounded-md hover:bg-sky-500 hover:bg-opacity-75">
                        Dashboard
                    </a>
                    <a href="./customers.php"
                        class="block px-3 py-2 text-base font-medium text-white rounded-md hover:bg-sky-500 hover:bg-opacity-75">
                        Customers
                    </a>
                    <a href="./transactions.php"
                        class="block px-3 py-2 text-base font-medium text-white rounded-md hover:bg-sky-500 hover:bg-opacity-75">
                        Transactions
                    </a>
                    <a href="./profile.php"
                        class="block px-3 py-2 text-base font-medium text-white rounded-md hover:bg-sky-500 hover:bg-opacity-75">
                        Update Profile
                    </a>
                    <?php if ($currentUser['email'] === 'system_admin@gmail.com'):  ?>
                    <a href="./admin_users.php"
                        class="block px-3 py-2 text-base font-medium text-white rounded-md hover:bg-sky-500 hover:bg-opacity-75">
                        Admin Users
                    </a>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
              <div class="hidden gap-2 sm:ml-6 sm:flex sm:items-center">
                <!-- Profile dropdown -->
                <div
                  class="relative ml-3"
                  x-data="{ open: false }">
                  <div>
                    <button
                      @click="open = !open"
                      type="button"
                      class="flex text-sm bg-white rounded-full focus:outline-none"
                      id="user-menu-button"
                      aria-expanded="false"
                      aria-haspopup="true">
                      <span class="sr-only">Open user menu</span>
                      <span
                        class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-sky-100">
                        <span class="font-medium leading-none text-sky-700">
                            <?php echo htmlspecialchars(getInitials($currentUser['name'])); ?>
                        </span>
                      </span>
                      <!-- <img
                        class="w-10 h-10 rounded-full"
                        src="https://avatars.githubusercontent.com/u/831997"
                        alt="Ahmed Shamim Hasan Shaon" /> -->
                    </button>
                  </div>

                  <!-- Dropdown menu -->
                  <div
                    x-show="open"
                    @click.away="open = false"
                    class="absolute right-0 z-10 w-48 py-1 mt-2 origin-top-right bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                    role="menu"
                    aria-orientation="vertical"
                    aria-labelledby="user-menu-button"
                    tabindex="-1">
                    <a
                      href="../logout.php"
                      class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                      role="menuitem"
                      tabindex="-1"
                      id="user-menu-item-2"
                      >Sign out</a
                    >
                  </div>
                </div>
              </div>
              <div class="flex items-center -mr-2 sm:hidden">
                <!-- Mobile menu button -->
                <button
                  @click="mobileMenuOpen = !mobileMenuOpen"
                  type="button"
                  class="inline-flex items-center justify-center p-2 rounded-md text-sky-100 hover:bg-sky-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-inset focus:ring-sky-500"
                  aria-controls="mobile-menu"
                  aria-expanded="false">
                  <span class="sr-only">Open main menu</span>
                  <!-- Icon when menu is closed -->
                  <svg
                    x-show="!mobileMenuOpen"
                    class="block w-6 h-6"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                    aria-hidden="true">
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                  </svg>

                  <!-- Icon when menu is open -->
                  <svg
                    x-show="mobileMenuOpen"
                    xmlns="http://www.w3.org/2000/svg"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                    class="w-6 h-6">
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>
            </div>
          </div>

          <!-- Mobile menu, show/hide based on menu state. -->
          <div
            x-show="mobileMenuOpen"
            class="sm:hidden"
            id="mobile-menu">
            <div class="pt-2 pb-3 space-y-1">

              <a href="./dashboard.php"
                class="block px-3 py-2 text-base font-medium text-white rounded-md hover:bg-sky-500 hover:bg-opacity-75">
                Dashboard
              </a>
              <a href="./customers.php"
                class="block px-3 py-2 text-base font-medium text-white rounded-md hover:bg-sky-500 hover:bg-opacity-75">
                Customers
              </a>
              <a href="./transactions.php"
                class="block px-3 py-2 text-base font-medium text-white rounded-md hover:bg-sky-500 hover:bg-opacity-75">
                Transactions
              </a>
              <a href="./profile.php"
                class="block px-3 py-2 text-base font-medium text-white rounded-md hover:bg-sky-500 hover:bg-opacity-75">
                Update Profile
              </a>
              <?php if ($currentUser['email'] === 'system_admin@gmail.com'):  ?>
              <a href="./admin_users.php"
                class="block px-3 py-2 text-base font-medium text-white rounded-md hover:bg-sky-500 hover:bg-opacity-75">
                Admin Users
              </a>
              <?php endif; ?>

            </div>
            <div class="pt-4 pb-3 border-t border-sky-700">
              <div class="flex items-center px-5">
                <div class="flex-shrink-0">
                  <!-- <img
                    class="w-10 h-10 rounded-full"
                    src="https://avatars.githubusercontent.com/u/831997"
                    alt="" /> -->
                  <span
                    class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-sky-100">
                    <span class="font-medium leading-none text-sky-700">
                          <?php echo htmlspecialchars(getInitials($currentUser['name'])); ?>
                    </span>
                  </span>
                </div>
                <div class="ml-3">
                  <div class="text-base font-medium text-white">
                      <?php echo htmlspecialchars($currentUser['name']); ?>
                  </div>
                  <div class="text-sm font-medium text-sky-300">
                      <?php echo htmlspecialchars($currentUser['email']); ?>
                  </div>
                </div>
                <button
                  type="button"
                  class="flex-shrink-0 p-1 ml-auto rounded-full bg-sky-600 text-sky-200 hover:text-white focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-sky-600">
                  <span class="sr-only">View notifications</span>
                  <svg
                    class="w-6 h-6"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="currentColor"
                    aria-hidden="true">
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                  </svg>
                </button>
              </div>
              <div class="px-2 mt-3 space-y-1">
                <a
                  href="../logout.php"
                  class="block px-3 py-2 text-base font-medium text-white rounded-md hover:bg-sky-500 hover:bg-opacity-75"
                  >Sign out</a
                >
              </div>
            </div>
          </div>
        </nav>

        <header class="py-10">
          <div class="px-4 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold tracking-tight text-white">
                <?php echo htmlspecialchars($currentUser['name']); ?>
            </h1>
          </div>
        </header>
      </div>

      <main class="-mt-32">
        <div class="mx-auto max-w-7xl px-4 pb-12 sm:px-6 lg:px-8">
          <div class="bg-white rounded-lg py-4">
            <div class="px-4 sm:px-6 lg:px-8">
              <div class="sm:flex sm:items-center">
                <div class="w-full flex justify-center">
                  <h1 class="mt-2 text-3xl text-blue-700">
                     BongoBank
                  </h1>
                </div>
              </div>
              <div class="mt-10 flow-root">
                <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                  <div
                    class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                    <table class="min-w-full divide-y divide-gray-300">
                      <caption class="text-lg font-semibold text-gray-900">Summary Statistics</caption>
                      <thead>
                        <tr>
                          <th
                            scope="col"
                            class="whitespace-nowrap py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">
                            Total customers
                          </th>
                          <th
                            scope="col"
                            class="whitespace-nowrap py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">
                            Total transactions
                          </th>
                          <th
                            scope="col"
                            class="whitespace-nowrap py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">
                            Amount of transactions
                          </th>
                          <th
                            scope="col"
                            class="whitespace-nowrap py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">
                            Total deposit
                          </th>
                          <th
                            scope="col"
                            class="whitespace-nowrap py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">
                            Total withdraw
                          </th>
                          <th
                            scope="col"
                            class="whitespace-nowrap px-2 py-3.5 text-left text-sm font-semibold text-gray-900">
                            Total balance
                          </th>
                        </tr>
                      </thead>
                      <tbody class="divide-y divide-gray-200 bg-white">
                        <tr>
                          <td
                            class="whitespace-nowrap py-4 pl-4 pr-3 text-sm text-gray-800 sm:pl-0">
                            <?php echo htmlspecialchars($totalCustomers); ?>
                          </td>
                          <td
                            class="whitespace-nowrap py-4 pl-4 pr-3 text-sm text-gray-500 sm:pl-0">
                            <?php echo htmlspecialchars($totalTransactions); ?>
                          </td>
                          <td
                            class="whitespace-nowrap py-4 pl-4 pr-3 text-sm text-gray-800 sm:pl-0">
                            <?php echo htmlspecialchars($totalTransactionAmount); ?>
                          </td>
                          <td
                            class="whitespace-nowrap px-2 py-4 text-sm font-medium text-emerald-600">
                            <?php echo htmlspecialchars($totalDepositAmount); ?>
                          </td>
                          <td
                            class="whitespace-nowrap px-2 py-4 text-sm font-medium text-emerald-600">
                            <?php echo htmlspecialchars($totalWithdrawAmount); ?>
                          </td>
                          <td
                            class="whitespace-nowrap px-2 py-4 text-sm text-gray-500">
                            <?php echo htmlspecialchars($totalBalanceAmount); ?>
                          </td>
                          
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>

              <div class="mt-10 flow-root">
                <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                  <div
                    class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                    <table class="min-w-full divide-y divide-gray-300">
                      <caption class="text-lg font-semibold text-gray-900">Summary Statistics for the last month</caption>
                      <thead>
                        <tr>
                          <th
                            scope="col"
                            class="whitespace-nowrap py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">
                            New customers
                          </th>
                          <th
                            scope="col"
                            class="whitespace-nowrap py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">
                            Number of transactions
                          </th>
                          <th
                            scope="col"
                            class="whitespace-nowrap py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">
                            Transaction Amount
                          </th>
                          <th
                            scope="col"
                            class="whitespace-nowrap py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">
                            Deposit Amount
                          </th>
                          <th
                            scope="col"
                            class="whitespace-nowrap py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">
                            Withdrawn Amount
                          </th>
                          <th
                            scope="col"
                            class="whitespace-nowrap px-2 py-3.5 text-left text-sm font-semibold text-gray-900">
                            Balance
                          </th>
                        </tr>
                      </thead>
                      <tbody class="divide-y divide-gray-200 bg-white">
                        <tr>
                          <td
                            class="whitespace-nowrap py-4 pl-4 pr-3 text-sm text-gray-800 sm:pl-0">
                            <?php echo htmlspecialchars($customersAddedLastMonth); ?>
                          </td>
                          <td
                            class="whitespace-nowrap py-4 pl-4 pr-3 text-sm text-gray-500 sm:pl-0">
                            <?php echo htmlspecialchars($transactionsLastMonth); ?>
                          </td>
                          <td
                            class="whitespace-nowrap py-4 pl-4 pr-3 text-sm text-gray-800 sm:pl-0">
                            <?php echo htmlspecialchars($transactionAmountLastMonth); ?>
                          </td>
                          <td
                            class="whitespace-nowrap px-2 py-4 text-sm font-medium text-emerald-600">
                            <?php echo htmlspecialchars($depositAmountLastMonth); ?>
                          </td>
                          <td
                            class="whitespace-nowrap px-2 py-4 text-sm font-medium text-emerald-600">
                            <?php echo htmlspecialchars($withdrawAmountLastMonth); ?>
                          </td>
                          <td
                            class="whitespace-nowrap px-2 py-4 text-sm text-gray-500">
                            <?php echo htmlspecialchars($balanceAmountLastMonth); ?>
                          </td>
                          
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>

            </div>
          </div>
        </div>
      </main>
    </div>
  </body>
</html>
