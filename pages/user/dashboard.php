<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/ecommerce-website/config/database.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Require login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user data
$user_stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$user_stmt->execute([$user_id]);
$user = $user_stmt->fetch();

// Fetch order statistics
$total_orders = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ?");
$total_orders->execute([$user_id]);
$total_orders = $total_orders->fetchColumn();

$pending_orders = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ? AND order_status IN ('pending', 'confirmed', 'packed', 'shipped')");
$pending_orders->execute([$user_id]);
$pending_orders = $pending_orders->fetchColumn();

$completed_orders = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ? AND order_status = 'delivered'");
$completed_orders->execute([$user_id]);
$completed_orders = $completed_orders->fetchColumn();

$total_spent = $pdo->prepare("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE user_id = ? AND order_status = 'delivered'");
$total_spent->execute([$user_id]);
$total_spent = $total_spent->fetchColumn();

// Fetch recent orders
$recent_orders = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
$recent_orders->execute([$user_id]);
$recent_orders = $recent_orders->fetchAll();

// Wishlist count
$wishlist_count = $pdo->prepare("SELECT COUNT(*) FROM wishlist WHERE user_id = ?");
$wishlist_count->execute([$user_id]);
$wishlist_count = $wishlist_count->fetchColumn();

// Cart count
$cart_count = $pdo->prepare("SELECT COALESCE(SUM(quantity), 0) FROM cart WHERE user_id = ?");
$cart_count->execute([$user_id]);
$cart_count = $cart_count->fetchColumn();

include '../../includes/header.php';
?>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

    body {
        font-family: 'Plus Jakarta Sans', sans-serif;
        background-color: #fafafa;
    }

    .stat-card {
        @apply bg-white rounded-[2rem] p-8 border border-slate-100 shadow-sm hover:shadow-lg transition-all;
    }

    .quick-link {
        @apply flex items-center gap-4 p-5 bg-white rounded-2xl border border-slate-100 hover:border-orange-500 hover:shadow-md transition-all group;
    }
</style>

