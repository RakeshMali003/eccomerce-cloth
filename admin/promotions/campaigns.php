<?php
$base_path = $_SERVER['DOCUMENT_ROOT'] . '/ecommerce-website/';
require_once "../../config/database.php";

include $base_path . 'includes/admin-header.php';
include $base_path . 'includes/sidebar.php';
include $base_path . 'includes/notifications.php';

// Fetch current campaigns (simulated or from discounts table)
$stmt = $pdo->query("SELECT * FROM discounts WHERE status = 'active' ORDER BY created_at DESC");
$campaigns = $stmt->fetchAll();
?>

<main class="p-6 lg:p-12 bg-[#f8fafc] min-h-screen">
    <div class="mb-10">
        <h2 class="text-3xl font-black tracking-tighter text-slate-900">Campaign Manager<span
                class="text-indigo-600">.</span></h2>
        <p class="text-slate-400 text-sm font-medium">Coordinate multi-channel marketing efforts and seasonal sales
            events.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-2 space-y-6">
            <div class="bg-white p-10 rounded-[3rem] border border-slate-100 shadow-sm">
                <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-6 border-b pb-4">Active
                    Sprints</h3>
                <div class="space-y-4">
                    <?php foreach ($campaigns as $c): ?>
                        <div
                            class="flex items-center justify-between p-6 bg-slate-50 rounded-3xl border border-transparent hover:border-indigo-100 transition-all">
                            <div class="flex items-center gap-4">
                                <div
                                    class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-indigo-600 shadow-sm">
                                    <i class="fas fa-rocket"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-black text-slate-900">
                                        <?= htmlspecialchars($c['code']) ?>
                                    </p>
                                    <p class="text-[10px] text-slate-400 font-bold uppercase">
                                        <?= date('M d', strtotime($c['start_date'])) ?> -
                                        <?= date('M d', strtotime($c['end_date'])) ?>
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-xs font-black text-indigo-600">
                                    <?= $c['value'] ?>
                                    <?= $c['type'] == 'percentage' ? '%' : ' OFF' ?>
                                </p>
                                <span class="text-[9px] font-black text-slate-400 uppercase">Live</span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <?php if (empty($campaigns)): ?>
                        <div class="text-center py-10">
                            <p class="text-sm font-bold text-slate-300 italic">No active campaigns running.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="md:col-span-1 space-y-6">
            <div class="bg-indigo-900 p-8 rounded-[3rem] text-white">
                <h3 class="text-xs font-black uppercase tracking-[0.2em] text-indigo-300 mb-6">Omnichannel Reach</h3>
                <div class="space-y-6">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-indigo-800 rounded-xl flex items-center justify-center"><i
                                class="fas fa-paper-plane"></i></div>
                        <div>
                            <p class="text-xs font-black">Push Notifications</p>
                            <p class="text-[10px] text-indigo-200">Broadcast to all devices.</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-indigo-800 rounded-xl flex items-center justify-center"><i
                                class="fas fa-envelope"></i></div>
                        <div>
                            <p class="text-xs font-black">Email Blast</p>
                            <p class="text-[10px] text-indigo-200">Sync with newsletter list.</p>
                        </div>
                    </div>
                </div>
                <button onclick="window.location.href='promo-codes.php'"
                    class="w-full mt-10 bg-white text-indigo-900 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-orange-600 hover:text-white transition-all">Launch
                    Phase</button>
            </div>
        </div>
    </div>
</main>
<?php include $base_path . 'includes/admin-footer.php'; ?>
</body>

</html>