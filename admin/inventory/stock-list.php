<?php
$base_path = __DIR__ . '/../../';
require_once $base_path . 'config/database.php';

include $base_path . 'includes/sidebar.php'; 
include $base_path . 'includes/notifications.php'; 

// Fetch Stock Data with Valuation
$query = "SELECT p.product_id, p.name, p.sku, p.stock, p.min_stock_level, p.price, 
          (p.stock * p.price) as inventory_value,
          c.name as category_name
          FROM products p 
          LEFT JOIN categories c ON p.category_id = c.category_id 
          ORDER BY p.stock ASC"; // Shows lowest stock first
$stock_items = $pdo->query($query)->fetchAll();

// Calculate Summary Stats
$total_items = 0;
$total_value = 0;
$low_stock_count = 0;

foreach($stock_items as $item) {
    $total_items += $item['stock'];
    $total_value += $item['inventory_value'];
    if($item['stock'] <= $item['min_stock_level']) $low_stock_count++;
}
?>

<?php
// Fetch last 15 stock movements
$log_query = "SELECT l.*, p.name as product_name 
              FROM stock_logs l 
              JOIN products p ON l.product_id = p.product_id 
              ORDER BY l.created_at DESC LIMIT 15";
$logs = $pdo->query($log_query)->fetchAll();
?>

<main class="p-6 lg:p-12">
    <div class="mb-10 flex justify-between items-end">
        <div>
            <h2 class="text-3xl font-black tracking-tighter text-slate-900">Inventory Stock<span class="text-orange-600">.</span></h2>
            <p class="text-slate-400 text-sm font-medium">Real-time tracking of your warehouse levels.</p>
        </div>
        <div class="flex gap-3">
             <button onclick="window.print()" class="bg-white border border-slate-200 text-slate-600 px-6 py-3 rounded-xl font-bold text-xs hover:bg-slate-50 transition-all">
                <i class="fas fa-print mr-2"></i> Print Report
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Items in Hand</p>
            <h3 class="text-3xl font-black text-slate-900"><?= number_format($total_items) ?></h3>
        </div>
        <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Stock Valuation</p>
            <h3 class="text-3xl font-black text-emerald-600">₹<?= number_format($total_value, 2) ?></h3>
        </div>
        <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Out of Stock / Low</p>
            <h3 class="text-3xl font-black text-red-500"><?= $low_stock_count ?></h3>
        </div>
    </div>

    <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="bg-slate-50 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                <tr>
                    <th class="px-8 py-6">Item Detail</th>
                    <th class="px-6 py-6">Category</th>
                    <th class="px-6 py-6 text-center">Status</th>
                    <th class="px-6 py-6 text-center">Current Qty</th>
                    <th class="px-6 py-6 text-right">Value (Qty x Price)</th>
                    <th class="px-8 py-6 text-center">Quick Adjust</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php foreach($stock_items as $item): 
                    $is_low = $item['stock'] <= $item['min_stock_level'];
                    $status_class = $item['stock'] == 0 ? 'bg-red-100 text-red-700' : ($is_low ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700');
                    $status_text = $item['stock'] == 0 ? 'Out of Stock' : ($is_low ? 'Low Stock' : 'Healthy');
                ?>
                <tr class="hover:bg-slate-50/50 transition-all">
                    <td class="px-8 py-5">
                        <p class="text-sm font-black text-slate-900"><?= htmlspecialchars($item['name']) ?></p>
                        <p class="text-[10px] text-slate-400 font-bold"><?= $item['sku'] ?></p>
                    </td>
                    <td class="px-6 py-5 text-xs font-bold text-slate-500"><?= $item['category_name'] ?: 'General' ?></td>
                    <td class="px-6 py-5 text-center">
                        <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase <?= $status_class ?>">
                            <?= $status_text ?>
                        </span>
                    </td>
                    <td class="px-6 py-5 text-center">
                        <span class="text-sm font-black <?= $is_low ? 'text-red-600' : 'text-slate-900' ?>">
                            <?= $item['stock'] ?>
                        </span>
                    </td>
                    <td class="px-6 py-5 text-right font-black text-slate-900">
                        ₹<?= number_format($item['inventory_value'], 2) ?>
                    </td>
                    <td class="px-8 py-5 text-center">
                        <button onclick="openAdjustment(<?= $item['product_id'] ?>, '<?= addslashes($item['name']) ?>', <?= $item['stock'] ?>)" 
                                class="bg-slate-900 text-white w-8 h-8 rounded-lg hover:bg-orange-600 transition-all">
                            <i class="fas fa-plus-minus text-[10px]"></i>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
<div class="mt-12">
    <h3 class="text-xl font-black text-slate-900 mb-6 px-4">Recent Stock Movements<span class="text-orange-600">.</span></h3>
    <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="bg-slate-50 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                <tr>
                    <th class="px-8 py-4">Time</th>
                    <th class="px-6 py-4">Product</th>
                    <th class="px-6 py-4 text-center">Previous</th>
                    <th class="px-6 py-4 text-center">Adjustment</th>
                    <th class="px-6 py-4 text-center">Final Stock</th>
                    <th class="px-8 py-4">Reason</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php foreach($logs as $l): ?>
                <tr class="text-xs font-bold text-slate-600">
                    <td class="px-8 py-4 text-slate-400"><?= date('H:i | d M', strtotime($l['created_at'])) ?></td>
                    <td class="px-6 py-4 text-slate-900"><?= htmlspecialchars($l['product_name']) ?></td>
                    <td class="px-6 py-4 text-center text-slate-400"><?= $l['old_qty'] ?></td>
                    <td class="px-6 py-4 text-center <?= $l['adjustment'] >= 0 ? 'text-emerald-500' : 'text-red-500' ?>">
                        <?= $l['adjustment'] > 0 ? '+'.$l['adjustment'] : $l['adjustment'] ?>
                    </td>
                    <td class="px-6 py-4 text-center text-slate-900 font-black"><?= $l['new_qty'] ?></td>
                    <td class="px-8 py-4 italic text-slate-400 font-medium"><?= htmlspecialchars($l['reason']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</main>

<div id="adjModal" class="fixed inset-0 z-[150] hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeAdj()"></div>
    <form action="process_stock.php" method="POST" class="bg-white w-full max-w-md rounded-[2.5rem] shadow-2xl relative z-10 p-8">
        <input type="hidden" name="product_id" id="adj_pid">
        <h3 class="text-xl font-black text-slate-900 mb-2">Adjust Stock</h3>
        <p id="adj_name" class="text-sm text-slate-400 font-medium mb-6"></p>

        <div class="space-y-4">
            <div>
                <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Current Quantity</label>
                <input type="text" id="adj_current" disabled class="input-premium w-full bg-slate-50 opacity-60">
            </div>
            <div>
                <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Adjustment (+ or -)</label>
                <input type="number" name="adjustment_qty" required placeholder="e.g. 10 or -5" class="input-premium w-full">
            </div>
            <button type="submit" class="w-full bg-slate-900 text-white py-4 rounded-2xl font-black uppercase tracking-widest hover:bg-orange-600 transition-all shadow-lg">
                Update Inventory
            </button>
        </div>
    </form>



                </div>
<script>
function openAdjustment(id, name, current) {
    document.getElementById('adj_pid').value = id;
    document.getElementById('adj_name').innerText = name;
    document.getElementById('adj_current').value = current;
    document.getElementById('adjModal').classList.replace('hidden', 'flex');
}
function closeAdj() {
    document.getElementById('adjModal').classList.replace('flex', 'hidden');
}
</script>