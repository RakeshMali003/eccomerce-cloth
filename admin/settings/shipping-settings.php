<?php
$base_path = $_SERVER['DOCUMENT_ROOT'] . '/ecommerce-website/';
require_once "../../config/database.php";

include $base_path . 'includes/admin-header.php';
include $base_path . 'includes/sidebar.php';
include $base_path . 'includes/notifications.php';

// Fetch shipping settings
$stmt = $pdo->prepare("SELECT * FROM settings WHERE group_name = 'shipping' ORDER BY setting_key");
$stmt->execute();
$settings = $stmt->fetchAll();
?>

<main class="p-6 lg:p-12 bg-[#f8fafc] min-h-screen">
    <div class="mb-10 flex items-center gap-4">
        <a href="site-settings.php"
            class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-slate-400 hover:text-slate-900 transition-all shadow-sm">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h2 class="text-3xl font-black tracking-tighter text-slate-900">Logistics Config<span
                    class="text-orange-600">.</span></h2>
            <p class="text-slate-400 text-sm font-medium">Define shipping rates, free delivery thresholds, and carrier
                preferences.</p>
        </div>
    </div>

    <form action="process_settings.php" method="POST" class="max-w-4xl space-y-10">
        <div class="bg-white p-10 rounded-[3rem] border border-slate-100 shadow-sm space-y-8">
            <?php foreach ($settings as $s): ?>
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase text-slate-400 ml-4">
                        <?= str_replace('_', ' ', $s['setting_key']) ?>
                    </label>
                    <input type="text" name="settings[<?= $s['setting_key'] ?>]"
                        value="<?= htmlspecialchars($s['setting_value']) ?>"
                        class="w-full bg-slate-50 px-8 py-5 rounded-[2rem] text-sm font-black outline-none border-2 border-transparent focus:border-orange-500 transition-all">
                </div>
            <?php endforeach; ?>
        </div>

        <button type="submit"
            class="w-full bg-slate-900 text-white py-8 rounded-[3rem] font-black uppercase tracking-[0.3em] shadow-2xl shadow-slate-200 hover:bg-orange-600 transition-all group">
            Apply Shipping Logic <i class="fas fa-truck ml-4 group-hover:translate-x-3 transition-transform"></i>
        </button>
    </form>
</main>
<?php include $base_path . 'includes/admin-footer.php'; ?>
</body>

</html>