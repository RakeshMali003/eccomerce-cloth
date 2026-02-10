<?php
require_once __DIR__ . '/loader.php'; // Handles Config, DB, Functions, Session

// Use PROJECT_ROOT defined in loader.php
include PROJECT_ROOT . '/includes/admin-header.php';
include PROJECT_ROOT . '/includes/sidebar.php';

use Core\Database;
use Core\Cache;

if (!has_permission('dashboard')) {
    echo "<h1>Access Denied</h1><p>Contact Administrator.</p>";
    exit;
}

$db = Database::getInstance()->getConnection();
$cache = new Cache();
$cacheKey = 'admin_dashboard_stats';

// Check if hard refresh requested
if (isset($_GET['refresh'])) {
    $cache->delete($cacheKey);
}

$stats = $cache->get($cacheKey);

if (!$stats) {
    // 1. Fetch Key Metrics
    $today_sales = $db->query("SELECT SUM(total_amount) FROM orders WHERE DATE(created_at) = CURDATE() AND order_status != 'cancelled'")->fetchColumn() ?? 0;
    $today_orders = $db->query("SELECT COUNT(*) FROM orders WHERE DATE(created_at) = CURDATE()")->fetchColumn() ?? 0;
    $new_customers = $db->query("SELECT COUNT(*) FROM users WHERE DATE(created_at) = CURDATE() AND role = 'retail'")->fetchColumn() ?? 0;
    $low_stock_count = $db->query("SELECT COUNT(*) FROM products WHERE stock <= min_stock_level AND status = 1")->fetchColumn() ?? 0;

    // 2. Fetch Order Status Counts
    $status_counts = $db->query("SELECT order_status, COUNT(*) as qty FROM orders GROUP BY order_status")->fetchAll(PDO::FETCH_KEY_PAIR);

    // 3. Recent Logistics (Orders) names join
    $recent_orders = $db->query("SELECT o.*, u.name as customer_name, u.city 
                                 FROM orders o 
                                 JOIN users u ON o.user_id = u.user_id 
                                 ORDER BY o.created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

    // 4. Stock Alerts
    $stock_alerts = $db->query("SELECT name, stock FROM products WHERE stock <= min_stock_level AND status = 1 LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

    // 5. Payment Pulse
    $online_received = $db->query("SELECT SUM(total_amount) FROM orders WHERE payment_method = 'online' AND payment_status = 'paid'")->fetchColumn() ?? 0;
    $cod_pending = $db->query("SELECT SUM(total_amount) FROM orders WHERE payment_method = 'cod' AND payment_status = 'pending'")->fetchColumn() ?? 0;
    $refunded = $db->query("SELECT SUM(total_amount) FROM orders WHERE order_status = 'returned'")->fetchColumn() ?? 0;

    // 6. Chart Data
    $chart_data = $db->query("SELECT DATE_FORMAT(created_at, '%a') as day, SUM(total_amount) as total 
                              FROM orders 
                              WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
                              GROUP BY DATE(created_at) 
                              ORDER BY created_at ASC")->fetchAll(PDO::FETCH_KEY_PAIR);

    $stats = compact('today_sales', 'today_orders', 'new_customers', 'low_stock_count', 'status_counts', 'recent_orders', 'stock_alerts', 'online_received', 'cod_pending', 'refunded', 'chart_data');

    // Cache for 5 minutes
    $cache->set($cacheKey, $stats, 300);
} else {
    extract($stats);
}

// Chart Prep
$days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
$sales_points = [];
foreach ($days as $day) {
    $sales_points[] = $chart_data[$day] ?? 0;
}
?>

<main class="p-4 md:p-10 flex-1">
    <div class="md:hidden mb-6">
        <div class="flex items-center gap-3 bg-white border border-slate-200 px-4 py-3 rounded-2xl shadow-sm flex-1">
            <i class="fas fa-search text-slate-400"></i>
            <input type="text" placeholder="Search system..."
                class="bg-transparent outline-none text-sm font-semibold w-full">
        </div>
        <a href="?refresh=true"
            class="bg-orange-50 text-orange-600 p-3 rounded-xl hover:bg-orange-600 hover:text-white transition-all">
            <i class="fas fa-sync-alt"></i>
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-md transition-all group">
            <div class="flex justify-between items-start mb-4">
                <div
                    class="w-12 h-12 bg-orange-50 text-orange-600 rounded-2xl flex items-center justify-center text-xl group-hover:bg-orange-600 group-hover:text-white transition-all">
                    <i class="fas fa-indian-rupee-sign"></i>
                </div>
                <span class="text-[10px] font-black text-emerald-500 bg-emerald-50 px-2 py-1 rounded-lg">+12.5%</span>
            </div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Today's Sales</p>
            <h3 class="text-2xl font-black text-slate-900 mt-1">₹<?= number_format($today_sales) ?></h3>
        </div>

        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-md transition-all group">
            <div class="flex justify-between items-start mb-4">
                <div
                    class="w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center text-xl group-hover:bg-blue-600 group-hover:text-white transition-all">
                    <i class="fas fa-shopping-bag"></i>
                </div>
                <span class="text-[10px] font-black text-slate-400 bg-slate-50 px-2 py-1 rounded-lg">Realtime</span>
            </div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Today's Orders</p>
            <h3 class="text-2xl font-black text-slate-900 mt-1"><?= $today_orders ?></h3>
        </div>

        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-md transition-all group">
            <div class="flex justify-between items-start mb-4">
                <div
                    class="w-12 h-12 bg-purple-50 text-purple-600 rounded-2xl flex items-center justify-center text-xl group-hover:bg-purple-600 group-hover:text-white transition-all">
                    <i class="fas fa-user-plus"></i>
                </div>
            </div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">New Customers</p>
            <h3 class="text-2xl font-black text-slate-900 mt-1"><?= $new_customers ?></h3>
        </div>

        <div
            class="bg-red-50 p-6 rounded-[2rem] border border-red-100 shadow-sm hover:shadow-md transition-all group cursor-pointer">
            <div class="flex justify-between items-start mb-4">
                <div
                    class="w-12 h-12 bg-white text-red-600 rounded-2xl flex items-center justify-center text-xl shadow-sm">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
            </div>
            <p class="text-[10px] font-black text-red-400 uppercase tracking-widest">Low Stock Alert</p>
            <h3 class="text-2xl font-black text-red-600 mt-1"><?= str_pad($low_stock_count, 2, '0', STR_PAD_LEFT) ?>
                Items</h3>
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <a href="orders/orders-list.php?status=pending"
            class="bg-amber-50 border border-amber-100 p-4 rounded-2xl text-center hover:scale-105 transition-transform">
            <p class="text-[9px] font-black text-amber-600 uppercase">Pending</p>
            <h4 class="text-xl font-black text-amber-700"><?= $status_counts['pending'] ?? 0 ?></h4>
        </a>
        <a href="orders/orders-list.php?status=processing"
            class="bg-sky-50 border border-sky-100 p-4 rounded-2xl text-center hover:scale-105 transition-transform">
            <p class="text-[9px] font-black text-sky-600 uppercase">Processing</p>
            <h4 class="text-xl font-black text-sky-700"><?= $status_counts['processing'] ?? 0 ?></h4>
        </a>
        <a href="orders/orders-list.php?status=shipped"
            class="bg-indigo-50 border border-indigo-100 p-4 rounded-2xl text-center hover:scale-105 transition-transform">
            <p class="text-[9px] font-black text-indigo-600 uppercase">Shipped</p>
            <h4 class="text-xl font-black text-indigo-700"><?= $status_counts['shipped'] ?? 0 ?></h4>
        </a>
        <a href="orders/orders-list.php?status=returned"
            class="bg-slate-50 border border-slate-100 p-4 rounded-2xl text-center hover:scale-105 transition-transform">
            <p class="text-[9px] font-black text-slate-600 uppercase">Returns</p>
            <h4 class="text-xl font-black text-slate-700"><?= $status_counts['returned'] ?? 0 ?></h4>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

        <div class="lg:col-span-8 bg-white rounded-[2.5rem] p-8 border border-slate-100 shadow-sm">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-black text-slate-900">Recent Logistics</h3>
                <div class="flex gap-2">
                    <button class="bg-slate-50 text-slate-400 p-2 rounded-lg hover:text-orange-600"><i
                            class="fas fa-filter text-xs"></i></button>
                    <a href="orders.php"
                        class="text-[10px] font-black text-orange-600 uppercase tracking-widest bg-orange-50 px-4 py-2 rounded-xl">View
                        All</a>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr
                            class="text-[10px] font-black text-slate-300 uppercase tracking-widest border-b border-slate-50">
                            <th class="pb-4 px-2">Order ID</th>
                            <th class="pb-4">Customer</th>
                            <th class="pb-4">Amount</th>
                            <th class="pb-4">Mode</th>
                            <th class="pb-4">Status</th>
                            <th class="pb-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <?php foreach ($recent_orders as $ro): ?>
                            <tr class="group hover:bg-slate-50/50 transition-colors">
                                <td class="py-4 px-2 font-bold text-sm text-slate-400">#ORD-<?= $ro['order_id'] ?></td>
                                <td class="py-4">
                                    <p class="text-sm font-bold text-slate-900">
                                        <?= htmlspecialchars($ro['customer_name']) ?>
                                    </p>
                                    <p class="text-[10px] text-slate-400"><?= htmlspecialchars($ro['city']) ?></p>
                                </td>
                                <td class="py-4 font-black text-slate-900">₹<?= number_format($ro['total_amount']) ?></td>
                                <td class="py-4"><span
                                        class="text-[9px] font-bold text-blue-500 border border-blue-100 px-2 py-0.5 rounded-md uppercase"><?= $ro['payment_method'] ?></span>
                                </td>
                                <td class="py-4"><span
                                        class="text-[9px] font-black text-amber-600 bg-amber-50 px-2 py-1 rounded-full uppercase"><?= $ro['order_status'] ?></span>
                                </td>
                                <td class="py-4 text-right">
                                    <a href="orders/orders-list.php"
                                        class="w-8 h-8 rounded-lg bg-slate-100 text-slate-400 hover:bg-orange-600 hover:text-white transition-all flex items-center justify-center"><i
                                            class="fas fa-eye text-xs"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="lg:col-span-4 space-y-8">

            <div class="bg-slate-900 rounded-[2.5rem] p-8 shadow-xl shadow-slate-200">
                <h3 class="text-white text-sm font-black uppercase tracking-widest mb-6">Quick Launch</h3>
                <div class="grid grid-cols-2 gap-4">
                    <a href="products/add-product.php"
                        class="flex flex-col items-center gap-2 p-4 bg-white/5 rounded-2xl hover:bg-orange-600 transition-all group">
                        <i class="fas fa-plus-circle text-orange-500 group-hover:text-white"></i>
                        <span
                            class="text-[9px] font-bold text-slate-400 group-hover:text-white uppercase">Product</span>
                    </a>
                    <a href="billing/invoices.php"
                        class="flex flex-col items-center gap-2 p-4 bg-white/5 rounded-2xl hover:bg-orange-600 transition-all group">
                        <i class="fas fa-file-invoice text-blue-400 group-hover:text-white"></i>
                        <span
                            class="text-[9px] font-bold text-slate-400 group-hover:text-white uppercase">Invoice</span>
                    </a>
                    <a href="inventory/stock-update.php"
                        class="flex flex-col items-center gap-2 p-4 bg-white/5 rounded-2xl hover:bg-orange-600 transition-all group">
                        <i class="fas fa-boxes-stacked text-purple-400 group-hover:text-white"></i>
                        <span class="text-[9px] font-bold text-slate-400 group-hover:text-white uppercase">Add
                            Stock</span>
                    </a>
                    <a href="promotions/coupons.php"
                        class="flex flex-col items-center gap-2 p-4 bg-white/5 rounded-2xl hover:bg-orange-600 transition-all group">
                        <i class="fas fa-ticket text-emerald-400 group-hover:text-white"></i>
                        <span class="text-[9px] font-bold text-slate-400 group-hover:text-white uppercase">Coupon</span>
                    </a>
                </div>
            </div>

            <div class="bg-white rounded-[2.5rem] p-8 border border-slate-100 shadow-sm">
                <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-6 flex items-center gap-2">
                    <i class="fas fa-triangle-exclamation text-red-500"></i> Stock Alerts
                </h3>
                <div class="space-y-4">
                    <?php if (empty($stock_alerts)): ?>
                        <p class="text-[10px] text-slate-300 font-bold italic p-4 text-center">No inventory issues detected.
                        </p>
                    <?php endif; ?>
                    <?php foreach ($stock_alerts as $sa): ?>
                        <div class="flex items-center justify-between p-3 bg-red-50 rounded-2xl border border-red-100">
                            <div>
                                <p class="text-xs font-bold text-slate-900"><?= htmlspecialchars($sa['name']) ?></p>
                                <p class="text-[10px] text-red-500 font-bold italic">Only <?= $sa['stock'] ?> left</p>
                            </div>
                            <a href="inventory/stock-update.php"
                                class="bg-white text-red-600 text-[10px] font-black px-3 py-1.5 rounded-xl shadow-sm border border-red-100">Stock
                                +</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="lg:col-span-8 bg-white rounded-[2.5rem] p-8 border border-slate-100 shadow-sm">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-black text-slate-900">Sales Analytics</h3>
                <select
                    class="bg-slate-50 border-none rounded-xl text-[10px] font-black uppercase px-3 py-2 outline-none">
                    <option>Weekly View</option>
                    <option>Monthly View</option>
                </select>
            </div>
            <div class="h-64">
                <canvas id="salesChart"></canvas>
            </div>
        </div>

        <div class="lg:col-span-4 bg-white rounded-[2.5rem] p-8 border border-slate-100 shadow-sm">
            <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-6">Payment Pulse</h3>
            <div class="space-y-6">
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                        <span class="text-xs font-bold text-slate-500">Online Received</span>
                    </div>
                    <span class="text-sm font-black text-slate-900">₹<?= number_format($online_received) ?></span>
                </div>
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 rounded-full bg-amber-500"></div>
                        <span class="text-xs font-bold text-slate-500">COD Pending</span>
                    </div>
                    <span class="text-sm font-black text-slate-900">₹<?= number_format($cod_pending) ?></span>
                </div>
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 rounded-full bg-red-500"></div>
                        <span class="text-xs font-bold text-slate-500">Refunded</span>
                    </div>
                    <span class="text-sm font-black text-slate-900">₹<?= number_format($refunded) ?></span>
                </div>
                <div class="pt-4 mt-4 border-t border-slate-50">
                    <div class="flex justify-between mb-2">
                        <span class="text-[10px] font-black text-slate-400 uppercase">Target (₹10L)</span>
                        <span class="text-[10px] font-black text-orange-600">50%</span>
                    </div>
                    <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                        <div class="bg-orange-600 h-full" style="width: 50%"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('salesChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            datasets: [{
                label: 'Sales (₹)',
                data: <?= json_encode($sales_points) ?>,
                borderColor: '#FF6F1E',
                backgroundColor: 'rgba(255, 111, 30, 0.1)',
                fill: true,
                tension: 0.4,
                borderWidth: 4,
                pointRadius: 0,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { display: false },
                x: { grid: { display: false }, ticks: { font: { weight: 'bold', size: 10 } } }
            }
        }
    });
</script>
<?php include $base_path . 'includes/admin-footer.php'; ?>