<?php
require_once '../config/database.php';
include '../includes/header.php';

// Fetch Active Discount Banners (for Hero)
$hero_discount = $pdo->query("SELECT * FROM discounts WHERE status = 'active' AND NOW() BETWEEN start_date AND end_date ORDER BY value DESC LIMIT 1")->fetch();

// Fetch Latest Products
$stmt = $pdo->prepare("SELECT p.*, c.name as cat_name FROM products p LEFT JOIN categories c ON p.category_id = c.category_id WHERE p.status = 'active' ORDER BY p.created_at DESC LIMIT 8");
$stmt->execute();
$featured = $stmt->fetchAll();

// Fetch Top Categories
$cat_stmt = $pdo->query("SELECT c.*, (SELECT main_image FROM products WHERE category_id = c.category_id LIMIT 1) as cat_img FROM categories c LIMIT 4");
$categories = $cat_stmt->fetchAll();
?>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@700;800&family=Playfair+Display:ital,wght@1,900&display=swap');

    .hero-font {
        font-family: 'Playfair Display', serif;
    }

    .brand-font {
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    .glass-nav {
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(20px);
    }

    .animate-float {
        animation: float 6s ease-in-out infinite;
    }

    @keyframes float {
        0% {
            transform: translateY(0px);
        }

        50% {
            transform: translateY(-20px);
        }

        100% {
            transform: translateY(0px);
        }
    }
</style>

<div class="bg-white overflow-hidden">
    <!-- Hero Section -->
    <section class="relative min-h-[90vh] flex items-center pt-20">
        <div class="absolute inset-0 bg-gradient-to-br from-orange-50/50 to-white -z-10"></div>
        <div class="container mx-auto px-6 lg:px-12 grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div class="space-y-8">
                <div
                    class="inline-flex items-center gap-2 bg-orange-100/50 border border-orange-200 px-4 py-2 rounded-full">
                    <span class="w-2 h-2 bg-orange-600 rounded-full"></span>
                    <span class="text-[10px] font-black uppercase text-orange-600 tracking-wider">Autumn Collection
                        2025</span>
                </div>
                <h1 class="text-6xl md:text-8xl hero-font leading-tight italic">
                    <?php echo $hero_discount ? e($hero_discount['code']) . "<br>" : "Weaves of <br>"; ?>
                    <span class="text-orange-600 drop-shadow-sm">
                        <?php echo $hero_discount ? round($hero_discount['value']) . "% OFF" : "Grace & Style"; ?>
                    </span>
                </h1>
                <p class="text-lg text-slate-500 max-w-lg leading-relaxed font-medium">
                    <?php echo $hero_discount ? "Use code " . e($hero_discount['code']) . " to save big on our latest collection." : "Experience the timeless elegance of hand-crafted ethnic wear, designed for the modern connoisseur."; ?>
                </p>
                <div class="flex flex-wrap gap-4 pt-4">
                    <a href="products/product-list.php"
                        class="px-10 py-5 bg-slate-900 text-white rounded-2xl font-black uppercase tracking-widest hover:bg-orange-600 transition-all shadow-xl shadow-slate-200">Shop
                        Catalog</a>
                    <a href="products/categories.php"
                        class="px-10 py-5 bg-white border border-slate-200 text-slate-900 rounded-2xl font-black uppercase tracking-widest hover:bg-slate-50 transition-all">Collections</a>
                </div>
            </div>

            <div class="relative hidden lg:block">
                <div
                    class="w-[500px] h-[650px] bg-orange-100 rounded-[5rem] overflow-hidden rotate-2 animate-float shadow-2xl">
                    <img src="../assets/images/products/default.png"
                        class="w-full h-full object-cover -rotate-2 scale-110">
                </div>
                <div
                    class="absolute -bottom-10 -left-10 bg-white p-8 rounded-[3rem] shadow-2xl border border-slate-100 max-w-xs transition-all hover:scale-105">
                    <p class="text-[10px] font-black uppercase text-orange-600 mb-2">Editor's Pick</p>
                    <p class="font-bold text-slate-900 leading-snug">"The level of detail in their embroidery is simply
                        unmatched."</p>
                    <div class="flex gap-1 mt-4 text-orange-400 text-xs">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i
                            class="fas fa-star"></i><i class="fas fa-star"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Categories Grid -->
    <section class="py-24 container mx-auto px-6 lg:px-12">
        <div class="flex justify-between items-end mb-12">
            <div>
                <h2 class="text-3xl font-black tracking-tight mb-2">Signature Collections</h2>
                <p class="text-slate-400 font-medium">Browse by ethnic categories</p>
            </div>
            <a href="products/categories.php"
                class="text-sm font-bold text-orange-600 border-b-2 border-orange-600 pb-1">View All Sets</a>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
            <?php foreach ($categories as $cat): ?>
                <a href="products/product-list.php?category=<?php echo $cat['category_id']; ?>"
                    class="group block relative aspect-square bg-slate-100 rounded-[3rem] overflow-hidden border border-slate-100">
                    <img src="<?php echo get_product_image($cat['cat_img'] ?? ''); ?>"
                        class="w-full h-full object-cover group-hover:scale-110 transition-all grayscale group-hover:grayscale-0 opacity-60 group-hover:opacity-100">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-900/60 to-transparent flex items-end p-8">
                        <h4 class="text-white font-black uppercase text-sm tracking-widest">
                            <?php echo e($cat['name']); ?>
                        </h4>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Latest Products -->
    <section class="py-24 bg-slate-50">
        <div class="container mx-auto px-6 lg:px-12">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-black tracking-tight mb-4 italic hero-font">Trending Now</h2>
                <p class="text-slate-400 font-medium max-w-lg mx-auto leading-relaxed">Most loved pieces from our latest
                    drop. Hand-picked for the season's festivities.</p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 lg:gap-10">
                <?php foreach ($featured as $p): ?>
                    <a href="products/product-details.php?id=<?php echo $p['product_id']; ?>"
                        class="group p-4 bg-white rounded-[3rem] border border-slate-100 shadow-sm transition-all hover:shadow-xl hover:-translate-y-2">
                        <div class="aspect-[3/4] bg-slate-50 rounded-[2.5rem] overflow-hidden mb-6">
                            <img src="<?php echo get_product_image($p['main_image']); ?>"
                                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                        </div>
                        <div class="px-2">
                            <p class="text-[9px] font-black text-slate-300 uppercase tracking-widest">
                                <?php echo e($p['cat_name'] ?? 'Collection'); ?>
                            </p>
                            <h3 class="text-sm font-bold text-slate-900 mt-1 truncate">
                                <?php echo e($p['name']); ?>
                            </h3>
                            <div class="flex items-center justify-between mt-4">
                                <p class="text-lg font-black text-orange-600">
                                    <?php echo format_price($p['price'] * (1 - $p['discount_percent'] / 100)); ?>
                                </p>
                                <span
                                    class="w-10 h-10 bg-slate-50 rounded-full flex items-center justify-center text-slate-900 group-hover:bg-slate-900 group-hover:text-white transition-all">
                                    <i class="fas fa-plus text-[10px]"></i>
                                </span>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>

            <div class="text-center mt-16">
                <a href="products/product-list.php"
                    class="inline-block px-12 py-5 border-2 border-slate-900 text-slate-900 rounded-2xl font-black uppercase tracking-widest hover:bg-slate-900 hover:text-white transition-all">Explore
                    Entire Catalog</a>
            </div>
        </div>
    </section>
</div>

<?php include '../includes/footer.php'; ?>