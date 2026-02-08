<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Security Check: Only admins allowed
if (!isset($_SESSION['admin_id'])) {
    // Determine the path back to login
    $request_uri = $_SERVER['REQUEST_URI'];
    if (strpos($request_uri, '/admin/') !== false && basename($_SERVER['PHP_SELF']) !== 'index.php') {
        header("Location: /ecommerce-website/admin/index.php");
        exit();
    }
}

$base_url = "http://localhost/ecommerce-website/admin/";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gurukrupa Admin | Command Center</title>

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
            <!-- Top bar / Global Navigation branding could go here -->