<?php
require_once '../../config/database.php';
include '../../includes/header.php';

$stmt = $pdo->query("SELECT c.*, (SELECT COUNT(*) FROM products p WHERE p.category_id = c.category_id AND p.status = 1) as p_count FROM categories c ORDER BY name ASC");
$categories = $stmt->fetchAll();
?>

<main class="container mx-auto px-4 lg:px-12 py-12">
    <div class="mb-12">
        <h1 class="text-4xl font-black tracking-tight mb-2">Collections <span class="text-orange-600">.</span></h1>
        <p class="text-slate-400 font-medium">Browse our hand-picked ethnic ranges</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <?php foreach ($categories as $c): ?>
            <a href="product-list.php?category=<?php echo $c['category_id']; ?>"
                class="group block relative rounded-[3rem] overflow-hidden bg-white border border-slate-100 shadow-sm p-8 transition-all hover:border-orange-200">
                <div class="flex justify-between items-start mb-10">
                    <div>
                        <h3 class="text-xl font-bold text-slate-900">
                            <?php echo htmlspecialchars($c['name']); ?>
                        </h3>
                        <p class="text-xs text-slate-400 font-medium mt-1">
                            <?php echo $c['p_count']; ?> Articles Available
                        </p>
                    </div>
                    <div
                        class="w-12 h-12 bg-slate-50 rounded-2xl flex items-center justify-center text-slate-400 group-hover:bg-orange-600 group-hover:text-white transition-all">
                        <i class="fas fa-arrow-right"></i>
                    </div>
                </div>

                <div class="flex gap-2">
                    <div class="w-20 h-20 bg-slate-50 rounded-2xl overflow-hidden border border-slate-100">
                        <img src="../../assets/images/products/default.png"
                            class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all">
                    </div>
                    <!-- Mini placeholders for catalog look -->
                    <div class="w-20 h-20 bg-slate-50 rounded-2xl border border-slate-100 border-dashed"></div>
                    <div class="w-20 h-20 bg-slate-50 rounded-2xl border border-slate-100 border-dashed"></div>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</main>

<?php include '../../includes/footer.php'; ?>