<?php
session_start();
require_once 'config/database.php';
require_once 'config/config.php';
require_once 'includes/functions.php';
require_once 'includes/cms_helper.php';
require_once 'core/Database.php';
require_once 'core/Cache.php';

use Core\Database;
use Core\Cache;

$page = isset($_GET['page']) ? $_GET['page'] : 'home';
include 'includes/header.php';

// Initialize Core Components
$db = Database::getInstance()->getConnection();
$cache = new Cache();

// Fetch new electrical arrivals (Cached for 10 minutes)
$cacheKey = 'home_new_arrivals';
$new_arrivals = $cache->get($cacheKey);

if (!$new_arrivals) {
    // Cache Miss: Query DB
    $stmt = $db->query("
        SELECT p.*, c.name as category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.category_id 
        WHERE p.status = 'active' 
        ORDER BY p.created_at DESC 
        LIMIT 8
    ");
    $new_arrivals = $stmt->fetchAll();

    // Save to Cache
    $cache->set($cacheKey, $new_arrivals, 600);
}

?>

<main class="bg-[#fafafa] pb-20">

    <!-- Flash Sale Banner -->
    <section class="bg-yellow-500 py-3 relative overflow-hidden">
        <div class="container mx-auto px-4 flex flex-col md:flex-row justify-center items-center gap-4 text-black">
            <div class="flex items-center gap-2 font-black tracking-widest text-sm uppercase">
                <i class="fas fa-bolt animate-pulse text-xl"></i>
                <span>Exclusive HPL Distributor Offer</span>
            </div>
            <div class="hidden md:flex gap-4 font-mono text-xl font-black opacity-80">
                <span>LIMITED TIME DEALS ON BULK ORDERS</span>
            </div>
            <a href="pages/wholesale.php"
                class="bg-black text-white px-6 py-1 rounded-full text-xs font-bold hover:bg-white hover:text-black transition-all uppercase tracking-widest">
                Check Partner Rates
            </a>
        </div>
    </section>

    <div class="container mx-auto px-4 mt-8">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-6">

            <!-- Main Hero Banner -->
            <div
                class="md:col-span-8 relative rounded-[2.5rem] overflow-hidden h-[500px] md:h-[600px] group shadow-xl bg-zinc-900">
                <!-- Electrical Theme Background -->
                <img src="<?= get_cms_content('home', 'hero_image', 'https://images.unsplash.com/photo-1555963966-b7ae5404f6fc?auto=format&fit=crop&q=80&w=1200') ?>"
                    class="w-full h-full object-cover opacity-60 transition-transform duration-1000 group-hover:scale-110"
                    alt="Electrical Shop">

                <div
                    class="absolute inset-0 bg-gradient-to-t from-black via-black/40 to-transparent p-8 md:p-16 flex flex-col justify-end">
                    <div
                        class="mb-4 bg-yellow-500 text-black w-max px-4 py-1.5 rounded-lg text-sm font-black animate-pulse uppercase tracking-wide">
                        Authorized HPL Distributor
                    </div>
                    <h1 class="text-white text-4xl md:text-7xl font-sans font-bold mb-4 leading-tight">
                        <?= get_cms_content('home', 'hero_title', 'Power Your <br><span class="text-yellow-500">World Safely</span>') ?>
                    </h1>
                    <p class="text-gray-300 text-lg mb-8 max-w-lg">
                        <?= get_cms_content('home', 'hero_desc', 'Premium Switches, Wires, LED Lights & MCBs from top brands like HPL, Anchor, Polycab & Havells.') ?>
                    </p>
                    <div class="flex flex-wrap gap-4">
                        <a href="#products"
                            class="bg-yellow-500 text-black px-10 py-4 rounded-xl font-black hover:bg-white hover:text-black transition-all transform hover:-translate-y-1 uppercase tracking-widest text-xs">
                            View Catalog
                        </a>
                        <a href="pages/contact.php"
                            class="bg-zinc-800 text-white px-10 py-4 rounded-xl font-bold hover:bg-zinc-700 transition-all flex items-center gap-2 text-xs uppercase tracking-widest border border-zinc-700">
                            <i class="fas fa-phone-alt text-yellow-500"></i> Contact Us
                        </a>
                    </div>
                </div>
            </div>

            <!-- Side Banners -->
            <div class="md:col-span-4 flex flex-col gap-6">

                <!-- Secondary Banner 1 -->
                <div class="h-1/2 relative rounded-[2.5rem] overflow-hidden group bg-white border border-gray-100">
                    <img src="https://images.unsplash.com/photo-1565814329452-e1efa11c5b89?auto=format&fit=crop&q=80&w=600"
                        class="w-full h-full object-cover opacity-90 group-hover:scale-105 transition-all duration-500"
                        alt="LED Lighting">
                    <div
                        class="absolute inset-0 p-8 flex flex-col justify-end bg-gradient-to-t from-black/80 to-transparent">
                        <h3 class="text-white text-2xl font-bold mb-1">LED Lighting</h3>
                        <p class="text-gray-300 text-xs mb-3">Save up to 80% Energy</p>
                        <a href="#"
                            class="text-yellow-500 font-bold uppercase text-xs tracking-widest hover:text-white">Shop
                            LEDs &rarr;</a>
                    </div>
                </div>

                <!-- Secondary Banner 2 -->
                <div class="h-1/2 relative rounded-[2.5rem] overflow-hidden group bg-zinc-800">
                    <div class="absolute inset-0 p-8 flex flex-col justify-center items-center text-center">
                        <div
                            class="w-16 h-16 bg-yellow-500/20 text-yellow-500 rounded-full flex items-center justify-center mb-4 text-3xl">
                            <i class="fas fa-tools"></i>
                        </div>
                        <h3 class="text-2xl font-black text-white mb-2">PROJECT <br>SUPPLY</h3>
                        <p class="text-gray-400 text-sm mb-4">Contractors & Builders</p>
                        <a href="pages/services.php"
                            class="bg-yellow-500 text-black px-6 py-2 rounded-lg text-xs font-black hover:bg-white transition-colors uppercase tracking-widest">
                            Get Quote
                        </a>
                    </div>
                </div>

            </div>
        </div>

        <!-- Features Grid -->
        <div class="mt-12 grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white p-6 rounded-2xl flex items-center gap-4 shadow-sm border border-gray-100">
                <div class="text-yellow-600 text-2xl"><i class="fas fa-certificate"></i></div>
                <div>
                    <h5 class="text-sm font-bold">Authorized Partner</h5>
                    <p class="text-[10px] text-gray-400 uppercase tracking-tighter">HPL & Anchor</p>
                </div>
            </div>
            <div class="bg-white p-6 rounded-2xl flex items-center gap-4 shadow-sm border border-gray-100">
                <div class="text-yellow-600 text-2xl"><i class="fas fa-tag"></i></div>
                <div>
                    <h5 class="text-sm font-bold">Wholesale Rates</h5>
                    <p class="text-[10px] text-gray-400 uppercase tracking-tighter">Direct Distributor Pricing</p>
                </div>
            </div>
            <div class="bg-white p-6 rounded-2xl flex items-center gap-4 shadow-sm border border-gray-100">
                <div class="text-yellow-600 text-2xl"><i class="fas fa-truck-fast"></i></div>
                <div>
                    <h5 class="text-sm font-bold">Site Delivery</h5>
                    <p class="text-[10px] text-gray-400 uppercase tracking-tighter">For Bulk Orders</p>
                </div>
            </div>
            <div class="bg-white p-6 rounded-2xl flex items-center gap-4 shadow-sm border border-gray-100">
                <div class="text-yellow-600 text-2xl"><i class="fas fa-shield-halved"></i></div>
                <div>
                    <h5 class="text-sm font-bold">Genuine Products</h5>
                    <p class="text-[10px] text-gray-400 uppercase tracking-tighter">100% Original Warranty</p>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Introduction Section -->
<section class="bg-white py-20 overflow-hidden">
    <div class="container mx-auto px-4 md:px-10">
        <div class="flex flex-col lg:flex-row gap-16 items-center">

            <div class="lg:w-1/2 relative">
                <div class="aspect-square rounded-[3rem] overflow-hidden bg-zinc-100">
                    <img src="https://images.unsplash.com/photo-1621905251189-08b45d6a269e?auto=format&fit=crop&q=80&w=800"
                        class="w-full h-full object-cover" alt="Joshi Electrical Shop">
                </div>
                <div
                    class="absolute -bottom-8 -right-8 bg-yellow-500 p-8 rounded-[2rem] shadow-xl text-center hidden md:block">
                    <p class="text-4xl font-black text-black">2024</p>
                    <p class="text-xs font-bold uppercase tracking-widest">Established</p>
                </div>
            </div>

            <div class="lg:w-1/2">
                <span class="text-yellow-600 font-bold uppercase tracking-widest text-xs mb-2 block">About Us</span>
                <h2 class="text-4xl md:text-5xl font-black text-zinc-900 mb-6">
                    <?= get_cms_content('home', 'intro_title', 'Welcome to <br>Joshi Electrical') ?>
                </h2>
                <p class="text-zinc-500 text-lg leading-relaxed mb-6">
                    <?= get_cms_content('home', 'intro_text', 'Founded by <strong>Dr. Praveen Joshi</strong>, we are a trusted name in electrical supplies. As an <strong>Authorized Distributor for HPL</strong>, we bring you the safest and most reliable electrical components for your home and business.') ?>
                </p>
                <p class="text-zinc-500 text-lg leading-relaxed mb-8">
                    <?= get_cms_content('home', 'intro_text_2', 'From modular switches to heavy-duty industrial cables, we stock everything you need under one roof. Quality and customer safety are our top priorities.') ?>
                </p>
                <div class="flex gap-4">
                    <a href="pages/about.php"
                        class="text-zinc-900 border-b-2 border-yellow-500 font-bold hover:text-yellow-600 transition-colors">Read
                        Our Story</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="bg-zinc-50 py-20" id="products">
    <div class="container mx-auto px-4 md:px-10">
        <div class="text-center mb-16">
            <h3 class="text-3xl md:text-5xl font-black text-zinc-900 mb-4">Our Products</h3>
            <p class="text-zinc-500">High-quality electrical essentials for every need.</p>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Category 1: Switches -->
            <a href="pages/products/product-list.php?category=Switches"
                class="group block relative overflow-hidden rounded-[2.5rem] h-80">
                <img src="https://images.unsplash.com/photo-1556636530-6b7482d80e3d?auto=format&fit=crop&q=80&w=600"
                    class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                    alt="Switches">
                <div
                    class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent p-8 flex flex-col justify-end">
                    <div
                        class="w-12 h-12 bg-yellow-500 rounded-full flex items-center justify-center mb-4 text-zinc-900 text-xl">
                        <i class="fas fa-toggle-on"></i>
                    </div>
                    <h4 class="text-2xl font-bold text-white group-hover:text-yellow-400 transition-colors">Modular
                        Switches</h4>
                    <p
                        class="text-zinc-300 text-xs mt-2 opacity-0 group-hover:opacity-100 transition-opacity transform translate-y-4 group-hover:translate-y-0 duration-300">
                        Stylish & Durable</p>
                </div>
            </a>

            <!-- Category 2: Wires -->
            <a href="pages/products/product-list.php?category=Wires"
                class="group block relative overflow-hidden rounded-[2.5rem] h-80">
                <img src="https://images.unsplash.com/photo-1627960393049-3a6d47dd073c?auto=format&fit=crop&q=80&w=600"
                    class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                    alt="Wires">
                <div
                    class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent p-8 flex flex-col justify-end">
                    <div
                        class="w-12 h-12 bg-yellow-500 rounded-full flex items-center justify-center mb-4 text-zinc-900 text-xl">
                        <i class="fas fa-bezier-curve"></i>
                    </div>
                    <h4 class="text-2xl font-bold text-white group-hover:text-yellow-400 transition-colors">Wires &
                        Cables</h4>
                    <p
                        class="text-zinc-300 text-xs mt-2 opacity-0 group-hover:opacity-100 transition-opacity transform translate-y-4 group-hover:translate-y-0 duration-300">
                        FR & Zero Halogen</p>
                </div>
            </a>

            <!-- Category 3: Lighting -->
            <a href="pages/products/product-list.php?category=Lighting"
                class="group block relative overflow-hidden rounded-[2.5rem] h-80">
                <img src="https://images.unsplash.com/photo-1565814329452-e1efa11c5b89?auto=format&fit=crop&q=80&w=600"
                    class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                    alt="Lighting">
                <div
                    class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent p-8 flex flex-col justify-end">
                    <div
                        class="w-12 h-12 bg-yellow-500 rounded-full flex items-center justify-center mb-4 text-zinc-900 text-xl">
                        <i class="far fa-lightbulb"></i>
                    </div>
                    <h4 class="text-2xl font-bold text-white group-hover:text-yellow-400 transition-colors">LED Lighting
                    </h4>
                    <p
                        class="text-zinc-300 text-xs mt-2 opacity-0 group-hover:opacity-100 transition-opacity transform translate-y-4 group-hover:translate-y-0 duration-300">
                        Energy Efficient</p>
                </div>
            </a>

            <!-- Category 4: MCB -->
            <a href="pages/products/product-list.php?category=Safety"
                class="group block relative overflow-hidden rounded-[2.5rem] h-80">
                <img src="https://plus.unsplash.com/premium_photo-1678822987158-b08e50529d3c?q=80&w=2070&auto=format&fit=crop"
                    class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                    alt="MCB">
                <div
                    class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent p-8 flex flex-col justify-end">
                    <div
                        class="w-12 h-12 bg-yellow-500 rounded-full flex items-center justify-center mb-4 text-zinc-900 text-xl">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h4 class="text-2xl font-bold text-white group-hover:text-yellow-400 transition-colors">MCB & Safety
                    </h4>
                    <p
                        class="text-zinc-300 text-xs mt-2 opacity-0 group-hover:opacity-100 transition-opacity transform translate-y-4 group-hover:translate-y-0 duration-300">
                        Switchgear & Protection</p>
                </div>
            </a>
        </div>

        <div class="mt-8 text-center">
            <p class="text-zinc-400 text-sm font-bold uppercase tracking-widest">Also Stocking: Geysers • Fans •
                Inverters</p>
        </div>
    </div>
</section>

<!-- Best Sellers Grid -->
<section class="bg-white py-20">
    <div class="container mx-auto px-4 md:px-10">
        <div class="flex flex-col md:flex-row justify-between items-end mb-12 gap-4">
            <div>
                <h3 class="text-3xl font-black text-zinc-900">Featured Products</h3>
                <p class="text-zinc-500 mt-2">Top rated items available now</p>
            </div>
            <a href="pages/products/product-list.php"
                class="text-zinc-900 font-bold border-b-2 border-yellow-500 pb-1 hover:text-yellow-600 transition-all">View
                All Catalog</a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10">
            <?php if (empty($new_arrivals)): ?>
                <!-- Fallback / Empty State -->
                <div class="col-span-4 text-center py-16 bg-zinc-50 rounded-[2rem]">
                    <i class="fas fa-box-open text-zinc-300 text-6xl mb-4"></i>
                    <h3 class="text-xl font-bold text-zinc-400">Inventory Updating...</h3>
                    <p class="text-zinc-400">New electrical stock arriving soon.</p>
                </div>
            <?php else: ?>
                <?php foreach ($new_arrivals as $product): ?>
                    <div class="group">
                        <div
                            class="relative aspect-[4/5] overflow-hidden rounded-[2rem] bg-zinc-100 mb-6 border border-zinc-100">
                            <!-- Use PHP to get image, fallback to a generic electrical placeholder if needed -->
                            <a href="pages/products/product-details.php?id=<?= $product['product_id'] ?>">
                                <img src="<?= get_product_image($product['main_image']) ?>"
                                    class="w-full h-full object-cover p-8 group-hover:scale-105 transition-all duration-500"
                                    alt="<?= htmlspecialchars($product['name']) ?>">
                            </a>

                            <?php if ($product['discount_percent'] > 0): ?>
                                <span
                                    class="absolute top-4 left-4 bg-red-500 text-white px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-wide">
                                    Save <?= round($product['discount_percent']) ?>%
                                </span>
                            <?php endif; ?>

                            <a href="pages/products/product-details.php?id=<?= $product['product_id'] ?>"
                                class="absolute bottom-4 left-4 right-4 bg-zinc-900 text-white py-3 rounded-xl font-bold text-xs uppercase tracking-widest text-center translate-y-20 opacity-0 group-hover:translate-y-0 group-hover:opacity-100 transition-all duration-300">
                                View Item
                            </a>
                        </div>
                        <h4 class="text-zinc-900 font-bold truncate"><?= htmlspecialchars($product['name']) ?></h4>
                        <div class="flex gap-2 items-center mt-1">
                            <span class="text-yellow-600 font-black">₹<?= number_format($product['price']) ?></span>
                            <?php if ($product['wholesale_price']): ?>
                                <span class="text-[10px] text-zinc-400 bg-zinc-100 px-2 rounded font-bold uppercase">Wholesale:
                                    ₹<?= number_format($product['wholesale_price']) ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Brands Grid -->
<section class="bg-zinc-900 py-20 overflow-hidden">
    <div class="container mx-auto px-4 text-center">
        <div class="mb-12">
            <h2 class="text-xs font-black tracking-[0.4em] text-yellow-500 uppercase mb-2">Verified Partners</h2>
            <p class="text-3xl text-white font-bold">Authorized Distributors For</p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 px-4 md:px-0">
            <!-- Brand Item -->
            <div
                class="bg-zinc-800 h-24 rounded-xl flex items-center justify-center hover:bg-white hover:scale-105 transition-all duration-300 group cursor-pointer">
                <span
                    class="text-2xl font-black text-zinc-500 group-hover:text-black tracking-widest uppercase">HPL</span>
            </div>
            <!-- Brand Item -->
            <div
                class="bg-zinc-800 h-24 rounded-xl flex items-center justify-center hover:bg-white hover:scale-105 transition-all duration-300 group cursor-pointer">
                <span
                    class="text-2xl font-black text-zinc-500 group-hover:text-[#da291c] tracking-widest uppercase">Anchor</span>
            </div>
            <!-- Brand Item -->
            <div
                class="bg-zinc-800 h-24 rounded-xl flex items-center justify-center hover:bg-white hover:scale-105 transition-all duration-300 group cursor-pointer">
                <span
                    class="text-2xl font-black text-zinc-500 group-hover:text-[#e31e24] tracking-widest uppercase">Polycab</span>
            </div>
            <!-- Brand Item -->
            <div
                class="bg-zinc-800 h-24 rounded-xl flex items-center justify-center hover:bg-white hover:scale-105 transition-all duration-300 group cursor-pointer">
                <span
                    class="text-2xl font-black text-zinc-500 group-hover:text-[#ec1c24] tracking-widest uppercase">Havells</span>
            </div>
            <!-- Brand Item -->
            <div
                class="bg-zinc-800 h-24 rounded-xl flex items-center justify-center hover:bg-white hover:scale-105 transition-all duration-300 group cursor-pointer">
                <span
                    class="text-2xl font-black text-zinc-500 group-hover:text-[#0b5ed7] tracking-widest uppercase">Philips</span>
            </div>
            <!-- Brand Item -->
            <div
                class="bg-zinc-800 h-24 rounded-xl flex items-center justify-center hover:bg-white hover:scale-105 transition-all duration-300 group cursor-pointer">
                <span
                    class="text-2xl font-black text-zinc-500 group-hover:text-[#005eb8] tracking-widest uppercase">Crompton</span>
            </div>
            <!-- Brand Item -->
            <div
                class="bg-zinc-800 h-24 rounded-xl flex items-center justify-center hover:bg-white hover:scale-105 transition-all duration-300 group cursor-pointer">
                <span
                    class="text-2xl font-black text-zinc-500 group-hover:text-green-600 tracking-widest uppercase">Syska</span>
            </div>
            <!-- Brand Item -->
            <div
                class="bg-zinc-800 h-24 rounded-xl flex items-center justify-center hover:bg-white hover:scale-105 transition-all duration-300 group cursor-pointer">
                <span
                    class="text-2xl font-black text-zinc-500 group-hover:text-[#00558e] tracking-widest uppercase">Bajaj</span>
            </div>
        </div>
    </div>
</section>

<!-- Location Section -->
<section class="bg-white py-20 border-t border-zinc-100">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
            <div class="space-y-8">
                <div>
                    <span class="text-yellow-600 font-bold uppercase tracking-widest text-xs mb-2 block">Visit
                        Store</span>
                    <h2 class="text-4xl font-black text-zinc-900 mb-6">Joshi Electrical</h2>
                    <div class="space-y-4 text-zinc-600">
                        <p class="flex items-start gap-4">
                            <i class="fas fa-map-pin mt-1 text-yellow-500"></i>
                            <span>Opp. Civil Hospital, Main Road, [City Name], Uttar Pradesh - [Zip Code]</span>
                        </p>
                        <p class="flex items-center gap-4">
                            <i class="fas fa-phone-alt text-yellow-500"></i>
                            <span>+91 99565 10247, +91 7007465665</span>
                        </p>
                        <p class="flex items-center gap-4">
                            <i class="fas fa-clock text-yellow-500"></i>
                            <span>Opne: 9:00 AM - 9:00 PM (Daily)</span>
                        </p>
                    </div>
                </div>
                <div class="flex gap-4">
                    <a href="https://wa.me/917007465665"
                        class="bg-[#25D366] text-white px-8 py-4 rounded-xl font-bold flex items-center gap-2 hover:opacity-90 transition-all">
                        <i class="fab fa-whatsapp text-lg"></i> WhatsApp Us
                    </a>
                    <a href="pages/contact.php"
                        class="bg-zinc-900 text-white px-8 py-4 rounded-xl font-bold hover:bg-yellow-600 hover:text-black transition-all">
                        Get Directions
                    </a>
                </div>
            </div>

            <div class="h-[400px] bg-zinc-100 rounded-[3rem] overflow-hidden border border-zinc-200">
                <!-- Embedded Map Placeholder -->
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3502.9!2d77.0!3d28.0!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMjjCsDAwJzAwLjAiTiA3N8KwMDAnMDAuMCJF!5e0!3m2!1sen!2sin!4v1620000000000!5m2!1sen!2sin"
                    width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>