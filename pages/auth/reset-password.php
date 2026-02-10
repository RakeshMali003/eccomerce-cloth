<?php
require_once __DIR__ . '/../../config/database.php';

$email = $_GET['email'] ?? '';
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pass = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if (strlen($pass) < 8) {
        $error = "Password must be at least 8 digits.";
    } elseif ($pass !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        $hashed_pass = password_hash($pass, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt->execute([$hashed_pass, $email]);
        header("Location: login.php?msg=Password updated successfully! Please login.");
        exit();
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
        <div class="text-center mb-8">
            <h1 class="text-3xl font-black tracking-tight">Reset Password</h1>
            <p class="text-slate-400 text-sm mt-2">Create a secure new password</p>
        </div>

        <?php if ($error): ?>
            <div
                class="bg-red-50 text-red-500 p-4 rounded-2xl mb-6 text-xs font-bold uppercase text-center border border-red-100">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST" class="space-y-4">
            <input type="password" name="password" placeholder="New Password" required
                class="w-full px-6 py-4 bg-slate-50 rounded-2xl outline-none focus:ring-2 focus:ring-orange-500/20 font-semibold">
            <input type="password" name="confirm_password" placeholder="Confirm Password" required
                class="w-full px-6 py-4 bg-slate-50 rounded-2xl outline-none focus:ring-2 focus:ring-orange-500/20 font-semibold">
            <button type="submit"
                class="w-full bg-slate-900 text-white py-5 rounded-2xl font-black uppercase tracking-widest hover:bg-orange-600 transition-all shadow-xl shadow-slate-200">Update
                Password</button>
        </form>
    </div>
</body>

</html>