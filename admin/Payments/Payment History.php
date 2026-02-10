<?php
$base_path = __DIR__ . '/../../';
require_once $base_path . 'config/database.php';

include $base_path . 'includes/admin-header.php';
include $base_path . 'includes/sidebar.php';
include $base_path . 'includes/notifications.php';

// --- FILTER LOGIC ---
$where = ["1=1"];
$params = [];

// 1. Filter by Date Range
$range = $_GET['range'] ?? 'all';
switch ($range) {
    case 'today':
        $where[] = "DATE(sp.payment_date) = CURRENT_DATE";
        break;
    case 'yesterday':
        $where[] = "DATE(sp.payment_date) = DATE_SUB(CURRENT_DATE, INTERVAL 1 DAY)";
        break;
    case 'week':
        $where[] = "sp.payment_date >= DATE_SUB(CURRENT_DATE, INTERVAL 7 DAY)";
        break;
    case 'month':
        $where[] = "MONTH(sp.payment_date) = MONTH(CURRENT_DATE) AND YEAR(sp.payment_date) = YEAR(CURRENT_DATE)";
        break;
    case '6months':
        $where[] = "sp.payment_date >= DATE_SUB(CURRENT_DATE, INTERVAL 6 MONTH)";
        break;
    case 'year':
        $where[] = "YEAR(sp.payment_date) = YEAR(CURRENT_DATE)";
        break;
}

// 2. Filter by Supplier
if (!empty($_GET['supplier'])) {
    $where[] = "sp.supplier_id = ?";
    $params[] = $_GET['supplier'];
}

// 3. Filter by Method
if (!empty($_GET['method'])) {
    $where[] = "sp.payment_mode = ?";
    $params[] = $_GET['method'];
}

$where_clause = implode(" AND ", $where);
$query = "SELECT sp.*, s.name as supplier_name 
          FROM supplier_payments sp 
          JOIN suppliers s ON sp.supplier_id = s.supplier_id 
          WHERE $where_clause
          ORDER BY sp.payment_date DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$payments = $stmt->fetchAll();

// Fetch Suppliers for dropdown
$suppliers = $pdo->query("SELECT supplier_id, name FROM suppliers ORDER BY name ASC")->fetchAll();
?>

