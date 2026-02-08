<?php
$base_path = $_SERVER['DOCUMENT_ROOT'] . '/ecommerce-website/';
require_once $base_path . 'config/database.php';

include $base_path . 'includes/admin-header.php';
include $base_path . 'includes/sidebar.php';
include $base_path . 'includes/notifications.php';

// Fetch Pending and Partially Paid Bills
$sql = "SELECT b.*, s.name as supplier_name 
        FROM supplier_bills b 
        JOIN suppliers s ON b.supplier_id = s.supplier_id 
        WHERE b.payment_status NOT IN ('PAID', 'Paid') 
        ORDER BY b.due_date ASC";
$pending = $pdo->query($sql)->fetchAll();

// KPI Calculations
$total_due = array_sum(array_column($pending, 'balance_amount'));
$overdue_sum = 0;
foreach ($pending as $item) {
    if (strtotime($item['due_date']) < time()) {
        $overdue_sum += $item['balance_amount'];
    }
}
?>

<main class="p-6 lg:p-12 bg-[#f8fafc] min-h-screen">
    <div class="mb-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
        <div>
            <h2 class="text-3xl font-black tracking-tighter text-slate-900">Finance: Supplier Dues<span
                    class="text-emerald-600">.</span></h2>
            <p class="text-slate-400 text-sm font-medium">Clear your liabilities and maintain good vendor credit scores.
            </p>
        </div>
        <div class="flex gap-3">
            <a href="../Payments/Payment History.php"
                class="bg-white border border-slate-200 px-6 py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-50 transition-all shadow-sm">
                <i class="fas fa-history mr-2"></i> Payment History
            </a>
        </div>
    </div>

    <!-- Financial KPIs -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">
        <div class="bg-white p-8 rounded-[3rem] border border-slate-100 shadow-sm relative overflow-hidden">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-500/5 rounded-full blur-2xl"></div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Outstanding</p>
            <h3 class="text-2xl font-black text-slate-900">₹
                <?= number_format($total_due, 2) ?>
            </h3>
            <p class="text-[9px] text-emerald-600 font-bold uppercase mt-2">Active Credit Line</p>
        </div>
        <div
            class="bg-white p-8 rounded-[3rem] border-2 border-red-100 shadow-xl shadow-red-50/50 relative overflow-hidden">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-red-500/5 rounded-full blur-2xl"></div>
            <p class="text-[10px] font-black text-red-400 uppercase tracking-widest mb-1">Critical Overdue</p>
            <h3 class="text-2xl font-black text-red-600">₹
                <?= number_format($overdue_sum, 2) ?>
            </h3>
            <p class="text-[9px] text-red-500 font-black uppercase mt-2 animate-pulse">Pay immediately to avoid interest
            </p>
        </div>
        <div class="bg-slate-900 p-8 rounded-[3rem] text-white shadow-xl shadow-slate-200 lg:block hidden">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Vendor Trust Score</p>
            <h3 class="text-2xl font-black">94<span class="text-slate-500">/100</span></h3>
            <div class="mt-4 w-full bg-slate-800 h-1.5 rounded-full overflow-hidden">
                <div class="bg-emerald-500 h-full w-[94%]"></div>
            </div>
        </div>
    </div>

    <!-- Dues Table -->
    <div class="bg-white rounded-[3rem] border border-slate-100 shadow-sm overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-slate-50 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                <tr>
                    <th class="px-8 py-6">Bill & Supplier</th>
                    <th class="px-6 py-6 text-center">Payment Cycle</th>
                    <th class="px-6 py-6 text-right">Dues Left</th>
                    <th class="px-6 py-6 text-center">Liability Status</th>
                    <th class="px-8 py-6 text-right">Settlement</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php foreach ($pending as $b):
                    $due_ts = strtotime($b['due_date']);
                    $is_overdue = $due_ts < time();
                    $days_left = ceil(($due_ts - time()) / 86400);
                    ?>
                    <tr class="hover:bg-slate-50 transition-all group">
                        <td class="px-8 py-5">
                            <p class="text-sm font-black text-slate-900 italic">
                                <?= htmlspecialchars($b['supplier_name']) ?>
                            </p>
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1">Ref: #
                                <?= $b['bill_number'] ?>
                            </p>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <p
                                class="text-xs font-bold <?= $is_overdue ? 'text-red-600 underline decoration-2' : 'text-slate-600' ?>">
                                <?= date('d M, Y', $due_ts) ?>
                            </p>
                            <?php if ($is_overdue): ?>
                                <span
                                    class="text-[8px] font-black text-red-500 uppercase bg-red-50 px-2 py-0.5 rounded ml-1">Overdue</span>
                            <?php else: ?>
                                <span class="text-[8px] font-black text-slate-400 uppercase">In
                                    <?= $days_left ?> Days
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-5 text-right">
                            <p class="text-sm font-black text-slate-900">₹
                                <?= number_format($b['balance_amount'], 2) ?>
                            </p>
                            <p class="text-[9px] text-slate-300 font-bold">Total: ₹
                                <?= number_format($b['total_amount'], 2) ?>
                            </p>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <?php
                            $status = strtoupper($b['payment_status']);
                            $status_class = match ($status) {
                                'PARTIALLY_PAID', 'PARTIAL' => 'bg-blue-100 text-blue-700',
                                default => 'bg-amber-100 text-amber-700'
                            };
                            ?>
                            <span class="px-4 py-1.5 rounded-xl text-[9px] font-black uppercase <?= $status_class ?>">
                                <?= str_replace('_', ' ', $status) ?>
                            </span>
                        </td>
                        <td class="px-8 py-5 text-right">
                            <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-all">
                                <button onclick="markAsPaid(<?= $b['bill_id'] ?>)"
                                    class="bg-emerald-600 text-white px-5 py-2.5 rounded-xl text-[9px] font-black uppercase tracking-widest hover:bg-slate-900 transition-all shadow-lg shadow-emerald-50">
                                    Release Payment
                                </button>
                                <button
                                    onclick='openBillModal(<?= htmlspecialchars(json_encode($b), ENT_QUOTES, "UTF-8") ?>)'
                                    class="w-10 h-10 flex items-center justify-center rounded-xl bg-slate-50 text-slate-300 hover:text-slate-900 transition-all border border-slate-100">
                                    <i class="fas fa-file-invoice text-xs"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if (empty($pending)): ?>
                    <tr>
                        <td colspan="5" class="p-20 text-center">
                            <i class="fas fa-check-double text-emerald-100 text-6xl mb-4"></i>
                            <p class="text-sm font-black text-slate-300 uppercase tracking-widest">Excellent! Zero
                                outstanding liabilities.</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

