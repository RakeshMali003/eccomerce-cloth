<?php

$base_path = $_SERVER['DOCUMENT_ROOT'] . '/ecommerce-website/';


    include $base_path . 'includes/sidebar.php'; 
    
    include $base_path . 'includes/notifications.php'; 

require_once "../../config/database.php";

// Fetch Suppliers for Dropdown
$suppliers = $pdo->query("SELECT supplier_id, name FROM suppliers WHERE status = 1")->fetchAll();
// Fetch Products for Repeater
$products = $pdo->query("SELECT product_id, name FROM products")->fetchAll();

// Add this logic at the top of Purchase Bill.php
$po_data = null;
$po_items = [];

if (isset($_GET['convert_po'])) {
    $po_id = (int)$_GET['convert_po'];
    
    // Fetch PO Details
    $stmt = $pdo->prepare("SELECT * FROM purchase_orders WHERE po_id = ?");
    $stmt->execute([$po_id]);
    $po_data = $stmt->fetch();

    // Fetch PO Items
    $stmt = $pdo->prepare("SELECT pi.*, p.name FROM purchase_items pi JOIN products p ON pi.product_id = p.product_id WHERE pi.po_id = ?");
    $stmt->execute([$po_id]);
    $po_items = $stmt->fetchAll();
}
?>

<main class="p-6 lg:p-12">
    <div class="mb-10">
        <h2 class="text-3xl font-black tracking-tighter">Purchase Entry<span class="text-orange-600">.</span></h2>
        <p class="text-slate-400 text-sm">Log new stock arrivals and generate supplier bills.</p>
    </div>

    <form action="process_purchase.php" method="POST" class="space-y-8">
        <div class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm grid grid-cols-1 md:grid-cols-4 gap-6">
            <div>
                <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Supplier</label>
                <select name="supplier_id" required class="w-full bg-slate-50 p-4 rounded-2xl font-bold outline-none border-2 border-transparent focus:border-orange-500">
                    <option value="">Select Vendor</option>
                    <?php foreach($suppliers as $s): ?>
                        <option value="<?= $s['supplier_id'] ?>"><?= $s['name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Bill Number</label>
                <input type="text" name="bill_number" required placeholder="Ex: INV-990" class="w-full bg-slate-50 p-4 rounded-2xl font-bold outline-none">
            </div>
            <div>
                <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Bill Date</label>
                <input type="date" name="bill_date" value="<?= date('Y-m-d') ?>" class="w-full bg-slate-50 p-4 rounded-2xl font-bold outline-none">
            </div>
            <div>
                <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Due Date</label>
                <input type="date" name="due_date" required class="w-full bg-slate-50 p-4 rounded-2xl font-bold outline-none border-2 border-orange-100">
            </div>
        </div>

        <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden">
            <table class="w-full text-left" id="purchaseTable">
                <thead class="bg-slate-50 text-[10px] font-black text-slate-400 uppercase">
                    <tr>
                        <th class="px-8 py-4">Product Item</th>
                        <th class="px-4 py-4 text-center">Quantity</th>
                        <th class="px-4 py-4 text-center">Unit Cost (₹)</th>
                        <th class="px-8 py-4 text-right">Total (₹)</th>
                        <th class="px-4 py-4"></th>
                    </tr>
                </thead>
                <tbody id="rowContainer">
                    <tr class="item-row border-b border-slate-50">
                        <td class="px-8 py-4">
                           <input 
    list="products_list"
    name="product_name[]" 
    required 
    class="w-full p-2 font-bold outline-none"
    placeholder="Select product">

<datalist id="products_list">
    <?php foreach($products as $p): ?>
        <option 
            data-id="<?= $p['product_id'] ?>" 
            value="<?= $p['name'] ?>">
        </option>
    <?php endforeach; ?>
</datalist>
<input type="hidden" name="po_id" value="<?= isset($_GET['convert_po']) ? $_GET['convert_po'] : '' ?>">

                        </td>
                        <td class="px-4 py-4">
                            <input type="number" name="qty[]" class="qty w-20 mx-auto block text-center font-bold" value="1" min="1">
                        </td>
                        <td class="px-4 py-4">
                            <input type="number" name="cost[]" class="cost w-24 mx-auto block text-center font-bold" value="0" step="0.01">
                        </td>
                        <td class="px-8 py-4 text-right font-black text-slate-900">
                            <span class="row-total">0.00</span>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <button type="button" class="text-red-300 hover:text-red-500 remove-row"><i class="fas fa-times"></i></button>
                        </td>
                    </tr>
                </tbody>
            </table>
            
            <div class="p-6 bg-slate-50/50 flex justify-between items-center">
                <button type="button" id="addRow" class="bg-white border border-slate-200 px-6 py-2 rounded-xl text-[10px] font-black uppercase hover:bg-slate-900 hover:text-white transition-all">
                    + Add Item
                </button>
                <div class="text-right">
                    <p class="text-[10px] font-black text-slate-400 uppercase">Grand Total</p>
                    <h3 class="text-3xl font-black text-slate-900">₹<span id="grandTotal">0.00</span></h3>
                    <input type="hidden" name="total_amount" id="total_amount_input">
                </div>
            </div>
        </div>

        <button type="submit" class="w-full bg-orange-600 text-white py-6 rounded-[2rem] font-black uppercase tracking-[0.2em] shadow-xl shadow-orange-100 hover:bg-slate-900 transition-all">
            Confirm & Increase Stock
        </button>
    </form>
</main>

<script>
document.getElementById('addRow').addEventListener('click', function() {
    const container = document.getElementById('rowContainer');
    const firstRow = container.querySelector('.item-row');
    const newRow = firstRow.cloneNode(true);
    newRow.querySelector('.qty').value = 1;
    newRow.querySelector('.cost').value = 0;
    newRow.querySelector('.row-total').innerText = "0.00";
    container.appendChild(newRow);
});

document.addEventListener('input', function(e) {
    if (e.target.classList.contains('qty') || e.target.classList.contains('cost')) {
        calculateTotals();
    }
});

document.addEventListener('click', function(e) {
    if (e.target.closest('.remove-row')) {
        const rows = document.querySelectorAll('.item-row');
        if (rows.length > 1) e.target.closest('.item-row').remove();
        calculateTotals();
    }
});

function calculateTotals() {
    let grandTotal = 0;
    document.querySelectorAll('.item-row').forEach(row => {
        const qty = parseFloat(row.querySelector('.qty').value) || 0;
        const cost = parseFloat(row.querySelector('.cost').value) || 0;
        const total = qty * cost;
        row.querySelector('.row-total').innerText = total.toFixed(2);
        grandTotal += total;
    });
    document.getElementById('grandTotal').innerText = grandTotal.toLocaleString('en-IN', {minimumFractionDigits: 2});
    document.getElementById('total_amount_input').value = grandTotal;
}
</script>