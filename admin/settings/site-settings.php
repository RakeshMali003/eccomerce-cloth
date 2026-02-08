<?php
$base_path = $_SERVER['DOCUMENT_ROOT'] . '/ecommerce-website/';
require_once "../../config/database.php";

include $base_path . 'includes/admin-header.php';
include $base_path . 'includes/sidebar.php';
include $base_path . 'includes/notifications.php';

// Fetch all settings grouped
$stmt = $pdo->query("SELECT * FROM settings ORDER BY group_name, setting_key");
$all_settings = $stmt->fetchAll();

$grouped_settings = [];
foreach ($all_settings as $s) {
    $grouped_settings[$s['group_name']][] = $s;
}
?>

<main class="p-6 lg:p-12 bg-[#f8fafc] min-h-screen">
    <div class="mb-10">
        <h2 class="text-3xl font-black tracking-tighter text-slate-900">System Configuration<span
                class="text-orange-600">.</span></h2>
        <p class="text-slate-400 text-sm font-medium">Control global behavior, branding, and operational thresholds.</p>
    </div>

    <form action="process_settings.php" method="POST" class="space-y-12 max-w-4xl">
        <?php foreach ($grouped_settings as $group => $settings): ?>
            <div class="bg-white rounded-[3rem] border border-slate-100 shadow-sm overflow-hidden">
                <div class="px-10 py-6 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-xs font-black uppercase tracking-[0.3em] text-slate-400">
                        <?= $group ?> Parameters
                    </h3>
                    <div
                        class="w-8 h-8 rounded-lg bg-white flex items-center justify-center text-[10px] text-slate-300 shadow-sm">
                        <i class="fas fa-sliders-h"></i>
                    </div>
                </div>

                <div class="p-10 space-y-8">
                    <?php foreach ($settings as $s): ?>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-center">
                            <div class="md:col-span-1">
                                <label class="text-[10px] font-black uppercase text-slate-900 tracking-widest block mb-1">
                                    <?= str_replace('_', ' ', $s['setting_key']) ?>
                                </label>
                                <p class="text-[9px] text-slate-400 font-bold uppercase italic">Identifier:
                                    <?= $s['setting_key'] ?>
                                </p>
                            </div>
                            <div class="md:col-span-2">
                                <?php if ($s['setting_key'] == 'site_address'): ?>
                                    <textarea name="settings[<?= $s['setting_key'] ?>]"
                                        class="w-full bg-slate-50 p-5 rounded-[2rem] text-sm font-black outline-none border-2 border-transparent focus:border-orange-500 transition-all min-h-[100px]"><?= htmlspecialchars($s['setting_value']) ?></textarea>
                                <?php else: ?>
                                    <input type="text" name="settings[<?= $s['setting_key'] ?>]"
                                        value="<?= htmlspecialchars($s['setting_value']) ?>"
                                        class="w-full bg-slate-50 px-8 py-5 rounded-[2rem] text-sm font-black outline-none border-2 border-transparent focus:border-orange-500 transition-all">
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="flex justify-end pr-4">
            <button type="submit"
                class="bg-slate-900 text-white px-12 py-6 rounded-[2.5rem] font-black uppercase tracking-[0.3em] shadow-2xl shadow-slate-200 hover:bg-orange-600 transition-all group">
                Persist Configuration <i
                    class="fas fa-check-circle ml-4 group-hover:scale-125 transition-transform"></i>
            </button>
        </div>
    </form>
</main>
<?php include $base_path . 'includes/admin-footer.php'; ?>