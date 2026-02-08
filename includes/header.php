<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/functions.php';
// Set this to your local project folder name
$base_url = "http://localhost/ecommerce-website/";
$isLoggedIn = isset($_SESSION['user_id']); // Logic to check login status
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gurukrupa - Weaves of Grace</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        body {
            font-family: 'Inter', sans-serif;
        }

        .logo-blend {
            mix-blend-mode: multiply;
            filter: contrast(110%);
        }

        /* Animation: Sliding Underline */
        .nav-link {
            position: relative;
            @apply text-gray-700 font-medium transition-colors duration-300 py-2;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 0;
            background-color: #ea580c;
            transition: width 0.3s ease;
        }

        .nav-link:hover::after {
            width: 100%;
        }

        .nav-link:hover {
            @apply text-orange-600;
        }

        /* Animation: Smooth Dropdown Float */
        .dropdown-animate {
            transform: translateY(12px);
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .group:hover .dropdown-animate {
            transform: translateY(0);
            opacity: 1;
            visibility: visible;
        }

        /* Animation: Icon Interaction */
        .icon-action:hover {
            transform: translateY(-3px) scale(1.1);
            @apply text-orange-600;
        }

        .icon-action {
            @apply transition-all duration-200 inline-block;
        }

        #mobile-menu {
            transition: transform 0.4s cubic-bezier(0.77, 0.2, 0.05, 1.0);
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
    </style>
</head>

<body class="bg-gray-50">

    <div
        class="bg-zinc-900 text-white text-[10px] md:text-[11px] py-2 px-6 flex justify-between items-center tracking-[0.15em] font-medium uppercase">
        <div class="hidden md:block">✨ Quality Craftsmanship Since 1995</div>
        <div class="flex gap-6 mx-auto md:mx-0">
            <a href="<?php echo $base_url; ?>pages/orders/order-tracking.php" class="hover:text-orange-400">Track
                Order</a>
            <a href="#" class="hover:text-orange-400">Help Center</a>
            <?php if ($isLoggedIn): ?>
                <a href="<?php echo $base_url; ?>pages/auth/logout.php"
                    class="text-orange-500 font-bold border-l border-zinc-700 pl-4">Logout</a>
            <?php endif; ?>
        </div>
    </div>

    <nav class="bg-white border-b sticky top-0 z-50">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="flex justify-between items-center h-24">

                <div class="flex items-center gap-6">
                    <button id="menu-btn" class="lg:hidden text-2xl hover:text-orange-600 transition">
                        <i class="fas fa-bars-staggered"></i>
                    </button>

                    <a href="<?php echo $base_url; ?>index.php" class="flex items-center gap-3 group">
                        <img src="<?php echo $base_url; ?>assets/images/logo/logo.png" alt="Gurukrupa Logo"
                            class="h-14 logo-blend group-hover:scale-105 transition-transform duration-300 w-auto max-w-[200px]"
                            onerror="this.src='https://via.placeholder.com/200x60?text=GURUKRUPA'">
                    </a>
                </div>

                <div class="hidden lg:flex items-center gap-8">
                    <a href="<?php echo $base_url; ?>index.php" class="nav-link">Home</a>
                    <a href="<?php echo $base_url; ?>pages/about.php" class="nav-link">About</a>
                    <a href="<?php echo $base_url; ?>pages/products/product-list.php" class="nav-link">Product</a>
                    <a href="<?php echo $base_url; ?>pages/wholesale.php" class="nav-link">Wholesale</a>
                    <?php if ($isLoggedIn): ?>
                        <a href="<?php echo $base_url; ?>pages/orders/order-history.php" class="nav-link">My Orders</a>
                    <?php endif; ?>
                    <a href="<?php echo $base_url; ?>pages/contact.php" class="nav-link">Contact Us</a>
                </div>

                <div class="flex items-center gap-4">
                    <button class="icon-action p-2 hidden sm:block"><i class="fas fa-search text-lg"></i></button>

                    <div class="relative group">
                        <?php if ($isLoggedIn): ?>
                            <button class="icon-action p-2 flex items-center gap-2">
                                <i class="fas fa-user-circle text-xl"></i>
                                <span
                                    class="text-xs font-bold hidden md:block"><?php echo explode(' ', $_SESSION['user_name'])[0]; ?></span>
                            </button>
                            <div
                                class="dropdown-animate absolute right-0 top-full w-48 bg-white shadow-2xl rounded-2xl border border-gray-100 py-3 z-50">
                                <a href="<?php echo $base_url; ?>pages/user/dashboard.php"
                                    class="block px-6 py-2 text-sm font-semibold hover:bg-orange-50 hover:text-orange-600">Dashboard</a>
                                <a href="<?php echo $base_url; ?>pages/profile/profile.php"
                                    class="block px-6 py-2 text-sm font-semibold hover:bg-orange-50 hover:text-orange-600">My
                                    Profile</a>
                                <a href="<?php echo $base_url; ?>pages/orders/order-history.php"
                                    class="block px-6 py-2 text-sm font-semibold hover:bg-orange-50 hover:text-orange-600">Track
                                    Orders</a>
                                <hr class="my-2 border-gray-100">
                                <a href="<?php echo $base_url; ?>pages/auth/logout.php"
                                    class="block px-6 py-2 text-sm font-bold text-red-500 hover:bg-red-50">Sign Out</a>
                            </div>
                        <?php else: ?>
                            <a href="<?php echo $base_url; ?>pages/auth/login.php" class="icon-action p-2">
                                <i class="far fa-user text-lg"></i>
                            </a>
                        <?php endif; ?>
                    </div>

                    <a href="<?php echo $base_url; ?>pages/cart/wishlist.php" class="relative icon-action p-2">
                        <i class="far fa-heart text-lg"></i>
                        <span
                            class="absolute top-1 right-0 bg-orange-600 text-white text-[9px] font-bold rounded-full h-4 w-4 flex items-center justify-center">0</span>
                    </a>

                    <a href="<?php echo $base_url; ?>pages/cart/cart.php" class="relative icon-action p-2">
                        <i class="fas fa-shopping-bag text-lg"></i>
                        <span
                            class="absolute top-1 right-0 bg-black text-white text-[9px] font-bold rounded-full h-4 w-4 flex items-center justify-center">3</span>
                    </a>
                </div>
            </div>
        </div>

        <div id="mobile-menu"
            class="fixed inset-y-0 left-0 w-[280px] bg-white shadow-2xl transform -translate-x-full z-[70] flex flex-col">
            <div class="p-6 border-b flex justify-between items-center bg-gray-50">
                <img src="<?php echo $base_url; ?>assets/images/logo/logo.png" alt="Logo" class="h-10 logo-blend">
                <button id="close-btn" class="text-3xl text-gray-400 hover:text-zinc-900">&times;</button>
            </div>

            <div class="flex-1 px-6 py-8 flex flex-col gap-5 overflow-y-auto no-scrollbar font-bold text-gray-700">
                <a href="<?php echo $base_url; ?>index.php" class="border-b pb-2 hover:text-orange-600">Home</a>
                <a href="<?php echo $base_url; ?>pages/about.php" class="border-b pb-2 hover:text-orange-600">About</a>
                <a href="<?php echo $base_url; ?>pages/products/product-list.php"
                    class="border-b pb-2 hover:text-orange-600">Products</a>
                <a href="<?php echo $base_url; ?>pages/wholesale.php"
                    class="border-b pb-2 hover:text-orange-600">Wholesale</a>
                <?php if ($isLoggedIn): ?>
                    <a href="<?php echo $base_url; ?>pages/user/dashboard.php"
                        class="border-b pb-2 hover:text-orange-600">My Account</a>
                <?php else: ?>
                    <a href="<?php echo $base_url; ?>pages/auth/login.php" class="border-b pb-2 hover:text-orange-600">Login
                        / Register</a>
                <?php endif; ?>
                <a href="<?php echo $base_url; ?>pages/contact.php" class="border-b pb-2 hover:text-orange-600">Contact
                    Us</a>
            </div>

            <div class="p-6 border-t bg-zinc-900 text-white text-center">
                <button class="w-full bg-orange-600 py-3 rounded-lg font-bold uppercase tracking-widest text-xs">Shop
                    Now</button>
            </div>
        </div>
        <div id="overlay" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden z-[60]"></div>
    </nav>

    <script>
        const menuBtn = document.getElementById('menu-btn');
        const closeBtn = document.getElementById('close-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        const overlay = document.getElementById('overlay');

        function toggleMenu() {
            mobileMenu.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
            document.body.classList.toggle('overflow-hidden');
        }

        menuBtn.addEventListener('click', toggleMenu);
        closeBtn.addEventListener('click', toggleMenu);
        overlay.addEventListener('click', toggleMenu);
    </script>