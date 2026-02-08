<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/ecommerce-website/config/database.php';

$email = $_GET['email'] ?? '';
$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $otp = $_POST['otp'];
    // Placeholder OTP check (e.g., 123456)
    if ($otp === '123456') {
        header("Location: reset-password.php?email=" . urlencode($email));
        exit();
    } else {
        $error = "Invalid OTP. Please try '123456' for the demo.";
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
            <h1 class="text-3xl font-black tracking-tight">Verify OTP</h1>
            <p class="text-slate-400 text-sm mt-2">Code sent to
                <?php echo htmlspecialchars($email); ?>
            </p>
        </div>

        <?php if ($error): ?>
            <div
                class="bg-red-50 text-red-500 p-4 rounded-2xl mb-6 text-xs font-bold uppercase text-center border border-red-100">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST" class="space-y-4 text-center">
            <input type="text" name="otp" placeholder="Enter 6-digit OTP" maxlength="6" required
                class="w-full px-6 py-4 bg-slate-50 rounded-2xl outline-none focus:ring-2 focus:ring-orange-500/20 font-semibold text-center text-xl tracking-[0.5em]">
            <p class="text-[10px] text-slate-400 font-bold uppercase">Demo Code: <span
                    class="text-orange-600">123456</span></p>
            <button type="submit"
                class="w-full bg-slate-900 text-white py-5 rounded-2xl font-black uppercase tracking-widest hover:bg-orange-600 transition-all shadow-xl shadow-slate-200">Verify
                & Proceed</button>
        </form>
    </div>
</body>

</html>