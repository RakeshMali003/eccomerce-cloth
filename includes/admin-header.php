<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/functions.php';

// Security Check: Only admins allowed
if (!isset($_SESSION['admin_id'])) {
    // Determine the path back to login
    // Determine the path back to login
    $request_uri = $_SERVER['REQUEST_URI'];
    // Allow access to login page
    if (basename($_SERVER['PHP_SELF']) !== 'index.php') {
        header("Location: " . ADMIN_URL . "index.php");
        exit();
    }
}

$base_url = ADMIN_URL;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Joshi Electricals Admin | Command Center</title>

    <!-- CSS Dependencies -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            overflow-x: hidden;
        }

        /* Custom Scrollbar for Sidebar */
        .sidebar-scroll::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar-scroll::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar-scroll::-webkit-scrollbar-thumb {
            background: #e2e8f0;
            border-radius: 10px;
        }

        /* Glassmorphism Utilities */
        .glass-header {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(226, 232, 240, 0.5);
        }

        /* Input Premium Effects */
        .input-premium {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .input-premium:focus {
            box-shadow: 0 10px 25px -5px rgba(234, 88, 12, 0.1), 0 8px 10px -6px rgba(234, 88, 12, 0.1);
        }
    </style>
</head>

<body class="bg-slate-50 min-h-screen">
    <!-- Main UI Container -->
    <div class="flex">
        <!-- Sidebar placeholder (Included in each page) -->

        <!-- Main Content Wrapper -->
        <div class="flex-1 ml-0 lg:ml-72 transition-all duration-300">
            <!-- Top Navigation Bar -->
            <div
                class="glass-header sticky top-0 z-40 px-8 py-4 flex justify-between items-center bg-white/80 backdrop-blur-md border-b border-slate-200">
                <button class="lg:hidden text-slate-500 hover:text-slate-900 transition-colors">
                    <i class="fas fa-bars text-xl"></i>
                </button>

                <div class="flex items-center gap-6 ml-auto">
                    <div class="relative group">
                        <button class="flex items-center gap-3 hover:bg-slate-50 py-2 px-4 rounded-xl transition-all">
                            <div
                                class="w-10 h-10 rounded-full bg-slate-900 text-white flex items-center justify-center font-bold text-sm">
                                <?= strtoupper(substr($_SESSION['admin_name'] ?? 'A', 0, 1)) ?>
                            </div>
                            <div class="text-left hidden md:block">
                                <p class="text-sm font-black text-slate-800 leading-tight">
                                    <?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?>
                                </p>
                                <p class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Administrator
                                </p>
                            </div>
                            <i class="fas fa-chevron-down text-slate-300 text-xs"></i>
                        </button>

                        <!-- Dropdown -->
                        <div
                            class="absolute right-0 top-full mt-2 w-56 bg-white rounded-2xl shadow-xl border border-slate-100 p-2 hidden group-hover:block transition-all transform origin-top-right z-50">
                            <a href="<?= $base_url ?>profile/update-profile.php"
                                class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-50 text-sm font-bold text-slate-600 hover:text-slate-900 transition-colors">
                                <i class="fas fa-user-circle text-slate-400"></i> Update Profile
                            </a>
                            <a href="<?= $base_url ?>profile/change-password.php"
                                class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-50 text-sm font-bold text-slate-600 hover:text-slate-900 transition-colors">
                                <i class="fas fa-key text-slate-400"></i> Change Password
                            </a>
                            <div class="h-px bg-slate-100 my-1"></div>
                            <a href="<?= $base_url ?>logout.php"
                                class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-red-50 text-sm font-bold text-red-500 hover:text-red-600 transition-colors">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                        </div>
                    </div>
                </div>
            </div>