<?php
$base_path = $_SERVER['DOCUMENT_ROOT'] . '/ecommerce-website/';
require_once "../../config/database.php";


include $base_path . 'includes/sidebar.php';
$low_stock_query = "SELECT p.name, p.stock, p.min_stock_level, s.name as supplier 
                    FROM products p 
                    LEFT JOIN suppliers s ON p.preferred_supplier_id = s.supplier_id 
                    WHERE p.stock <= p.min_stock_level";

$low_items = $pdo->query($low_stock_query)->fetchAll(PDO::FETCH_ASSOC);

// 2. VISIBLE ALERT UI
if (!empty($low_items)): ?>
    <div class="mb-8 overflow-hidden rounded-[2rem] border border-red-100 bg-white shadow-xl shadow-red-50">
        <div class="flex flex-wrap items-center justify-between gap-4 bg-red-600 p-6 text-white">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white/20 backdrop-blur-md">
                    <i class="fas fa-exclamation-triangle animate-bounce"></i>
                </div>
                <div>
                    <h3 class="text-lg font-black tracking-tight uppercase">Low Stock Warning</h3>
                    <p class="text-xs font-bold opacity-80"><?= count($low_items) ?> products require immediate reordering.</p>
                </div>
            </div>
            <button onclick="toggleAlertDetails()" class="rounded-xl bg-white px-6 py-3 text-[10px] font-black uppercase tracking-widest text-red-600 hover:bg-slate-900 hover:text-white transition-all">
                Review Inventory
            </button>
        </div>

        <div id="lowStockAlertDetail" class="hidden p-8 transition-all">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-50 text-[10px] font-black uppercase tracking-widest text-slate-400">
                            <th class="pb-4">Product Name</th>
                            <th class="pb-4 text-center">In Stock</th>
                            <th class="pb-4 text-center">Minimum</th>
                            <th class="pb-4">Preferred Supplier</th>
                            <th class="pb-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <?php foreach ($low_items as $item): ?>
                            <tr class="group hover:bg-slate-50 transition-colors">
                                <td class="py-4 text-sm font-black text-slate-900"><?= htmlspecialchars($item['name']) ?></td>
                                <td class="py-4 text-center">
                                    <span class="rounded-lg bg-red-100 px-3 py-1 text-xs font-black text-red-600">
                                        <?= $item['stock'] ?>
                                    </span>
                                </td>
                                <td class="py-4 text-center text-xs font-bold text-slate-400"><?= $item['min_stock_level'] ?></td>
                                <td class="py-4 text-xs font-bold text-slate-600"><?= $item['supplier'] ?? 'Not Assigned' ?></td>
                                <td class="py-4 text-right">
                                    <a href="../Purchases/make-payment.php" class="text-[10px] font-black uppercase text-blue-600 hover:text-blue-900 underline underline-offset-4">Order Now</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="mt-8 flex justify-end border-t border-slate-50 pt-6">
                <a href="../Purchases/Purchase%20Order.php" class="inline-block rounded-2xl bg-slate-900 px-8 py-4 text-[10px] font-black uppercase tracking-widest text-white hover:bg-orange-600 transition-all shadow-lg">
                    Create Bulk Purchase Order
                </a>
            </div>
        </div>
    </div>

  <script>
function toggleAlertDetails() {
    const detail = document.getElementById('lowStockAlertDetail');
    
    // Check if the element is currently hidden
    if (detail.classList.contains('hidden')) {
        // Remove 'hidden' and add 'block' to make it visible
        detail.classList.remove('hidden');
        detail.classList.add('block', 'animate-in', 'fade-in', 'slide-in-from-top-2');
    } else {
        // Re-hide the element
        detail.classList.add('hidden');
        detail.classList.remove('block');
    }
}
</script>
<?php endif; ?>