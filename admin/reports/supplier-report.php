<?php
$base_path = __DIR__ . '/../../';
require_once "../../config/database.php";

include $base_path . 'includes/admin-header.php';
include $base_path . 'includes/sidebar.php';
include $base_path . 'includes/notifications.php';

// Fetch vendor performance data
$sql = "SELECT s.supplier_id, s.supplier_name, s.contact_person, s.phone,
               COUNT(po.po_id) as total_pos,
               SUM(po.total_amount) as total_purchase,
               (SELECT SUM(balance_amount) FROM supplier_bills WHERE supplier_id = s.supplier_id) as total_outstanding
        FROM suppliers s
        LEFT JOIN purchase_orders po ON s.supplier_id = po.supplier_id
        GROUP BY s.supplier_id
        ORDER BY total_purchase DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$suppliers = $stmt->fetchAll();

$grand_totals = [
    'purchases' => 0,
    'owed' => 0
];

foreach ($suppliers as $sup) {
    $grand_totals['purchases'] += $sup['total_purchase'];
    $grand_totals['owed'] += $sup['total_outstanding'];
}
?>

<main class="p-6 lg:p-12 bg-[#f8fafc] min-h-screen">
    <div class="mb-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
        <div>
            <h2 class="text-3xl font-black tracking-tighter text-slate-900">Procurement: Vendor Analysis<span
                    class="text-orange-600">.</span></h2>
            <p class="text-slate-400 text-sm font-medium">Evaluate supplier reliability and financial liability</p>
        </div>
        <div class="flex gap-3">
            <button onclick="window.print()"
                class="bg-white border border-slate-200 px-6 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-50 transition-all shadow-sm">
                <i class="fas fa-file-export mr-2"></i> Vendor List
            </button>
        </div>
    </div>

    <!-- Vendor KPIs -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
        <div class="bg-white p-8 rounded-[3rem] border border-slate-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Procurement Vol.
                </p>
                <h3 class="text-2xl font-black text-slate-900">₹
                    <?= number_format($grand_totals['purchases'], 2) ?>
                </h3>
            </div>
            <div class="w-14 h-14 bg-blue-50 text-blue-500 rounded-2xl flex items-center justify-center text-xl">
                <i class="fas fa-shopping-cart"></i>
            </div>
        </div>
        <div class="bg-white p-8 rounded-[3rem] border border-slate-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Owed to Suppliers
                </p>
                <h3 class="text-2xl font-black text-red-600">₹
                    <?= number_format($grand_totals['owed'], 2) ?>
                </h3>
            </div>
            <div class="w-14 h-14 bg-red-50 text-red-500 rounded-2xl flex items-center justify-center text-xl">
                <i class="fas fa-hand-holding-dollar"></i>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-[3rem] border border-slate-100 shadow-sm overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-slate-50 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                <tr>
                    <th class="px-8 py-6">Vendor Details</th>
                    <th class="px-6 py-6 text-center">Orders (PO)</th>
                    <th class="px-6 py-6 text-right">Total Purchase</th>
                    <th class="px-6 py-6 text-right">Outstanding Dues</th>
                    <th class="px-8 py-6 text-right">Financial Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php foreach ($suppliers as $sup):
                    $isDue = ($sup['total_outstanding'] > 0);
                    ?>
                    <tr class="hover:bg-slate-50 transition-all">
                        <td class="px-8 py-5">
                            <p class="text-xs font-black text-slate-900">
                                <?= htmlspecialchars($sup['supplier_name']) ?>
                            </p>
                            <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest italic">
                                <?= $sup['contact_person'] ?> |
                                <?= $sup['phone'] ?>
                            </p>
                        </td>
                        <td class="px-6 py-5 text-center text-xs font-bold text-slate-900">
                            <?= $sup['total_pos'] ?>
                        </td>
                        <td class="px-6 py-5 text-right text-xs font-bold text-slate-500">₹
                            <?= number_format($sup['total_purchase'], 2) ?>
                        </td>
                        <td
                            class="px-6 py-5 text-right text-xs font-black <?= $isDue ? 'text-red-500' : 'text-slate-400' ?>">
                            ₹
                            <?= number_format($sup['total_outstanding'], 2) ?>
                        </td>
                        <td class="px-8 py-5 text-right">
                            <span
                                class="px-3 py-1 rounded-lg text-[9px] font-black uppercase <?= $isDue ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700' ?>">
                                <?= $isDue ? 'Dues Pending' : 'Account Settled' ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>
<?php include $base_path . "includes/admin-footer.php"; ?>
</body>

</html>