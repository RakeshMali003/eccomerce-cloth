<?php

require_once __DIR__ . '/../../config/database.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $pass = $_POST['password'];
    $role = 'user';

    if (strlen($pass) < 8) {
        $error = "Password must be at least 8 digits/characters.";
    } else {
        $hashed_pass = password_hash($pass, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, password, role, status, created_at) VALUES (?, ?, ?, ?, ?, 'active', NOW())");
        try {
            $stmt->execute([$name, $email, $phone, $hashed_pass, $role]);
            header("Location: login.php?msg=Registration successful! Please login.");
        } catch (Exception $e) {
            $error = "Email or Phone already registered.";
        }
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
            <h1 class="text-3xl font-black tracking-tight">Register</h1>
            <p class="text-slate-400 text-sm mt-2">Join the Joshi Electricals Community</p>
        </div>

        <?php if ($error): ?>
            <div
                class="bg-red-50 text-red-500 p-4 rounded-2xl mb-6 text-xs font-bold uppercase text-center border border-red-100">
                <?php echo $error; ?></div>
        <?php endif; ?>

        <form action="" method="POST" class="space-y-4">
            <input type="text" name="name" placeholder="Full Name" required
                class="w-full px-6 py-4 bg-slate-50 rounded-2xl outline-none focus:ring-2 focus:ring-orange-500/20 font-semibold">
            <input type="email" name="email" placeholder="Email Address" required
                class="w-full px-6 py-4 bg-slate-50 rounded-2xl outline-none focus:ring-2 focus:ring-orange-500/20 font-semibold">
            <input type="text" name="phone" placeholder="Mobile Number" required
                class="w-full px-6 py-4 bg-slate-50 rounded-2xl outline-none focus:ring-2 focus:ring-orange-500/20 font-semibold">
            <input type="password" name="password" placeholder="Password (Min. 8 Digits)" required
                class="w-full px-6 py-4 bg-slate-50 rounded-2xl outline-none focus:ring-2 focus:ring-orange-500/20 font-semibold">
            <button type="submit"
                class="w-full bg-orange-600 text-white py-5 rounded-2xl font-black uppercase tracking-widest hover:bg-slate-900 transition-all shadow-xl shadow-orange-200">Register</button>
        </form>

        <p class="text-center mt-8 text-xs font-bold text-slate-400 uppercase">Already have an account? <a
                href="login.php" class="text-orange-600 underline">Login Here</a></p>
    </div>
</body>

</html>