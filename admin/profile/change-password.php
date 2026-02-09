<?php
$base_path = $_SERVER['DOCUMENT_ROOT'] . '/ecommerce-website/';
require_once "../../config/database.php";
require_once "../../includes/functions.php";

include $base_path . 'includes/admin-header.php';
include $base_path . 'includes/sidebar.php';
include $base_path . 'includes/notifications.php';

// Auth Check
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../index.php");
    exit;
}

$admin_id = $_SESSION['admin_id'];
$msg = '';
$err = '';

// Handle Password Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_pass = $_POST['current_password'];
    $new_pass = $_POST['new_password'];
    $confirm_pass = $_POST['confirm_password'];

    if ($new_pass !== $confirm_pass) {
        $err = "New passwords do not match.";
    } else {
        $stmt = $pdo->prepare("SELECT password FROM admin WHERE admin_id = ?");
        $stmt->execute([$admin_id]);
        $stored_pass = $stmt->fetchColumn();

        // Since we don't know the hashing algo used previously, assuming password_verify
        // If plain text, adjust accordingly. Most systems use password_hash.
        // Checking if password matches (assuming it might be plain text for legacy or hashed)
        // Since I don't see the login logic, I'll attempt standard verification

        $verified = false;
        if (password_verify($current_pass, $stored_pass)) {
            $verified = true;
        } elseif ($current_pass === $stored_pass) { // Fallback for plain text
            $verified = true;
        }

        if ($verified) {
            $new_hash = password_hash($new_pass, PASSWORD_DEFAULT);
            $update = $pdo->prepare("UPDATE admin SET password = ? WHERE admin_id = ?");
            if ($update->execute([$new_hash, $admin_id])) {
                $msg = "Password changed successfully!";
            } else {
                $err = "Database error.";
            }
        } else {
            $err = "Incorrect current password.";
        }
    }
}
?>

<main class="p-6 lg:p-12 bg-[#f8fafc] min-h-screen">
    <div class="max-w-xl mx-auto">
        <div class="mb-10 text-center">
            <h2 class="text-3xl font-black tracking-tighter text-slate-900">Security<span
                    class="text-orange-600">.</span></h2>
            <p class="text-slate-400 text-sm font-medium">Update your account password</p>
        </div>

        <?php if ($msg): ?>
            <div
                class="bg-emerald-100 text-emerald-700 p-4 rounded-xl mb-6 font-bold text-center border-l-4 border-emerald-500">
                <?= $msg ?>
            </div>
        <?php endif; ?>

        <?php if ($err): ?>
            <div class="bg-red-100 text-red-700 p-4 rounded-xl mb-6 font-bold text-center border-l-4 border-red-500">
                <?= $err ?>
            </div>
        <?php endif; ?>

        <div class="bg-white p-10 rounded-[2.5rem] shadow-xl border border-slate-100">
            <form method="POST" class="space-y-6">
                <div class="space-y-1">
                    <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Current Password</label>
                    <input type="password" name="current_password" required
                        class="w-full px-6 py-4 bg-slate-50 rounded-2xl border-none focus:ring-2 focus:ring-orange-500/20 font-bold outline-none text-slate-900">
                </div>

                <div class="space-y-1">
                    <label class="text-[10px] font-black uppercase text-slate-400 ml-2">New Password</label>
                    <input type="password" name="new_password" required minlength="6"
                        class="w-full px-6 py-4 bg-slate-50 rounded-2xl border-none focus:ring-2 focus:ring-orange-500/20 font-bold outline-none text-slate-900">
                </div>

                <div class="space-y-1">
                    <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Confirm New Password</label>
                    <input type="password" name="confirm_password" required minlength="6"
                        class="w-full px-6 py-4 bg-slate-50 rounded-2xl border-none focus:ring-2 focus:ring-orange-500/20 font-bold outline-none text-slate-900">
                </div>

                <button type="submit"
                    class="w-full bg-slate-900 text-white py-5 rounded-2xl font-black uppercase tracking-widest hover:bg-orange-600 transition-all shadow-lg mt-4">
                    Update Password
                </button>
            </form>
        </div>
    </div>
</main>
<?php include $base_path . "includes/admin-footer.php"; ?>