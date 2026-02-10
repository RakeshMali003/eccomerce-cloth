<?php
$base_path = __DIR__ . '/../../';
require_once $base_path . 'config/database.php';

include $base_path . 'includes/admin-header.php';
include $base_path . 'includes/sidebar.php';
include $base_path . 'includes/notifications.php';

// Fetch suppliers, but prioritize those with an active balance first
$suppliers = $pdo->query("
    SELECT s.supplier_id, s.name, COALESCE(SUM(b.balance_amount), 0) as total_debt 
    FROM suppliers s
    LEFT JOIN supplier_bills b ON s.supplier_id = b.supplier_id AND b.payment_status != 'Paid'
    WHERE s.status = 1 
    GROUP BY s.supplier_id 
    ORDER BY total_debt DESC, s.name ASC
")->fetchAll();

// 1. Total Payable (The grand total of all remaining balances)
$totalPayable = $pdo->query("SELECT SUM(balance_amount) FROM supplier_bills WHERE payment_status != 'Paid'")->fetchColumn() ?: 0;

// 2. Pending Bills (Count of invoices not yet fully settled)
$pendingCount = $pdo->query("SELECT COUNT(*) FROM supplier_bills WHERE payment_status != 'Paid'")->fetchColumn() ?: 0;

// 3. Paid This Month (Cash flow out for the current month)
$paidThisMonth = $pdo->query("SELECT SUM(amount) FROM supplier_payments WHERE status = 'Cleared' AND MONTH(payment_date) = MONTH(CURRENT_DATE()) AND YEAR(payment_date) = YEAR(CURRENT_DATE())")->fetchColumn() ?: 0;

// 4. Upcoming Payments (Bills due in the next 7 days)
$upcomingDues = $pdo->query("SELECT COUNT(*) FROM supplier_bills WHERE payment_status != 'Paid' AND due_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)")->fetchColumn() ?: 0;

// 5. Recent Transactions for the Activity Feed
$recentActivities = $pdo->query("
    (SELECT bill_date as date, CONCAT('New Bill from ', s.name) as msg, total_amount as amt, 'bill' as type 
     FROM supplier_bills b JOIN suppliers s ON b.supplier_id = s.supplier_id)
    UNION ALL
    (SELECT payment_date as date, CONCAT('Payment to ', s.name) as msg, amount as amt, 'pay' as type 
     FROM supplier_payments p JOIN suppliers s ON p.supplier_id = s.supplier_id)
    ORDER BY date DESC LIMIT 5")->fetchAll();
?>
<main class="p-6 lg:p-12 bg-[#f8fafc] min-h-screen">
    <div class="mb-10">
        <h2 class="text-4xl font-black tracking-tighter text-slate-900">Financial Pulse<span
                class="text-orange-600">.</span></h2>
        <p class="text-slate-400 text-sm font-medium">Real-time summary of supplier liabilities and payments.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-12">

        <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Payable</p>
            <h3 class="text-3xl font-black text-slate-900">₹<?= number_format($totalPayable, 2) ?></h3>
            <div class="mt-4 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-red-500"></span>
                <span class="text-[9px] font-bold text-slate-400 uppercase">Current Liability</span>
            </div>
        </div>

        <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm border-b-4 border-b-orange-500">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Unsettled Bills</p>
            <h3 class="text-3xl font-black text-orange-600"><?= $pendingCount ?> <span class="text-sm">Invoices</span>
            </h3>
        </div>

        <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Paid (Dec 2025)</p>
            <h3 class="text-3xl font-black text-emerald-600">₹<?= number_format($paidThisMonth, 2) ?></h3>
        </div>

        <div class="bg-slate-900 p-8 rounded-[2.5rem] shadow-2xl">
            <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Due in 7 Days</p>
            <h3 class="text-3xl font-black text-white"><?= str_pad($upcomingDues, 2, '0', STR_PAD_LEFT) ?> <span
                    class="text-sm text-orange-500">Alerts</span></h3>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <div class="lg:col-span-2 bg-white rounded-[3rem] p-10 border border-slate-100 shadow-sm">
            <h4 class="text-lg font-black text-slate-900 mb-8 tracking-tight">Recent Financial Activity</h4>
            <div class="space-y-6">
                <?php foreach ($recentActivities as $act): ?>
                    <div
                        class="flex items-center justify-between p-4 rounded-2xl bg-slate-50/50 border border-transparent hover:border-slate-100 transition-all">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-10 h-10 rounded-xl <?= $act['type'] == 'bill' ? 'bg-orange-100 text-orange-600' : 'bg-emerald-100 text-emerald-600' ?> flex items-center justify-center">
                                <i
                                    class="fas <?= $act['type'] == 'bill' ? 'fa-file-invoice-dollar' : 'fa-check-double' ?> text-xs"></i>
                            </div>
                            <div>
                                <p class="text-xs font-black text-slate-900"><?= $act['msg'] ?></p>
                                <p class="text-[9px] font-bold text-slate-400 uppercase">
                                    <?= date('d M, H:i', strtotime($act['date'])) ?>
                                </p>
                            </div>
                        </div>
                        <p class="text-sm font-black <?= $act['type'] == 'bill' ? 'text-slate-900' : 'text-emerald-600' ?>">
                            <?= $act['type'] == 'bill' ? '+' : '-' ?> ₹<?= number_format($act['amt'], 2) ?>
                        </p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="bg-slate-900 rounded-[3rem] p-10 shadow-2xl text-white">
            <h4 class="text-lg font-black mb-8 tracking-tight">Quick Operations</h4>
            <div class="grid grid-cols-1 gap-4">
                <a href="Purchases/Purchase Bill.php"
                    class="flex items-center justify-between p-5 bg-white/5 rounded-2xl hover:bg-orange-600 transition-all group">
                    <span class="text-[10px] font-black uppercase tracking-widest">New Purchase Bill</span>
                    <i class="fas fa-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
                </a>
                <a href="Payments/Make Payment.php"
                    class="flex items-center justify-between p-5 bg-white/5 rounded-2xl hover:bg-emerald-600 transition-all group">
                    <span class="text-[10px] font-black uppercase tracking-widest">Record Payment</span>
                    <i class="fas fa-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
                </a>
                <a href="reports/Pending Bills.php"
                    class="flex items-center justify-between p-5 bg-white/5 rounded-2xl hover:bg-blue-600 transition-all group">
                    <span class="text-[10px] font-black uppercase tracking-widest">View Aging Report</span>
                    <i class="fas fa-arrow-right text-xs group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 bg-white p-10 rounded-[3rem] shadow-sm border border-slate-100">
            <h4 class="text-xl font-black mb-6 tracking-tight">Post Payment</h4>

            <form action="process_payment.php" method="POST" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="relative">
                        <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Search Supplier</label>
                        <input type="text" id="supplier_search" placeholder="Type name..."
                            class="w-full bg-slate-50 p-4 rounded-2xl font-bold outline-none border-2 border-transparent focus:border-orange-500">

                        <div id="search_results"
                            class="absolute z-50 w-full bg-white shadow-2xl rounded-2xl mt-2 max-h-60 overflow-y-auto hidden border border-slate-100">
                            <?php foreach ($suppliers as $s): ?>
                                <div class="p-4 hover:bg-slate-50 cursor-pointer border-b border-slate-50 supplier-opt"
                                    data-id="<?= $s['supplier_id'] ?>" data-name="<?= $s['name'] ?>">
                                    <p class="text-sm font-black"><?= $s['name'] ?></p>
                                    <p class="text-[9px] text-red-500 font-bold uppercase tracking-widest">Owed:
                                        ₹<?= number_format($s['total_debt'], 2) ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <input type="hidden" name="supplier_id" id="selected_supplier_id">
                    </div>

                    <div>
                        <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Select Invoice</label>
                        <select name="bill_id" id="pay_bill" required
                            class="w-full bg-slate-50 p-4 rounded-2xl font-bold outline-none appearance-none">
                            <option value="">Search Supplier First</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="space-y-1">
                        <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Amount</label>
                        <input type="number" name="amount" placeholder="₹ 0.00" required
                            class="w-full bg-slate-50 p-4 rounded-2xl font-bold outline-none">
                    </div>

                    <div class="space-y-1">
                        <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Mode</label>
                        <select name="payment_mode" onchange="toggleRefFields(this.value)"
                            class="w-full bg-slate-50 p-4 rounded-2xl font-bold outline-none">
                            <option value="Cash">Cash</option>
                            <option value="Online">Online / UPI</option>
                            <option value="Cheque">Cheque</option>
                        </select>
                    </div>

                    <div class="space-y-1">
                        <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Reference</label>
                        <input type="text" name="transaction_id" id="ref_field" placeholder="ID / Cheque #"
                            class="w-full bg-slate-50 p-4 rounded-2xl font-bold outline-none">
                    </div>
                </div>

                <button type="submit"
                    class="w-full bg-slate-900 text-white py-6 rounded-[2rem] font-black uppercase tracking-widest hover:bg-emerald-600 transition-all shadow-xl shadow-slate-200">
                    Synchronize Payment
                </button>
            </form>
        </div>

        <div class="bg-slate-900 rounded-[3rem] p-10 text-white shadow-2xl">
            <h4 class="text-lg font-black mb-6 tracking-tight text-orange-500">Urgent Dues</h4>
            <div class="space-y-4">
                <?php
                // Fetch top 5 most urgent unpaid bills
                $urgent = $pdo->query("SELECT b.*, s.name FROM supplier_bills b JOIN suppliers s ON b.supplier_id = s.supplier_id WHERE b.balance_amount > 0 ORDER BY b.due_date ASC LIMIT 5")->fetchAll();
                foreach ($urgent as $u):
                    ?>
                    <div class="p-4 bg-white/5 rounded-2xl border border-white/10 cursor-pointer hover:bg-white/10 transition-all"
                        onclick="quickSelect('<?= $u['supplier_id'] ?>', '<?= $u['name'] ?>')">
                        <div class="flex justify-between items-start mb-2">
                            <p class="text-xs font-black"><?= $u['name'] ?></p>
                            <span class="text-[8px] bg-red-500 text-white px-2 py-0.5 rounded-full font-bold">Due</span>
                        </div>
                        <div class="flex justify-between items-end">
                            <p class="text-[10px] text-slate-500">INV: <?= $u['bill_number'] ?></p>
                            <p class="text-sm font-black text-orange-500">₹<?= number_format($u['balance_amount']) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <script>
        function toggleRefFields(val) {
            const field = document.getElementById('ref_field');
            field.classList.toggle('hidden', val === 'Cash');
        }

        async function fetchPendingBills(supplierId) {
            if (!supplierId) {
                document.getElementById('pay_bill').innerHTML = '<option value="">Choose Supplier First</option>';
                return;
            }

            try {
                // Change this path if the file is in another folder, e.g., '../suppliers/get_pending_bills.php'
                const response = await fetch(`get_pending_bills.php?supplier_id=${supplierId}`);

                if (!response.ok) throw new Error('Network response was not ok');

                const bills = await response.json();
                const select = document.getElementById('pay_bill');

                if (bills.length === 0) {
                    select.innerHTML = '<option value="">No Pending Bills Found</option>';
                } else {
                    select.innerHTML = bills.map(b =>
                        `<option value="${b.bill_id}">${b.bill_number} (Bal: ₹${parseFloat(b.balance_amount).toLocaleString('en-IN')})</option>`
                    ).join('');
                }
            } catch (error) {
                console.error("Fetch error:", error);
                notify("Could not load bills. Check console.", "error");
            }
        }

        // 1. Search Functionality
        const supplierInput = document.getElementById('supplier_search');
        const resultsBox = document.getElementById('search_results');
        const options = document.querySelectorAll('.supplier-opt');

        supplierInput.addEventListener('input', function () {
            const val = this.value.toLowerCase();
            resultsBox.classList.remove('hidden');
            let count = 0;

            options.forEach(opt => {
                const text = opt.getAttribute('data-name').toLowerCase();
                if (text.includes(val)) {
                    opt.classList.remove('hidden');
                    count++;
                } else {
                    opt.classList.add('hidden');
                }
            });
            if (val === "") resultsBox.classList.add('hidden');
        });

        // 2. Selection Functionality
        document.querySelectorAll('.supplier-opt').forEach(opt => {
            opt.addEventListener('click', function () {
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');
                quickSelect(id, name);
            });
        });

        function quickSelect(id, name) {
            document.getElementById('selected_supplier_id').value = id;
            document.getElementById('supplier_search').value = name;
            document.getElementById('search_results').classList.add('hidden');
            fetchPendingBills(id); // Your existing function
            notify("Vendor Selected: " + name, "success");
        }

        // 3. Close search when clicking outside
        document.addEventListener('click', (e) => {
            if (!e.target.closest('#supplier_search')) resultsBox.classList.add('hidden');
        });
    </script>
</main>
<?php include $base_path . 'includes/admin-footer.php'; ?>