<?php

session_start();
require '../vendor/autoload.php';

use BongoBank\Admin;

if (!isset($_SESSION['user']) || $_SESSION['user']['type'] !== 'admin') 
{
    header('Location: ../login.php');
    exit;
}

$currentUser = $_SESSION['user'];
$success_message = null;
$error_message = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') 
{
    $name            = $_POST['name'];
    $mobile          = $_POST['mobile'];
    $password        = $_POST['password'];
    $passwordConfirm = $_POST['password_confirm'];

    if ($password !== $passwordConfirm) 
    {
        $error_message = "Passwords do not match.";
    } 
    else 
    {
        if (Admin::updateProfile($currentUser['email'], $name, $mobile, $password)) 
        {
            $_SESSION['user']['name'] = $name;
            $success_message = "Profile updated successfully.";
        } 
        else 
        {
            $error_message = "Failed to update profile.";
        }
    }
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

    <title>Update Profile</title>
  </head>
  <body class="h-full">
    <div class="min-h-full">
      <div class="bg-sky-600 pb-32">
        <!-- Navigation -->
        <nav
          class="border-b border-sky-300 border-opacity-25 bg-sky-600"
          x-data="{ mobileMenuOpen: false, userMenuOpen: false }">
          <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 justify-between">
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
              <div class="hidden sm:ml-6 sm:flex gap-2 sm:items-center">
                <!-- Profile dropdown -->
                <div
                  class="relative ml-3"
                  x-data="{ open: false }">
                  <div>
                    <button
                      @click="open = !open"
                      type="button"
                      class="flex rounded-full bg-white text-sm focus:outline-none"
                      id="user-menu-button"
                      aria-expanded="false"
                      aria-haspopup="true">
                      <span class="sr-only">Open user menu</span>
                      <!-- <img
                        class="h-10 w-10 rounded-full"
                        src="https://avatars.githubusercontent.com/u/831997"
                        alt="Ahmed Shamim Hasan Shaon" /> -->
                      <span
                        class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-sky-100">
                        <span class="font-medium leading-none text-sky-700">
                            <?php echo htmlspecialchars(getInitials($currentUser['name'])); ?>
                        </span>
                      </span>
                    </button>
                  </div>

                  <!-- Dropdown menu -->
                  <div
                    x-show="open"
                    @click.away="open = false"
                    class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                    role="menu"
                    aria-orientation="vertical"
                    aria-labelledby="user-menu-button"
                    tabindex="-1">
                    <a
                      href="../logout.php"
                      class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                      role="menuitem"
                      tabindex="-1"
                      id="user-menu-item-2">
                      Sign out
                    </a>
                  </div>
                </div>
              </div>
              <div class="-mr-2 flex items-center sm:hidden">
                <!-- Mobile menu button -->
                <button
                  @click="mobileMenuOpen = !mobileMenuOpen"
                  type="button"
                  class="inline-flex items-center justify-center rounded-md p-2 text-sky-100 hover:bg-sky-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-inset focus:ring-sky-500"
                  aria-controls="mobile-menu"
                  aria-expanded="false">
                  <span class="sr-only">Open main menu</span>
                  <!-- Icon when menu is closed -->
                  <svg
                    x-show="!mobileMenuOpen"
                    class="block h-6 w-6"
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
            <div class="space-y-1 pt-2 pb-3">
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
            <div class="border-t border-sky-700 pb-3 pt-4">
              <div class="flex items-center px-5">
                <div class="flex-shrink-0">
                  <!-- <img
                    class="h-10 w-10 rounded-full"
                    src="https://avatars.githubusercontent.com/u/831997"
                    alt="Ahmed Shamim Hasan Shaon" /> -->
                  <span
                    class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-sky-100">
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
                  class="ml-auto flex-shrink-0 rounded-full bg-sky-600 p-1 text-sky-200 hover:text-white focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-sky-600">
                  <span class="sr-only">View notifications</span>
                  <svg
                    class="h-6 w-6"
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
              <div class="mt-3 space-y-1 px-2">
                <a
                  href="../logout.php"
                  class="block rounded-md px-3 py-2 text-base font-medium text-white hover:bg-sky-500 hover:bg-opacity-75">
                  Sign out
                </a>
              </div>
            </div>
          </div>
        </nav>
        <header class="py-10">
          <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold tracking-tight text-white">
              Update Profile
            </h1>
          </div>
        </header>
      </div>

      <main class="-mt-32">
        <div class="mx-auto max-w-7xl px-4 pb-12 sm:px-6 lg:px-8">
          <div class="bg-white rounded-lg pb-8">
            <!-- List of All The Transactions -->
            <div class="px-4 sm:px-6 lg:px-8">
              <!-- <div class="sm:flex sm:items-center">
                <div class="sm:flex-auto">
                  <p class="mt-2 text-sm text-gray-700">
                     List of transactions made by the customers. 
                  </p>
                </div>
              </div> -->
              <div class="mt-0 flow-root">
                <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                  <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">

                  <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-[480px]">
                    <div class="px-6 py-12 bg-white shadow sm:rounded-lg sm:px-12">
                      <?php if (isset($success_message)): ?>
                        <div class="text-green-600 text-sm">
                      <?php echo $success_message; ?>
                        </div>
                      <?php elseif (isset($error_message)): ?>
                        <div class="text-red-600 text-sm">
                      <?php echo $error_message; ?>
                        </div>
                      <?php endif; ?>
                        <form class="space-y-6" action="" method="POST">
                          <div>
                            <label for="name" class="block text-sm font-medium leading-6 text-gray-900">Name</label>
                            <div class="mt-2">
                              <input type="text" name="name" id="name" required value="<?php echo htmlspecialchars($currentUser['name']); ?>"
                                     class="block w-full p-2 text-gray-900 border-0 rounded-md shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-emerald-600 sm:text-sm sm:leading-6" />
                            </div>
                          </div>

                          <div>
                            <label for="mobile" class="block text-sm font-medium leading-6 text-gray-900">Mobile No</label>
                            <div class="mt-2">
                              <input type="text" name="mobile" id="mobile" required value="<?php echo htmlspecialchars($currentUser['mobile']); ?>"
                                     class="block w-full p-2 text-gray-900 border-0 rounded-md shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-emerald-600 sm:text-sm sm:leading-6" />
                            </div>
                          </div>

                          <div>
                            <label for="password" class="block text-sm font-medium leading-6 text-gray-900">Password</label>
                            <div class="mt-2">
                              <input type="password" name="password" id="password"
                                     class="block w-full p-2 text-gray-900 border-0 rounded-md shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-emerald-600 sm:text-sm sm:leading-6" />
                            </div>
                          </div>

                          <div>
                            <label for="password_confirm" class="block text-sm font-medium leading-6 text-gray-900">Confirm Password</label>
                            <div class="mt-2">
                                <input type="password" name="password_confirm" id="password_confirm"
                                       class="block w-full p-2 text-gray-900 border-0 rounded-md shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-emerald-600 sm:text-sm sm:leading-6" />
                            </div>
                          </div>

                          <div>
                            <button type="submit"
                                    class="flex w-full justify-center rounded-md bg-emerald-600 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-emerald-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-600">
                                    Update Profile
                            </button>
                          </div>
                        </form>
                      </div>
                    </div>

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
