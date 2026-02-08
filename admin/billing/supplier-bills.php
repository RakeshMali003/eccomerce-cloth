<?php
$base_path = $_SERVER['DOCUMENT_ROOT'] . '/ecommerce-website/';
require_once $base_path . 'config/database.php';

include $base_path . 'includes/admin-header.php';
include $base_path . 'includes/sidebar.php';
include $base_path . 'includes/notifications.php';

// Fetch Suppliers for Dropdown
$suppliers = $pdo->query("SELECT supplier_id, name FROM suppliers WHERE status = 1")->fetchAll();
// Fetch Products for Repeater
$products = $pdo->query("SELECT product_id, name FROM products")->fetchAll();

$po_data = null;
$po_items = [];

if (isset($_GET['convert_po'])) {
    $po_id = (int) $_GET['convert_po'];
    $stmt = $pdo->prepare("SELECT * FROM purchase_orders WHERE po_id = ?");
    $stmt->execute([$po_id]);
    $po_data = $stmt->fetch();

    $stmt = $pdo->prepare("SELECT pi.*, p.name FROM purchase_items pi JOIN products p ON pi.product_id = p.product_id WHERE pi.po_id = ?");
    $stmt->execute([$po_id]);
    $po_items = $stmt->fetchAll();
}
?>

