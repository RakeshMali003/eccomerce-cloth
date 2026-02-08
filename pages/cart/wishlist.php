<?php
require_once '../../config/database.php';
include '../../includes/header.php';

$is_logged_in = isset($_SESSION['user_id']);
$user_id = $is_logged_in ? $_SESSION['user_id'] : null;

// Handle wishlist actions via GET (for AJAX-like behavior)
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $product_id = isset($_GET['product_id']) ? (int) $_GET['product_id'] : 0;

    if ($is_logged_in && $product_id > 0) {
        if ($action === 'remove') {
            $pdo->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?")->execute([$user_id, $product_id]);
        } elseif ($action === 'add') {
            // Check if already in wishlist
            $check = $pdo->prepare("SELECT wishlist_id FROM wishlist WHERE user_id = ? AND product_id = ?");
            $check->execute([$user_id, $product_id]);
            if (!$check->fetch()) {
                $pdo->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)")->execute([$user_id, $product_id]);
            }
        } elseif ($action === 'move_to_cart') {
            // Move item from wishlist to cart
            $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, 1) 
                          ON DUPLICATE KEY UPDATE quantity = quantity + 1")->execute([$user_id, $product_id]);
            $pdo->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?")->execute([$user_id, $product_id]);
        } elseif ($action === 'move_all_to_cart') {
            // Move all wishlist items to cart
            $items = $pdo->prepare("SELECT product_id FROM wishlist WHERE user_id = ?");
            $items->execute([$user_id]);
            foreach ($items->fetchAll() as $item) {
                $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, 1) 
                              ON DUPLICATE KEY UPDATE quantity = quantity + 1")->execute([$user_id, $item['product_id']]);
            }
            $pdo->prepare("DELETE FROM wishlist WHERE user_id = ?")->execute([$user_id]);
        }
    }
    header("Location: wishlist.php");
    exit();
}

