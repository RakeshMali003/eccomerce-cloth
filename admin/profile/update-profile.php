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

// Handle Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);

    if ($name && $email) {
        $stmt = $pdo->prepare("UPDATE admin SET username = ?, email = ?, phone = ? WHERE admin_id = ?");
        if ($stmt->execute([$name, $email, $phone, $admin_id])) {
            $_SESSION['admin_name'] = $name; // Update session
            $msg = "Profile updated successfully!";
        } else {
            $err = "Failed to update profile.";
        }
    } else {
        $err = "Name and Email are required.";
    }
}

// Fetch Current Data
$stmt = $pdo->prepare("SELECT * FROM admin WHERE admin_id = ?");
$stmt->execute([$admin_id]);
$admin = $stmt->fetch();
?>

<main class="p-6 lg:p-12 bg-[#f8fafc] min-h-screen">
    <div class="max-w-xl mx-auto">
        <div class="mb-10 text-center">
            <h2 class="text-3xl font-black tracking-tighter text-slate-900">My Profile<span
                    class="text-orange-600">.</span></h2>
            <p class="text-slate-400 text-sm font-medium">Manage your personal information</p>
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
            <div class="flex justify-center mb-8">
                <div
                    class="w-24 h-24 rounded-full bg-slate-900 text-white flex items-center justify-center text-3xl font-black">
                    <?= strtoupper(substr($admin['name'], 0, 1)) ?>
                </div>
            </div>

            <form method="POST" class="space-y-6">
                <div class="space-y-1">
                    <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Full Name</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($admin['name']) ?>" required
                        class="w-full px-6 py-4 bg-slate-50 rounded-2xl border-none focus:ring-2 focus:ring-orange-500/20 font-bold outline-none text-slate-900">
                </div>

                <div class="space-y-1">
                    <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Email Address</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($admin['email']) ?>" required
                        class="w-full px-6 py-4 bg-slate-50 rounded-2xl border-none focus:ring-2 focus:ring-orange-500/20 font-bold outline-none text-slate-900">
                </div>

                <div class="space-y-1">
                    <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Phone Number</label>
                    <input type="text" name="phone" value="<?= htmlspecialchars($admin['phone'] ?? '') ?>"
                        class="w-full px-6 py-4 bg-slate-50 rounded-2xl border-none focus:ring-2 focus:ring-orange-500/20 font-bold outline-none text-slate-900">
                </div>

                <button type="submit"
                    class="w-full bg-slate-900 text-white py-5 rounded-2xl font-black uppercase tracking-widest hover:bg-orange-600 transition-all shadow-lg mt-4">
                    Save Changes
                </button>
            </form>
        </div>
    </div>
</main>
<?php include $base_path . "includes/admin-footer.php"; ?>