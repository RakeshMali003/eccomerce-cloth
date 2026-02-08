<?php
$base_path = $_SERVER['DOCUMENT_ROOT'] . '/ecommerce-website/';
require_once "../../config/database.php";

include $base_path . 'includes/admin-header.php';
include $base_path . 'includes/sidebar.php';
include $base_path . 'includes/notifications.php';

// Fetch courier performance
$courier_sql = "SELECT courier_name, 
                       COUNT(*) as total_shipments,
                       SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) as delivered_count,
                       SUM(CASE WHEN status = 'returned' THEN 1 ELSE 0 END) as returned_count
                FROM order_shipments 
                GROUP BY courier_name";
$couriers = $pdo->query($courier_sql)->fetchAll();

// Fetch recent delivery logs
$logs_sql = "SELECT s.*, o.order_id, u.name as customer_name, u.city
             FROM order_shipments s
             JOIN orders o ON s.order_id = o.order_id
             JOIN users u ON o.user_id = u.user_id
             ORDER BY s.shipment_id DESC LIMIT 50";
$logs = $pdo->query($logs_sql)->fetchAll();
?>

<main class="p-6 lg:p-12 bg-[#f8fafc] min-h-screen">
    <div class="mb-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
        <div>
            <h2 class="text-3xl font-black tracking-tighter text-slate-900">Logistics Intelligence<span
                    class="text-orange-600">.</span></h2>
            <p class="text-slate-400 text-sm font-medium">Track fulfillment efficiency and courier performance metrics
            </p>
        </div>
        <div class="flex gap-3">
            <button onclick="window.print()"
                class="bg-white border border-slate-200 px-6 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-50 transition-all shadow-sm">
                <i class="fas fa-truck-fast mr-2"></i> Logistics Log
            </button>
        </div>
    </div>

    <!-- Logistics KPIs -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        <?php foreach ($couriers as $c):
            $rate = ($c['delivered_count'] / $c['total_shipments']) * 100;
            ?>
            <div class="bg-white p-8 rounded-[3rem] border border-slate-100 shadow-sm relative overflow-hidden">
                <div class="absolute -right-2 topper-0 opacity-5 text-4xl"><i class="fas fa-shipping-fast"></i></div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">
                    <?= $c['courier_name'] ?>
                </p>
                <h3 class="text-2xl font-black text-slate-900">
                    <?= $c['total_shipments'] ?> <span class="text-[10px] text-slate-400">Pkts</span>
                </h3>
                <div class="mt-4 w-full bg-slate-50 h-1.5 rounded-full overflow-hidden">
                    <div class="bg-emerald-500 h-full transition-all" style="width: <?= $rate ?>%"></div>
                </div>
                <p class="text-[9px] font-bold text-emerald-600 uppercase mt-2">
                    <?= number_format($rate, 1) ?>% Success Rate
                </p>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="bg-white rounded-[3rem] border border-slate-100 shadow-sm overflow-hidden">
        <div class="p-8 border-b border-slate-50 flex justify-between items-center">
            <h4 class="text-xs font-black uppercase tracking-widest text-slate-900">Real-time Fulfillment Log</h4>
            <span class="px-3 py-1 bg-blue-50 text-blue-500 rounded-lg text-[9px] font-black uppercase">Live Sync
                Active</span>
        </div>
        <table class="w-full text-left">
            <thead class="bg-slate-50 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                <tr>
                    <th class="px-8 py-6">Shipment ID</th>
                    <th class="px-6 py-6">Order</th>
                    <th class="px-6 py-6">Destination</th>
                    <th class="px-6 py-6 text-center">Courier</th>
                    <th class="px-6 py-6 text-center">Status</th>
                    <th class="px-8 py-6 text-right">Last Update</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php foreach ($logs as $log): ?>
                    <tr class="hover:bg-slate-50 transition-all group">
                        <td class="px-8 py-5">
                            <p class="text-xs font-black text-orange-600 tracking-widest">#
                                <?= $log['tracking_number'] ?>
                            </p>
                        </td>
                        <td class="px-6 py-5 text-xs font-bold text-slate-900">ORD-
                            <?= $log['order_id'] ?>
                        </td>
                        <td class="px-6 py-5">
                            <p class="text-xs font-black text-slate-900">
                                <?= htmlspecialchars($log['customer_name']) ?>
                            </p>
                            <p class="text-[9px] text-slate-400 font-bold uppercase italic">
                                <?= $log['city'] ?>
                            </p>
                        </td>
                        <td class="px-6 py-5 text-center text-xs font-bold text-slate-500 italic">
                            <?= $log['courier_name'] ?>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <span
                                class="px-3 py-1 rounded-lg text-[9px] font-black uppercase <?= $log['status'] == 'delivered' ? 'bg-emerald-100 text-emerald-600' : 'bg-blue-100 text-blue-600' ?>">
                                <?= $log['status'] ?>
                            </span>
                        </td>
                        <td class="px-8 py-5 text-right text-[10px] font-bold text-slate-400">
                            <?= $log['shipped_at'] ? date('d M, h:i A', strtotime($log['shipped_at'])) : 'Pending' ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($logs)): ?>
                    <tr>
                        <td colspan="6" class="px-8 py-20 text-center">
                            <i class="fas fa-truck-ramp-box text-slate-100 text-6xl mb-4"></i>
                            <p class="text-sm font-black text-slate-300 uppercase tracking-widest">No logistics data
                                recorded yet</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>
<?php include $base_path . "includes/admin-footer.php"; ?>