<!-- Bill View Modal -->
<div id="billViewModal" class="fixed inset-0 z-[150] hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeBillModal()"></div>
    <div class="bg-white w-full max-w-xl rounded-[3rem] shadow-2xl relative z-10 overflow-hidden">
        <div class="p-8 border-b border-slate-50 flex justify-between items-center bg-slate-50/30">
            <div>
                <h3 class="text-xl font-black text-slate-900 uppercase tracking-tighter italic">Bill Dossier</h3>
                <p id="modal_bill_no" class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-0.5">
                </p>
            </div>
            <button onclick="closeBillModal()"
                class="w-10 h-10 rounded-xl bg-white text-slate-200 hover:text-red-500 transition-all shadow-sm border border-slate-50">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="p-10 space-y-8">
            <div class="grid grid-cols-2 gap-8">
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase mb-1">Issuing Supplier</p>
                    <p id="modal_supplier" class="text-lg font-black text-slate-900"></p>
                </div>
                <div class="text-right">
                    <p class="text-[10px] font-black text-slate-400 uppercase mb-1">Balance Due</p>
                    <p id="modal_amount" class="text-3xl font-black text-emerald-600"></p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 border-y border-slate-50 py-8">
                <div>
                    <label class="text-[9px] font-black text-slate-300 uppercase block mb-1">Effective Date</label>
                    <p id="modal_created" class="text-xs font-black text-slate-900"></p>
                </div>
                <div class="text-right">
                    <label class="text-[9px] font-black text-slate-300 uppercase block mb-1">Liability Deadline</label>
                    <p id="modal_due" class="text-xs font-black text-slate-900"></p>
                </div>
            </div>

            <div class="flex gap-4">
                <button id="modal_pay_btn"
                    class="flex-[2] bg-slate-900 text-white py-5 rounded-[2rem] font-black uppercase tracking-[0.2em] hover:bg-emerald-600 transition-all shadow-xl shadow-slate-200">
                    Settle Account Now
                </button>
                <button onclick="closeBillModal()"
                    class="flex-1 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-900 transition-all">
                    Discard
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function openBillModal(bill) {
        document.getElementById('modal_bill_no').innerText = "Voucher Registry: #" + bill.bill_number;
        document.getElementById('modal_supplier').innerText = bill.supplier_name;
        document.getElementById('modal_amount').innerText = "₹" + parseFloat(bill.balance_amount).toLocaleString('en-IN', { minimumFractionDigits: 2 });
        document.getElementById('modal_due').innerText = new Date(bill.due_date).toLocaleDateString();
        document.getElementById('modal_created').innerText = new Date(bill.bill_date).toLocaleDateString();

        document.getElementById('modal_pay_btn').onclick = function () { markAsPaid(bill.bill_id); };

        const modal = document.getElementById('billViewModal');
        modal.classList.replace('hidden', 'flex');
    }

    function closeBillModal() {
        document.getElementById('billViewModal').classList.replace('flex', 'hidden');
    }

    function markAsPaid(billId) {
        if (confirm("Initiate financial settlement for this bill?")) {
            window.location.href = `../Payments/Make Payment.php?bill_id=${billId}`;
        }
    }
</script>
<?php include $base_path . 'includes/admin-footer.php'; ?>