<div class="container mx-auto px-4 lg:px-12 py-10 lg:py-16">
    <!-- Welcome Header -->
    <div class="mb-12">
        <h1 class="text-4xl font-extrabold tracking-tight text-slate-900">
            Welcome back, <span class="text-orange-600">
                <?= htmlspecialchars(explode(' ', $user['name'])[0]) ?>
            </span>
        </h1>
        <p class="text-slate-400 mt-2">Here's an overview of your account activity.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
        <!-- Left Column - Stats & Orders -->
        <div class="lg:col-span-8 space-y-8">
            <!-- Stats Grid -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="stat-card">
                    <div
                        class="w-12 h-12 bg-orange-50 text-orange-600 rounded-2xl flex items-center justify-center mb-4">
                        <i class="fas fa-shopping-bag text-xl"></i>
                    </div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Orders</p>
                    <h3 class="text-2xl font-black text-slate-900 mt-1">
                        <?= $total_orders ?>
                    </h3>
                </div>
                <div class="stat-card">
                    <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center mb-4">
                        <i class="fas fa-clock text-xl"></i>
                    </div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">In Progress</p>
                    <h3 class="text-2xl font-black text-slate-900 mt-1">
                        <?= $pending_orders ?>
                    </h3>
                </div>
                <div class="stat-card">
                    <div
                        class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center mb-4">
                        <i class="fas fa-check-circle text-xl"></i>
                    </div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Completed</p>
                    <h3 class="text-2xl font-black text-slate-900 mt-1">
                        <?= $completed_orders ?>
                    </h3>
                </div>
                <div class="stat-card">
                    <div
                        class="w-12 h-12 bg-purple-50 text-purple-600 rounded-2xl flex items-center justify-center mb-4">
                        <i class="fas fa-indian-rupee-sign text-xl"></i>
                    </div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Spent</p>
                    <h3 class="text-2xl font-black text-slate-900 mt-1">₹
                        <?= number_format($total_spent) ?>
                    </h3>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="bg-white rounded-[2.5rem] p-8 border border-slate-100 shadow-sm">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-black text-slate-900">Recent Orders</h3>
                    <a href="../orders/order-history.php"
                        class="text-[10px] font-black text-orange-600 uppercase tracking-widest hover:underline">View
                        All</a>
                </div>

                <?php if (empty($recent_orders)): ?>
                    <div class="text-center py-12">
                        <i class="fas fa-shopping-bag text-slate-200 text-5xl mb-4"></i>
                        <p class="text-slate-400 text-sm">No orders yet. Start shopping!</p>
                        <a href="../products/product-list.php"
                            class="inline-block mt-4 bg-orange-600 text-white px-6 py-3 rounded-xl font-bold text-sm hover:bg-slate-900 transition-all">
                            Browse Products
                        </a>
                    </div>
                <?php else: ?>
                    <div class="space-y-3">
                        <?php foreach ($recent_orders as $order): ?>
                            <div
                                class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl hover:bg-slate-100 transition-all">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow-sm">
                                        <i class="fas fa-box text-slate-400"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-slate-900">#ORD-
                                            <?= $order['order_id'] ?>
                                        </p>
                                        <p class="text-[10px] text-slate-400">
                                            <?= date('d M Y', strtotime($order['created_at'])) ?>
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-black text-slate-900">₹
                                        <?= number_format($order['total_amount']) ?>
                                    </p>
                                    <span class="text-[9px] font-bold px-2 py-1 rounded-lg uppercase
                                        <?php
                                        switch ($order['order_status']) {
                                            case 'delivered':
                                                echo 'bg-emerald-100 text-emerald-600';
                                                break;
                                            case 'cancelled':
                                                echo 'bg-red-100 text-red-600';
                                                break;
                                            case 'shipped':
                                                echo 'bg-blue-100 text-blue-600';
                                                break;
                                            default:
                                                echo 'bg-amber-100 text-amber-600';
                                        }
                                        ?>">
                                        <?= $order['order_status'] ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Right Column - Profile & Quick Links -->
        <div class="lg:col-span-4 space-y-8">
            <!-- Profile Card -->
            <div class="bg-slate-900 rounded-[2.5rem] p-8 text-white">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-16 h-16 bg-white/10 rounded-full flex items-center justify-center text-2xl">
                        <i class="fas fa-user"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold">
                            <?= htmlspecialchars($user['name']) ?>
                        </h3>
                        <p class="text-slate-400 text-sm">
                            <?= htmlspecialchars($user['email']) ?>
                        </p>
                    </div>
                </div>
                <div class="space-y-3 text-sm">
                    <div class="flex items-center gap-3 text-slate-300">
                        <i class="fas fa-phone w-5"></i>
                        <span>
                            <?= htmlspecialchars($user['phone'] ?? 'Not set') ?>
                        </span>
                    </div>
                    <div class="flex items-center gap-3 text-slate-300">
                        <i class="fas fa-map-marker-alt w-5"></i>
                        <span>
                            <?= htmlspecialchars($user['city'] ?? 'Location not set') ?>
                        </span>
                    </div>
                </div>
                <a href="../profile/profile.php"
                    class="block w-full mt-6 py-4 bg-white/10 rounded-2xl text-center text-sm font-bold hover:bg-orange-600 transition-all">
                    Edit Profile
                </a>
            </div>

            <!-- Quick Links -->
            <div class="bg-white rounded-[2.5rem] p-8 border border-slate-100 shadow-sm">
                <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-6">Quick Access</h3>
                <div class="space-y-3">
                    <a href="../cart/wishlist.php" class="quick-link">
                        <div
                            class="w-10 h-10 bg-orange-50 text-orange-600 rounded-xl flex items-center justify-center group-hover:bg-orange-600 group-hover:text-white transition-all">
                            <i class="far fa-heart"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-bold text-slate-900">My Wishlist</p>
                            <p class="text-[10px] text-slate-400">
                                <?= $wishlist_count ?> items saved
                            </p>
                        </div>
                        <i class="fas fa-chevron-right text-slate-300 group-hover:text-orange-600"></i>
                    </a>
                    <a href="../cart/cart.php" class="quick-link">
                        <div
                            class="w-10 h-10 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-all">
                            <i class="fas fa-shopping-bag"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-bold text-slate-900">Shopping Bag</p>
                            <p class="text-[10px] text-slate-400">
                                <?= $cart_count ?> items in cart
                            </p>
                        </div>
                        <i class="fas fa-chevron-right text-slate-300 group-hover:text-blue-600"></i>
                    </a>
                    <a href="../orders/order-tracking.php" class="quick-link">
                        <div
                            class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white transition-all">
                            <i class="fas fa-truck"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-bold text-slate-900">Track Orders</p>
                            <p class="text-[10px] text-slate-400">View shipment status</p>
                        </div>
                        <i class="fas fa-chevron-right text-slate-300 group-hover:text-emerald-600"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>