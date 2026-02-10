<?php
$base_path = __DIR__ . '/../../';
require_once "../../config/database.php";
include $base_path . 'includes/sidebar.php';

// Fetch Recent Paid Orders to show receipts
$receiptsSql = "SELECT o.order_id, o.total_amount, o.payment_method, o.created_at, u.name, i.invoice_number 
                FROM orders o 
                JOIN users u ON o.user_id = u.user_id 
                LEFT JOIN invoices i ON o.order_id = i.order_id
                WHERE o.payment_status = 'paid' 
                ORDER BY o.created_at DESC";
$receipts = $pdo->query($receiptsSql)->fetchAll();
?>

<main class="p-6 lg:p-12">
    <div class="mb-10 flex justify-between items-center">
        <div>
            <h2 class="text-3xl font-black tracking-tighter">Payment Receipts<span class="text-emerald-600">.</span>
            </h2>
            <p class="text-slate-400 text-sm">Review and download payment confirmations for orders.</p>
        </div>
    </div>

    <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-slate-50 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                <tr>
                    <th class="px-8 py-6">Receipt / Order</th>
                    <th class="px-6 py-6">Customer</th>
                    <th class="px-6 py-6 font-center">Method</th>
                    <th class="px-6 py-6 text-right">Amount</th>
                    <th class="px-8 py-6 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php foreach ($receipts as $r): ?>
                    <tr>
                        <td class="px-8 py-5">
                            <p class="text-sm font-black text-slate-900">RC-
                                <?= $r['order_id'] ?>
                                <?= date('y') ?>
                            </p>
                            <p class="text-[10px] text-slate-400 font-bold uppercase">Order #ORD-
                                <?= $r['order_id'] ?>
                            </p>
                        </td>
                        <td class="px-6 py-5">
                            <p class="text-xs font-black text-slate-900">
                                <?= htmlspecialchars($r['name']) ?>
                            </p>
                            <p class="text-[9px] text-slate-400">
                                <?= date('d M, Y', strtotime($r['created_at'])) ?>
                            </p>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <span class="text-[10px] font-black uppercase text-slate-500 bg-slate-50 px-3 py-1 rounded-lg">
                                <?= $r['payment_method'] ?>
                            </span>
                        </td>
                        <td class="px-6 py-5 text-right font-black text-slate-900">₹
                            <?= number_format($r['total_amount'], 2) ?>
                        </td>
                        <td class="px-8 py-5 text-right">
                            <a href="generate-receipt.php?id=<?= $r['order_id'] ?>"
                                class="text-blue-600 hover:text-blue-800 transition-all">
                                <i class="fas fa-download"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($receipts)): ?>
                    <tr>
                        <td colspan="5" class="p-20 text-center text-slate-400 font-bold">No payments found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>