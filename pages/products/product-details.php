<?php
require_once '../../config/config.php';
require_once '../../includes/functions.php'; // Includes Core DB & Cache
include '../../includes/header.php';

use Core\Database;
use Core\Cache;

$db = Database::getInstance()->getConnection();
$cache = new Cache();

$product_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Handle Review Submission (Clear Cache on Submit)
$success_msg = '';
$error_msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review']) && $user_id) {
    $rating = (int) $_POST['rating'];
    $review_text = trim($_POST['review_text']);

    if ($rating >= 1 && $rating <= 5) {
        $stmt = $db->prepare("INSERT INTO product_reviews (product_id, user_id, rating, review_text) VALUES (?, ?, ?, ?)");
        $stmt->execute([$product_id, $user_id, $rating, $review_text]);
        
        // Clear caches so new review shows up
        $cache->delete("product_reviews_$product_id");
        $cache->delete("product_$product_id"); // Update if avg rating is stored on product
        
        $success_msg = "Review submitted successfully!";
        echo "<script>window.location.href='product-details.php?id=$product_id';</script>";
        exit;
    } else {
        $error_msg = "Invalid rating selected.";
    }
}

// 1. Fetch Product (Cached)
$cacheKey = "product_$product_id";
$product = $cache->get($cacheKey);

if (!$product) {
    $stmt = $db->prepare("SELECT * FROM products WHERE product_id = ? AND status = 1");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($product) {
        $cache->set($cacheKey, $product, 3600); // 1 Hour Cache
    }
}

if (!$product) {
    echo "<div class='container mx-auto py-20 text-center'><h2 class='text-2xl font-bold'>Product not found</h2><a href='product-list.php' class='text-orange-600 hover:underline mt-4 block'>Back to Products</a></div>";
    include '../../includes/footer.php';
    exit;
}

// 2. Fetch Reviews (Cached)
$reviewCacheKey = "product_reviews_$product_id";
$reviews = $cache->get($reviewCacheKey);