<main class="p-6 lg:p-12 bg-[#f8fafc] min-h-screen">
    <div class="mb-10 flex justify-between items-start">
        <div>
            <h2 class="text-3xl font-black tracking-tighter text-slate-900">Payment Outbox<span
                    class="text-orange-600">.</span></h2>
            <p class="text-slate-400 text-sm font-medium">History of all disbursements made to vendors.</p>
        </div>
        <button onclick="window.print()"
            class="bg-white border border-slate-200 p-4 rounded-2xl hover:bg-slate-50 transition-all shadow-sm">
            <i class="fas fa-print text-slate-600"></i>
        </button>
    </div>

    <form method="GET"
        class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm mb-10 flex flex-wrap items-end gap-4">
        <div class="flex-1 min-w-[150px]">
            <label class="text-[10px] font-black uppercase text-slate-400 ml-2 mb-2 block">Timeframe</label>
            <select name="range" onchange="this.form.submit()"
                class="w-full bg-slate-50 p-3 rounded-xl text-xs font-bold outline-none border-none">
                <option value="all" <?= $range == 'all' ? 'selected' : '' ?>>All History</option>
                <option value="today" <?= $range == 'today' ? 'selected' : '' ?>>Today</option>
                <option value="yesterday" <?= $range == 'yesterday' ? 'selected' : '' ?>>Yesterday</option>
                <option value="week" <?= $range == 'week' ? 'selected' : '' ?>>Last 7 Days</option>
                <option value="month" <?= $range == 'month' ? 'selected' : '' ?>>This Month</option>
                <option value="6months" <?= $range == '6months' ? 'selected' : '' ?>>Last 6 Months</option>
                <option value="year" <?= $range == 'year' ? 'selected' : '' ?>>This Year</option>
            </select>
        </div>

        <div class="flex-1 min-w-[150px]">
            <label class="text-[10px] font-black uppercase text-slate-400 ml-2 mb-2 block">Supplier</label>
            <select name="supplier" onchange="this.form.submit()"
                class="w-full bg-slate-50 p-3 rounded-xl text-xs font-bold outline-none border-none">
                <option value="">All Suppliers</option>
                <?php foreach ($suppliers as $s): ?>
                    <option value="<?= $s['supplier_id'] ?>" <?= ($_GET['supplier'] ?? '') == $s['supplier_id'] ? 'selected' : '' ?>><?= $s['name'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="flex-1 min-w-[150px]">
            <label class="text-[10px] font-black uppercase text-slate-400 ml-2 mb-2 block">Method</label>
            <select name="method" onchange="this.form.submit()"
                class="w-full bg-slate-50 p-3 rounded-xl text-xs font-bold outline-none border-none">
                <option value="">All Methods</option>
                <option value="Online" <?= ($_GET['method'] ?? '') == 'Online' ? 'selected' : '' ?>>Online / UPI</option>
                <option value="Cash" <?= ($_GET['method'] ?? '') == 'Cash' ? 'selected' : '' ?>>Cash</option>
                <option value="Bank Transfer" <?= ($_GET['method'] ?? '') == 'Bank Transfer' ? 'selected' : '' ?>>Bank
                    Transfer</option>
                <option value="Cheque" <?= ($_GET['method'] ?? '') == 'Cheque' ? 'selected' : '' ?>>Cheque</option>
            </select>
        </div>

        <button type="button" onclick="resetFilters()"
            class="p-3 bg-red-50 text-red-500 rounded-xl text-xs font-bold hover:bg-red-500 hover:text-white transition-all border-none cursor-pointer">
            <i class="fas fa-undo"></i>
        </button>
    </form>

    <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-slate-50 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                <tr>
                    <th class="px-8 py-6">Transaction Date</th>
                    <th class="px-6 py-6">Vendor Name</th>
                    <th class="px-6 py-6">Method</th>
                    <th class="px-6 py-6">Ref ID</th>
                    <th class="px-6 py-6 text-right">Amount Paid</th>
                    <th class="px-8 py-6 text-center">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php if (empty($payments)): ?>
                    <tr>
                        <td colspan="6" class="p-20 text-center text-slate-300 font-bold uppercase text-xs italic">No
                            matching transactions found</td>
                    </tr>
                <?php else:
                    foreach ($payments as $p): ?>
                        <tr class="hover:bg-slate-50 transition-all group">
                            <td class="px-8 py-5 text-xs font-bold text-slate-500">
                                        <?= date('d M, Y', strtotime($p['payment_date'])) ?>
                            </td>
                            <td class="px-6 py-5">
                                <p class="text-xs font-black text-slate-900"><?= htmlspecialchars($p['supplier_name']) ?></p>
                            </td>
                            <td class="px-6 py-5">
                                <span
                                    class="text-[9px] font-black uppercase bg-slate-100 px-3 py-1.5 rounded-lg text-slate-500">
                                   <?= $p['payment_mode'] ?>
                                </span>
                            </td>
                            <td class="px-6 py-5 text-xs font-medium text-slate-400 italic">
                                        <?= $p['transaction_id'] ?: 'No Ref' ?>
                            </td>
                            <td class="px-6 py-5 text-right font-black text-slate-900">
                                ₹<?= number_format($p['amount'], 2) ?>
                            </td>
                            <td class="px-8 py-5">
                                <div class="flex justify-center gap-2">
                                    <button onclick="printReceipt(<?= $p['payment_id'] ?>)"
                                        class="w-9 h-9 rounded-xl border border-slate-100 flex items-center justify-center hover:bg-slate-900 hover:text-white transition-all">
                                        <i class="fas fa-print text-xs"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</main>
<script>
    function resetFilters() {
        // This clears all ?range=... &supplier=... from the URL and reloads the same page
        window.location.href = window.location.pathname;
    }

    // Optional: To make the page feel more like an app, 
    // you can auto-submit on every change
    document.querySelectorAll('select').forEach(select => {
        select.addEventListener('change', function () {
            this.form.submit();
        });
    });
</script>
<?php include $base_path . 'includes/admin-footer.php'; ?>