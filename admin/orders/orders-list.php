<?php
$base_path = $_SERVER['DOCUMENT_ROOT'] . '/ecommerce-website/';
require_once "../../config/database.php";

include $base_path . 'includes/admin-header.php';
include $base_path . 'includes/sidebar.php';
include $base_path . 'includes/notifications.php';

// 1. PHP Helper
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

// 1. Fetch Orders with Filters
if (!has_permission('orders')) {
    echo "<script>alert('Access Denied'); window.location.href='../dashboard.php';</script>";
    exit;
}

$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$limit = 10;
$status_filter = $_GET['status'] ?? 'all';
$search_query = $_GET['search'] ?? '';
$where_clauses = [];
$params = [];

if ($status_filter !== 'all') {
    $where_clauses[] = "o.order_status = :status";
    $params[':status'] = $status_filter;
}

if ($search_query !== '') {
    $where_clauses[] = "(o.order_id LIKE :search OR u.name LIKE :search)";
    $params[':search'] = "%$search_query%";
}

$where_sql = !empty($where_clauses) ? "WHERE " . implode(" AND ", $where_clauses) : "";

// 3. Fetch Orders
$sql = "SELECT o.*, u.name as customer_name, u.phone, u.email, i.invoice_id 
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

// 4. Count Totals for Top Bar
$counts = $pdo->query("SELECT order_status, COUNT(*) as qty FROM orders GROUP BY order_status")->fetchAll(PDO::FETCH_KEY_PAIR);
$all_statuses = ['pending', 'confirmed', 'packed', 'shipped', 'delivered', 'cancelled', 'returned'];
?>

<main class="p-6 lg:p-12 bg-[#f8fafc] min-h-screen">
    <div class="mb-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
        <div>
            <h2 class="text-3xl font-black tracking-tighter text-slate-900">Operations Hub: Orders<span
                    class="text-orange-600">.</span></h2>
            <p class="text-slate-400 text-sm font-medium">Manage fulfillment, shipping, and customer invoices</p>
        </div>
        <div class="flex gap-3">
            <button onclick="bulkMarkShipped()"
                class="bg-slate-900 text-white px-6 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-orange-600 transition-all shadow-xl">
                <i class="fas fa-truck mr-2"></i> Mark Shipped
            </button>
            <button onclick="window.print()"
                class="bg-white border border-slate-200 px-6 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-50 transition-all shadow-sm">
                <i class="fas fa-print mr-2"></i> Print List
            </button>
        </div>
    </div>

    <!-- Quick Filters & Search -->
    <div class="flex flex-col lg:flex-row gap-6 mb-8 items-center justify-between">
        <div class="flex gap-2 overflow-x-auto pb-2 w-full lg:w-auto">
            <a href="?status=all"
                class="px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all <?= $status_filter == 'all' ? 'bg-orange-600 text-white' : 'bg-white text-slate-400 hover:bg-slate-50' ?>">
                All (<?= array_sum($counts) ?>)
            </a>
            <?php foreach ($all_statuses as $st): ?>
                <a href="?status=<?= $st ?>"
                    class="px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all <?= $status_filter == $st ? 'bg-orange-600 text-white' : 'bg-white text-slate-400 hover:bg-slate-50' ?>">
                    <?= ucfirst($st) ?> (<?= $counts[$st] ?? 0 ?>)
                </a>
            <?php endforeach; ?>
        </div>

        <div class="w-full lg:w-72 relative">
            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-300"></i>
            <input type="text" id="orderSearch" placeholder="Search Order ID or Name..."
                class="w-full bg-white border border-slate-100 pl-12 pr-4 py-3 rounded-2xl text-xs font-bold focus:border-orange-500 outline-none shadow-sm">
        </div>
    </div>

    <div class="bg-white rounded-[3rem] border border-slate-100 shadow-sm overflow-hidden overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-slate-50 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                <tr>
                    <th class="px-8 py-6">
                        <input type="checkbox" id="selectAll"
                            class="w-4 h-4 rounded border-slate-200 text-orange-600 focus:ring-orange-500">
                    </th>
                    <th class="px-6 py-6">Order ID</th>
                    <th class="px-6 py-6">Customer</th>
                    <th class="px-6 py-6 text-center">Date</th>
                    <th class="px-6 py-6 text-right">Amount</th>
                    <th class="px-6 py-6 text-center">Status</th>
                    <th class="px-8 py-6 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php foreach ($orders as $o): ?>
                    <tr class="hover:bg-slate-50 transition-all group">
                        <td class="px-8 py-5">
                            <input type="checkbox" name="order_ids[]" value="<?= $o['order_id'] ?>"
                                class="order-checkbox w-4 h-4 rounded border-slate-200 text-orange-600 focus:ring-orange-500">
                        </td>
                        <td class="px-6 py-5 font-black text-slate-900 text-sm">#ORD-<?= $o['order_id'] ?></td>
                        <td class="px-6 py-5">
                            <p class="text-xs font-black text-slate-900"><?= htmlspecialchars($o['customer_name']) ?></p>
                            <p class="text-[10px] text-slate-400 font-bold"><?= $o['phone'] ?></p>
                        </td>
                        <td class="px-6 py-5 text-center text-xs font-bold text-slate-500">
                            <?= date('d M, Y', strtotime($o['created_at'])) ?>
                        </td>
                        <td class="px-6 py-5 text-right font-black text-slate-900">
                            ₹<?= number_format($o['total_amount'], 2) ?></td>

                        <td class="px-6 py-5 text-center">
                            <div class="relative inline-block group/status">
                                <div id="status-display-<?= $o['order_id'] ?>"
                                    class="px-4 py-2 rounded-xl text-[9px] font-black uppercase tracking-tight whitespace-nowrap <?= getStatusStyle($o['order_status']) ?>">
                                    <?= $o['order_status'] ?>
                                </div>
                                <select onchange="updateOrderStatus(<?= $o['order_id'] ?>, this.value)"
                                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                    <?php foreach ($all_statuses as $st): ?>
                                        <option value="<?= $st ?>" <?= $o['order_status'] == $st ? 'selected' : '' ?>>
                                            <?= ucfirst($st) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </td>

                        <td class="px-8 py-5 text-right">
                            <div class="flex justify-end gap-2 lg:opacity-0 group-hover:opacity-100 transition-all">
                                <button onclick="viewOrderDetails(<?= $o['order_id'] ?>)"
                                    class="w-10 h-10 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center hover:bg-slate-900 hover:text-white transition-all shadow-sm">
                                    <i class="fas fa-eye text-xs"></i>
                                </button>
                                <?php if ($o['order_status'] == 'packed'): ?>
                                    <button onclick="openShippingModal(<?= $o['order_id'] ?>)"
                                        class="w-10 h-10 rounded-xl bg-purple-500 text-white flex items-center justify-center hover:bg-purple-600 transition-all shadow-lg shadow-purple-100">
                                        <i class="fas fa-truck-loading text-xs"></i>
                                    </button>
                                <?php endif; ?>
                                <?php if ($o['invoice_id']): ?>
                                    <a href="../billing/invoices.php?id=<?= $o['invoice_id'] ?>" target="_blank"
                                        class="w-10 h-10 rounded-xl bg-orange-500 text-white flex items-center justify-center hover:bg-orange-600 transition-all shadow-lg shadow-orange-100">
                                        <i class="fas fa-file-invoice text-xs"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>

