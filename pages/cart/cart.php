<?php
require_once '../../config/database.php';
include '../../includes/header.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>window.location.href='../auth/login.php';</script>";
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch Cart Items
$stmt = $pdo->prepare("
    SELECT c.cart_id, c.quantity, p.product_id, p.name, p.price, p.wholesale_price, p.min_wholesale_qty, p.main_image, p.stock 
    FROM cart c 
    JOIN products p ON c.product_id = p.product_id 
    WHERE c.user_id = ?
");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total_amount = 0;
$total_items = 0;
?>

<style>
    /* Custom Design Fixes */
    .card {
        background: white;
        border-radius: 2rem;
        border: 1px solid #f3f4f6;
        box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
    }

    .accent-bg {
        background-color: #FF6F1E;
    }

    .accent-text {
        color: #FF6F1E;
    }

    .qty-input {
        width: 100%;
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        padding: 0.5rem;
        text-align: center;
        font-weight: 700;
        font-size: 0.75rem;
    }

    .qty-input:focus {
        outline: 2px solid #FF6F1E;
        border-color: transparent;
    }
</style>

<div class="container mx-auto px-4 lg:px-10 py-6 lg:py-12">

    <header class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4 border-b pb-6">
        <div>
            <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight">Your Bag <span
                    class="text-gray-400 font-light">(<?= count($cart_items) ?>)</span></h1>
            <div class="flex gap-2 mt-2">
                <a href="?mode=retail"
                    class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest border transition-all <?= (!isset($_GET['mode']) || $_GET['mode'] == 'retail') ? 'bg-zinc-900 text-white border-zinc-900' : 'bg-white text-gray-400 border-gray-200 hover:border-zinc-900' ?>">
                    Retail
                </a>
                <a href="?mode=wholesale"
                    class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest border transition-all <?= (isset($_GET['mode']) && $_GET['mode'] == 'wholesale') ? 'bg-orange-600 text-white border-orange-600' : 'bg-white text-gray-400 border-gray-200 hover:border-orange-600' ?>">
                    Wholesale
                </a>
            </div>
        </div>

        <?php if (count($cart_items) > 0): ?>
            <div class="flex items-center gap-4">
                <a href="cart-action.php?action=clear"
                    class="text-[10px] font-bold uppercase text-gray-400 hover:text-red-500 transition-colors"
                    onclick="return confirm('Clear entire cart?')">
                    <i class="fas fa-trash-sweep mr-1"></i> Clear Bag
                </a>
            </div>
        <?php endif; ?>
    </header>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline"><?= $_SESSION['success'] ?></span>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline"><?= $_SESSION['error'] ?></span>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <div class="lg:col-span-8 space-y-4">

            <?php if (empty($cart_items)): ?>
                <div class="text-center py-20 bg-white rounded-[2rem] border-2 border-dashed border-gray-100">
                    <i class="fas fa-shopping-basket text-slate-200 text-6xl mb-4"></i>
                    <p class="text-gray-400 font-bold mb-4">Your bag is empty</p>
                    <a href="../products/product-list.php"
                        class="inline-block bg-zinc-900 text-white px-6 py-3 rounded-xl font-bold uppercase text-xs tracking-widest hover:bg-orange-600 transition-all">Start
                        Shopping</a>
                </div>
            <?php else: ?>
                <?php foreach ($cart_items as $item):
                    $mode = $_GET['mode'] ?? 'retail';
                    $is_wholesale_mode = $mode === 'wholesale';

                    $is_wholesale_qualified = $item['quantity'] >= $item['min_wholesale_qty'];
                    $effective_price = $is_wholesale_qualified ? $item['wholesale_price'] : $item['price'];

                    $subtotal = $effective_price * $item['quantity'];
                    $total_amount += $subtotal;
                    $total_items += $item['quantity'];
                    ?>
                    <div
                        class="card p-4 md:p-6 relative group <?= ($is_wholesale_mode && !$is_wholesale_qualified) ? 'border-orange-200 bg-orange-50/10' : '' ?>">
                        <div class="flex gap-4 md:gap-6">
                            <div class="w-24 md:w-32 shrink-0 aspect-[3/4] rounded-xl overflow-hidden bg-gray-50 border">
                                <img src="<?= get_product_image($item['main_image']) ?>" class="w-full h-full object-cover">
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="text-sm md:text-base font-bold truncate pr-6">
                                            <?= htmlspecialchars($item['name']) ?>
                                        </h3>
                                        <div class="mt-1">
                                            <?php if ($is_wholesale_qualified): ?>
                                                <span
                                                    class="bg-green-100 text-green-700 px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-tight">Wholesale
                                                    Active</span>
                                                <p class="text-lg font-bold text-green-600"><?= format_price($effective_price) ?>
                                                </p>
                                                <p class="text-[9px] text-gray-400 line-through"><?= format_price($item['price']) ?>
                                                </p>
                                            <?php else: ?>
                                                <p class="text-[9px] font-bold text-gray-400 uppercase">Retail Price</p>
                                                <p class="text-lg font-bold text-gray-900"><?= format_price($effective_price) ?></p>
                                                <?php if ($is_wholesale_mode): ?>
                                                    <p class="text-[9px] text-orange-600 font-bold mt-1">
                                                        Add <?= $item['min_wholesale_qty'] - $item['quantity'] ?> more for <span
                                                            class="underline">Wholesale Price
                                                            (<?= format_price($item['wholesale_price']) ?>)</span>
                                                    </p>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <a href="cart-action.php?action=remove&product_id=<?= $item['product_id'] ?>"
                                        class="text-gray-300 hover:text-red-500 transition-colors"
                                        onclick="return confirm('Remove item?')"><i class="fas fa-times"></i></a>
                                </div>

                                <div class="flex items-center gap-4 mt-6">
                                    <form action="cart-action.php" method="POST"
                                        class="flex items-center bg-gray-100 rounded-lg p-0.5">
                                        <input type="hidden" name="action" value="update">
                                        <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">

                                        <!-- Decrease -->
                                        <button type="submit" name="quantity" value="<?= $item['quantity'] - 1 ?>"
                                            class="w-8 h-8 flex items-center justify-center text-xs hover:bg-white rounded-md transition-colors <?= $item['quantity'] <= 1 ? 'opacity-50 cursor-not-allowed' : '' ?>"
                                            <?= $item['quantity'] <= 1 ? 'disabled' : '' ?>><i class="fas fa-minus"></i></button>

                                        <span class="w-8 text-center font-bold text-xs"><?= $item['quantity'] ?></span>

                                        <!-- Increase -->
                                        <button type="submit" name="quantity" value="<?= $item['quantity'] + 1 ?>"
                                            class="w-8 h-8 flex items-center justify-center text-xs hover:bg-white rounded-md transition-colors <?= $item['stock'] <= $item['quantity'] ? 'opacity-50 cursor-not-allowed' : '' ?>"
                                            <?= $item['stock'] <= $item['quantity'] ? 'disabled' : '' ?>><i
                                                class="fas fa-plus"></i></button>
                                    </form>
                                </div>

                                <div class="mt-4 pt-3 border-t flex justify-between items-center">
                                    <span class="text-xs font-bold text-gray-400">Subtotal</span>
                                    <p class="text-lg font-black accent-text"><?= format_price($subtotal) ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <?php if (!empty($cart_items)): ?>
            <div class="lg:col-span-4">
                <div class="sticky top-28 space-y-4">
                    <div class="card p-6 md:p-8">
                        <h2 class="text-lg font-bold mb-6 border-b pb-4">Order Summary</h2>

                        <div class="space-y-3 text-xs">
                            <div class="flex justify-between text-gray-500"><span>Bag Total (<?= $total_items ?>
                                    items)</span><span
                                    class="font-bold text-gray-900"><?= format_price($total_amount) ?></span></div>
                            <div class="flex justify-between text-gray-500"><span>GST (Inc.)</span><span
                                    class="font-bold text-gray-900">₹0</span></div>
                            <div class="flex justify-between text-gray-500"><span>Shipping</span><span
                                    class="text-green-600 font-bold uppercase">Free</span></div>

                            <div class="pt-4 border-t flex justify-between items-center">
                                <span class="text-sm font-bold">Payable Amount</span>
                                <span class="text-2xl font-black accent-text"><?= format_price($total_amount) ?></span>
                            </div>
                        </div>

                        <a href="checkout.php"
                            class="block w-full accent-bg text-white py-4 rounded-xl font-bold uppercase text-[10px] tracking-widest mt-8 shadow-lg shadow-orange-200 hover:brightness-110 active:scale-95 transition-all text-center">
                            Checkout Securely
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>