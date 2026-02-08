<?php
require_once '../../config/database.php';
include '../../includes/header.php';

$product_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Handle Review Submission
$success_msg = '';
$error_msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review']) && $user_id) {
    $rating = (int) $_POST['rating'];
    $review_text = trim($_POST['review_text']);

    if ($rating >= 1 && $rating <= 5) {
        $stmt = $pdo->prepare("INSERT INTO product_reviews (product_id, user_id, rating, review_text) VALUES (?, ?, ?, ?)");
        $stmt->execute([$product_id, $user_id, $rating, $review_text]);
        $success_msg = "Review submitted successfully!";
        // Redirect to avoid resubmission
        echo "<script>window.location.href='product-details.php?id=$product_id';</script>";
        exit;
    } else {
        $error_msg = "Invalid rating selected.";
    }
}

// Fetch Product
$stmt = $pdo->prepare("SELECT * FROM products WHERE product_id = ? AND status = 1");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    echo "<div class='container mx-auto py-20 text-center'><h2 class='text-2xl font-bold'>Product not found</h2><a href='product-list.php' class='text-orange-600 hover:underline mt-4 block'>Back to Products</a></div>";
    include '../../includes/footer.php';
    exit;
}

// Fetch Reviews
$stmt_reviews = $pdo->prepare("
    SELECT r.*, u.full_name 
    FROM product_reviews r 
    JOIN users u ON r.user_id = u.user_id 
    WHERE r.product_id = ? 
    ORDER BY r.created_at DESC
");
$stmt_reviews->execute([$product_id]);
$reviews = $stmt_reviews->fetchAll(PDO::FETCH_ASSOC);

// Calculate Average Rating
$avg_rating = 0;
$review_count = count($reviews);
if ($review_count > 0) {
    $sum = 0;
    foreach ($reviews as $r)
        $sum += $r['rating'];
    $avg_rating = round($sum / $review_count, 1);
}
?>

<div class="bg-[#FBFBFB] py-12">
    <div class="container mx-auto px-4 lg:px-6">

        <!-- Breadcrumb -->
        <nav class="flex mb-8 text-xs font-bold uppercase tracking-widest text-zinc-400">
            <a href="product-list.php" class="hover:text-orange-600">Products</a>
            <span class="mx-2">/</span>
            <span class="text-zinc-900"><?= htmlspecialchars($product['name']) ?></span>
        </nav>

        <div class="flex flex-col lg:flex-row gap-12">
            <!-- Image Gallery -->
            <div class="w-full lg:w-1/2">
                <div
                    class="aspect-[3/4] rounded-[2.5rem] overflow-hidden bg-white shadow-xl shadow-zinc-200/50 mb-6 sticky top-28">
                    <img src="<?= get_product_image($product['main_image']) ?>" class="w-full h-full object-cover">
                </div>
            </div>

            <!-- Product Info -->
            <div class="w-full lg:w-1/2 space-y-8">
                <div>
                    <h1 class="text-3xl lg:text-5xl font-serif italic text-zinc-900 mb-4">
                        <?= htmlspecialchars($product['name']) ?></h1>

                    <div class="flex items-center gap-4 mb-6">
                        <div class="flex text-orange-500 text-sm">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="<?= $i <= $avg_rating ? 'fas' : 'far' ?> fa-star"></i>
                            <?php endfor; ?>
                        </div>
                        <span class="text-xs font-bold text-zinc-400 uppercase tracking-wide"><?= $review_count ?>
                            Reviews</span>
                    </div>

                    <div class="flex items-baseline gap-4">
                        <span
                            class="text-3xl font-black text-orange-600 tracking-tight"><?= format_price($product['price']) ?></span>
                        <?php if (($product['wholesale_price'] ?? 0) > 0): ?>
                            <span class="text-sm font-bold text-zinc-400 uppercase tracking-widest">Wholesale:
                                <?= format_price($product['wholesale_price']) ?></span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="prose prose-zinc">
                    <p class="text-zinc-500 leading-relaxed">
                        <?= nl2br(htmlspecialchars($product['description'] ?? 'No description available.')) ?>
                    </p>
                </div>

                <!-- Add to Cart Form -->
                <div class="space-y-6 pt-8 border-t border-zinc-100">
                    <form action="../../pages/cart/cart-action.php" method="POST" class="flex gap-4">
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="product_id" value="<?= $product_id ?>">

                        <div class="w-20">
                            <input type="number" name="quantity" value="1" min="1" max="<?= $product['stock'] ?>"
                                class="w-full h-full text-center font-bold border-zinc-200 rounded-xl focus:ring-orange-500 focus:border-orange-500">
                        </div>

                        <button type="submit"
                            class="flex-1 bg-zinc-900 text-white py-4 rounded-xl font-bold uppercase tracking-widest hover:bg-orange-600 transition-all shadow-lg hover:shadow-orange-600/30">
                            Add to Cart
                        </button>

                        <button type="button"
                            class="w-16 bg-white text-zinc-900 border border-zinc-200 py-4 rounded-xl flex items-center justify-center hover:bg-zinc-50 transition-all">
                            <i class="far fa-heart"></i>
                        </button>
                    </form>

                    <?php if (($product['min_wholesale_qty'] ?? 0) > 1): ?>
                        <div class="bg-orange-50 p-4 rounded-xl border border-orange-100 flex items-start gap-3">
                            <i class="fas fa-info-circle text-orange-600 mt-1"></i>
                            <div>
                                <p class="text-xs font-bold text-orange-800 uppercase tracking-wide mb-1">Wholesale
                                    Information</p>
                                <p class="text-xs text-orange-700">Minimum order quantity for wholesale pricing is
                                    <?= $product['min_wholesale_qty'] ?> units.</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="grid grid-cols-2 gap-4 text-xs font-bold text-zinc-500 pt-8">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-check-circle text-green-500"></i>
                        <span>In Stock (<?= $product['stock'] ?> units)</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-truck text-zinc-400"></i>
                        <span>Free Shipping</span>
                    </div>
                </div>

                <!-- Reviews Section -->
                <div class="mt-16 pt-10 border-t border-zinc-200">
                    <h3 class="text-2xl font-bold text-zinc-900 mb-8">Customer Reviews</h3>

                    <!-- Review Form -->
                    <?php if ($user_id): ?>
                        <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-zinc-100 mb-10">
                            <h4 class="text-sm font-bold uppercase tracking-widest mb-4">Write a Review</h4>
                            <form method="POST">
                                <input type="hidden" name="submit_review" value="1">
                                <div class="mb-4">
                                    <label class="block text-xs font-bold text-zinc-500 uppercase mb-2">Rating</label>
                                    <select name="rating"
                                        class="w-full border-zinc-200 rounded-xl focus:ring-orange-500 focus:border-orange-500">
                                        <option value="5">⭐⭐⭐⭐⭐ (Excellent)</option>
                                        <option value="4">⭐⭐⭐⭐ (Good)</option>
                                        <option value="3">⭐⭐⭐ (Average)</option>
                                        <option value="2">⭐⭐ (Poor)</option>
                                        <option value="1">⭐ (Terrible)</option>
                                    </select>
                                </div>
                                <div class="mb-4">
                                    <label class="block text-xs font-bold text-zinc-500 uppercase mb-2">Review</label>
                                    <textarea name="review_text" rows="3"
                                        class="w-full border-zinc-200 rounded-xl focus:ring-orange-500 focus:border-orange-500"
                                        placeholder="Share your experience..."></textarea>
                                </div>
                                <button type="submit"
                                    class="bg-orange-600 text-white px-6 py-3 rounded-xl font-bold uppercase text-xs tracking-widest hover:bg-zinc-900 transition-all">Submit
                                    Review</button>
                            </form>
                        </div>
                    <?php else: ?>
                        <p class="text-sm text-zinc-500 mb-8">Please <a href="../auth/login.php"
                                class="text-orange-600 font-bold underline">login</a> to write a review.</p>
                    <?php endif; ?>

                    <!-- Reviews List -->
                    <div class="space-y-6">
                        <?php if ($review_count > 0): ?>
                            <?php foreach ($reviews as $review): ?>
                                <div class="bg-white p-6 rounded-[2rem] border border-zinc-100">
                                    <div class="flex justify-between items-start mb-4">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-10 h-10 bg-zinc-100 rounded-full flex items-center justify-center font-bold text-zinc-500">
                                                <?= strtoupper(substr($review['full_name'], 0, 1)) ?>
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold text-zinc-900">
                                                    <?= htmlspecialchars($review['full_name']) ?></p>
                                                <div class="flex text-orange-500 text-[10px]">
                                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                                        <i class="<?= $i <= $review['rating'] ? 'fas' : 'far' ?> fa-star"></i>
                                                    <?php endfor; ?>
                                                </div>
                                            </div>
                                        </div>
                                        <span
                                            class="text-[10px] font-bold text-zinc-400 uppercase"><?= date('M d, Y', strtotime($review['created_at'])) ?></span>
                                    </div>
                                    <p class="text-sm text-zinc-600 leading-relaxed">
                                        <?= nl2br(htmlspecialchars($review['review_text'])) ?></p>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-zinc-400 text-sm italic">No reviews yet. Be the first to review!</p>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>