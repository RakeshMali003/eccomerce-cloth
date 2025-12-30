<?php

$base_path = $_SERVER['DOCUMENT_ROOT'] . '/ecommerce-website/';

include $base_path . 'includes/sidebar.php'; 
include $base_path . 'includes/notifications.php'; 
require_once "../../config/database.php";

// Fetch Payment History with Supplier Names
$query = "SELECT sp.*, s.name as supplier_name 
          FROM supplier_payments sp 
          JOIN suppliers s ON sp.supplier_id = s.supplier_id 
          ORDER BY sp.payment_date DESC";
$payments = $pdo->query($query)->fetchAll();
?>
<main class="p-6 lg:p-12 bg-[#f8fafc] min-h-screen">
    <div class="mb-10 flex justify-between items-end">
        <div>
            <h2 class="text-3xl font-black tracking-tighter text-slate-900">Payment Outbox<span class="text-orange-600">.</span></h2>
            <p class="text-slate-400 text-sm font-medium">History of all disbursements made to vendors.</p>
        </div>
        <div class="flex gap-3">
             <button onclick="window.print()" class="bg-white border border-slate-200 p-4 rounded-2xl hover:bg-slate-50 transition-all">
                <i class="fas fa-print text-slate-600"></i>
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <div class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Paid (MTD)</p>
            <h3 class="text-2xl font-black text-slate-900">₹<?php 
                $mtd = $pdo->query("SELECT SUM(amount) FROM supplier_payments WHERE MONTH(payment_date) = MONTH(CURRENT_DATE)")->fetchColumn();
                echo number_format($mtd ?: 0, 2);
            ?></h3>
        </div>
    </div>

    <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-slate-50 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                <tr>
                    <th class="px-8 py-6">Date</th>
                    <th class="px-6 py-6">Supplier</th>
                    <th class="px-6 py-6">Method</th>
                    <th class="px-6 py-6">Reference</th>
                    <th class="px-6 py-6 text-right">Amount</th>
                    <th class="px-8 py-6 text-center">Status</th>
                       <th class="px-8 py-6 text-center">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php if (empty($payments)): ?>
                    <tr><td colspan="6" class="p-20 text-center text-slate-300 font-bold uppercase text-xs">No payment records found</td></tr>
                <?php else: foreach($payments as $p): ?>
                <tr class="hover:bg-slate-50 transition-all group">
                    <td class="px-8 py-5 text-xs font-bold text-slate-500">
                        <?= date('d M, Y', strtotime($p['payment_date'])) ?>
                    </td>
                    <td class="px-6 py-5">
                        <p class="text-xs font-black text-slate-900"><?= htmlspecialchars($p['supplier_name']) ?></p>
                    </td>
                    <td class="px-6 py-5">
                        <span class="text-[10px] font-black uppercase bg-slate-100 px-3 py-1 rounded-lg text-slate-600">
                            <?= $p['payment_mode'] ?>
                        </span>
                    </td>
                    <td class="px-6 py-5 text-xs font-medium text-slate-400 italic">
                        <?= $p['transaction_id'] ?: 'N/A' ?>
                    </td>
                    <td class="px-6 py-5 text-right font-black text-slate-900">
                        ₹<?= number_format($p['amount'], 2) ?>
                    </td>
                    <td class="px-8 py-5 text-center">
                        <?php if($p['status'] == 'Cleared'): ?>
                            <span class="bg-emerald-50 text-emerald-600 px-4 py-2 rounded-xl text-[10px] font-black uppercase">Cleared</span>
                        <?php else: ?>
                            <span class="bg-orange-50 text-orange-600 px-4 py-2 rounded-xl text-[10px] font-black uppercase">Pending</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-8 py-5 text-right">
    <div class="flex justify-end gap-2">
        <button onclick="printReceipt(<?= $p['payment_id'] ?>)" 
                class="w-10 h-10 rounded-xl border border-slate-100 flex items-center justify-center hover:bg-slate-900 hover:text-white transition-all group">
            <i class="fas fa-print text-xs"></i>
        </button>
    </div>
</td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</main>
<script>
function printReceipt(paymentId) {
    const width = 800;
    const height = 600;
    // This ensures it looks for the file starting from the website root
    const url = '<?php echo $base_url; ?>/admin/Purchases/print_payment.php?id=' + paymentId;
    
    window.open(url, 'Payment Receipt', `width=${width},height=${height}`);
}
</script>