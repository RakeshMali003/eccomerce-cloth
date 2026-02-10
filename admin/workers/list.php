<?php
$base_path = __DIR__ . '/../../';
require_once $base_path . 'config/database.php';
require_once $base_path . 'includes/functions.php';

include $base_path . 'includes/admin-header.php';
include $base_path . 'includes/sidebar.php';

if (!has_permission('workers')) {
    echo "<script>alert('Access Denied'); window.location.href='../../dashboard.php';</script>";
    exit;
}

// Fetch Workers
$stmt = $pdo->prepare("
    SELECT w.*, u.name, u.email, u.phone, u.status 
    FROM workers w
    JOIN users u ON w.user_id = u.user_id
    ORDER BY w.created_at DESC
");
$stmt->execute();
$workers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<main class="p-6 lg:p-10 bg-slate-50/50 min-h-screen">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-6">
        <div>
            <h2 class="text-3xl font-black tracking-tight text-slate-800">Team Management<span
                    class="text-orange-600">.</span></h2>
            <p class="text-slate-500 font-medium text-sm">Manage staff roles and access permissions</p>
        </div>
        <div class="flex gap-3">
            <button onclick="toggleModal('addWorkerModal', true)"
                class="bg-slate-900 text-white px-8 py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-orange-600 transition-all shadow-xl shadow-slate-200 flex items-center gap-2">
                <i class="fas fa-plus"></i> Add Worker
            </button>
        </div>
    </div>

    <!-- Workers Table -->
    <div class="bg-white rounded-[3rem] border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-[9px] font-black text-slate-400 uppercase tracking-widest bg-slate-50/50">
                        <th class="px-8 py-5">Staff Member</th>
                        <th class="px-6 py-5">Role & Access</th>
                        <th class="px-6 py-5">Assignment</th>
                        <th class="px-6 py-5">Status</th>
                        <th class="px-8 py-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php foreach ($workers as $worker): ?>
                        <tr class="hover:bg-slate-50/50 transition-all group">
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="w-10 h-10 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center font-bold">
                                        <?= strtoupper(substr($worker['name'], 0, 1)) ?>
                                    </div>
                                    <div>
                                        <span class="text-sm font-bold text-slate-800 block">
                                            <?= e($worker['name']) ?>
                                        </span>
                                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter">
                                            <?= e($worker['email']) ?>
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <span
                                    class="bg-slate-100 text-slate-600 px-3 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest">
                                    <?= e($worker['role']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-5">
                                <span class="text-xs font-bold text-slate-600">
                                    <?= e($worker['assigned_section'] ?: 'General') ?>
                                </span>
                            </td>
                            <td class="px-6 py-5">
                                <span
                                    class="w-2 h-2 rounded-full inline-block mr-2 <?= $worker['status'] == 'active' ? 'bg-emerald-500' : 'bg-red-500' ?>"></span>
                                <span class="text-[10px] font-black uppercase text-slate-400">
                                    <?= ucfirst($worker['status']) ?>
                                </span>
                            </td>
                            <td class="px-8 py-5 text-right">
                                <form method="POST" action="process_worker.php"
                                    onsubmit="return confirm('Remove worker access?')" class="inline-block">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="worker_id" value="<?= $worker['worker_id'] ?>">
                                    <input type="hidden" name="user_id" value="<?= $worker['user_id'] ?>">
                                    <button type="submit" class="text-slate-300 hover:text-red-500 transition-colors"><i
                                            class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<!-- Add Worker Modal -->
<div id="addWorkerModal"
    class="fixed inset-0 z-[200] bg-slate-900/40 backdrop-blur-sm hidden items-center justify-center p-4">
    <div class="bg-white w-full max-w-lg rounded-[3rem] shadow-2xl p-10 md:p-12 relative">
        <button onclick="toggleModal('addWorkerModal', false)"
            class="absolute top-8 right-8 text-slate-400 hover:text-slate-900"><i
                class="fas fa-times text-xl"></i></button>

        <h3 class="text-2xl font-black tracking-tight mb-2">Onboard Staff</h3>
        <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-8">Create new worker profile</p>

        <form action="process_worker.php" method="POST" enctype="multipart/form-data" class="space-y-6">
            <input type="hidden" name="action" value="add">

            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Full Name</label>
                    <input type="text" name="name" required
                        class="w-full px-6 py-4 bg-slate-50 rounded-2xl border-none focus:ring-2 focus:ring-orange-500/20 font-semibold outline-none">
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Email</label>
                    <input type="email" name="email" required
                        class="w-full px-6 py-4 bg-slate-50 rounded-2xl border-none focus:ring-2 focus:ring-orange-500/20 font-semibold outline-none">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Phone</label>
                    <input type="text" name="phone" required
                        class="w-full px-6 py-4 bg-slate-50 rounded-2xl border-none focus:ring-2 focus:ring-orange-500/20 font-semibold outline-none">
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Alternate Contact</label>
                    <input type="text" name="contact_number"
                        class="w-full px-6 py-4 bg-slate-50 rounded-2xl border-none focus:ring-2 focus:ring-orange-500/20 font-semibold outline-none">
                </div>
            </div>

            <div class="space-y-1">
                <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Address</label>
                <textarea name="address" rows="2"
                    class="w-full px-6 py-4 bg-slate-50 rounded-2xl border-none focus:ring-2 focus:ring-orange-500/20 font-semibold outline-none"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Role</label>
                    <select name="role" required
                        class="w-full px-6 py-4 bg-slate-50 rounded-2xl border-none focus:ring-2 focus:ring-orange-500/20 font-bold text-xs outline-none">
                        <option value="Manager">Manager</option>
                        <option value="Staff">Staff</option>
                        <option value="Intern">Intern</option>
                    </select>
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Section</label>
                    <select name="section"
                        class="w-full px-6 py-4 bg-slate-50 rounded-2xl border-none focus:ring-2 focus:ring-orange-500/20 font-bold text-xs outline-none">
                        <option value="General">General</option>
                        <option value="Sales">Sales</option>
                        <option value="Inventory">Inventory</option>
                        <option value="Support">Support</option>
                    </select>
                </div>
            </div>

            <div class="space-y-1">
                <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Salary</label>
                <input type="number" name="salary" step="0.01" placeholder="0.00"
                    class="w-full px-6 py-4 bg-slate-50 rounded-2xl border-none focus:ring-2 focus:ring-orange-500/20 font-semibold outline-none">
            </div>

            <div class="space-y-1">
                <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Bank / Payment Details</label>
                <textarea name="bank_details" rows="2" placeholder="Bank Name, Account No, IFSC..."
                    class="w-full px-6 py-4 bg-slate-50 rounded-2xl border-none focus:ring-2 focus:ring-orange-500/20 font-semibold outline-none"></textarea>
            </div>

            <div class="space-y-1">
                <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Documents (ID, Resume)</label>
                <input type="file" name="documents[]" multiple
                    class="w-full px-6 py-4 bg-slate-50 rounded-2xl border-none text-xs font-bold text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-slate-900 file:text-white hover:file:bg-orange-600 transition-all cursor-pointer">
            </div>

            <div class="space-y-2">
                <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Access Permissions</label>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3 bg-slate-50 p-4 rounded-2xl">
                    <label class="flex items-center gap-2 text-xs font-bold text-slate-700">
                        <input type="checkbox" name="permissions[]" value="dashboard" checked
                            class="w-4 h-4 rounded text-orange-600 focus:ring-orange-500"> Dashboard
                    </label>
                    <label class="flex items-center gap-2 text-xs font-bold text-slate-700">
                        <input type="checkbox" name="permissions[]" value="orders"
                            class="w-4 h-4 rounded text-orange-600 focus:ring-orange-500"> Orders
                    </label>
                    <label class="flex items-center gap-2 text-xs font-bold text-slate-700">
                        <input type="checkbox" name="permissions[]" value="products"
                            class="w-4 h-4 rounded text-orange-600 focus:ring-orange-500"> Products
                    </label>
                    <label class="flex items-center gap-2 text-xs font-bold text-slate-700">
                        <input type="checkbox" name="permissions[]" value="users"
                            class="w-4 h-4 rounded text-orange-600 focus:ring-orange-500"> Users
                    </label>
                    <label class="flex items-center gap-2 text-xs font-bold text-slate-700">
                        <input type="checkbox" name="permissions[]" value="reports"
                            class="w-4 h-4 rounded text-orange-600 focus:ring-orange-500"> Reports
                    </label>
                    <label class="flex items-center gap-2 text-xs font-bold text-slate-700">
                        <input type="checkbox" name="permissions[]" value="settings"
                            class="w-4 h-4 rounded text-orange-600 focus:ring-orange-500"> Settings
                    </label>
                    <label class="flex items-center gap-2 text-xs font-bold text-slate-700">
                        <input type="checkbox" name="permissions[]" value="workers"
                            class="w-4 h-4 rounded text-orange-600 focus:ring-orange-500"> Manage Workers
                    </label>
                </div>
            </div>

            <div class="space-y-1">
                <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Password</label>
                <input type="password" name="password" required
                    class="w-full px-6 py-4 bg-slate-50 rounded-2xl border-none focus:ring-2 focus:ring-orange-500/20 font-semibold outline-none"
                    placeholder="Initial password">
            </div>

            <button type="submit"
                class="w-full bg-slate-900 text-white py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-xl hover:bg-orange-600 transition-all mt-4">
                Create Profile
            </button>
        </form>
    </div>
</div>

<script>
    function toggleModal(id, show) {
        document.getElementById(id).style.display = show ? 'flex' : 'none';
        document.body.style.overflow = show ? 'hidden' : 'auto';
    }
</script>

<?php include $base_path . 'includes/admin-footer.php'; ?>