// Fetch wishlist items from database
$wishlist = [];
if ($is_logged_in) {
    $stmt = $pdo->prepare("
        SELECT w.wishlist_id, w.product_id, w.created_at,
               p.name, p.price, p.wholesale_price, p.stock, p.main_image, p.discount_percent,
               c.name as category_name
        FROM wishlist w 
        JOIN products p ON w.product_id = p.product_id 
        LEFT JOIN categories c ON p.category_id = c.category_id
        WHERE w.user_id = ?
        ORDER BY w.created_at DESC
    ");
    $stmt->execute([$user_id]);
    $wishlist = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<style>
    .wish-item {
        transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .wish-item.removing {
        opacity: 0;
        transform: scale(0.9) translateY(20px);
    }
</style>

<div id="deleteModal"
    class="fixed inset-0 bg-slate-900/60 backdrop-blur-md z-[1000] hidden items-center justify-center p-4">
    <div id="modalBox"
        class="bg-white rounded-[3rem] p-10 max-w-sm w-full shadow-2xl scale-95 opacity-0 transition-all duration-300">
        <div
            class="w-20 h-20 bg-orange-50 text-orange-600 rounded-full flex items-center justify-center mx-auto mb-6 text-3xl">
            <i class="fas fa-heart-broken"></i>
        </div>
        <h3 class="text-2xl font-extrabold text-center mb-2">Remove Item?</h3>
        <p class="text-slate-500 text-center text-sm mb-10 leading-relaxed">This article will be removed from your
            curated list.</p>
        <div class="flex gap-4">
            <button onclick="closeModal()"
                class="flex-1 py-4 rounded-2xl font-bold text-slate-400 hover:bg-slate-50 transition">Cancel</button>
            <a id="confirmDeleteBtn" href="#"
                class="flex-1 py-4 bg-slate-900 text-white rounded-2xl font-bold hover:bg-red-600 transition-all text-center">Remove</a>
        </div>
    </div>
</div>

<div class="container mx-auto px-4 lg:px-12 py-10 lg:py-20">

    <header class="mb-12 flex flex-col md:flex-row justify-between items-end gap-6 border-b border-slate-200 pb-10">
        <div>
            <h1 class="text-4xl lg:text-5xl font-extrabold tracking-tighter">Your Curation <span
                    class="text-slate-300 font-light">(<?= count($wishlist) ?>)</span></h1>
            <p class="text-slate-400 text-xs font-bold uppercase tracking-[0.3em] mt-3">Saved Articles</p>
        </div>

        <?php if (!empty($wishlist)): ?>
            <div class="flex gap-4">
                <a href="?action=move_all_to_cart"
                    class="px-8 py-4 bg-orange-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-900 transition-all shadow-xl shadow-orange-200 active:scale-95">
                    Add All to Bag
                </a>
            </div>
        <?php endif; ?>
    </header>

    <?php if (!$is_logged_in): ?>
        <div class="text-center py-20 bg-white rounded-[3rem] border-2 border-dashed border-slate-100">
            <i class="fas fa-user-lock text-6xl text-slate-200 mb-6"></i>
            <h3 class="text-xl font-bold text-slate-900 mb-2">Login Required</h3>
            <p class="text-slate-400 text-sm mb-8">Please login to view your wishlist.</p>
            <a href="../auth/login.php"
                class="inline-block bg-orange-600 text-white px-8 py-4 rounded-2xl font-black uppercase text-[10px] tracking-widest shadow-xl shadow-orange-200">
                Login Now
            </a>
        </div>
    <?php elseif (empty($wishlist)): ?>
        <div class="text-center py-20 bg-white rounded-[3rem] border-2 border-dashed border-slate-100">
            <i class="far fa-heart text-6xl text-slate-200 mb-6"></i>
            <h3 class="text-xl font-bold text-slate-900 mb-2">Your Wishlist is Empty</h3>
            <p class="text-slate-400 text-sm mb-8">Start adding products you love to your wishlist.</p>
            <a href="../products/product-list.php"
                class="inline-block bg-orange-600 text-white px-8 py-4 rounded-2xl font-black uppercase text-[10px] tracking-widest shadow-xl shadow-orange-200">
                Browse Products
            </a>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8" id="wishlistGrid">
            <?php foreach ($wishlist as $item): ?>
                <div class="wish-item bg-white rounded-[2.5rem] border border-slate-100 overflow-hidden group shadow-sm hover:shadow-xl transition-all"
                    id="item-<?= $item['product_id'] ?>">
                    <div class="relative aspect-[3/4] overflow-hidden">
                        <img src="<?= get_product_image($item['main_image']) ?>"
                            class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                        <button onclick="triggerDelete(<?= $item['product_id'] ?>)"
                            class="absolute top-5 right-5 w-10 h-10 bg-white/80 backdrop-blur-md rounded-full flex items-center justify-center text-slate-400 hover:text-red-500 transition-all opacity-0 group-hover:opacity-100">
                            <i class="fas fa-times"></i>
                        </button>
                        <div class="absolute bottom-4 left-4">
                            <?php if ($item['stock'] > 0): ?>
                                <span
                                    class="px-3 py-1 bg-green-500 text-white text-[8px] font-black uppercase rounded-lg shadow-lg">In
                                    Stock</span>
                            <?php else: ?>
                                <span
                                    class="px-3 py-1 bg-red-500 text-white text-[8px] font-black uppercase rounded-lg shadow-lg">Out
                                    of Stock</span>
                            <?php endif; ?>
                        </div>
                        <?php if ($item['discount_percent'] > 0): ?>
                            <div class="absolute top-4 left-4">
                                <span
                                    class="px-3 py-1 bg-orange-600 text-white text-[8px] font-black uppercase rounded-lg shadow-lg">
                                    -<?= round($item['discount_percent']) ?>% OFF
                                </span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="p-6">
                        <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest">
                            <?= e($item['category_name'] ?? 'Collection') ?></p>
                        <h3 class="font-bold text-slate-900 truncate mt-1"><?= e($item['name']) ?></h3>
                        <div class="flex justify-between items-center mt-4 pt-4 border-t border-slate-50">
                            <div>
                                <p class="text-[8px] font-bold text-slate-300 uppercase">Retail Price</p>
                                <p class="text-lg font-black text-orange-600"><?= format_price($item['price']) ?></p>
                            </div>
                            <div class="text-right">
                                <p class="text-[8px] font-bold text-slate-400 uppercase">Dealer</p>
                                <p class="text-sm font-bold text-slate-500"><?= format_price($item['wholesale_price']) ?></p>
                            </div>
                        </div>
                        <a href="?action=move_to_cart&product_id=<?= $item['product_id'] ?>"
                            class="block w-full mt-6 py-4 bg-slate-50 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-900 hover:text-white transition-all text-center">
                            Move to Bag
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
    let deleteProductId = null;

    function triggerDelete(productId) {
        deleteProductId = productId;
        const modal = document.getElementById('deleteModal');
        const box = document.getElementById('modalBox');
        const confirmBtn = document.getElementById('confirmDeleteBtn');

        confirmBtn.href = '?action=remove&product_id=' + productId;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        setTimeout(() => {
            box.classList.remove('scale-95', 'opacity-0');
            box.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeModal() {
        const modal = document.getElementById('deleteModal');
        const box = document.getElementById('modalBox');
        box.classList.add('scale-95', 'opacity-0');
        box.classList.remove('scale-100', 'opacity-100');
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }, 300);
    }
</script>

<?php include '../../includes/footer.php'; ?>