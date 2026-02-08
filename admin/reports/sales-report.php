<?php
$base_path = $_SERVER['DOCUMENT_ROOT'] . '/ecommerce-website/';
require_once "../../config/database.php";

include $base_path . 'includes/sidebar.php';

$month = $_GET['month'] ?? date('m');
$year = $_GET['year'] ?? date('Y');

// 1. Monthly Stats
$statsSql = "SELECT 
                COUNT(*) as total_orders,
                SUM(total_amount) as gross_sales,
                SUM(gst_amount) as total_gst,
                SUM(total_amount - gst_amount) as taxable_sales
             FROM orders 
             WHERE MONTH(created_at) = :m AND YEAR(created_at) = :y AND order_status != 'cancelled'";
$stmt = $pdo->prepare($statsSql);
$stmt->execute(['m' => $month, 'y' => $year]);
$stats = $stmt->fetch();

// 2. Daily Sales for Chart
$dailySql = "SELECT DATE(created_at) as date, SUM(total_amount) as total 
             FROM orders 
             WHERE MONTH(created_at) = :m AND YEAR(created_at) = :y AND order_status != 'cancelled'
             GROUP BY DATE(created_at) ORDER BY date ASC";
$stmtDaily = $pdo->prepare($dailySql);
$stmtDaily->execute(['m' => $month, 'y' => $year]);
$dailySales = $stmtDaily->fetchAll(PDO::FETCH_KEY_PAIR);

// 3. Top Products
$topSql = "SELECT p.name, SUM(oi.quantity) as qty, SUM(oi.total_price) as revenue
           FROM order_items oi
           JOIN products p ON oi.product_id = p.product_id
           JOIN orders o ON oi.order_id = o.order_id
           WHERE MONTH(o.created_at) = :m AND YEAR(o.created_at) = :y AND o.order_status != 'cancelled'
           GROUP BY p.product_id ORDER BY revenue DESC LIMIT 5";
$stmtTop = $pdo->prepare($topSql);
$stmtTop->execute(['m' => $month, 'y' => $year]);
$topProducts = $stmtTop->fetchAll();
?>

<main class="p-6 lg:p-12 bg-[#f8fafc] min-h-screen">
    <div class="mb-10 flex justify-between items-center">
        <div>
            <h2 class="text-3xl font-black tracking-tighter text-slate-900">Revenue Analysis<span
                    class="text-indigo-600">.</span></h2>
            <p class="text-slate-400 text-sm font-medium">Sales performance for
                <?= date('F Y', mktime(0, 0, 0, $month, 1, $year)) ?>
            </p>
        </div>

        <form method="GET" class="flex gap-2">
            <select name="month" onchange="this.form.submit()"
                class="bg-white p-3 rounded-xl border border-slate-100 text-xs font-bold outline-none">
                <?php for ($i = 1; $i <= 12; $i++)
                    echo "<option value='$i' " . ($month == $i ? 'selected' : '') . ">" . date('F', mktime(0, 0, 0, $i, 1)) . "</option>"; ?>
            </select>
            <select name="year" onchange="this.form.submit()"
                class="bg-white p-3 rounded-xl border border-slate-100 text-xs font-bold outline-none">
                <option value="2025" <?= $year == '2025' ? 'selected' : '' ?>>2025</option>
                <option value="2024" <?= $year == '2024' ? 'selected' : '' ?>>2024</option>
            </select>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-12">
        <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Gross Revenue</p>
            <h3 class="text-3xl font-black text-slate-900">₹
                <?= number_format($stats['gross_sales'] ?? 0, 2) ?>
            </h3>
        </div>
        <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Taxable Sales</p>
            <h3 class="text-3xl font-black text-slate-600">₹
                <?= number_format($stats['taxable_sales'] ?? 0, 2) ?>
            </h3>
        </div>
        <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">GST Collected</p>
            <h3 class="text-3xl font-black text-indigo-600">₹
                <?= number_format($stats['total_gst'] ?? 0, 2) ?>
            </h3>
        </div>
        <div class="bg-slate-900 p-8 rounded-[2.5rem] shadow-xl text-white">
            <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Conversations</p>
            <h3 class="text-3xl font-black">
                <?= $stats['total_orders'] ?? 0 ?> <span class="text-sm">Orders</span>
            </h3>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 bg-white rounded-[3rem] p-10 border border-slate-100 shadow-sm">
            <h4 class="text-lg font-black text-slate-900 mb-8 tracking-tight">Daily Performance</h4>
            <!-- Basic Table for now as Chart.js requires more setup in a report context -->
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="text-[10px] font-black text-slate-400 uppercase">
                        <tr>
                            <th class="py-4">Date</th>
                            <th class="py-4 text-right">Revenue</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <?php foreach ($dailySales as $date => $total): ?>
                            <tr>
                                <td class="py-3 text-sm font-bold text-slate-600">
                                    <?= date('d M', strtotime($date)) ?>
                                </td>
                                <td class="py-3 text-right text-sm font-black text-slate-900">₹
                                    <?= number_format($total, 2) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-[3rem] p-10 border border-slate-100 shadow-sm">
            <h4 class="text-lg font-black text-slate-900 mb-8 tracking-tight">Best Sellers</h4>
            <div class="space-y-6">
                <?php foreach ($topProducts as $p): ?>
                    <div class="flex justify-between items-center group">
                        <div>
                            <p class="text-sm font-black text-slate-900 group-hover:text-indigo-600 transition-all">
                                <?= htmlspecialchars($p['name']) ?>
                            </p>
                            <p class="text-[10px] font-bold text-slate-400 uppercase">
                                <?= $p['qty'] ?> Units Sold
                            </p>
                        </div>
                        <p class="text-sm font-black text-slate-900">₹
                            <?= number_format($p['revenue'], 2) ?>
                        </p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</main>