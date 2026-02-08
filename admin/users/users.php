<?php
$base_path = $_SERVER['DOCUMENT_ROOT'] . '/ecommerce-website/';
require_once $base_path . 'config/database.php';
require_once $base_path . 'includes/functions.php';

include $base_path . 'includes/admin-header.php';
include $base_path . 'includes/sidebar.php';

// ----- Customer Summary -----
$statsQuery = "
    SELECT 
        COUNT(*) AS total_customers,
        SUM(status = 'active') AS active_customers,
        SUM(status = 'inactive') AS suspended_customers
    FROM users
    WHERE role = 'user'
";
$stats = $pdo->query($statsQuery)->fetch(PDO::FETCH_ASSOC);

$total_customers = $stats['total_customers'] ?? 0;
$active_customers = $stats['active_customers'] ?? 0;
$suspended_customers = $stats['suspended_customers'] ?? 0;

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
    .glass-card {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .kpi-gradient-1 {
        background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
    }

    .kpi-gradient-2 {
        background: linear-gradient(135deg, #10b981 0%, #3b82f6 100%);
    }

    .kpi-gradient-3 {
        background: linear-gradient(135deg, #f59e0b 0%, #ef4444 100%);
    }
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
            <button onclick="toggleModal('addUserModal', true)"
                class="bg-slate-900 text-white px-8 py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-orange-600 transition-all shadow-xl shadow-slate-200 flex items-center gap-2">
                <i class="fas fa-plus"></i> Initialize Account
            </button>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <div
            class="kpi-gradient-1 p-8 rounded-[2.5rem] text-white shadow-xl shadow-indigo-100 relative overflow-hidden group">
            <i
                class="fas fa-users absolute -right-2 -bottom-2 text-8xl opacity-10 group-hover:scale-110 transition-transform"></i>
            <p class="text-[10px] font-bold uppercase tracking-widest opacity-80">Total Community</p>
            <h3 class="text-4xl font-black mt-2"><?php echo number_format($total_customers); ?></h3>
            <div class="mt-4 flex items-center gap-2 text-[10px] font-bold bg-white/10 w-fit px-3 py-1 rounded-full">
                <span class="w-1.5 h-1.5 bg-white rounded-full"></span> Active Directory
            </div>
        </div>
        <div
            class="kpi-gradient-2 p-8 rounded-[2.5rem] text-white shadow-xl shadow-emerald-100 relative overflow-hidden group">
            <i
                class="fas fa-user-check absolute -right-2 -bottom-2 text-8xl opacity-10 group-hover:scale-110 transition-transform"></i>
            <p class="text-[10px] font-bold uppercase tracking-widest opacity-80">Verified Users</p>
            <h3 class="text-4xl font-black mt-2"><?php echo number_format($active_customers); ?></h3>
            <div class="mt-4 flex items-center gap-2 text-[10px] font-bold bg-white/10 w-fit px-3 py-1 rounded-full">
                <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full animate-pulse"></span> Pulse Active
            </div>
        </div>
        <div
            class="kpi-gradient-3 p-8 rounded-[2.5rem] text-white shadow-xl shadow-orange-100 relative overflow-hidden group">
            <i
                class="fas fa-user-slash absolute -right-2 -bottom-2 text-8xl opacity-10 group-hover:scale-110 transition-transform"></i>
            <p class="text-[10px] font-bold uppercase tracking-widest opacity-80">Access Restricted</p>
            <h3 class="text-4xl font-black mt-2"><?php echo number_format($suspended_customers); ?></h3>
            <div class="mt-4 flex items-center gap-2 text-[10px] font-bold bg-white/10 w-fit px-3 py-1 rounded-full">
                <span class="w-1.5 h-1.5 bg-white rounded-full"></span> Security Hold
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-[3rem] border border-slate-100 shadow-sm overflow-hidden glass-card">
        <div class="p-8 border-b border-slate-50 flex justify-between items-center">
            <h4 class="text-[10px] font-black uppercase tracking-widest text-slate-400">User Identification Ledger</h4>
            <div class="flex gap-2">
                <input type="text" placeholder="Search accounts..."
                    class="bg-slate-50 border-none rounded-xl px-4 py-2 text-xs focus:ring-2 focus:ring-orange-500/20 outline-none w-64">
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-[9px] font-black text-slate-400 uppercase tracking-widest bg-slate-50/50">
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
                                    <?php echo e($user['phone'] ?: 'No Phone'); ?></p>
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
                                <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button onclick='openViewModal(<?php echo json_encode($user); ?>)'
                                        class="w-9 h-9 flex items-center justify-center bg-blue-50 text-blue-500 rounded-xl hover:bg-blue-600 hover:text-white transition-all shadow-sm">
                                        <i class="fas fa-eye text-xs"></i>
                                    </button>
                                    <button onclick='openEditModal(<?php echo json_encode($user); ?>)'
                                        class="w-9 h-9 flex items-center justify-center bg-slate-50 text-slate-500 rounded-xl hover:bg-slate-900 hover:text-white transition-all shadow-sm">
                                        <i class="fas fa-pencil text-xs"></i>
                                    </button>
                                    <form method="POST" action="process_user.php"
                                        onsubmit="return confirm('Suspend user access?')">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                        <button type="submit"
                                            class="w-9 h-9 flex items-center justify-center bg-red-50 text-red-500 rounded-xl hover:bg-red-600 hover:text-white transition-all shadow-sm">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

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