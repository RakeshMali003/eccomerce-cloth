<?php
require_once '../../config/database.php';
require_once '../../includes/functions.php';
include '../../includes/header.php';

// Fetch categories for sidebar
$stmt_cats = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
$categories = $stmt_cats->fetchAll(PDO::FETCH_ASSOC);

// Base SQL
$sql = "SELECT p.*, c.name as category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.category_id 
        WHERE p.status = 1";

// Filter by Category
$category_filter_id = isset($_GET['category_id']) ? (int) $_GET['category_id'] : null;
if ($category_filter_id) {
    $sql .= " AND p.category_id = :cat_id";
}

// Sorting logic
$sort_option = $_GET['sort'] ?? 'newest';
switch ($sort_option) {
    case 'price_low':
        $sql .= " ORDER BY p.price ASC";
        break;
    case 'price_high':
        $sql .= " ORDER BY p.price DESC";
        break;
    case 'best_seller':
        // Assuming we track sales or just random for now if not tracked
        $sql .= " ORDER BY p.stock ASC"; // Just a proxy
        break;
    default:
        $sql .= " ORDER BY p.created_at DESC";
}

$stmt = $pdo->prepare($sql);
if ($category_filter_id) {
    $stmt->bindValue(':cat_id', $category_filter_id, PDO::PARAM_INT);
}
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
$total_products = count($products);
?>