<div id="orderModal" class="fixed inset-0 z-[150] hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeModal()"></div>
    <div
        class="bg-white w-full max-w-2xl rounded-[3rem] shadow-2xl relative z-10 overflow-hidden transform transition-all">
        <div id="modal_body"></div>
    </div>
</div>

<script>
    function viewOrderDetails(id) {
        const modal = document.getElementById('orderModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        fetch(`order-details.php?id=${id}`)
            .then(res => res.text())
            .then(html => document.getElementById('modal_body').innerHTML = html)
            .catch(err => alert("Error loading order details"));
    }

    function closeModal() {
        document.getElementById('orderModal').classList.add('hidden');
        document.getElementById('orderModal').classList.remove('flex');
    }

    function updateOrderStatus(orderId, newStatus) {
        const display = document.getElementById(`status-display-${orderId}`);
        const originalContent = display.innerHTML;
        const originalClass = display.className;
        display.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i>';

        fetch(`update-order-status.php?id=${orderId}&status=${newStatus}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    display.innerHTML = newStatus.toUpperCase();
                    display.className = "px-4 py-2 rounded-xl text-[9px] font-black uppercase tracking-tight " + getJSStyle(newStatus);
                    setTimeout(() => { location.reload(); }, 500);
                } else {
                    alert("Error: " + data.error);
                    display.innerHTML = originalContent;
                    display.className = originalClass;
                }
            })
            .catch(err => {
                alert("System Error");
                display.innerHTML = originalContent;
                display.className = originalClass;
            });
    }

    function getJSStyle(status) {
        const s = status.toLowerCase().trim();
        const styles = {
            'pending': 'bg-amber-100 text-amber-700',
            'confirmed': 'bg-blue-100 text-blue-700',
            'packed': 'bg-slate-900 text-white',
            'shipped': 'bg-purple-100 text-purple-700',
            'delivered': 'bg-emerald-100 text-emerald-700',
            'cancelled': 'bg-red-100 text-red-700',
            'returned': 'bg-orange-100 text-orange-700'
        };
        return styles[s] || 'bg-slate-100 text-slate-400';
    }

    // --- BULK ACTIONS ---
    document.getElementById('selectAll').addEventListener('change', function () {
        document.querySelectorAll('.order-checkbox').forEach(cb => cb.checked = this.checked);
    });

    function getSelectedOrders() {
        return Array.from(document.querySelectorAll('.order-checkbox:checked')).map(cb => cb.value);
    }

    function bulkMarkShipped() {
        const ids = getSelectedOrders();
        if (ids.length === 0) return alert("Select at least one order.");
        if (confirm("Mark " + ids.length + " orders as Shipped?")) {
            fetch('bulk_update_status.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ ids: ids, status: 'shipped' })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alert("Bulk update successful!");
                        location.reload();
                    } else alert(data.error);
                });
        }
    }

    // --- SEARCH LOGIC ---
    document.getElementById('orderSearch').addEventListener('input', function (e) {
        const val = e.target.value.toLowerCase();
        document.querySelectorAll('tbody tr').forEach(tr => {
            const text = tr.innerText.toLowerCase();
            tr.style.display = text.includes(val) ? '' : 'none';
        });
    });
</script>
<?php include $base_path . 'includes/admin-footer.php'; ?>
</body>

</html>