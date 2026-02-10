<?php
session_start();
require_once __DIR__ . '/../../includes/functions.php'; // Includes Core classes
require_once __DIR__ . '/../../core/RateLimiter.php';

use Core\Database;
use Core\RateLimiter;

$db = Database::getInstance()->getConnection();
$limiter = new RateLimiter();
$ip = $_SERVER['REMOTE_ADDR'];
$error = "";

// MAX 5 Attempts per minute
if (!$limiter->check($ip, 5, 60)) {
    die("<h1>Too Many Attempts</h1><p>Please wait 1 minute before trying again.</p>");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['identifier']; // This can be email or phone
    $pass = $_POST['password'];

    $stmt = $db->prepare("SELECT * FROM users WHERE email = ? OR phone = ?");
    $stmt->execute([$id, $id]);
    $user = $stmt->fetch();

    if ($user && password_verify($pass, $user['password'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['role'] = $user['role'];
        header("Location: ../../index.php");
        exit();
    } else {
        $error = "Invalid Credentials. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-slate-50 flex items-center justify-center min-h-screen p-6">
    <div class="bg-white p-10 rounded-[3rem] shadow-2xl w-full max-w-md border border-slate-100 relative">
        <a href="../../index.php"
            class="absolute -top-12 left-0 text-slate-400 hover:text-orange-600 font-bold text-xs uppercase tracking-widest transition">
            <i class="fas fa-arrow-left mr-2"></i> Back to Website
        </a>

        <div class="text-center mb-8">
            <h1 class="text-3xl font-black tracking-tight text-slate-900">Login</h1>
            <p class="text-slate-400 text-sm mt-2">Welcome back to Joshi Electricals</p>
        </div>

        <?php if ($error): ?>
            <div
                class="bg-orange-50 text-orange-600 p-4 rounded-2xl mb-6 text-xs font-bold uppercase text-center border border-orange-100">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST" class="space-y-4">
            <input type="text" name="identifier" placeholder="Email or Mobile" required
                class="w-full px-6 py-4 bg-slate-50 rounded-2xl outline-none focus:ring-2 focus:ring-orange-500/20 font-semibold">
            <input type="password" name="password" placeholder="Password" required
                class="w-full px-6 py-4 bg-slate-50 rounded-2xl outline-none focus:ring-2 focus:ring-orange-500/20 font-semibold">
            <button type="submit"
                class="w-full bg-slate-900 text-white py-5 rounded-2xl font-black uppercase tracking-widest hover:bg-orange-600 transition-all shadow-xl">Login
                Securely</button>
        </form>

        <div class="relative my-8">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-slate-100"></div>
            </div>
            <div class="relative flex justify-center text-xs uppercase"><span
                    class="bg-white px-4 text-slate-300 font-bold">Or</span></div>
        </div>

        <button
            class="w-full border border-slate-200 py-4 rounded-2xl flex items-center justify-center gap-3 hover:bg-slate-50 transition-all">
            <img src="https://www.gstatic.com/images/branding/product/1x/gsa_512dp.png" class="w-5 h-5">
            <span class="text-sm font-bold text-slate-600">Google Access</span>
        </button>

        <p class="text-center mt-8 text-xs font-bold text-slate-400 uppercase">New User? <a href="register.php"
                class="text-orange-600 underline">Create Account</a></p>
    </div>
</body>

</html>