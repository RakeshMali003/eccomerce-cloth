<?php
$base_path = $_SERVER['DOCUMENT_ROOT'] . '/ecommerce-website/';
require_once "../../config/database.php";
include $base_path . 'includes/sidebar.php';

// Fetch Returned Orders
$returnedSql = "SELECT o.*, u.name FROM orders o JOIN users u ON o.user_id = u.user_id WHERE o.order_status = 'returned' ORDER BY o.updated_at DESC";
$returns = $pdo->query($returnedSql)->fetchAll();
?>

<main class="p-6 lg:p-12">
    <div class="mb-10">
        <h2 class="text-3xl font-black tracking-tighter">Returns & Refunds<span class="text-orange-600">.</span></h2>
        <p class="text-slate-400 text-sm">Track and manage returned inventory and customer refunds.</p>
    </div>

    <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-slate-50 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                <tr>
                    <th class="px-8 py-6">Order</th>
                    <th class="px-6 py-6">Customer</th>
                    <th class="px-6 py-6 text-right">Refund Amount</th>
                    <th class="px-8 py-6 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php foreach ($returns as $r): ?>
                    <tr>
                        <td class="px-8 py-5">
                            <p class="text-sm font-black text-slate-900">#ORD-
                                <?= $r['order_id'] ?>
                            </p>
                            <p class="text-[10px] text-slate-400 font-bold">
                                <?= date('d M, Y', strtotime($r['updated_at'])) ?>
                            </p>
                        </td>
                        <td class="px-6 py-5 text-sm font-bold text-slate-600">
                            <?= $r['name'] ?>
                        </td>
                        <td class="px-6 py-5 text-right font-black text-orange-600">₹
                            <?= number_format($r['total_amount'], 2) ?>
                        </td>
                        <td class="px-8 py-5 text-right">
                            <button
                                class="bg-slate-900 text-white px-4 py-2 rounded-xl text-[10px] font-black uppercase">Process
                                Refund</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($returns)): ?>
                    <tr>
                        <td colspan="4" class="p-20 text-center text-slate-400 font-bold">No return requests found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>