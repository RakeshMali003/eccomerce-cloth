<?php
require_once "../../config/database.php";
$id = $_GET['id'];

// 1. Fetch Order & Customer Details
$order = $pdo->prepare("SELECT o.*, u.name, u.email, u.phone, u.address, u.city 
                        FROM orders o 
                        JOIN users u ON o.user_id = u.user_id 
                        WHERE o.order_id = ?");
$order->execute([$id]);
$o = $order->fetch();

// 2. Fetch Order Items
$items = $pdo->prepare("SELECT oi.*, p.name, p.sku, pv.size, pv.color 
                        FROM order_items oi 
                        JOIN products p ON oi.product_id = p.product_id 
                        LEFT JOIN product_variants pv ON oi.variant_id = pv.variant_id 
                        WHERE oi.order_id = ?");
$items->execute([$id]);
$order_items = $items->fetchAll();

// 3. Fetch Shipment Details
$shipment = $pdo->prepare("SELECT * FROM order_shipments WHERE order_id = ? ORDER BY shipment_id DESC LIMIT 1");
$shipment->execute([$id]);
$s = $shipment->fetch();

// Status Timeline Classes
function getTimelineStepClass($current_status, $target_status)
{
    $order = ['pending', 'confirmed', 'packed', 'shipped', 'delivered'];
    $current_idx = array_search($current_status, $order);
    $target_idx = array_search($target_status, $order);

    if ($current_idx >= $target_idx)
        return 'bg-emerald-500 text-white';
    return 'bg-slate-100 text-slate-400';
}
?>

<div class="p-8 bg-slate-900 text-white flex justify-between items-center border-b border-white/5">
    <div>
        <h3 class="text-xl font-black uppercase tracking-tighter">Order #ORD-<?= $o['order_id'] ?></h3>
        <div class="flex items-center gap-2 mt-1">
            <span class="w-2 h-2 rounded-full bg-orange-500 animate-pulse"></span>
            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Received on
                <?= date('d M, Y • H:i', strtotime($o['created_at'])) ?>
            </p>
        </div>
    </div>
    <div class="text-right">
        <span
            class="px-5 py-2.5 bg-white/10 backdrop-blur-md text-white rounded-2xl text-[10px] font-black uppercase tracking-widest border border-white/10"><?= $o['order_status'] ?></span>
    </div>
</div>

<div class="p-10 space-y-12 overflow-y-auto max-h-[75vh] sidebar-scroll">
    <!-- Visual Timeline -->
    <div class="relative px-8 pt-4 pb-10">
        <div class="absolute top-[3.25rem] left-20 right-20 h-0.5 bg-slate-100 -z-10"></div>
        <div class="flex justify-between items-start">
            <?php
            $steps = [
                ['id' => 'confirmed', 'label' => 'Confirmed', 'icon' => 'fa-check'],
                ['id' => 'packed', 'label' => 'Packed', 'icon' => 'fa-box-open'],
                ['id' => 'shipped', 'label' => 'Shipped', 'icon' => 'fa-truck-fast'],
                ['id' => 'delivered', 'label' => 'Delivered', 'icon' => 'fa-home-user']
            ];
            foreach ($steps as $step):
                $class = getTimelineStepClass($o['order_status'], $step['id']);
                ?>
                <div class="flex flex-col items-center text-center space-y-3">
                    <div
                        class="w-10 h-10 rounded-xl flex items-center justify-center text-xs shadow-sm transition-all <?= $class ?>">
                        <i class="fas <?= $step['icon'] ?>"></i>
                    </div>
                    <p class="text-[9px] font-black uppercase tracking-widest text-slate-400"><?= $step['label'] ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
        <div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Shipping Destination</p>
            <h4 class="text-lg font-black text-slate-900"><?= htmlspecialchars($o['name']) ?></h4>
            <div class="text-sm text-slate-500 mt-2 font-medium leading-relaxed">
                <?= htmlspecialchars($o['address']) ?><br>
                <?= htmlspecialchars($o['city']) ?><br>
                <div class="mt-4 flex items-center gap-3">
                    <span
                        class="text-xs font-black text-slate-900 uppercase tracking-tighter italic border-b border-orange-500"><?= $o['phone'] ?></span>
                    <span
                        class="text-[9px] text-slate-300 font-bold uppercase italic tracking-widest"><?= $o['email'] ?></span>
                </div>
            </div>
        </div>
        <div class="bg-slate-50 p-8 rounded-[2.5rem] border border-slate-100 relative overflow-hidden">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-orange-500/5 rounded-full blur-2xl"></div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Logistic Insight</p>
            <?php if ($s): ?>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <p class="text-xs font-bold text-slate-600">Partner:</p>
                        <p class="text-xs font-black text-slate-900 uppercase italic"><?= $s['courier_name'] ?></p>
                    </div>
                    <div class="flex justify-between items-center">
                        <p class="text-xs font-bold text-slate-600">Tracking ID:</p>
                        <p class="text-xs font-black text-orange-600 tracking-widest">#<?= $s['tracking_number'] ?></p>
                    </div>
                    <?php if ($s['shipped_at']): ?>
                        <div class="flex justify-between items-center pt-2 border-t border-slate-200/50">
                            <p class="text-[9px] font-bold text-slate-400 uppercase">Dispatched At:</p>
                            <p class="text-[9px] font-black text-slate-900">
                                <?= date('d M, h:i A', strtotime($s['shipped_at'])) ?>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="py-4 text-center">
                    <i class="fas fa-box-open text-slate-200 text-4xl mb-3"></i>
                    <p class="text-[10px] font-black text-slate-400 uppercase">Waiting for Fulfillment</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="space-y-4">
        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Line Items</p>
        <div class="space-y-2">
            <?php foreach ($order_items as $item): ?>
                <div
                    class="flex justify-between items-center bg-white border border-slate-50 p-5 rounded-3xl hover:border-slate-200 transition-all group">
                    <div class="flex items-center gap-4">
                        <div
                            class="w-12 h-12 rounded-2xl bg-slate-50 flex items-center justify-center text-slate-300 group-hover:bg-orange-50 group-hover:text-orange-500 transition-all">
                            <i class="fas fa-tshirt"></i>
                        </div>
                        <div>
                            <p class="text-sm font-black text-slate-900 tracking-tight">
                                <?= htmlspecialchars($item['name']) ?>
                            </p>
                            <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest"><?= $item['size'] ?> /
                                <?= $item['color'] ?> • <span class="text-slate-900 italic"><?= $item['sku'] ?></span>
                            </p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-[9px] font-black text-slate-400 uppercase mb-1">Qty: <?= $item['quantity'] ?> × </p>
                        <p class="text-sm font-black text-slate-900">₹<?= number_format($item['unit_price'], 2) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div
        class="flex flex-col md:flex-row justify-between items-start md:items-end gap-8 pt-10 border-t border-slate-50">
        <div class="flex flex-wrap gap-2">
            <?php if ($o['order_status'] == 'shipped' || $o['order_status'] == 'packed'): ?>
                <button onclick="updateOrderStatus(<?= $o['order_id'] ?>, 'delivered')"
                    class="bg-emerald-600 text-white px-8 py-4 rounded-[2rem] text-[10px] font-black uppercase tracking-widest shadow-xl shadow-emerald-100 hover:bg-emerald-700 transition-all">
                    <i class="fas fa-check mr-2"></i> Mark Delivered
                </button>
            <?php endif; ?>

            <?php if (isset($o['invoice_id']) && $o['invoice_id']): ?>
                <a href="../billing/invoices.php?id=<?= $o['invoice_id'] ?>" target="_blank"
                    class="bg-white border border-slate-200 text-slate-900 px-8 py-4 rounded-[2rem] text-[10px] font-black uppercase tracking-widest hover:bg-slate-50 transition-all shadow-sm">
                    <i class="fas fa-file-invoice mr-2"></i> Open Invoice
                </a>
            <?php else: ?>
                <button onclick="generateInvoice(<?= $o['order_id'] ?>)"
                    class="bg-orange-600 text-white px-8 py-4 rounded-[2rem] text-[10px] font-black uppercase tracking-widest shadow-xl shadow-orange-100 hover:bg-orange-700 transition-all">
                    <i class="fas fa-plus mr-2"></i> Generate Invoice
                </button>
            <?php endif; ?>

            <button onclick="closeModal()"
                class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-slate-900 transition-all ml-auto">Close
                View</button>
        </div>

        <div class="w-full md:w-64 space-y-3 bg-slate-50 p-6 rounded-[2rem]">
            <div class="flex justify-between text-xs text-slate-500 font-bold">
                <span>Subtotal:</span>
                <span>₹<?= number_format($o['total_amount'] - $o['gst_amount'], 2) ?></span>
            </div>
            <div class="flex justify-between text-xs text-slate-500 font-bold">
                <span>GST:</span>
                <span>₹<?= number_format($o['gst_amount'], 2) ?></span>
            </div>
            <div class="flex justify-between items-center bg-slate-900 p-4 -mx-4 -mb-4 rounded-b-[2rem] text-white">
                <span class="text-[10px] font-black uppercase tracking-widest opacity-60">Total Bill</span>
                <span class="text-xl font-black italic">₹<?= number_format($o['total_amount'], 2) ?></span>
            </div>
        </div>
    </div>
</div>