<main class="p-6 lg:p-12 bg-[#f8fafc] min-h-screen">
    <div class="mb-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
        <div>
            <h2 class="text-3xl font-black tracking-tighter text-slate-900">Procurement: Supplier Bills<span
                    class="text-orange-600">.</span></h2>
            <p class="text-slate-400 text-sm font-medium">Log stock arrivals and generate payable liabilities.</p>
        </div>
        <div class="flex gap-3">
            <a href="../billing/supplier-dues.php"
                class="bg-white border border-slate-200 px-6 py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-50 transition-all shadow-sm">
                <i class="fas fa-hand-holding-dollar mr-2"></i> View Dues
            </a>
        </div>
    </div>

    <form action="../Purchases/process_purchase.php" method="POST" class="space-y-10">
        <input type="hidden" name="po_id" value="<?= $_GET['convert_po'] ?? '' ?>">

        <!-- Bill Header -->
        <div
            class="bg-white p-10 rounded-[3rem] border border-slate-100 shadow-sm grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="space-y-2">
                <label class="text-[10px] font-black uppercase text-slate-400 ml-4">Supplier Partner</label>
                <div class="relative">
                    <i class="fas fa-store absolute left-5 top-1/2 -translate-y-1/2 text-slate-300"></i>
                    <select name="supplier_id" required
                        class="w-full bg-slate-50 pl-14 pr-6 py-5 rounded-[2rem] text-sm font-black outline-none border-2 border-transparent focus:border-orange-500 transition-all appearance-none">
                        <option value="">Select Vendor</option>
                        <?php foreach ($suppliers as $s): ?>
                            <option value="<?= $s['supplier_id'] ?>" <?= ($po_data && $po_data['supplier_id'] == $s['supplier_id']) ? 'selected' : '' ?>>
                                <?= $s['name'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="space-y-2">
                <label class="text-[10px] font-black uppercase text-slate-400 ml-4">Invoice / Bill #</label>
                <div class="relative">
                    <i class="fas fa-file-invoice absolute left-5 top-1/2 -translate-y-1/2 text-slate-300"></i>
                    <input type="text" name="bill_number" required placeholder="Ex: VND-2024-001"
                        class="w-full bg-slate-50 pl-14 pr-6 py-5 rounded-[2rem] text-sm font-black outline-none border-2 border-transparent focus:border-orange-500 transition-all">
                </div>
            </div>
            <div class="space-y-2">
                <label class="text-[10px] font-black uppercase text-slate-400 ml-4">Bill Date</label>
                <input type="date" name="bill_date" value="<?= date('Y-m-d') ?>"
                    class="w-full bg-slate-50 px-8 py-5 rounded-[2rem] text-sm font-black outline-none border-2 border-transparent focus:border-orange-500 transition-all">
            </div>
            <div class="space-y-2">
                <label class="text-[10px] font-black uppercase text-slate-400 ml-4">Due Date</label>
                <input type="date" name="due_date" required
                    class="w-full bg-white border-2 border-orange-100 px-8 py-5 rounded-[2rem] text-sm font-black outline-none focus:border-orange-500 transition-all">
            </div>
        </div>

        <!-- Line Items -->
        <div class="bg-white rounded-[3rem] border border-slate-100 shadow-sm overflow-hidden">
            <table class="w-full text-left" id="purchaseTable">
                <thead class="bg-slate-50 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                    <tr>
                        <th class="px-10 py-6">Stock Item</th>
                        <th class="px-6 py-6 text-center">Unit Qty</th>
                        <th class="px-6 py-6 text-center">Cost Price (₹)</th>
                        <th class="px-6 py-6 text-right">Extended Total</th>
                        <th class="px-10 py-6 text-center">Action</th>
                    </tr>
                </thead>
                <tbody id="rowContainer" class="divide-y divide-slate-50">
                    <?php if (!empty($po_items)): ?>
                        <?php foreach ($po_items as $item): ?>
                            <tr class="item-row hover:bg-slate-50/50 transition-all">
                                <td class="px-10 py-6">
                                    <input list="products_list" name="product_name[]" value="<?= $item['name'] ?>" required
                                        class="w-full bg-transparent p-2 text-sm font-black outline-none"
                                        placeholder="Search product...">
                                </td>
                                <td class="px-6 py-6">
                                    <input type="number" name="qty[]"
                                        class="qty w-24 mx-auto block bg-slate-50 py-3 rounded-xl text-center text-sm font-black outline-none border-2 border-transparent focus:border-orange-500"
                                        value="<?= $item['quantity'] ?>" min="1">
                                </td>
                                <td class="px-6 py-6">
                                    <input type="number" name="cost[]"
                                        class="cost w-32 mx-auto block bg-slate-50 py-3 rounded-xl text-center text-sm font-black outline-none border-2 border-transparent focus:border-orange-500"
                                        value="<?= $item['cost_price'] ?>" step="0.01">
                                </td>
                                <td class="px-6 py-6 text-right font-black text-slate-900">₹<span class="row-total">
                                        <?= number_format($item['total'], 2) ?>
                                    </span></td>
                                <td class="px-10 py-6 text-center">
                                    <button type="button"
                                        class="w-10 h-10 rounded-xl bg-red-50 text-red-300 hover:text-red-500 transition-all remove-row"><i
                                            class="fas fa-times text-xs"></i></button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr class="item-row hover:bg-slate-50/50 transition-all">
                            <td class="px-10 py-6">
                                <input list="products_list" name="product_name[]" required
                                    class="w-full bg-transparent p-2 text-sm font-black outline-none"
                                    placeholder="Search product...">
                            </td>
                            <td class="px-6 py-6">
                                <input type="number" name="qty[]"
                                    class="qty w-24 mx-auto block bg-slate-50 py-3 rounded-xl text-center text-sm font-black outline-none border-2 border-transparent focus:border-orange-500"
                                    value="1" min="1">
                            </td>
                            <td class="px-6 py-6">
                                <input type="number" name="cost[]"
                                    class="cost w-32 mx-auto block bg-slate-50 py-3 rounded-xl text-center text-sm font-black outline-none border-2 border-transparent focus:border-orange-500"
                                    value="0" step="0.01">
                            </td>
                            <td class="px-6 py-6 text-right font-black text-slate-900">₹<span class="row-total">0.00</span>
                            </td>
                            <td class="px-10 py-6 text-center">
                                <button type="button"
                                    class="w-10 h-10 rounded-xl bg-red-50 text-red-300 hover:text-red-500 transition-all remove-row"><i
                                        class="fas fa-times text-xs"></i></button>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <datalist id="products_list">
                <?php foreach ($products as $p): ?>
                    <option value="<?= htmlspecialchars($p['name']) ?>">
                        <?= $p['product_id'] ?>
                    </option>
                <?php endforeach; ?>
            </datalist>

            <div class="p-10 bg-slate-50/30 flex justify-between items-center border-t border-slate-50">
                <button type="button" id="addRow"
                    class="bg-white border border-slate-200 px-8 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-900 hover:text-white transition-all shadow-sm">
                    <i class="fas fa-plus mr-2"></i> Append Item
                </button>
                <div class="text-right">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Financial Commitment
                    </p>
                    <h3 class="text-4xl font-black text-slate-900 tracking-tighter italic">₹<span
                            id="grandTotal">0.00</span></h3>
                    <input type="hidden" name="total_amount" id="total_amount_input">
                </div>
            </div>
        </div>

        <button type="submit"
            class="w-full bg-slate-900 text-white py-8 rounded-[3rem] font-black uppercase tracking-[0.3em] shadow-2xl shadow-slate-200 hover:bg-orange-600 transition-all group">
            Publish Bill & Finalize Stock Entry <i
                class="fas fa-arrow-right ml-4 group-hover:translate-x-2 transition-transform"></i>
        </button>
    </form>
</main>

<script>
    document.getElementById('addRow').addEventListener('click', function () {
        const container = document.getElementById('rowContainer');
        const firstRow = container.querySelector('.item-row');
        const newRow = firstRow.cloneNode(true);
        newRow.querySelector('input[list]').value = "";
        newRow.querySelector('.qty').value = 1;
        newRow.querySelector('.cost').value = 0;
        newRow.querySelector('.row-total').innerText = "0.00";
        container.appendChild(newRow);
    });

    document.addEventListener('input', function (e) {
        if (e.target.classList.contains('qty') || e.target.classList.contains('cost')) {
            calculateTotals();
        }
    });

    document.addEventListener('click', function (e) {
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
            row.querySelector('.row-total').innerText = total.toLocaleString('en-IN', { minimumFractionDigits: 2 });
            grandTotal += total;
        });
        document.getElementById('grandTotal').innerText = grandTotal.toLocaleString('en-IN', { minimumFractionDigits: 2 });
        document.getElementById('total_amount_input').value = grandTotal;
    }

    // Initial calculation
    calculateTotals();
</script>
<?php include $base_path . 'includes/admin-footer.php'; ?>