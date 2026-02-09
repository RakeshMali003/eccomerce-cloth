<?php
$base_path = $_SERVER['DOCUMENT_ROOT'] . '/ecommerce-website/';
require_once $base_path . 'config/database.php';
require_once $base_path . 'includes/functions.php';

include $base_path . 'includes/admin-header.php';
include $base_path . 'includes/sidebar.php';
include $base_path . 'includes/notifications.php';

if (!has_permission('users')) {
    echo "<script>alert('Access Denied'); window.location.href='../dashboard.php';</script>";
    exit;
}

// ... (keep existing stats queries) ...

// ---- Customers List ----
$customersQuery = "
    SELECT 
        u.*,
        COUNT(o.order_id) AS total_orders,
        COALESCE(SUM(CASE 
            WHEN o.payment_status = 'paid' THEN o.total_amount 
            ELSE 0 
        END), 0) AS total_spent
    FROM users u
    LEFT JOIN orders o ON o.user_id = u.user_id
    WHERE u.role = 'user'
    GROUP BY u.user_id
    ORDER BY u.created_at DESC
";

$stmt = $pdo->prepare($customersQuery);
$stmt->execute();
$customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
    /* ... (existing styles) ... */
    .glass-card {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    /* ... */
</style>

<main class="p-6 lg:p-10 bg-slate-50/50 min-h-screen">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-6">
        <div>
            <h2 class="text-3xl font-black tracking-tight text-slate-800">User Ecosystem<span
                    class="text-orange-600">.</span></h2>
            <p class="text-slate-500 font-medium text-sm">Managing your retail and wholesale community</p>
        </div>
        <div class="flex gap-3">
            <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
                <button onclick="submitBulkDelete()" id="bulkDeleteBtn" style="display:none;"
                    class="bg-red-500 text-white px-8 py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-red-600 transition-all shadow-xl shadow-red-200 flex items-center gap-2">
                    <i class="fas fa-trash"></i> Delete Selected
                </button>
            <?php endif; ?>
            <button onclick="toggleModal('addUserModal', true)"
                class="bg-slate-900 text-white px-8 py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-orange-600 transition-all shadow-xl shadow-slate-200 flex items-center gap-2">
                <i class="fas fa-plus"></i> Initialize Account
            </button>
        </div>
    </div>

    <!-- Stats Grid (Keep as is) -->
    <!-- ... -->

    <!-- Users Table -->
    <div class="bg-white rounded-[3rem] border border-slate-100 shadow-sm overflow-hidden glass-card">
        <div class="p-8 border-b border-slate-50 flex justify-between items-center">
            <h4 class="text-[10px] font-black uppercase tracking-widest text-slate-400">User Identification Ledger</h4>
            <!-- ... Search input ... -->
        </div>

        <form id="bulkDeleteForm" action="process_user.php" method="POST">
            <input type="hidden" name="action" value="bulk_delete">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-[9px] font-black text-slate-400 uppercase tracking-widest bg-slate-50/50">
                            <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
                                <th class="px-8 py-5 w-10">
                                    <input type="checkbox" id="selectAll" onclick="toggleSelectAll()"
                                        class="w-4 h-4 rounded text-orange-600 focus:ring-orange-500 cursor-pointer">
                                </th>
                            <?php endif; ?>
                            <th class="px-8 py-5">Profile</th>
                            <th class="px-6 py-5">Communication</th>
                            <th class="px-6 py-5 text-center">Transactions</th>
                            <th class="px-6 py-5">Monetary Value</th>
                            <th class="px-6 py-5">Access Rank</th>
                            <th class="px-8 py-5 text-right">Utility</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <?php foreach ($customers as $user): ?>
                            <tr class="hover:bg-slate-50/50 transition-all group">
                                <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
                                    <td class="px-8 py-5">
                                        <input type="checkbox" name="delete_ids[]" value="<?php echo $user['user_id']; ?>"
                                            onclick="checkSelection()"
                                            class="row-checkbox w-4 h-4 rounded text-orange-600 focus:ring-orange-500 cursor-pointer">
                                    </td>
                                <?php endif; ?>
                                <td class="px-8 py-5">
                                    <div class="flex items-center gap-4">
                                        <div class="relative">
                                            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($user['name']); ?>&background=f8fafc&color=0f172a&bold=true"
                                                class="w-12 h-12 rounded-2xl shadow-sm border-2 border-white">
                                            <span
                                                class="absolute -bottom-1 -right-1 w-4 h-4 rounded-full border-2 border-white <?php echo $user['status'] == 'active' ? 'bg-emerald-500' : 'bg-slate-300'; ?>"></span>
                                        </div>
                                        <div class="max-w-[150px]">
                                            <span
                                                class="text-sm font-black text-slate-800 block truncate"><?php echo e($user['name']); ?></span>
                                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter">ID:
                                                #<?php echo $user['user_id']; ?> • Joined
                                                <?php echo date('M Y', strtotime($user['created_at'])); ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <p class="text-xs font-bold text-slate-700"><?php echo e($user['email']); ?></p>
                                    <p class="text-[10px] text-slate-400 font-bold">
                                        <?php echo e($user['phone'] ?: 'No Phone'); ?>
                                    </p>
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <div class="inline-flex flex-col">
                                        <span
                                            class="text-sm font-black text-slate-800"><?php echo $user['total_orders']; ?></span>
                                        <span class="text-[8px] font-black text-slate-300 uppercase">Requests</span>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex flex-col">
                                        <span
                                            class="text-sm font-black text-slate-800"><?php echo format_price($user['total_spent']); ?></span>
                                        <span class="text-[8px] font-black text-emerald-500 uppercase">Lifetime</span>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <span
                                        class="px-3 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest <?php echo $user['status'] == 'active' ? 'bg-emerald-50 text-emerald-600' : 'bg-red-50 text-red-600'; ?>">
                                        <?php echo $user['status']; ?>
                                    </span>
                                </td>
                                <td class="px-8 py-5">
                                    <div
                                        class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <button type="button" onclick='openViewModal(<?php echo json_encode($user); ?>)'
                                            class="w-9 h-9 flex items-center justify-center bg-blue-50 text-blue-500 rounded-xl hover:bg-blue-600 hover:text-white transition-all shadow-sm">
                                            <i class="fas fa-eye text-xs"></i>
                                        </button>
                                        <button type="button" onclick='openEditModal(<?php echo json_encode($user); ?>)'
                                            class="w-9 h-9 flex items-center justify-center bg-slate-50 text-slate-500 rounded-xl hover:bg-slate-900 hover:text-white transition-all shadow-sm">
                                            <i class="fas fa-pencil text-xs"></i>
                                        </button>
                                        <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
                                            <button type="button" onclick="confirmDelete(<?php echo $user['user_id']; ?>)"
                                                class="w-9 h-9 flex items-center justify-center bg-red-50 text-red-500 rounded-xl hover:bg-red-600 hover:text-white transition-all shadow-sm">
                                                <i class="fas fa-trash text-xs"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </form>
    </div>
</main>

<form id="singleDeleteForm" method="POST" action="process_user.php">
    <input type="hidden" name="action" value="delete">
    <input type="hidden" name="user_id" id="deleteIdInput">
</form>

<script>
    function toggleSelectAll() {
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.row-checkbox');
        checkboxes.forEach(cb => cb.checked = selectAll.checked);
        checkSelection();
    }

    function checkSelection() {
        const checkboxes = document.querySelectorAll('.row-checkbox:checked');
        const bulkBtn = document.getElementById('bulkDeleteBtn');
        if (bulkBtn) {
            bulkBtn.style.display = checkboxes.length > 0 ? 'flex' : 'none';
        }
    }

    function submitBulkDelete() {
        if (confirm('Are you sure you want to delete selected users? This action cannot be undone.')) {
            document.getElementById('bulkDeleteForm').submit();
        }
    }

    function confirmDelete(id) {
        if (confirm('Suspend user access?')) {
            document.getElementById('deleteIdInput').value = id;
            document.getElementById('singleDeleteForm').submit();
        }
    }

    function toggleModal(id, show) {
        document.getElementById(id).style.display = show ? 'flex' : 'none';
        document.body.style.overflow = show ? 'hidden' : 'auto';
    }

    function openEditModal(user) {
        // Simple bridge to existing modal logic if needed
        console.log('Edit User:', user);
        toggleModal('addUserModal', true); // For demo, using add modal shell
    }

    function openViewModal(user) {
        console.log('View User:', user);
        alert('Viewing details for ' + user.name);
    }
</script>

<!-- Modals -->
<div id="addUserModal"
    class="fixed inset-0 z-[200] bg-slate-900/40 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="bg-white w-full max-w-lg rounded-[3rem] shadow-2xl p-10 md:p-12">
        <h3 class="text-2xl font-black tracking-tight mb-2">Initialize Account</h3>
        <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-8">Customer Directory Additive</p>
        <form action="process_user.php" method="POST" class="space-y-6">
            <input type="hidden" name="action" value="add">
            <div class="space-y-1">
                <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Full Legal Name</label>
                <input type="text" name="name" required
                    class="w-full px-6 py-4 bg-slate-50 rounded-2xl border-none focus:ring-2 focus:ring-orange-500/20 font-semibold outline-none">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Email Access</label>
                    <input type="email" name="email" required
                        class="w-full px-6 py-4 bg-slate-50 rounded-2xl border-none focus:ring-2 focus:ring-orange-500/20 font-semibold outline-none">
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Primary Phone</label>
                    <input type="text" name="phone" required
                        class="w-full px-6 py-4 bg-slate-50 rounded-2xl border-none focus:ring-2 focus:ring-orange-500/20 font-semibold outline-none">
                </div>
            </div>
            <div class="flex gap-4 pt-4">
                <button type="button" onclick="toggleModal('addUserModal', false)"
                    class="flex-1 py-4 text-[10px] font-black uppercase text-slate-400">Cancel</button>
                <button type="submit"
                    class="flex-1 bg-slate-900 text-white py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-xl hover:bg-orange-600 transition-all">Add
                    User</button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleModal(id, show) {
        document.getElementById(id).style.display = show ? 'flex' : 'none';
        document.body.style.overflow = show ? 'hidden' : 'auto';
    }

    function openEditModal(user) {
        // Simple bridge to existing modal logic if needed
        console.log('Edit User:', user);
        toggleModal('addUserModal', true); // For demo, using add modal shell
    }

    function openViewModal(user) {
        console.log('View User:', user);
        alert('Viewing details for ' + user.name);
    }
</script>

<?php include $base_path . 'includes/admin-footer.php'; ?>
</body>

</html>