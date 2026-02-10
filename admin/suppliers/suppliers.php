<?php
$base_path = __DIR__ . '/../../';
require_once $base_path . 'config/database.php';

include $base_path . 'includes/admin-header.php';
include $base_path . 'includes/sidebar.php';
include $base_path . 'includes/notifications.php';

// Fetch Initial Data
$suppliers = $pdo->query("SELECT * FROM suppliers ORDER BY created_at DESC")->fetchAll();
// 1. Total Active Suppliers
$total_suppliers = count($suppliers);

// 2. Calculate Total Payable Balance (Total of all Pending/Received POs)
// Assuming total_amount in purchase_orders is what you owe
$payableQuery = $pdo->query("SELECT SUM(total_amount) FROM purchase_orders WHERE status != 'cancelled'");
$total_payable = $payableQuery->fetchColumn() ?: 0;

// 3. Calculate Bills Due This Week (Orders with expected delivery in next 7 days)
$dueQuery = $pdo->query("SELECT COUNT(*) FROM purchase_orders 
                         WHERE status = 'pending' 
                         AND expected_delivery BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)");
$due_this_week = $dueQuery->fetchColumn() ?: 0;
?>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

    body {
        font-family: 'Plus Jakarta Sans', sans-serif;
        background: #f8fafc;
    }

    .glass-panel {
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(12px);
    }

    .custom-scroll::-webkit-scrollbar {
        width: 4px;
    }

    .custom-scroll::-webkit-scrollbar-thumb {
        background: #e2e8f0;
        border-radius: 10px;
    }

    /* Slide-over Animation */
    .slide-over {
        transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
</style>


<main class="p-6 lg:p-12">







    <header
        class="sticky top-0 z-30 glass-panel border-b border-slate-100 px-8 py-4 flex justify-between items-center rounded-3xl mb-8">
        <div>
            <h2 class="text-2xl font-black tracking-tight text-slate-900">Supplier Hub</h2>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Global Logistics & Vendors</p>
        </div>
        <button onclick="openForm('add')"
            class="bg-slate-900 text-white px-6 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-orange-600 transition-all shadow-lg shadow-slate-200">
            + New Vendor
        </button>
    </header>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <div
            class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm border-b-4 border-b-orange-500 hover:shadow-md transition-all">
            <p class="text-[10px] font-black text-slate-400 uppercase mb-1">Active Partners</p>
            <h3 class="text-3xl font-black text-slate-900"><?php echo number_format($total_suppliers); ?></h3>
        </div>

        <div
            class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm border-b-4 border-b-red-500 hover:shadow-md transition-all">
            <p class="text-[10px] font-black text-slate-400 uppercase mb-1">Payable Balance</p>
            <h3 class="text-3xl font-black text-red-600">₹<?php echo number_format($total_payable, 2); ?></h3>
        </div>

        <div class="bg-slate-900 p-8 rounded-[2rem] shadow-xl hover:-translate-y-1 transition-all">
            <p class="text-[10px] font-black text-slate-500 uppercase mb-1">Due this week</p>
            <h3 class="text-3xl font-black text-orange-500"><?php echo str_pad($due_this_week, 2, '0', STR_PAD_LEFT); ?>
                Bills</h3>
        </div>
    </div>

    <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                        <th class="px-8 py-6">Identity</th>
                        <th class="px-6 py-6">Contact Info</th>
                        <th class="px-6 py-6">Terms</th>
                        <th class="px-6 py-6">Status</th>
                        <th class="px-8 py-6 text-right">Vault</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php if (empty($suppliers)): ?>
                        <tr>
                            <td colspan="5" class="p-20 text-center">
                                <i class="fas fa-box-open text-slate-200 text-5xl mb-4"></i>
                                <p class="text-slate-400 font-bold uppercase text-xs tracking-widest">No Vendors Found</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($suppliers as $s): ?>
                            <tr class="group hover:bg-slate-50/30 transition-all cursor-pointer"
                                onclick='fetchAndShowView(<?php echo $s["supplier_id"]; ?>)'>
                                <td class="px-8 py-5">
                                    <p class="text-sm font-black text-slate-900"><?php echo htmlspecialchars($s['name']); ?></p>
                                    <p class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter">GST:
                                        <?php echo $s['gstin'] ?: 'Not Provided'; ?></p>
                                </td>
                                <td class="px-6 py-5 text-xs font-bold text-slate-600">
                                    <i class="fas fa-phone mr-2 text-slate-300"></i><?php echo htmlspecialchars($s['phone']); ?>
                                </td>
                                <td class="px-6 py-5">
                                    <span
                                        class="text-[10px] font-black text-slate-500 bg-slate-100 px-3 py-1 rounded-lg uppercase">
                                        <?php echo $s['payment_terms'] ?: 'COD'; ?>
                                    </span>
                                </td>
                                <td class="px-6 py-5">
                                    <span
                                        class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest <?php echo ($s['status'] == 'active') ? 'bg-emerald-50 text-emerald-600' : 'bg-red-50 text-red-600'; ?>">
                                        <?php echo $s['status']; ?>
                                    </span>
                                </td>
                                <td class="px-8 py-5 text-right">
                                    <div class="flex justify-end gap-2" onclick="event.stopPropagation()">
                                        <button onclick='fetchAndShowEdit(<?php echo $s["supplier_id"]; ?>)'
                                            class="w-9 h-9 flex items-center justify-center bg-slate-50 text-slate-400 rounded-xl hover:bg-slate-900 hover:text-white transition-all">
                                            <i class="fas fa-pencil text-xs"></i>
                                        </button>
                                        <button onclick="confirmDeletion(<?php echo $s['supplier_id']; ?>)"
                                            class="w-9 h-9 flex items-center justify-center bg-slate-50 text-slate-400 rounded-xl hover:bg-red-500 hover:text-white transition-all">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>
</div>

<div id="sideDrawer" class="fixed inset-0 z-[100] hidden">
    <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="closeDrawer()"></div>
    <div
        class="absolute top-0 right-0 h-full w-full max-w-xl bg-white shadow-2xl slide-over translate-x-full flex flex-col">
        <div class="p-8 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
            <h3 id="drawerTitle" class="text-2xl font-black text-slate-900">Add Supplier</h3>
            <button onclick="closeDrawer()" class="text-slate-400 hover:text-red-500"><i
                    class="fas fa-times text-xl"></i></button>
        </div>

        <form action="process_supplier.php" method="POST" class="p-8 space-y-6 overflow-y-auto custom-scroll flex-1">
            <input type="hidden" name="action" id="formAction">
            <input type="hidden" name="supplier_id" id="s_id">

            <div class="space-y-1">
                <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Company Identity *</label>
                <input type="text" name="name" id="in_name" required
                    class="w-full bg-slate-50 p-4 rounded-2xl border-2 border-transparent focus:border-orange-500 outline-none text-sm font-bold">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Contact Person</label>
                    <input type="text" name="contact_person" id="in_contact"
                        class="w-full bg-slate-50 p-4 rounded-2xl border-2 border-transparent focus:border-orange-500 outline-none text-sm font-bold">
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Phone *</label>
                    <input type="text" name="phone" id="in_phone" required
                        class="w-full bg-slate-50 p-4 rounded-2xl border-2 border-transparent focus:border-orange-500 outline-none text-sm font-bold">
                </div>
            </div>
            <div class="space-y-1">
                <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Official Email</label>
                <input type="email" name="email" id="in_email"
                    class="w-full bg-slate-50 p-4 rounded-2xl border-2 border-transparent focus:border-orange-500 outline-none text-sm font-bold">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="text-[10px] font-black uppercase text-slate-400 ml-2">GSTIN</label>
                    <input type="text" name="gstin" id="in_gst"
                        class="w-full bg-slate-50 p-4 rounded-2xl border-2 border-transparent focus:border-orange-500 outline-none text-sm font-bold">
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Status</label>
                    <select name="status" id="in_status"
                        class="w-full bg-slate-50 p-4 rounded-2xl outline-none text-sm font-bold">
                        <option value="active">Active</option>
                        <option value="inactive">Suspended</option>
                    </select>
                </div>
            </div>
            <div class="space-y-1">
                <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Warehouse Address</label>
                <textarea name="address" id="in_addr" rows="2"
                    class="w-full bg-slate-50 p-4 rounded-2xl border-2 border-transparent focus:border-orange-500 outline-none text-sm font-bold resize-none"></textarea>
            </div>

            <div class="flex gap-4 pt-10">
                <button type="submit"
                    class="flex-1 bg-slate-900 text-white py-5 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] shadow-xl hover:bg-orange-600 transition-all">Synchronize
                    Vendor</button>
            </div>
        </form>
    </div>
</div>

<div id="viewModal" class="fixed inset-0 z-[110] hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-md" onclick="closeView()"></div>
    <div class="bg-white w-full max-w-4xl rounded-[3rem] shadow-2xl relative p-10 overflow-y-auto max-h-[90vh]">
        <div class="flex justify-between items-start mb-10">
            <div>
                <h3 id="v_name" class="text-4xl font-black text-slate-900 tracking-tighter"></h3>
                <p id="v_contact" class="text-orange-600 font-bold uppercase text-[10px] tracking-widest mt-1"></p>
            </div>
            <button onclick="closeView()"
                class="w-12 h-12 bg-slate-50 rounded-full flex items-center justify-center text-slate-400 hover:text-red-500 transition-all"><i
                    class="fas fa-times"></i></button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="md:col-span-1 space-y-6">
                <div class="bg-orange-50 p-6 rounded-[2rem]">
                    <p class="text-[9px] font-black text-orange-400 uppercase">Outstanding Balance</p>
                    <h4 id="v_outstanding" class="text-2xl font-black text-orange-600">₹0</h4>
                </div>
                <div class="space-y-4 px-4">
                    <div>
                        <p class="text-[9px] font-black text-slate-400 uppercase">Email</p>
                        <p id="v_email" class="text-xs font-bold text-slate-800"></p>
                    </div>
                    <div>
                        <p class="text-[9px] font-black text-slate-400 uppercase">Phone</p>
                        <p id="v_phone" class="text-xs font-bold text-slate-800"></p>
                    </div>
                    <div>
                        <p class="text-[9px] font-black text-slate-400 uppercase">Address</p>
                        <p id="v_address" class="text-xs font-medium text-slate-500 leading-relaxed"></p>
                    </div>
                </div>
            </div>

            <div class="md:col-span-2">
                <div class="flex gap-4 border-b border-slate-100 mb-6">
                    <button onclick="switchTab('history')" id="tab-history-btn"
                        class="view-tab-btn pb-3 text-[10px] font-black uppercase tracking-widest border-b-2 border-orange-500 text-slate-900">Purchase
                        History</button>
                    <button onclick="switchTab('ledger')" id="tab-ledger-btn"
                        class="view-tab-btn pb-3 text-[10px] font-black uppercase tracking-widest border-b-2 border-transparent text-slate-400">Bills
                        & Ledger</button>
                </div>

                <div id="tab-history" class="view-tab-content space-y-3">
                </div>

                <div id="tab-ledger" class="view-tab-content hidden space-y-3">
                    <div class="p-8 text-center bg-slate-50 rounded-3xl">
                        <i class="fas fa-receipt text-slate-200 text-3xl mb-3"></i>
                        <p class="text-xs font-bold text-slate-400 uppercase">Detailed Ledger Coming Soon</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script>

    // 🟢 1. Side Drawer (Add/Edit) Logic
    function openDrawer() {
        const drawer = document.getElementById('sideDrawer');
        const content = drawer.querySelector('.slide-over');
        drawer.classList.remove('hidden');
        void drawer.offsetWidth; // Force Reflow
        setTimeout(() => content.classList.remove('translate-x-full'), 10);
        document.body.style.overflow = 'hidden';
    }

    function closeDrawer() {
        const drawer = document.getElementById('sideDrawer');
        const content = drawer.querySelector('.slide-over');
        content.classList.add('translate-x-full');
        setTimeout(() => drawer.classList.add('hidden'), 400);
        document.body.style.overflow = 'auto';
    }

    // 🟢 2. Modal Logic
    function openViewModal() {
        const modal = document.getElementById('viewModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeView() {
        const modal = document.getElementById('viewModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = 'auto';
    }

    // 🟢 3. Action Handlers
    function openForm(mode) {
        if (mode === 'add') {
            document.getElementById('drawerTitle').innerText = "Onboard New Supplier";
            document.getElementById('formAction').value = "add";
            document.getElementById('s_id').value = "";
            // Reset Form
            const form = document.querySelector('#sideDrawer form');
            if (form) form.reset();
            openDrawer();
        }
    }

    async function fetchAndShowEdit(id) {
        try {
            notify("Loading profile...", "warning");
            const response = await fetch(`process_supplier.php?fetch_id=${id}`);
            const data = await response.json();

            document.getElementById('drawerTitle').innerText = "Update Vendor Identity";
            document.getElementById('formAction').value = "update";
            document.getElementById('s_id').value = data.supplier_id;
            document.getElementById('in_name').value = data.name;
            document.getElementById('in_contact').value = data.contact_person;
            document.getElementById('in_phone').value = data.phone;
            document.getElementById('in_email').value = data.email;
            document.getElementById('in_gst').value = data.gstin;
            document.getElementById('in_addr').value = data.address;
            document.getElementById('in_status').value = data.status;

            openDrawer();
        } catch (e) {
            notify("Communication Error", "error");
        }
    }

    // Switch between Tabs inside Modal
    function switchTab(tabName) {
        // Buttons
        document.querySelectorAll('.view-tab-btn').forEach(btn => {
            btn.classList.replace('border-orange-500', 'border-transparent');
            btn.classList.replace('text-slate-900', 'text-slate-400');
        });
        document.getElementById(`tab-${tabName}-btn`).classList.replace('border-transparent', 'border-orange-500');
        document.getElementById(`tab-${tabName}-btn`).classList.replace('text-slate-400', 'text-slate-900');

        // Content
        document.querySelectorAll('.view-tab-content').forEach(content => content.classList.add('hidden'));
        document.getElementById(`tab-${tabName}`).classList.remove('hidden');
    }

    async function fetchAndShowView(id) {
        try {
            notify("Opening Ledger...", "warning");
            const response = await fetch(`process_supplier.php?fetch_id=${id}`);
            const data = await response.json();

            // Fill Profile Data
            document.getElementById('v_name').innerText = data.name;
            document.getElementById('v_contact').innerText = data.contact_person || 'N/A';
            document.getElementById('v_email').innerText = data.email || 'N/A';
            document.getElementById('v_phone').innerText = data.phone || 'N/A';
            document.getElementById('v_address').innerText = `${data.address}, ${data.city}`;
            document.getElementById('v_outstanding').innerText = '₹' + new Intl.NumberFormat('en-IN').format(data.outstanding);

            // Fill Purchase History Tab
            const historyBox = document.getElementById('tab-history');
            historyBox.innerHTML = ''; // Clear previous

            if (data.purchases && data.purchases.length > 0) {
                data.purchases.forEach(po => {
                    const statusColor = po.status === 'received' ? 'bg-emerald-50 text-emerald-600' : 'bg-orange-50 text-orange-600';
                    historyBox.innerHTML += `
                    <div class="p-4 bg-slate-50 rounded-2xl flex justify-between items-center border border-transparent hover:border-slate-200 transition-all">
                        <div>
                            <p class="text-[10px] font-black text-slate-900 uppercase italic">#PO-${po.id}</p>
                            <p class="text-[9px] text-slate-400 font-bold">${po.date}</p>
                        </div>
                        <p class="text-xs font-black text-slate-900">₹${new Intl.NumberFormat('en-IN').format(po.amount)}</p>
                        <span class="text-[8px] font-black ${statusColor} px-2 py-0.5 rounded uppercase tracking-tighter">${po.status}</span>
                    </div>`;
                });
            } else {
                historyBox.innerHTML = '<p class="text-center py-10 text-xs font-bold text-slate-300 uppercase tracking-widest">No transaction history found</p>';
            }

            switchTab('history'); // Reset to first tab
            openViewModal();
        } catch (e) {
            notify("Failed to load supplier records", "error");
        }
    }
    // FIXED DELETE LOGIC: Prevents navigation issues
    function confirmDeletion(id) {
        if (confirm('Are you sure? This vendor and their history will be archived.')) {
            window.location.href = `process_supplier.php?delete_id=${id}`;
        }
    }
</script>


</body>

</html>