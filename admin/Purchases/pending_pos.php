<?php

$base_path = __DIR__ . '/../../';

include $base_path . 'includes/sidebar.php'; 
include $base_path . 'includes/notifications.php'; 
require_once "../../config/database.php";

// Fetch only pending Purchase Orders
$query = "SELECT po.*, s.name as supplier_name 
          FROM purchase_orders po 
          JOIN suppliers s ON po.supplier_id = s.supplier_id 
          WHERE po.status = 'pending' 
          ORDER BY po.created_at DESC";
$pending_pos = $pdo->query($query)->fetchAll();
?>

<main class="p-6 lg:p-12">
    <div class="mb-10 flex justify-between items-center">
        <div>
            <h2 class="text-3xl font-black tracking-tighter text-slate-900">Pending Deliveries<span class="text-orange-600">.</span></h2>
            <p class="text-slate-400 text-sm font-medium">Tracking issued Purchase Orders awaiting arrival.</p>
        </div>
        <a href="Purchase Order.php" class="bg-slate-900 text-white px-6 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-orange-600 transition-all">
            + Create New PO
        </a>
    </div>

    <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-slate-50 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                <tr>
                    <th class="px-8 py-6">PO Number</th>
                    <th class="px-6 py-6">Supplier</th>
                    <th class="px-6 py-6">Expected Date</th>
                    <th class="px-6 py-6 text-right">Total Amount</th>
                    <th class="px-8 py-6 text-center">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php if (empty($pending_pos)): ?>
                    <tr><td colspan="5" class="p-20 text-center text-slate-300 font-bold uppercase text-xs">No pending orders found</td></tr>
                <?php else: foreach($pending_pos as $po): ?>
                <tr class="hover:bg-slate-50 transition-all">
                    <td class="px-8 py-5 text-xs font-black text-slate-900"><?= $po['po_number'] ?></td>
                    <td class="px-6 py-5">
                        <p class="text-xs font-bold text-slate-700"><?= $po['supplier_name'] ?></p>
                        <p class="text-[9px] text-slate-400 uppercase">Issued: <?= date('d M', strtotime($po['created_at'])) ?></p>
                    </td>
                    <td class="px-6 py-5">
                        <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase <?= (strtotime($po['expected_delivery']) < time()) ? 'bg-red-50 text-red-600' : 'bg-blue-50 text-blue-600' ?>">
                            <?= date('d M, Y', strtotime($po['expected_delivery'])) ?>
                        </span>
                    </td>
                    <td class="px-6 py-5 text-right font-black text-slate-900">₹<?= number_format($po['total_amount'], 2) ?></td>
                    <td class="px-8 py-5 text-center">
                        <a href="Purchase Bill.php?convert_po=<?= $po['po_id'] ?>" 
                           class="bg-emerald-600 text-white px-5 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-900 transition-all">
                           Receive Goods
                        </a>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</main>