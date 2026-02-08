<?php
$base_path = $_SERVER['DOCUMENT_ROOT'] . '/ecommerce-website/';
require_once $base_path . 'config/database.php';

include $base_path . 'includes/admin-header.php';
include $base_path . 'includes/sidebar.php';
include $base_path . 'includes/notifications.php';

// Status style helper
function getStatusStyle($status)
{
    $status = strtolower(trim($status));
    return match ($status) {
        'pending' => 'bg-amber-100 text-amber-700',
        'confirmed' => 'bg-blue-100 text-blue-700',
        'packed' => 'bg-slate-900 text-white',
        'shipped' => 'bg-purple-100 text-purple-700',
        'delivered' => 'bg-emerald-100 text-emerald-700',
        'cancelled' => 'bg-red-100 text-red-700',
        'returned' => 'bg-orange-100 text-orange-700',
        default => 'bg-slate-100 text-slate-400',
    };
}

function getPaymentStyle($status)
{
    $status = strtolower(trim($status));
    return match ($status) {
        'paid', 'completed' => 'bg-emerald-100 text-emerald-700',
        'pending' => 'bg-amber-100 text-amber-700',
        'failed' => 'bg-red-100 text-red-700',
        'refunded' => 'bg-blue-100 text-blue-700',
        default => 'bg-slate-100 text-slate-400',
    };
}

// Filter Logic
$status_filter = $_GET['status'] ?? 'all';
$payment_filter = $_GET['payment'] ?? 'all';
$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';
$search_query = $_GET['search'] ?? '';

$where_clauses = [];
$params = [];

if ($status_filter !== 'all') {
    $where_clauses[] = "o.order_status = :status";
    $params[':status'] = $status_filter;
}

if ($payment_filter !== 'all') {
    $where_clauses[] = "o.payment_status = :payment";
    $params[':payment'] = $payment_filter;
}

if ($date_from) {
    $where_clauses[] = "DATE(o.created_at) >= :date_from";
    $params[':date_from'] = $date_from;
}

if ($date_to) {
    $where_clauses[] = "DATE(o.created_at) <= :date_to";
    $params[':date_to'] = $date_to;
}

if ($search_query !== '') {
    $where_clauses[] = "(o.order_id LIKE :search OR u.name LIKE :search OR u.email LIKE :search)";
    $params[':search'] = "%$search_query%";
}

$where_sql = !empty($where_clauses) ? "WHERE " . implode(" AND ", $where_clauses) : "";

// Fetch Orders
$sql = "SELECT o.*, u.name as customer_name, u.phone, u.email, u.city, i.invoice_id, i.invoice_number
        FROM orders o 
        JOIN users u ON o.user_id = u.user_id 
        LEFT JOIN invoices i ON o.order_id = i.order_id 
        $where_sql
        ORDER BY o.created_at DESC";

$stmt = $pdo->prepare($sql);
foreach ($params as $key => $val) {
    $stmt->bindValue($key, $val);
}
$stmt->execute();
$orders = $stmt->fetchAll();

// KPIs
$total_orders = count($orders);
$total_revenue = array_sum(array_column($orders, 'total_amount'));
$paid_orders = count(array_filter($orders, fn($o) => strtolower($o['payment_status'] ?? '') === 'paid'));
$pending_orders = count(array_filter($orders, fn($o) => strtolower($o['order_status']) === 'pending'));

// Status counts
$counts = $pdo->query("SELECT order_status, COUNT(*) as qty FROM orders GROUP BY order_status")->fetchAll(PDO::FETCH_KEY_PAIR);
$all_statuses = ['pending', 'confirmed', 'packed', 'shipped', 'delivered', 'cancelled', 'returned'];
?>

