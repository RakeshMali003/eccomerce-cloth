<?php
require_once '../../config/database.php';
include '../../includes/header.php';

$is_logged_in = isset($_SESSION['user_id']);
$user_id = $is_logged_in ? $_SESSION['user_id'] : null;

// Fetch user's orders with their items
$orders = [];
if ($is_logged_in) {
    $stmt = $pdo->prepare("
        SELECT o.*, 
               u.address as shipping_address, u.city, u.pincode, u.phone,
               (SELECT COUNT(*) FROM order_items WHERE order_id = o.order_id) as item_count,
               (SELECT SUM(quantity) FROM order_items WHERE order_id = o.order_id) as total_qty
        FROM orders o 
        JOIN users u ON o.user_id = u.user_id
        WHERE o.user_id = ?
        ORDER BY o.created_at DESC
    ");
    $stmt->execute([$user_id]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to get order items
function getOrderItems($pdo, $order_id)
{
    $stmt = $pdo->prepare("
        SELECT oi.*, p.name, p.main_image, p.sku
        FROM order_items oi
        JOIN products p ON oi.product_id = p.product_id
        WHERE oi.order_id = ?
    ");
    $stmt->execute([$order_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to get shipment info
function getShipment($pdo, $order_id)
{
    $stmt = $pdo->prepare("SELECT * FROM order_shipments WHERE order_id = ? ORDER BY shipment_id DESC LIMIT 1");
    $stmt->execute([$order_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Status badge helper
function getStatusBadge($status)
{
    $badges = [
        'pending' => ['class' => 'bg-yellow-50 text-yellow-600 border-yellow-100', 'icon' => 'clock'],
        'processing' => ['class' => 'bg-blue-50 text-blue-600 border-blue-100', 'icon' => 'cog'],
        'shipped' => ['class' => 'bg-indigo-50 text-indigo-600 border-indigo-100', 'icon' => 'truck'],
        'delivered' => ['class' => 'bg-green-50 text-green-600 border-green-100', 'icon' => 'check-circle'],
        'cancelled' => ['class' => 'bg-red-50 text-red-600 border-red-100', 'icon' => 'times-circle'],
    ];
    return $badges[$status] ?? $badges['pending'];
}
?>

<style>
    .expandable-grid {
        display: grid;
        grid-template-rows: 0fr;
        transition: grid-template-rows 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .order-card.active .expandable-grid {
        grid-template-rows: 1fr;
    }

    .content-overflow {
        overflow: hidden;
    }

    .arrow-icon {
        transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .order-card.active .arrow-icon {
        transform: rotate(180deg);
        color: #FF6F1E;
    }
</style>

<div class="container mx-auto px-4 lg:px-20 py-10 lg:py-16">

    <header class="mb-12 flex flex-col md:flex-row justify-between items-end gap-6">
        <div>
            <h1 class="text-3xl lg:text-4xl font-extrabold tracking-tight text-slate-900">My Orders</h1>
            <p class="text-slate-400 text-sm font-medium mt-1 uppercase tracking-widest text-[10px]">Track & manage your
                purchases</p>
        </div>
    </header>

    <div class="max-w-5xl mx-auto">

        <?php if (!$is_logged_in): ?>
            <div class="text-center py-20 bg-white rounded-[3rem] border-2 border-dashed border-slate-100">
                <i class="fas fa-user-lock text-6xl text-slate-200 mb-6"></i>
                <h3 class="text-xl font-bold text-slate-900 mb-2">Login Required</h3>
                <p class="text-slate-400 text-sm mb-8">Please login to view your orders.</p>
                <a href="../auth/login.php"
                    class="inline-block bg-orange-600 text-white px-8 py-4 rounded-2xl font-black uppercase text-[10px] tracking-widest shadow-xl shadow-orange-200">
                    Login Now
                </a>
            </div>
        <?php elseif (empty($orders)): ?>
            <div class="text-center py-20 bg-white rounded-[3rem] border-2 border-dashed border-slate-100">
                <i class="fas fa-shopping-bag text-6xl text-slate-200 mb-6"></i>
                <h3 class="text-xl font-bold text-slate-900 mb-2">No Orders Yet</h3>
                <p class="text-slate-400 text-sm mb-8">You haven't placed any orders yet.</p>
                <a href="../products/product-list.php"
                    class="inline-block bg-orange-600 text-white px-8 py-4 rounded-2xl font-black uppercase text-[10px] tracking-widest shadow-xl shadow-orange-200">
                    Start Shopping
                </a>
            </div>
        <?php else: ?>
            <?php foreach ($orders as $index => $order):
                $badge = getStatusBadge($order['order_status'] ?? 'pending');
                $items = getOrderItems($pdo, $order['order_id']);
                $shipment = getShipment($pdo, $order['order_id']);
                ?>
                <div class="order-card bg-white rounded-[2.5rem] border border-slate-200 mb-6 transition-all duration-300 hover:border-orange-300 hover:shadow-xl"
                    id="card-<?= $order['order_id'] ?>">
                    <div class="p-6 md:p-10 cursor-pointer flex items-center justify-between"
                        onclick="toggle('card-<?= $order['order_id'] ?>')">
                        <div class="flex flex-1 items-center gap-6 md:gap-12">
                            <div
                                class="w-14 h-14 <?= str_replace(['text-', 'border-'], ['bg-', ''], explode(' ', $badge['class'])[0]) ?> <?= explode(' ', $badge['class'])[1] ?> rounded-2xl flex items-center justify-center text-xl shrink-0 shadow-inner">
                                <i class="fas fa-<?= $badge['icon'] ?>"></i>
                            </div>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-12 flex-1">
                                <div>
                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Order ID</p>
                                    <p class="text-sm font-bold text-slate-900">#<?= $order['order_id'] ?></p>
                                </div>
                                <div class="hidden md:block">
                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Date</p>
                                    <p class="text-sm font-medium text-slate-600">
                                        <?= date('d M Y', strtotime($order['created_at'])) ?>
                                    </p>
                                </div>
                                <div>
                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Total</p>
                                    <p class="text-sm font-black text-orange-600"><?= format_price($order['total_amount']) ?>
                                    </p>
                                </div>
                                <div class="hidden md:block">
                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Status</p>
                                    <span
                                        class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-tighter border <?= $badge['class'] ?>">
                                        <?= ucfirst($order['order_status'] ?? 'pending') ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <i class="fas fa-chevron-down ml-6 text-slate-300 arrow-icon"></i>
                    </div>

                    <div class="expandable-grid">
                        <div class="content-overflow">
                            <div class="px-6 pb-10 md:px-10 border-t border-slate-50 bg-slate-50/50">

                                <!-- Order Items -->
                                <div class="py-8">
                                    <h4
                                        class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b pb-2 mb-4">
                                        Items (<?= $order['total_qty'] ?? count($items) ?> Pcs)
                                    </h4>
                                    <div class="space-y-3">
                                        <?php foreach ($items as $item): ?>
                                            <div class="flex items-center gap-4 bg-white p-3 rounded-2xl border border-slate-100">
                                                <img src="<?= get_product_image($item['main_image']) ?>"
                                                    class="w-12 h-16 rounded-xl object-cover shadow-sm">
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-xs font-bold text-slate-900 truncate"><?= e($item['name']) ?></p>
                                                    <p class="text-[10px] text-slate-400 font-bold mt-1">Qty:
                                                        <?= $item['quantity'] ?> × <?= format_price($item['unit_price']) ?>
                                                    </p>
                                                </div>
                                                <p class="text-xs font-black text-slate-900">
                                                    <?= format_price($item['quantity'] * $item['unit_price']) ?>
                                                </p>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>

                                <!-- Shipping & Payment Info -->
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 pt-6 border-t border-slate-100">
                                    <div class="bg-white p-6 rounded-2xl border border-slate-100">
                                        <h5 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">
                                            Shipping Address</h5>
                                        <p class="text-sm text-slate-600 leading-relaxed">
                                            <?= e($order['shipping_address']) ?><br>
                                            <?= e($order['city'] ?? '') ?>         <?= e($order['pincode'] ?? '') ?>
                                        </p>
                                    </div>
                                    <div class="bg-white p-6 rounded-2xl border border-slate-100">
                                        <h5 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Payment
                                            Info</h5>
                                        <p class="text-sm text-slate-600">
                                            Method: <span
                                                class="font-bold"><?= ucfirst($order['payment_method'] ?? 'N/A') ?></span><br>
                                            Status: <span
                                                class="font-bold text-<?= $order['payment_status'] === 'paid' ? 'green' : 'orange' ?>-600">
                                                <?= ucfirst($order['payment_status'] ?? 'Pending') ?>
                                            </span>
                                        </p>
                                    </div>
                                </div>

                                <!-- Shipment Tracking -->
                                <?php if ($shipment): ?>
                                    <div class="mt-6 bg-white p-6 rounded-2xl border border-slate-100">
                                        <h5 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Shipment
                                            Tracking</h5>
                                        <p class="text-sm text-slate-600">
                                            Courier: <span class="font-bold"><?= e($shipment['courier_name'] ?? 'N/A') ?></span><br>
                                            Tracking: <span
                                                class="font-bold text-blue-600"><?= e($shipment['tracking_number'] ?? 'N/A') ?></span>
                                        </p>
                                    </div>
                                <?php endif; ?>

                                <!-- Actions -->
                                <div class="flex gap-3 mt-6">
                                    <a href="order-tracking.php?id=<?= $order['order_id'] ?>"
                                        class="px-6 py-3 bg-slate-900 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-orange-600 transition-all">
                                        Track Order
                                    </a>
                                    <?php if (($order['order_status'] ?? '') === 'delivered'): ?>
                                        <a href="return-request.php?id=<?= $order['order_id'] ?>"
                                            class="px-6 py-3 bg-slate-100 text-slate-600 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-200 transition-all">
                                            Request Return
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script>
    function toggle(id) {
        const card = document.getElementById(id);
        card.classList.toggle('active');
    }
</script>

<?php include '../../includes/footer.php'; ?>