<?php
require_once __DIR__ . '/../../config/database.php';

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        // Logic to send OTP would go here. For now, redirect to OTP page.
        header("Location: otp-verification.php?email=" . urlencode($email));
        exit();
    } else {
        $error = "Email not found in our records.";
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
        <a href="login.php"
            class="absolute -top-12 left-0 text-slate-400 hover:text-orange-600 font-bold text-xs uppercase tracking-widest transition">
            <i class="fas fa-arrow-left mr-2"></i> Back to Login
        </a>

        <div class="text-center mb-8">
            <h1 class="text-3xl font-black tracking-tight">Forgot Password</h1>
            <p class="text-slate-400 text-sm mt-2">Enter your email and we'll send an OTP</p>
        </div>

        <?php if ($error): ?>
            <div
                class="bg-red-50 text-red-500 p-4 rounded-2xl mb-6 text-xs font-bold uppercase text-center border border-red-100">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST" class="space-y-4">
            <input type="email" name="email" placeholder="Email Address" required
                class="w-full px-6 py-4 bg-slate-50 rounded-2xl outline-none focus:ring-2 focus:ring-orange-500/20 font-semibold">
            <button type="submit"
                class="w-full bg-orange-600 text-white py-5 rounded-2xl font-black uppercase tracking-widest hover:bg-slate-900 transition-all shadow-xl shadow-orange-200">Send
                Reset Code</button>
        </form>
    </div>
</body>

</html>