<main class="p-6 lg:p-12 bg-[#f8fafc] min-h-screen">
    <div class="mb-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
        <div>
            <h2 class="text-3xl font-black tracking-tighter text-slate-900">Billing: Order Ledger<span
                    class="text-orange-600">.</span></h2>
            <p class="text-slate-400 text-sm font-medium">Customer orders, invoices, and payment tracking</p>
        </div>
        <div class="flex gap-3">
            <button onclick="exportToCSV()"
                class="bg-white border border-slate-200 px-6 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-50 transition-all shadow-sm">
                <i class="fas fa-download mr-2"></i> Export CSV
            </button>
            <button onclick="window.print()"
                class="bg-slate-900 text-white px-6 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-orange-600 transition-all shadow-xl">
                <i class="fas fa-print mr-2"></i> Print
            </button>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
        <div class="bg-white p-8 rounded-[3rem] border border-slate-100 shadow-sm">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Orders</p>
            <h3 class="text-3xl font-black text-slate-900">
                <?= $total_orders ?>
            </h3>
        </div>
        <div class="bg-white p-8 rounded-[3rem] border border-slate-100 shadow-sm border-b-4 border-b-emerald-500">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Revenue</p>
            <h3 class="text-2xl font-black text-emerald-600">₹
                <?= number_format($total_revenue, 2) ?>
            </h3>
        </div>
        <div class="bg-emerald-50 p-8 rounded-[3rem] border border-emerald-100 shadow-sm">
            <p class="text-[10px] font-black text-emerald-600 uppercase tracking-widest mb-1">Paid Orders</p>
            <h3 class="text-3xl font-black text-emerald-600">
                <?= $paid_orders ?>
            </h3>
        </div>
        <div class="bg-amber-50 p-8 rounded-[3rem] border border-amber-100 shadow-sm">
            <p class="text-[10px] font-black text-amber-600 uppercase tracking-widest mb-1">Pending Orders</p>
            <h3 class="text-3xl font-black text-amber-600">
                <?= $pending_orders ?>
            </h3>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white p-8 rounded-[3rem] border border-slate-100 shadow-sm mb-8">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-6 gap-4 items-end">
            <div>
                <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Order Status</label>
                <select name="status" class="w-full bg-slate-50 p-4 rounded-2xl font-bold outline-none">
                    <option value="all">All Status</option>
                    <?php foreach ($all_statuses as $st): ?>
                        <option value="<?= $st ?>" <?= $status_filter === $st ? 'selected' : '' ?>>
                            <?= ucfirst($st) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Payment</label>
                <select name="payment" class="w-full bg-slate-50 p-4 rounded-2xl font-bold outline-none">
                    <option value="all">All Payments</option>
                    <option value="paid" <?= $payment_filter === 'paid' ? 'selected' : '' ?>>Paid</option>
                    <option value="pending" <?= $payment_filter === 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="failed" <?= $payment_filter === 'failed' ? 'selected' : '' ?>>Failed</option>
                    <option value="refunded" <?= $payment_filter === 'refunded' ? 'selected' : '' ?>>Refunded</option>
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
                <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Search</label>
                <input type="text" name="search" value="<?= htmlspecialchars($search_query) ?>"
                    placeholder="Order ID, Name..." class="w-full bg-slate-50 p-4 rounded-2xl font-bold outline-none">
            </div>
            <div class="flex gap-2">
                <button type="submit"
                    class="flex-1 bg-slate-900 text-white py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-orange-600 transition-all">
                    <i class="fas fa-filter mr-2"></i>Apply
                </button>
                <a href="order-list.php"
                    class="w-12 h-12 flex items-center justify-center bg-slate-100 text-slate-400 rounded-2xl hover:bg-red-50 hover:text-red-500 transition-all">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Quick Filters -->
    <div class="flex gap-2 overflow-x-auto pb-4 mb-6">
        <a href="?status=all"
            class="px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all <?= $status_filter == 'all' ? 'bg-orange-600 text-white' : 'bg-white text-slate-400 hover:bg-slate-50' ?>">
            All (
            <?= array_sum($counts) ?>)
        </a>
        <?php foreach ($all_statuses as $st): ?>
            <a href="?status=<?= $st ?>"
                class="px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all <?= $status_filter == $st ? 'bg-orange-600 text-white' : 'bg-white text-slate-400 hover:bg-slate-50' ?>">
                <?= ucfirst($st) ?> (
                <?= $counts[$st] ?? 0 ?>)
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Orders Table -->
    <div class="bg-white rounded-[3rem] border border-slate-100 shadow-sm overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-slate-50 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                <tr>
                    <th class="px-8 py-6">Order ID</th>
                    <th class="px-6 py-6">Customer</th>
                    <th class="px-6 py-6 text-center">Date</th>
                    <th class="px-6 py-6 text-right">Amount</th>
                    <th class="px-6 py-6 text-center">Payment</th>
                    <th class="px-6 py-6 text-center">Status</th>
                    <th class="px-6 py-6 text-center">Invoice</th>
                    <th class="px-8 py-6 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php foreach ($orders as $o): ?>
                    <tr class="hover:bg-slate-50 transition-all group">
                        <td class="px-8 py-5">
                            <p class="text-sm font-black text-slate-900">#ORD-
                                <?= $o['order_id'] ?>
                            </p>
                        </td>
                        <td class="px-6 py-5">
                            <p class="text-xs font-black text-slate-900">
                                <?= htmlspecialchars($o['customer_name']) ?>
                            </p>
                            <p class="text-[10px] text-slate-400 font-bold">
                                <?= $o['phone'] ?>
                            </p>
                            <?php if ($o['city']): ?>
                                <p class="text-[9px] text-slate-300 font-bold uppercase">
                                    <?= $o['city'] ?>
                                </p>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <p class="text-xs font-bold text-slate-600">
                                <?= date('d M, Y', strtotime($o['created_at'])) ?>
                            </p>
                            <p class="text-[9px] text-slate-400">
                                <?= date('h:i A', strtotime($o['created_at'])) ?>
                            </p>
                        </td>
                        <td class="px-6 py-5 text-right">
                            <p class="text-sm font-black text-slate-900">₹
                                <?= number_format($o['total_amount'], 2) ?>
                            </p>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <span
                                class="px-4 py-1.5 rounded-xl text-[9px] font-black uppercase <?= getPaymentStyle($o['payment_status'] ?? 'pending') ?>">
                                <?= $o['payment_status'] ?? 'N/A' ?>
                            </span>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <span
                                class="px-4 py-1.5 rounded-xl text-[9px] font-black uppercase <?= getStatusStyle($o['order_status']) ?>">
                                <?= $o['order_status'] ?>
                            </span>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <?php if ($o['invoice_id']): ?>
                                <a href="invoice-view.php?id=<?= $o['invoice_id'] ?>"
                                    class="text-[10px] font-black text-orange-600 hover:text-orange-800 underline">
                                    #
                                    <?= $o['invoice_number'] ?? $o['invoice_id'] ?>
                                </a>
                            <?php else: ?>
                                <span class="text-[10px] text-slate-300">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-8 py-5 text-right">
                            <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-all">
                                <a href="../orders/order-details.php?id=<?= $o['order_id'] ?>"
                                    class="w-9 h-9 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center hover:bg-slate-900 hover:text-white transition-all">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                                <?php if ($o['invoice_id']): ?>
                                    <a href="invoice-view.php?id=<?= $o['invoice_id'] ?>" target="_blank"
                                        class="w-9 h-9 rounded-xl bg-orange-500 text-white flex items-center justify-center hover:bg-orange-600 transition-all shadow-lg shadow-orange-100">
                                        <i class="fas fa-file-invoice text-xs"></i>
                                    </a>
                                <?php else: ?>
                                    <a href="invoices.php?generate=<?= $o['order_id'] ?>"
                                        class="w-9 h-9 rounded-xl bg-emerald-500 text-white flex items-center justify-center hover:bg-emerald-600 transition-all shadow-lg shadow-emerald-100"
                                        title="Generate Invoice">
                                        <i class="fas fa-plus text-xs"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if (empty($orders)): ?>
                    <tr>
                        <td colspan="8" class="p-20 text-center">
                            <i class="fas fa-shopping-cart text-slate-100 text-6xl mb-4"></i>
                            <p class="text-sm font-black text-slate-300 uppercase tracking-widest">No orders found</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

<script>
    function exportToCSV() {
        const rows = [['Order ID', 'Customer', 'Date', 'Amount', 'Payment', 'Status', 'Invoice']];
        document.querySelectorAll('tbody tr').forEach(tr => {
            const cells = tr.querySelectorAll('td');
            if (cells.length >= 7) {
                rows.push([
                    cells[0].innerText.trim(),
                    cells[1].innerText.trim().replace(/\n/g, ' '),
                    cells[2].innerText.trim(),
                    cells[3].innerText.trim(),
                    cells[4].innerText.trim(),
                    cells[5].innerText.trim(),
                    cells[6].innerText.trim()
                ]);
            }
        });

        let csvContent = rows.map(e => e.join(",")).join("\n");
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement("a");
        link.href = URL.createObjectURL(blob);
        link.download = "orders_export_" + new Date().toISOString().slice(0, 10) + ".csv";
        link.click();
    }
</script>

<?php include $base_path . 'includes/admin-footer.php'; ?>