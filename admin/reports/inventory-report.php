<?php
$base_path = __DIR__ . '/../../';
require_once "../../config/database.php";

include $base_path . 'includes/admin-header.php';
include $base_path . 'includes/sidebar.php';
include $base_path . 'includes/notifications.php';

// Handle Stock Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_stock'])) {
    $p_id = $_POST['product_id'];
    $n_stock = $_POST['new_stock'];
    
    $stmt = $pdo->prepare("UPDATE products SET stock = ? WHERE product_id = ?");
    if ($stmt->execute([$n_stock, $p_id])) {
        $msg = "Stock updated successfully.";
    } else {
        $err = "Failed to update stock.";
    }
}

// Fetch stock data with valuation
$sql = "SELECT p.product_id, p.name, p.sku, p.price, 
               c.name as category_name,
               p.stock as total_stock,
               (SELECT cost_price FROM purchase_items WHERE product_id = p.product_id ORDER BY purchase_item_id DESC LIMIT 1) as last_cost
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.category_id
        ORDER BY p.stock ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$inventory = $stmt->fetchAll();
$totals = [
    'units' => 0,
    'retail_value' => 0,
    'cost_value' => 0
];

foreach ($inventory as &$item) {
    if ($item['total_stock'] === null)
        $item['total_stock'] = 0;

    $item['cost_price'] = $item['last_cost'] ?: ($item['price'] * 0.7);

    $totals['units'] += $item['total_stock'];
    $totals['retail_value'] += ($item['total_stock'] * $item['price']);
    $totals['cost_value'] += ($item['total_stock'] * $item['cost_price']);
}
?>

<main class="p-6 lg:p-12 bg-[#f8fafc] min-h-screen">
    <div class="mb-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
        <div>
            <h2 class="text-3xl font-black tracking-tighter text-slate-900">Stock Valuation & Assets<span
                    class="text-orange-600">.</span></h2>
            <p class="text-slate-400 text-sm font-medium">Monitor investment and real-time inventory health</p>
        </div>
        <div class="flex gap-3">
            <button onclick="window.print()"
                class="bg-white border border-slate-200 px-6 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-50 transition-all shadow-sm">
                <i class="fas fa-print mr-2"></i> Print Inventory
            </button>
        </div>
    </div>
    
    <?php if (isset($msg)): ?>
        <div class="bg-emerald-100 text-emerald-700 p-4 rounded-xl mb-6 font-bold text-center border-l-4 border-emerald-500">
            <?= $msg ?>
        </div>
    <?php endif; ?>

    <!-- Inventory KPIs -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <div class="bg-slate-900 p-8 rounded-[3rem] text-white shadow-xl shadow-slate-200">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Assets (At Cost)</p>
            <h3 class="text-2xl font-black">₹ <?= number_format($totals['cost_value'], 2) ?></h3>
        </div>
        <div class="bg-white p-8 rounded-[3rem] border border-slate-100 shadow-sm">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Potential Retail Value</p>
            <h3 class="text-2xl font-black text-slate-900">₹ <?= number_format($totals['retail_value'], 2) ?></h3>
        </div>
        <div class="bg-white p-8 rounded-[3rem] border border-slate-100 shadow-sm">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Units in Hand</p>
            <h3 class="text-2xl font-black text-orange-600"><?= number_format($totals['units']) ?></h3>
        </div>
    </div>

    <div class="bg-white rounded-[3rem] border border-slate-100 shadow-sm overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-slate-50 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                <tr>
                    <th class="px-8 py-6">Product Details</th>
                    <th class="px-6 py-6">Category</th>
                    <th class="px-6 py-6 text-center">Stock</th>
                    <th class="px-6 py-6 text-right">Last Cost</th>
                    <th class="px-6 py-6 text-right">Retail Price</th>
                    <th class="px-8 py-6 text-right">Asset Value</th>
                    <th class="px-6 py-6 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php foreach ($inventory as $item):
                    $lowStock = ($item['total_stock'] < 10);
                    ?>
                    <tr class="hover:bg-slate-50 transition-all">
                        <td class="px-8 py-5">
                            <p class="text-xs font-black text-slate-900"><?= htmlspecialchars($item['name']) ?></p>
                            <p class="text-[9px] text-slate-400 font-bold uppercase tracking-widest italic"><?= $item['sku'] ?></p>
                        </td>
                        <td class="px-6 py-5">
                            <span class="px-3 py-1 bg-slate-100 text-slate-500 rounded-lg text-[9px] font-black uppercase"><?= $item['category_name'] ?></span>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <p class="text-sm font-black <?= $lowStock ? 'text-red-500' : 'text-slate-900' ?>">
                                <?= $item['total_stock'] ?>
                                <?php if ($lowStock): ?>
                                    <i class="fas fa-exclamation-triangle text-[9px] ml-1"></i>
                                <?php endif; ?>
                            </p>
                        </td>
                        <td class="px-6 py-5 text-right text-xs font-bold text-slate-400">₹ <?= number_format($item['cost_price'], 2) ?></td>
                        <td class="px-6 py-5 text-right text-xs font-bold text-slate-900">₹ <?= number_format($item['price'] ?? 0, 2) ?></td>
                        <td class="px-8 py-5 text-right font-black text-slate-900">₹ <?= number_format($item['total_stock'] * $item['cost_price'], 2) ?></td>
                        <td class="px-6 py-5 text-center">
                        <form method="POST" class="flex items-center justify-center gap-2">
                            <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
                            <input type="hidden" name="update_stock" value="1">
                            <input type="number" name="new_stock" value="<?= $item['total_stock'] ?>" 
                                class="w-16 px-2 py-1 rounded-lg border border-slate-200 text-sm font-bold text-center focus:ring-2 focus:ring-orange-500 outline-none">
                            <button type="submit" class="text-orange-600 hover:text-orange-800 transition-colors">
                                <i class="fas fa-save"></i>
                            </button>
                        </form>
                    </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>
<?php include $base_path . "includes/admin-footer.php"; ?>
</body>

</html>