<body class="bg-[#FBFBFB]">

    <div class="container mx-auto px-4 lg:px-6 py-12">
        <div class="flex flex-col lg:flex-row gap-8">

            <!-- Sidebar -->
            <aside class="w-full lg:w-72 shrink-0">
                <div class="sticky top-28 space-y-8 h-[calc(100vh-140px)] overflow-y-auto no-scrollbar pb-10">
                    <div class="p-6 bg-zinc-900 rounded-[2rem] text-white shadow-xl shadow-zinc-200">
                        <h4 class="text-sm font-bold mb-2">Wholesale Mode</h4>
                        <p class="text-[10px] text-zinc-400 mb-4 uppercase tracking-widest">Showing Factory Rates</p>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" checked>
                            <div
                                class="w-11 h-6 bg-zinc-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-orange-600">
                            </div>
                        </label>
                    </div>

                    <div>
                        <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-5">Main Categories
                        </h3>
                        <ul class="space-y-3 text-sm font-bold text-zinc-600">
                            <li>
                                <a href="product-list.php"
                                    class="flex justify-between items-center transition <?= !$category_filter_id ? 'text-orange-600' : 'hover:text-orange-600' ?>">
                                    All Products
                                    <span>(<?= $pdo->query("SELECT COUNT(*) FROM products WHERE status=1")->fetchColumn() ?>)</span>
                                </a>
                            </li>
                            <?php foreach ($categories as $cat):
                                // Get count per category
                                $count_stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE category_id = ? AND status=1");
                                $count_stmt->execute([$cat['category_id']]);
                                $count = $count_stmt->fetchColumn();
                                ?>
                                <li>
                                    <a href="?category_id=<?= $cat['category_id'] ?>"
                                        class="flex justify-between items-center transition <?= ($category_filter_id == $cat['category_id']) ? 'text-orange-600' : 'hover:text-orange-600' ?>">
                                        <?= htmlspecialchars($cat['name']) ?>
                                        <span>(<?= $count ?>)</span>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </aside>

            <!-- Main Content -->
            <main class="flex-1">
                <div class="flex justify-between items-center mb-8 pb-6 border-b border-zinc-100">
                    <h2 class="text-2xl font-serif italic text-zinc-900">
                        <?= $category_filter_id ? 'Category Selection' : 'New Arrivals' ?>
                        <span
                            class="text-xs font-sans font-bold text-zinc-400 not-italic ml-2 uppercase tracking-widest">(<?= $total_products ?>
                            Products)</span>
                    </h2>
                    <div class="flex items-center gap-4">
                        <span class="text-[10px] font-bold uppercase text-zinc-400">Sort By</span>
                        <form id="sortForm" method="GET">
                            <?php if ($category_filter_id): ?>
                                <input type="hidden" name="category_id" value="<?= $category_filter_id ?>">
                            <?php endif; ?>
                            <select name="sort" onchange="document.getElementById('sortForm').submit()"
                                class="bg-transparent text-xs font-bold uppercase tracking-widest outline-none border-none cursor-pointer text-zinc-900">
                                <option value="newest" <?= $sort_option == 'newest' ? 'selected' : '' ?>>Newest First
                                </option>
                                <option value="price_low" <?= $sort_option == 'price_low' ? 'selected' : '' ?>>Price: Low
                                    to High</option>
                                <option value="price_high" <?= $sort_option == 'price_high' ? 'selected' : '' ?>>Price:
                                    High to Low</option>
                            </select>
                        </form>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-x-4 gap-y-12">

                    <?php if ($total_products === 0): ?>
                        <div class="col-span-full text-center py-20">
                            <i class="fas fa-box-open text-slate-200 text-6xl mb-4"></i>
                            <h3 class="text-xl font-bold text-slate-900 mb-2">No Products Found</h3>
                            <p class="text-slate-400 text-sm">Try adjusting your filters or check back later.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($products as $product): ?>
                            <div class="product-card group cursor-pointer"
                                onclick="window.location.href='product-details.php?id=<?= $product['product_id'] ?>'">
                                <div class="relative aspect-[3/4] rounded-[2rem] overflow-hidden bg-zinc-100 mb-5 shadow-sm">
                                    <img src="<?= get_product_image($product['main_image'] ?? '') ?>"
                                        class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                                        alt="<?= htmlspecialchars($product['name']) ?>">

                                    <?php if (($product['discount_percent'] ?? 0) > 0): ?>
                                        <div class="absolute top-4 left-4 flex flex-col gap-2">
                                            <span
                                                class="badge-zoom bg-orange-600 text-white px-3 py-1 rounded-full text-[8px] font-black uppercase tracking-tighter shadow-sm">
                                                -<?= round($product['discount_percent']) ?>%
                                            </span>
                                        </div>
                                    <?php endif; ?>

                                    <div
                                        class="action-bar absolute bottom-3 left-3 right-3 flex gap-2 translate-y-4 opacity-0 group-hover:translate-y-0 group-hover:opacity-100 transition-all duration-300">
                                        <form action="../../pages/cart/cart-action.php" method="POST" class="contents"
                                            onclick="event.stopPropagation()">
                                            <input type="hidden" name="action" value="add">
                                            <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
                                            <button type="submit"
                                                class="flex-1 bg-zinc-900 text-white py-2 rounded-xl text-[9px] font-black uppercase hover:bg-orange-600 transition-all shadow-lg flex items-center justify-center">
                                                Add +
                                            </button>
                                        </form>
                                        <a href="product-details.php?id=<?= $product['product_id'] ?>"
                                            class="flex-1 bg-white text-black py-2 rounded-xl text-[9px] font-black uppercase hover:bg-orange-600 hover:text-white transition-all text-center flex items-center justify-center">
                                            View
                                        </a>
                                    </div>
                                </div>
                                <div class="space-y-1.5 px-1">
                                    <div class="flex justify-between items-start">
                                        <h3 class="font-bold text-zinc-900 text-[13px] leading-tight truncate pr-2">
                                            <?= htmlspecialchars($product['name']) ?>
                                        </h3>
                                        <span class="text-xs font-extrabold text-orange-600">
                                            <?= format_price($product['price']) ?>
                                        </span>
                                    </div>

                                    <?php if (($product['wholesale_price'] ?? 0) > 0): ?>
                                        <div class="bg-zinc-50 p-2.5 rounded-xl border border-zinc-100">
                                            <div
                                                class="flex justify-between items-center text-[9px] font-bold uppercase text-zinc-500">
                                                <span>Bulk Rate</span>
                                                <span
                                                    class="text-zinc-900 font-extrabold"><?= format_price($product['wholesale_price']) ?></span>
                                            </div>
                                            <div class="mt-1 text-[8px] text-zinc-400 font-medium">
                                                Min Qty: <?= $product['min_wholesale_qty'] ?? 1 ?> pcs
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                </div>

                <div class="mt-24 flex flex-col items-center">
                    <p class="text-xs font-bold text-zinc-400 uppercase tracking-widest mb-6">
                        Viewing <?= $total_products ?> of
                        <?= $pdo->query("SELECT COUNT(*) FROM products WHERE status=1")->fetchColumn() ?> Designs
                    </p>
                    <div class="w-64 h-1 bg-zinc-200 rounded-full mb-8 overflow-hidden">
                        <?php
                        $total_all = $pdo->query("SELECT COUNT(*) FROM products WHERE status=1")->fetchColumn();
                        $percentage = ($total_all > 0) ? ($total_products / $total_all) * 100 : 0;
                        ?>
                        <div class="bg-zinc-900 h-full transition-all" style="width: <?= $percentage ?>%"></div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <?php include '../../includes/footer.php'; ?>