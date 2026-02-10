<?php
$base_path = __DIR__ . '/../../';
require_once $base_path . 'config/database.php';

include $base_path . 'includes/admin-header.php';
include $base_path . 'includes/sidebar.php';
include $base_path . 'includes/notifications.php';

// Filters
$status_filter = $_GET['status'] ?? '';
$supplier_filter = $_GET['supplier'] ?? '';
$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';
$sort_by = $_GET['sort'] ?? 'bill_date';
$sort_order = $_GET['order'] ?? 'DESC';

// Build Query
$where = ["1=1"];
$params = [];

if ($status_filter) {
    $where[] = "b.payment_status = ?";
    $params[] = $status_filter;
}

if ($supplier_filter) {
    $where[] = "b.supplier_id = ?";
    $params[] = $supplier_filter;
}

if ($date_from) {
    $where[] = "b.bill_date >= ?";
    $params[] = $date_from;
}

if ($date_to) {
    $where[] = "b.bill_date <= ?";
    $params[] = $date_to;
}

$where_sql = implode(" AND ", $where);
$allowed_sorts = ['bill_date', 'due_date', 'total_amount', 'balance_amount', 'supplier_name'];
$sort_by = in_array($sort_by, $allowed_sorts) ? $sort_by : 'bill_date';
$sort_order = strtoupper($sort_order) === 'ASC' ? 'ASC' : 'DESC';

// Fetch Bills
$sql = "SELECT b.*, s.name as supplier_name 
        FROM supplier_bills b 
        JOIN suppliers s ON b.supplier_id = s.supplier_id 
        WHERE $where_sql 
        ORDER BY " . ($sort_by === 'supplier_name' ? 's.name' : "b.$sort_by") . " $sort_order";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$bills = $stmt->fetchAll();

// KPIs
$total_bills = count($bills);
$total_amount = array_sum(array_column($bills, 'total_amount'));
$total_paid = $total_amount - array_sum(array_column($bills, 'balance_amount'));
$total_pending = array_sum(array_column($bills, 'balance_amount'));

// Fetch suppliers for dropdown
$suppliers = $pdo->query("SELECT supplier_id, name FROM suppliers WHERE status = 1 ORDER BY name")->fetchAll();
?>

