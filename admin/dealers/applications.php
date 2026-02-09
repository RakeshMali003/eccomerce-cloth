<?php
$base_path = $_SERVER['DOCUMENT_ROOT'] . '/ecommerce-website/';
require_once "../../config/database.php";
require_once "../../includes/functions.php";

include $base_path . 'includes/admin-header.php';
include $base_path . 'includes/sidebar.php';
include $base_path . 'includes/notifications.php';

if (!has_permission('dashboard')) {
    die("Access Denied");
}

// Handle Status Update
if (isset($_POST['update_status'])) {
    $id = $_POST['app_id'];
    $status = $_POST['status'];
    $pdo->prepare("UPDATE dealer_applications SET status = ? WHERE application_id = ?")->execute([$status, $id]);
    echo "<script>window.location.href='applications.php';</script>";
}

// Fetch Applications
$stmt = $pdo->query("SELECT * FROM dealer_applications ORDER BY created_at DESC");
$apps = $stmt->fetchAll();
?>

<main class="p-6 lg:p-12 bg-[#f8fafc] min-h-screen">
    <div class="mb-10">
        <h2 class="text-3xl font-black tracking-tighter text-slate-900">Dealer Applications<span
                class="text-orange-600">.</span></h2>
        <p class="text-slate-400 text-sm font-medium">Review and manage partnership requests</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        <?php foreach ($apps as $a): ?>
            <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-slate-100 flex flex-col h-full">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <span
                            class="px-3 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest 
                        <?= $a['status'] === 'approved' ? 'bg-emerald-100 text-emerald-600' : ($a['status'] === 'rejected' ? 'bg-red-100 text-red-600' : 'bg-amber-100 text-amber-600') ?>">
                            <?= $a['status'] ?>
                        </span>
                        <p class="text-[10px] text-slate-400 font-bold mt-2">
                            <?= date('d M Y', strtotime($a['created_at'])) ?>
                        </p>
                    </div>
                </div>

                <h3 class="text-xl font-black text-slate-900 mb-1">
                    <?= htmlspecialchars($a['shop_name']) ?>
                </h3>
                <p class="text-sm font-bold text-slate-600 mb-4">
                    <?= htmlspecialchars($a['owner_name']) ?>
                </p>

                <div class="space-y-2 mb-6 flex-1">
                    <p class="text-xs text-slate-500 flex items-center gap-2"><i class="fas fa-phone w-4"></i>
                        <?= htmlspecialchars($a['mobile']) ?>
                    </p>
                    <p class="text-xs text-slate-500 flex items-center gap-2"><i class="fas fa-map-marker-alt w-4"></i>
                        <?= htmlspecialchars($a['city_state']) ?>
                    </p>
                    <?php if ($a['gst_number']): ?>
                        <p class="text-xs text-slate-500 flex items-center gap-2"><i class="fas fa-receipt w-4"></i> GST:
                            <?= htmlspecialchars($a['gst_number']) ?>
                        </p>
                    <?php endif; ?>
                </div>

                <form method="POST" class="flex gap-2 pt-4 border-t border-slate-50">
                    <input type="hidden" name="update_status" value="1">
                    <input type="hidden" name="app_id" value="<?= $a['application_id'] ?>">

                    <?php if ($a['status'] !== 'approved'): ?>
                        <button type="submit" name="status" value="approved"
                            class="flex-1 bg-emerald-50 text-emerald-600 py-3 rounded-xl text-[10px] font-black uppercase hover:bg-emerald-600 hover:text-white transition-all">
                            Approve
                        </button>
                    <?php endif; ?>

                    <?php if ($a['status'] !== 'rejected'): ?>
                        <button type="submit" name="status" value="rejected"
                            class="flex-1 bg-red-50 text-red-600 py-3 rounded-xl text-[10px] font-black uppercase hover:bg-red-600 hover:text-white transition-all">
                            Reject
                        </button>
                    <?php endif; ?>
                </form>
            </div>
        <?php endforeach; ?>

        <?php if (empty($apps)): ?>
            <div class="col-span-full py-20 text-center text-slate-400 font-bold">No applications received yet.</div>
        <?php endif; ?>
    </div>
</main>
<?php include $base_path . "includes/admin-footer.php"; ?>