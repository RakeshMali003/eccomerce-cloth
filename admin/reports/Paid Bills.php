<?php
$base_path = $_SERVER['DOCUMENT_ROOT'] . '/ecommerce-website/';
require_once "../../config/database.php";

include $base_path . 'includes/admin-header.php';
include $base_path . 'includes/sidebar.php';
include $base_path . 'includes/notifications.php';

// Fetch Paid Bills
$stmt = $pdo->query("SELECT b.*, s.name as supplier_name 
                   FROM supplier_bills b 
                   JOIN suppliers s ON b.supplier_id = s.supplier_id 
                   WHERE b.payment_status = 'Paid' 
                   ORDER BY b.updated_at DESC");
$paid_bills = $stmt->fetchAll();

$total_paid = array_sum(array_column($paid_bills, 'total_amount'));
?>

<main class="p-6 lg:p-12 bg-[#f8fafc] min-h-screen">
    <div class="mb-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
        <div>
            <h2 class="text-3xl font-black tracking-tighter text-slate-900">Paid Bills Archive<span
                    class="text-emerald-600">.</span></h2>
            <p class="text-slate-400 text-sm font-medium">Historical record of all settled supplier liabilities and
                procurement costs.</p>
        </div>
        <div class="bg-white px-8 py-5 rounded-[2rem] border border-slate-100 shadow-sm text-right">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Settled</p>
            <h3 class="text-2xl font-black text-emerald-600 tracking-tighter">₹
                <?= number_format($total_paid, 2) ?>
            </h3>
        </div>
    </div>

    <div class="bg-white rounded-[3rem] border border-slate-100 shadow-sm overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-slate-50 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                <tr>
                    <th class="px-8 py-6">Bill / Invoice</th>
                    <th class="px-6 py-6">Supplier Partner</th>
                    <th class="px-6 py-6 text-center">Settled On</th>
                    <th class="px-6 py-6 text-right">Amount (₹)</th>
                    <th class="px-8 py-6 text-right">Reference</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php foreach ($paid_bills as $b): ?>
                    <tr class="hover:bg-slate-50 transition-all group">
                        <td class="px-8 py-5">
                            <p class="text-sm font-black text-slate-900">
                                <?= htmlspecialchars($b['bill_number']) ?>
                            </p>
                            <p class="text-[9px] text-slate-400 font-bold uppercase">ID: #
                                <?= $b['bill_id'] ?>
                            </p>
                        </td>
                        <td class="px-6 py-5">
                            <p class="text-xs font-bold text-slate-700">
                                <?= htmlspecialchars($b['supplier_name']) ?>
                            </p>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <p class="text-xs font-black text-slate-900">
                                <?= date('d M, Y', strtotime($b['updated_at'])) ?>
                            </p>
                        </td>
                        <td class="px-6 py-5 text-right font-black text-slate-900 italic">
                            ₹
                            <?= number_format($b['total_amount'], 2) ?>
                        </td>
                        <td class="px-8 py-5 text-right">
                            <span
                                class="bg-emerald-50 text-emerald-600 px-4 py-2 rounded-xl text-[9px] font-black uppercase tracking-widest border border-emerald-100">
                                Fully Settled
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($paid_bills)): ?>
                    <tr>
                        <td colspan="5" class="p-20 text-center text-slate-400 font-bold italic uppercase tracking-widest">
                            No settled bills in the current ledger.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>
<?php include $base_path . 'includes/admin-footer.php'; ?>
</body>

</html>