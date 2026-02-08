<?php
$base_path = $_SERVER['DOCUMENT_ROOT'] . '/ecommerce-website/';
require_once "../../config/database.php";

include $base_path . 'includes/admin-header.php';
include $base_path . 'includes/sidebar.php';
include $base_path . 'includes/notifications.php';

// Fetch top customers by loyalty (simulated calculation)
$stmt = $pdo->query("SELECT u.user_id, u.first_name, u.last_name, u.email, COUNT(o.order_id) as order_count, SUM(o.total_amount) as total_spent 
                   FROM users u 
                   JOIN orders o ON u.user_id = o.user_id 
                   WHERE u.role = 'customer' 
                   GROUP BY u.user_id 
                   ORDER BY total_spent DESC LIMIT 10");
$loyal_customers = $stmt->fetchAll();
?>

<main class="p-6 lg:p-12 bg-[#f8fafc] min-h-screen">
    <div class="mb-10">
        <h2 class="text-3xl font-black tracking-tighter text-slate-900">Loyalty & Retention<span
                class="text-indigo-600">.</span></h2>
        <p class="text-slate-400 text-sm font-medium">Rewarding your most valuable patrons with points and exclusive
            tiers.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="md:col-span-2 space-y-8">
            <div class="bg-white p-10 rounded-[3rem] border border-slate-100 shadow-sm">
                <h3 class="text-xs font-black uppercase tracking-[0.3em] text-slate-400 mb-8">Tier Manifest (Coming
                    Soon)</h3>
                <div class="grid grid-cols-3 gap-4 mb-10">
                    <div class="p-6 rounded-3xl bg-slate-50 border border-slate-100 text-center">
                        <div
                            class="w-10 h-10 bg-white rounded-xl shadow-sm mx-auto mb-3 flex items-center justify-center text-amber-600 font-black">
                            B</div>
                        <p class="text-[10px] font-black uppercase text-slate-900">Bronze</p>
                    </div>
                    <div
                        class="p-6 rounded-3xl bg-slate-50 border border-indigo-100 text-center relative overflow-hidden">
                        <div
                            class="w-10 h-10 bg-indigo-600 text-white rounded-xl shadow-sm mx-auto mb-3 flex items-center justify-center font-black">
                            S</div>
                        <p class="text-[10px] font-black uppercase text-indigo-600">Silver</p>
                    </div>
                    <div class="p-6 rounded-3xl bg-slate-50 border border-slate-100 text-center">
                        <div
                            class="w-10 h-10 bg-white rounded-xl shadow-sm mx-auto mb-3 flex items-center justify-center text-amber-500 font-black">
                            G</div>
                        <p class="text-[10px] font-black uppercase text-slate-900">Gold</p>
                    </div>
                </div>

                <h3 class="text-xs font-black uppercase tracking-[0.3em] text-slate-400 mb-6">Top Patrons</h3>
                <div class="space-y-4">
                    <?php foreach ($loyal_customers as $index => $c): ?>
                        <div class="flex items-center justify-between p-5 bg-slate-50/50 rounded-2xl">
                            <div class="flex items-center gap-4">
                                <span class="text-xs font-black text-slate-300">#
                                    <?= $index + 1 ?>
                                </span>
                                <div>
                                    <p class="text-xs font-black text-slate-900">
                                        <?= htmlspecialchars($c['first_name'] . ' ' . $c['last_name']) ?>
                                    </p>
                                    <p class="text-[9px] text-slate-400 font-bold uppercase">
                                        <?= $c['order_count'] ?> Orders
                                    </p>
                                </div>
                            </div>
                            <p class="text-xs font-black text-indigo-600">₹
                                <?= number_format($c['total_spent'], 2) ?>
                            </p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="md:col-span-1">
            <div class="bg-white p-8 rounded-[3rem] border border-slate-100 shadow-sm sticky top-12">
                <h4 class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-400 mb-6">Redemption Logic</h4>
                <div class="space-y-6">
                    <div>
                        <label class="text-[9px] font-black uppercase text-slate-900 mb-2 block">1 Point Value</label>
                        <input type="text" value="₹1.00" readonly
                            class="w-full bg-slate-50 border border-slate-100 px-4 py-3 rounded-xl text-xs font-black outline-none">
                    </div>
                    <div>
                        <label class="text-[9px] font-black uppercase text-slate-900 mb-2 block">Min Redemption</label>
                        <input type="text" value="500 Points" readonly
                            class="w-full bg-slate-50 border border-slate-100 px-4 py-3 rounded-xl text-xs font-black outline-none">
                    </div>
                    <p
                        class="text-[10px] text-slate-400 font-medium leading-relaxed bg-indigo-50 p-4 rounded-2xl italic">
                        "Loyalty is not a program, it's a relationship. Coming soon: Automated points issuance."
                    </p>
                </div>
            </div>
        </div>
    </div>
</main>
<?php include $base_path . 'includes/admin-footer.php'; ?>
</body>

</html>