<main class="p-6 lg:p-12 bg-[#f8fafc] min-h-screen">
    <div class="mb-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
        <div>
            <h2 class="text-3xl font-black tracking-tighter text-slate-900">Bills Ledger<span
                    class="text-orange-600">.</span></h2>
            <p class="text-slate-400 text-sm font-medium">Complete overview of all supplier invoices</p>
        </div>
        <div class="flex gap-3">
            <a href="supplier-bills.php"
                class="bg-slate-900 text-white px-6 py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-orange-600 transition-all shadow-lg shadow-slate-200">
                <i class="fas fa-plus mr-2"></i> New Bill
            </a>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
        <div class="bg-white p-8 rounded-[3rem] border border-slate-100 shadow-sm">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Bills</p>
            <h3 class="text-3xl font-black text-slate-900">
                <?= $total_bills ?>
            </h3>
        </div>
        <div class="bg-white p-8 rounded-[3rem] border border-slate-100 shadow-sm">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Amount</p>
            <h3 class="text-2xl font-black text-slate-900">₹
                <?= number_format($total_amount, 2) ?>
            </h3>
        </div>
        <div class="bg-emerald-50 p-8 rounded-[3rem] border border-emerald-100 shadow-sm">
            <p class="text-[10px] font-black text-emerald-600 uppercase tracking-widest mb-1">Paid</p>
            <h3 class="text-2xl font-black text-emerald-600">₹
                <?= number_format($total_paid, 2) ?>
            </h3>
        </div>
        <div class="bg-red-50 p-8 rounded-[3rem] border border-red-100 shadow-sm">
            <p class="text-[10px] font-black text-red-400 uppercase tracking-widest mb-1">Pending</p>
            <h3 class="text-2xl font-black text-red-600">₹
                <?= number_format($total_pending, 2) ?>
            </h3>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white p-8 rounded-[3rem] border border-slate-100 shadow-sm mb-8">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-6 gap-4 items-end">
            <div>
                <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Status</label>
                <select name="status" class="w-full bg-slate-50 p-4 rounded-2xl font-bold outline-none">
                    <option value="">All Status</option>
                    <option value="Paid" <?= $status_filter === 'Paid' ? 'selected' : '' ?>>Paid</option>
                    <option value="Pending" <?= $status_filter === 'Pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="Partial" <?= $status_filter === 'Partial' ? 'selected' : '' ?>>Partially Paid</option>
                </select>
            </div>
            <div>
                <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Vendor</label>
                <select name="supplier" class="w-full bg-slate-50 p-4 rounded-2xl font-bold outline-none">
                    <option value="">All Vendors</option>
                    <?php foreach ($suppliers as $s): ?>
                        <option value="<?= $s['supplier_id'] ?>" <?= $supplier_filter == $s['supplier_id'] ? 'selected' : '' ?>
                            >
                            <?= htmlspecialchars($s['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="text-[10px] font-black uppercase text-slate-400 ml-2">From Date</label>
                <input type="date" name="date_from" value="<?= $date_from ?>"
                    class="w-full bg-slate-50 p-4 rounded-2xl font-bold outline-none">
            </div>
            <div>
                <label class="text-[10px] font-black uppercase text-slate-400 ml-2">To Date</label>
                <input type="date" name="date_to" value="<?= $date_to ?>"
                    class="w-full bg-slate-50 p-4 rounded-2xl font-bold outline-none">
            </div>
            <div>
                <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Sort By</label>
                <select name="sort" class="w-full bg-slate-50 p-4 rounded-2xl font-bold outline-none">
                    <option value="bill_date" <?= $sort_by === 'bill_date' ? 'selected' : '' ?>>Bill Date</option>
                    <option value="due_date" <?= $sort_by === 'due_date' ? 'selected' : '' ?>>Due Date</option>
                    <option value="total_amount" <?= $sort_by === 'total_amount' ? 'selected' : '' ?>>Amount</option>
                    <option value="supplier_name" <?= $sort_by === 'supplier_name' ? 'selected' : '' ?>>Vendor Name
                    </option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit"
                    class="flex-1 bg-slate-900 text-white py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-orange-600 transition-all">
                    <i class="fas fa-filter mr-2"></i>Apply
                </button>
                <a href="all-bills.php"
                    class="w-12 h-12 flex items-center justify-center bg-slate-100 text-slate-400 rounded-2xl hover:bg-red-50 hover:text-red-500 transition-all">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Bills Table -->
    <div class="bg-white rounded-[3rem] border border-slate-100 shadow-sm overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-slate-50 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                <tr>
                    <th class="px-8 py-6">Bill #</th>
                    <th class="px-6 py-6">Vendor</th>
                    <th class="px-6 py-6 text-center">Bill Date</th>
                    <th class="px-6 py-6 text-center">Due Date</th>
                    <th class="px-6 py-6 text-right">Total</th>
                    <th class="px-6 py-6 text-right">Balance</th>
                    <th class="px-6 py-6 text-center">Status</th>
                    <th class="px-8 py-6 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php foreach ($bills as $b):
                    $is_overdue = strtotime($b['due_date']) < time() && strtoupper($b['payment_status']) !== 'PAID';
                    $status = strtoupper($b['payment_status']);
                    $status_class = match ($status) {
                        'PAID' => 'bg-emerald-100 text-emerald-600',
                        'PARTIAL', 'PARTIALLY_PAID' => 'bg-blue-100 text-blue-600',
                        default => 'bg-amber-100 text-amber-700'
                    };
                    ?>
                    <tr class="hover:bg-slate-50 transition-all group">
                        <td class="px-8 py-5">
                            <p class="text-sm font-black text-slate-900">#
                                <?= $b['bill_number'] ?>
                            </p>
                        </td>
                        <td class="px-6 py-5">
                            <p class="text-xs font-bold text-slate-700">
                                <?= htmlspecialchars($b['supplier_name']) ?>
                            </p>
                        </td>
                        <td class="px-6 py-5 text-center text-xs font-bold text-slate-500">
                            <?= date('d M, Y', strtotime($b['bill_date'])) ?>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <span class="text-xs font-bold <?= $is_overdue ? 'text-red-600' : 'text-slate-500' ?>">
                                <?= date('d M, Y', strtotime($b['due_date'])) ?>
                                <?php if ($is_overdue): ?>
                                    <i class="fas fa-exclamation-triangle ml-1"></i>
                                <?php endif; ?>
                            </span>
                        </td>
                        <td class="px-6 py-5 text-right text-sm font-black text-slate-900">
                            ₹
                            <?= number_format($b['total_amount'], 2) ?>
                        </td>
                        <td
                            class="px-6 py-5 text-right text-sm font-black <?= $b['balance_amount'] > 0 ? 'text-red-600' : 'text-emerald-600' ?>">
                            ₹
                            <?= number_format($b['balance_amount'], 2) ?>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <span class="px-4 py-1.5 rounded-xl text-[9px] font-black uppercase <?= $status_class ?>">
                                <?= str_replace('_', ' ', $status) ?>
                            </span>
                        </td>
                        <td class="px-8 py-5 text-right">
                            <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-all">
                                <?php if ($b['balance_amount'] > 0): ?>
                                    <a href="../Payments/Make Payment.php?bill_id=<?= $b['bill_id'] ?>"
                                        class="bg-emerald-600 text-white px-4 py-2 rounded-xl text-[9px] font-black uppercase hover:bg-slate-900 transition-all">
                                        Pay
                                    </a>
                                <?php endif; ?>
                                <a href="invoice-view.php?id=<?= $b['bill_id'] ?>"
                                    class="w-9 h-9 flex items-center justify-center bg-slate-100 text-slate-400 rounded-xl hover:bg-slate-900 hover:text-white transition-all">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if (empty($bills)): ?>
                    <tr>
                        <td colspan="8" class="p-20 text-center">
                            <i class="fas fa-file-invoice text-slate-100 text-6xl mb-4"></i>
                            <p class="text-sm font-black text-slate-300 uppercase tracking-widest">No bills found matching
                                your criteria</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

<?php include $base_path . 'includes/admin-footer.php'; ?>