if (!$reviews) {
    $stmt_reviews = $db->prepare("
        SELECT r.*, u.name as full_name 
        FROM product_reviews r 
        JOIN users u ON r.user_id = u.user_id 
        WHERE r.product_id = ? 
        ORDER BY r.created_at DESC
    ");
    $stmt_reviews->execute([$product_id]);
    $reviews = $stmt_reviews->fetchAll(PDO::FETCH_ASSOC);
    $cache->set($reviewCacheKey, $reviews, 3600);
}

// Calculate Average Rating
$avg_rating = 0;
$review_count = count($reviews);
if ($review_count > 0) {
    $sum = 0;
    foreach ($reviews as $r)
        $sum += $r['rating'];
    $avg_rating = round($sum / $review_count, 1);
}

// Check Wishlist Status (Cannot Cache globally, user specific)
$in_wishlist = false;
if ($user_id) {
    $stmt_w = $db->prepare("SELECT 1 FROM wishlist WHERE user_id = ? AND product_id = ?");
    $stmt_w->execute([$user_id, $product_id]);
    $in_wishlist = $stmt_w->fetchColumn();
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
                <div class="sticky top-28 space-y-4">
                    <div class="aspect-[3/4] rounded-[2.5rem] overflow-hidden bg-white shadow-xl shadow-zinc-200/50 relative">
                        <img id="mainImage" src="<?= get_product_image($product['main_image']) ?>" class="w-full h-full object-contain p-4">
                        <button type="button" id="wishlist-btn" onclick="toggleWishlist(<?= $product_id ?>)"
                            class="absolute top-6 right-6 w-12 h-12 bg-white/80 backdrop-blur-md rounded-full flex items-center justify-center hover:bg-white transition-all group shadow-lg z-10">
                            <i class="<?= $in_wishlist ? 'fas text-red-500' : 'far' ?> fa-heart text-xl group-hover:text-red-500 transition-colors"></i>
                        </button>
                    </div>
                    
                    <div class="grid grid-cols-5 gap-2">
                        <div onclick="changeImage('<?= get_product_image($product['main_image']) ?>')" class="cursor-pointer border-2 border-orange-500 rounded-xl overflow-hidden aspect-square">
                            <img src="<?= get_product_image($product['main_image']) ?>" class="w-full h-full object-cover">
                        </div>
                        <?php for($i=1; $i<=4; $i++): 
                            if(!empty($product['image_'.$i])): ?>
                            <div onclick="changeImage('<?= get_product_image($product['image_'.$i]) ?>')" class="cursor-pointer border border-zinc-200 hover:border-orange-500 rounded-xl overflow-hidden aspect-square opacity-70 hover:opacity-100 transition-all">
                                <img src="<?= get_product_image($product['image_'.$i]) ?>" class="w-full h-full object-cover">
                            </div>
                        <?php endif; endfor; ?>
                    </div>
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
                        <span id="price-display" class="text-3xl font-black text-orange-600 tracking-tight" 
                              data-base-price="<?= $product['price'] ?>"
                              data-wholesale-price="<?= $product['wholesale_price'] ?? 0 ?>"
                              data-min-wholesale="<?= $product['min_wholesale_qty'] ?? 1 ?>">
                            <?= format_price($product['price']) ?>
                        </span>
                        <?php if (($product['wholesale_price'] ?? 0) > 0): ?>
                            <span class="text-sm font-bold text-zinc-400 uppercase tracking-widest">
                                Wholesale: <?= format_price($product['wholesale_price']) ?> 
                                (Min <?= $product['min_wholesale_qty'] ?> Qty)
                            </span>
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

                        <div class="w-24">
                            <label class="sr-only">Quantity</label>
                            <input type="number" name="quantity" id="qty-input" value="1" min="1" max="<?= $product['stock'] ?>"
                                onchange="updatePrice()" onkeyup="updatePrice()"
                                class="w-full h-full text-center font-bold border-zinc-200 rounded-xl focus:ring-orange-500 focus:border-orange-500 text-lg">
                        </div>

                        <button type="submit" name="redirect" value="cart.php"
                            class="flex-1 bg-zinc-900 text-white py-4 rounded-xl font-bold uppercase tracking-widest hover:bg-orange-600 transition-all shadow-lg hover:shadow-orange-600/30">
                            Add to Cart
                        </button>

                        <button type="submit" name="redirect" value="checkout"
                            class="flex-1 bg-orange-600 text-white py-4 rounded-xl font-bold uppercase tracking-widest hover:bg-orange-700 transition-all shadow-lg shadow-orange-200">
                            Buy Now
                        </button>
                    </form>

                    <div id="wholesale-alert" class="hidden bg-orange-50 p-4 rounded-xl border border-orange-100 flex items-start gap-3 animation-fade-in">
                        <i class="fas fa-check-circle text-orange-600 mt-1"></i>
                        <div>
                            <p class="text-xs font-bold text-orange-800 uppercase tracking-wide mb-1">Wholesale Pricing Applied!</p>
                            <p class="text-xs text-orange-700">You are saving big with our bulk discount rates.</p>
                        </div>
                    </div>
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

        <!-- Similar Products -->
        <div class="mt-20 border-t border-zinc-200 pt-16">
            <h3 class="text-3xl font-black text-zinc-900 mb-10">Similar Products</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <?php
                $simCacheKey = "similar_prod_" . $product['category_id'] . "_" . $product_id;
                $similar = $cache->get($simCacheKey);

                if (!$similar) {
                    $stmt_sim = $db->prepare("SELECT * FROM products WHERE category_id = ? AND product_id != ? AND status = 1 LIMIT 4");
                    $stmt_sim->execute([$product['category_id'], $product_id]);
                    $similar = $stmt_sim->fetchAll();
                    $cache->set($simCacheKey, $similar, 3600);
                }
                
                if($similar):
                    foreach($similar as $sim):
                ?>
                <a href="product-details.php?id=<?= $sim['product_id'] ?>" class="group">
                    <div class="aspect-[3/4] bg-white rounded-2xl overflow-hidden mb-4 border border-zinc-100 relative">
                        <img src="<?= get_product_image($sim['main_image']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    </div>
                    <h4 class="font-bold text-zinc-900 truncate"><?= htmlspecialchars($sim['name']) ?></h4>
                    <p class="text-orange-600 font-bold mt-1"><?= format_price($sim['price']) ?></p>
                </a>
                <?php endforeach; else: ?>
                    <p class="text-zinc-400">No similar products found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
    function updatePrice() {
        const qtyInput = document.getElementById('qty-input');
        const priceDisplay = document.getElementById('price-display');
        const alertBox = document.getElementById('wholesale-alert');
        
        const qty = parseInt(qtyInput.value) || 1;
        const basePrice = parseFloat(priceDisplay.getAttribute('data-base-price'));
        const wholesalePrice = parseFloat(priceDisplay.getAttribute('data-wholesale-price'));
        const minWholesaleQty = parseInt(priceDisplay.getAttribute('data-min-wholesale'));

        let finalPrice = basePrice;
        let isWholesale = false;

        if (wholesalePrice > 0 && qty >= minWholesaleQty) {
            finalPrice = wholesalePrice;
            isWholesale = true;
        }

        // Format as Indian Rupee
        const formatter = new Intl.NumberFormat('en-IN', {
            style: 'currency',
            currency: 'INR'
        });

        priceDisplay.innerText = formatter.format(finalPrice); // Show unit price
        
        if(isWholesale) {
            alertBox.classList.remove('hidden');
            priceDisplay.classList.add('text-green-600');
            priceDisplay.classList.remove('text-orange-600');
        } else {
            alertBox.classList.add('hidden');
            priceDisplay.classList.remove('text-green-600');
            priceDisplay.classList.add('text-orange-600');
        }
    }

    function toggleWishlist(productId) {
        const btn = document.getElementById('wishlist-btn');
        const icon = btn.querySelector('i');
        
        // Optimistic UI update
        const wasActive = icon.classList.contains('fas');
        if (wasActive) {
             icon.classList.remove('fas', 'text-red-500');
             icon.classList.add('far');
        } else {
             icon.classList.remove('far');
             icon.classList.add('fas', 'text-red-500');
        }

        fetch('../../pages/cart/wishlist-action.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=toggle&product_id=' + productId
        })
        .then(response => response.json())
        .then(data => {
            if(data.status === 'error') {
                // Revert if error
                 if (wasActive) {
                     icon.classList.remove('far');
                     icon.classList.add('fas', 'text-red-500');
                } else {
                     icon.classList.remove('fas', 'text-red-500');
                     icon.classList.add('far');
                }
                
                alert(data.message);
                if(data.message.includes('login')) {
                    window.location.href = '../../pages/auth/login.php';
                }
            }
        })
        .catch(err => {
             console.error(err);
             // Revert logic on network error could go here
        });
    }

    function changeImage(src) {
        document.getElementById('mainImage').src = src;
    }

    // Initialize
    updatePrice();
</script>

<?php include '../../includes/footer.php'; ?>