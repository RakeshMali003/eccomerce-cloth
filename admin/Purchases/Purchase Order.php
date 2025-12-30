<?php

$base_path = $_SERVER['DOCUMENT_ROOT'] . '/ecommerce-website/';

include $base_path . 'includes/sidebar.php'; 
include $base_path . 'includes/notifications.php'; 
require_once "../../config/database.php";

// 1. Fetch Suppliers
$suppliers = $pdo->query("SELECT supplier_id, name FROM suppliers WHERE status = 1")->fetchAll();

// 2. Fetch Products
$products = $pdo->query("SELECT product_id, name FROM products")->fetchAll();

// 3. Fetch Low Stock Items (Using ALIAS for price to avoid 'Column Not Found' error)
try {
    $lowStockQuery = $pdo->query("
        SELECT 
            p.product_id, 
            p.name, 
            p.stock, 
            p.min_stock_level, 
            p.price AS cost_price, 
            p.preferred_supplier_id
        FROM products p 
        WHERE p.stock <= p.min_stock_level
    ");
    $lowStockItems = $lowStockQuery->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // If there's still a column mismatch, this prevents the whole page from crashing
    $lowStockItems = [];
    error_log("Stock Query Error: " . $e->getMessage());
}

echo "<script>const lowStockData = " . json_encode($lowStockItems) . ";</script>";


?>

<?php
// 1. Count how many items are currently low on stock
$lowStockCount = $pdo->query("SELECT COUNT(*) FROM products WHERE stock <= min_stock_level")->fetchColumn();

// 2. Fetch the top 5 most urgent items (those with the biggest gap between stock and min_level)
$urgentItems = $pdo->query("
    SELECT name, stock, min_stock_level, (min_stock_level - stock) as shortage
    FROM products 
    WHERE stock <= min_stock_level 
    ORDER BY shortage DESC 
    LIMIT 5
")->fetchAll();
?>


<main class="p-6 lg:p-12">

<div class="bg-white rounded-[3rem] p-10 border border-slate-100 shadow-sm">
    <div class="flex justify-between items-start mb-8">
        <div>
            <h4 class="text-lg font-black text-slate-900 tracking-tight">Inventory Alerts</h4>
            <p class="text-[10px] font-bold text-red-500 uppercase tracking-widest">Action Required</p>
        </div>
        <span class="bg-red-100 text-red-600 px-4 py-2 rounded-2xl font-black text-xs">
            <?= $lowStockCount ?> Items Low
        </span>
    </div>

    <div class="space-y-4">
        <?php if ($lowStockCount > 0): ?>
            <?php foreach($urgentItems as $item): ?>
                <div class="flex justify-between items-center p-4 bg-red-50/50 rounded-2xl border border-red-100/50">
                    <div>
                        <p class="text-xs font-black text-slate-900"><?= htmlspecialchars($item['name']) ?></p>
                        <p class="text-[9px] font-bold text-slate-400 uppercase">Current Stock: <?= $item['stock'] ?></p>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] font-black text-red-600 uppercase">Shortage</p>
                        <p class="text-sm font-black text-slate-900"><?= $item['shortage'] ?> Units</p>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <a href="Purchases/Purchase Order.php" class="block w-full text-center mt-6 bg-slate-900 text-white py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-orange-600 transition-all">
                Generate Restock Orders
            </a>
        <?php else: ?>
            <div class="text-center py-10">
                <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-check"></i>
                </div>
                <p class="text-xs font-bold text-slate-400 uppercase">All stock levels are healthy</p>
            </div>
        <?php endif; ?>
    </div>
</div>
  <script>
    const supplierSelect = document.querySelector('select[name="supplier_id"]');
    const rowContainer = document.getElementById('poRows');

    // --- FEATURE: AUTO-FILL LOW STOCK ---
    supplierSelect.addEventListener('change', function() {
        const selectedSupplier = this.value;
        if(!selectedSupplier) return;

        // Filter items that belong to THIS supplier and are LOW in stock
        const relevantItems = lowStockData.filter(item => item.preferred_supplier_id == selectedSupplier);

        if(relevantItems.length > 0) {
            rowContainer.innerHTML = ''; // Clear the initial empty row
            
            relevantItems.forEach(item => {
                addSmartRow(item);
            });
            calculateTotals();
            
            // Notification (using your existing notification system)
            if(typeof notify === 'function') {
                notify(`Added ${relevantItems.length} low-stock items automatically`, 'warning');
            }
        }
    });

    function addSmartRow(item = null) {
        const rowTotal = item ? (item.min_stock_level * 2 * item.cost_price).toFixed(2) : '0.00';
        const rowHtml = `
            <tr class="item-row border-b ${item ? 'bg-orange-50/50 border-orange-100' : 'border-slate-50'}">
                <td class="px-8 py-4">
                    <input list="products_list" name="product_name[]" value="${item ? item.name : ''}" required 
                           class="w-full p-2 font-bold outline-none bg-transparent" placeholder="Search product...">
                    <input type="hidden" name="product_id[]" value="${item ? item.product_id : ''}">
                    ${item ? `<p class="text-[9px] text-red-500 font-black uppercase mt-1">Stock: ${item.stock} / Min: ${item.min_stock_level}</p>` : ''}
                </td>
                <td class="px-4 py-4">
                    <input type="number" name="qty[]" class="qty-input w-20 mx-auto block text-center font-bold" 
                           value="${item ? (item.min_stock_level * 2) : 1}" min="1">
                </td>
                <td class="px-4 py-4">
                    <input type="number" name="rate[]" class="rate-input w-24 mx-auto block text-center font-bold" 
                           value="${item ? item.cost_price : 0}" step="0.01">
                </td>
                <td class="px-8 py-4 text-right font-black text-slate-900">
                    ₹<span class="row-total">${rowTotal}</span>
                </td>
                <td class="px-4 py-4 text-center">
                    <button type="button" class="text-slate-300 hover:text-red-500 remove-row"><i class="fas fa-trash"></i></button>
                </td>
            </tr>`;
        rowContainer.insertAdjacentHTML('beforeend', rowHtml);
    }

    // Manual "Add Item" Button
    document.getElementById('addItem').addEventListener('click', () => addSmartRow());

    // Remove Row logic
    document.addEventListener('click', e => {
        if(e.target.closest('.remove-row') && document.querySelectorAll('.item-row').length > 1) {
            e.target.closest('.item-row').remove();
            calculateTotals();
        }
    });

    // Math and ID Capture
    document.addEventListener('input', e => {
        if (e.target.getAttribute('list') === 'products_list') {
            const list = document.getElementById('products_list');
            const hidden = e.target.nextElementSibling;
            const opt = Array.from(list.options).find(o => o.value === e.target.value);
            hidden.value = opt ? opt.getAttribute('data-id') : "";
        }
        if (e.target.classList.contains('qty-input') || e.target.classList.contains('rate-input')) {
            calculateTotals();
        }
    });

    function calculateTotals() {
        let grand = 0;
        document.querySelectorAll('.item-row').forEach(row => {
            const q = parseFloat(row.querySelector('.qty-input').value) || 0;
            const r = parseFloat(row.querySelector('.rate-input').value) || 0;
            const total = q * r;
            row.querySelector('.row-total').innerText = total.toFixed(2);
            grand += total;
        });
        document.getElementById('grandTotal').innerText = grand.toLocaleString('en-IN', {minimumFractionDigits: 2});
        document.getElementById('total_amount_input').value = grand;
    }
</script>
    <div class="mb-10">
        <h2 class="text-3xl font-black tracking-tighter text-slate-900">Purchase Order<span class="text-orange-600">.</span></h2>
        <p class="text-slate-400 text-sm font-medium">Create a formal request for goods from your suppliers.</p>
    </div>

    <form action="process_po.php" method="POST" class="space-y-8">
        <div class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Supplier</label>
                <select name="supplier_id" required class="w-full bg-slate-50 p-4 rounded-2xl font-bold outline-none border-2 border-transparent focus:border-orange-500 appearance-none">
                    <option value="">Select Vendor</option>
                    <?php foreach($suppliers as $s): ?>
                        <option value="<?= $s['supplier_id'] ?>"><?= $s['name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="text-[10px] font-black uppercase text-slate-400 ml-2">PO Number</label>
                <input type="text" name="po_number" readonly value="PO-<?= time() ?>" class="w-full bg-slate-100 p-4 rounded-2xl font-bold text-slate-500 outline-none cursor-not-allowed">
            </div>
            <div>
                <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Expected Delivery</label>
                <input type="date" name="expected_delivery" required class="w-full bg-slate-50 p-4 rounded-2xl font-bold outline-none border-2 border-transparent focus:border-orange-500">
            </div>
        </div>

        <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-slate-50 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                    <tr>
                        <th class="px-8 py-4">Item Description</th>
                        <th class="px-4 py-4 text-center">Qty Required</th>
                        <th class="px-4 py-4 text-center">Estimated Rate (₹)</th>
                        <th class="px-8 py-4 text-right">Subtotal (₹)</th>
                        <th class="px-4 py-4"></th>
                    </tr>
                </thead>
                <tbody id="poRows">
                    <tr class="item-row border-b border-slate-50">
                        <td class="px-8 py-4">
                            <input list="products_list" name="product_name[]" required class="w-full p-2 font-bold outline-none" placeholder="Search product...">
                            <input type="hidden" name="product_id[]">
                        </td>
                        <td class="px-4 py-4">
                            <input type="number" name="qty[]" class="qty-input w-20 mx-auto block text-center font-bold" value="1" min="1">
                        </td>
                        <td class="px-4 py-4">
                            <input type="number" name="rate[]" class="rate-input w-24 mx-auto block text-center font-bold" value="0" step="0.01">
                        </td>
                        <td class="px-8 py-4 text-right font-black text-slate-900">
                            ₹<span class="row-total">0.00</span>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <button type="button" class="text-slate-300 hover:text-red-500 remove-row"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                </tbody>
            </table>
            
            <div class="p-6 bg-slate-50/50 flex justify-between items-center border-t border-slate-100">
                <button type="button" id="addItem" class="text-orange-600 font-black uppercase text-[10px] tracking-widest hover:text-slate-900 transition-all">
                    + Add Another Item
                </button>
                <div class="text-right">
                    <p class="text-[10px] font-black text-slate-400 uppercase">Estimated Total</p>
                    <h3 class="text-3xl font-black text-slate-900">₹<span id="grandTotal">0.00</span></h3>
                    <input type="hidden" name="total_amount" id="total_amount_input">
                </div>
            </div>
        </div>

        <button type="submit" class="w-full bg-slate-900 text-white py-6 rounded-[2.5rem] font-black uppercase tracking-[0.2em] shadow-xl hover:bg-orange-600 transition-all">
            Issue Purchase Order
        </button>
    </form>

    <datalist id="products_list">
        <?php foreach($products as $p): ?>
            <option data-id="<?= $p['product_id'] ?>" value="<?= $p['name'] ?>"></option>
        <?php endforeach; ?>
    </datalist>
</main>

<script>
    // 1. Add Row
    document.getElementById('addItem').addEventListener('click', () => {
        const row = document.querySelector('.item-row').cloneNode(true);
        row.querySelectorAll('input').forEach(i => i.value = i.classList.contains('qty-input') ? 1 : (i.classList.contains('rate-input') ? 0 : ''));
        row.querySelector('.row-total').innerText = '0.00';
        document.getElementById('poRows').appendChild(row);
    });

    // 2. Remove Row
    document.addEventListener('click', e => {
        if(e.target.closest('.remove-row') && document.querySelectorAll('.item-row').length > 1) {
            e.target.closest('.item-row').remove();
            calculateTotals();
        }
    });

    // 3. ID Capture for Datalist
    document.addEventListener('input', e => {
        if (e.target.getAttribute('list') === 'products_list') {
            const list = document.getElementById('products_list');
            const hidden = e.target.nextElementSibling;
            const opt = Array.from(list.options).find(o => o.value === e.target.value);
            hidden.value = opt ? opt.getAttribute('data-id') : "";
        }
        if (e.target.classList.contains('qty-input') || e.target.classList.contains('rate-input')) {
            calculateTotals();
        }
    });

    function calculateTotals() {
        let grand = 0;
        document.querySelectorAll('.item-row').forEach(row => {
            const q = parseFloat(row.querySelector('.qty-input').value) || 0;
            const r = parseFloat(row.querySelector('.rate-input').value) || 0;
            const total = q * r;
            row.querySelector('.row-total').innerText = total.toFixed(2);
            grand += total;
        });
        document.getElementById('grandTotal').innerText = grand.toLocaleString('en-IN', {minimumFractionDigits: 2});
        document.getElementById('total_amount_input').value = grand;
    }
</script>