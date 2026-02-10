<?php
$base_path = __DIR__ . '/../../';
require_once $base_path . 'config/database.php';

include $base_path . 'includes/sidebar.php';
include $base_path . 'includes/notifications.php';

// Fetch Pending and Partially Paid Bills
$sql = "SELECT b.*, s.name as supplier_name 
        FROM supplier_bills b 
        JOIN suppliers s ON b.supplier_id = s.supplier_id 
        WHERE b.payment_status NOT IN ('PAID', 'Paid') 
        ORDER BY b.due_date ASC";
$pending = $pdo->query($sql)->fetchAll();
?>

<main class="p-6 lg:p-12">
    <div class="mb-10">
        <h2 class="text-3xl font-black tracking-tighter text-slate-900">Accounts Payable<span
                class="text-orange-600">.</span></h2>
        <p class="text-slate-400 text-sm font-medium">Manage your outstanding debts and upcoming supplier payments.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Outstanding</p>
            <h3 class="text-2xl font-black text-slate-900">
                ₹<?= number_format(array_sum(array_column($pending, 'balance_amount')), 2) ?></h3>
        </div>
        <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm border-l-4 border-l-red-500">
            <p class="text-[10px] font-black text-red-400 uppercase tracking-widest mb-1">Overdue Amount</p>
            <?php
            $overdue_sum = 0;
            foreach ($pending as $item)
                if (strtotime($item['due_date']) < time())
                    $overdue_sum += $item['balance_amount'];
            ?>
            <h3 class="text-2xl font-black text-red-600">₹<?= number_format($overdue_sum, 2) ?></h3>
        </div>
    </div>

    <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="bg-slate-50 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                <tr>
                    <th class="px-8 py-6">Bill / Supplier</th>
                    <th class="px-6 py-6 text-center">Due Date</th>
                    <th class="px-6 py-6 text-right">Amount</th>
                    <th class="px-6 py-6 text-center">Status</th>
                    <th class="px-8 py-6 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php foreach ($pending as $b):
                    $is_overdue = strtotime($b['due_date']) < time();
                    ?>
                    <tr class="hover:bg-slate-50 transition-all <?= $is_overdue ? 'bg-red-50/30' : '' ?>">
                        <td class="px-8 py-5">
                            <p class="text-sm font-black text-slate-900"><?= htmlspecialchars($b['supplier_name']) ?></p>
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-tight">Bill
                                #<?= $b['bill_number'] ?></p>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <span class="text-xs font-bold <?= $is_overdue ? 'text-red-600' : 'text-slate-600' ?>">
                                <?= date('d M, Y', strtotime($b['due_date'])) ?>
                            </span>
                            <?php if ($is_overdue): ?>
                                <div class="text-[8px] font-black text-red-500 uppercase mt-1">⚠️ Overdue</div>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-5 text-right font-black text-slate-900">
                            ₹<?= number_format($b['total_amount'], 2) ?>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <?php
                            $status = strtoupper($b['payment_status']);
                            $status_class = match ($status) {
                                'PAID' => 'bg-emerald-100 text-emerald-700',
                                'PARTIALLY_PAID' => 'bg-blue-100 text-blue-700',
                                'PARTIAL' => 'bg-blue-100 text-blue-700',
                                default => 'bg-amber-100 text-amber-700'
                            };
                            ?>
                            <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase <?= $status_class ?>">
                                <?= str_replace('_', ' ', $status) ?>
                            </span>
                        </td>
                        <td class="px-8 py-5 text-right">
                            <div class="flex justify-end gap-2">
                                <button onclick="markAsPaid(<?= $b['bill_id'] ?>)"
                                    class="bg-slate-900 text-white px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-emerald-600 transition-all shadow-md">
                                    Mark Paid
                                </button>
                                <button
                                    onclick='openBillModal(<?= htmlspecialchars(json_encode($b), ENT_QUOTES, "UTF-8") ?>)'
                                    class="w-9 h-9 flex items-center justify-center rounded-xl bg-slate-100 text-slate-400 hover:text-slate-900 transition-all">
                                    <i class="fas fa-eye text-xs"></i>
                                </button>

                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if (empty($pending)): ?>
                    <tr>
                        <td colspan="5" class="p-20 text-center">
                            <i class="fas fa-check-circle text-emerald-100 text-6xl mb-4"></i>
                            <p class="text-slate-400 font-bold">Excellent! All supplier bills are clear.</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php
    // ... keep your existing PHP logic at the top ...
    ?>

    <div id="billViewModal" class="fixed inset-0 z-[150] hidden items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeBillModal()"></div>
        <div
            class="bg-white w-full max-w-2xl rounded-[3rem] shadow-2xl relative z-10 overflow-hidden transform transition-all">
            <div class="p-8 border-b border-slate-50 flex justify-between items-center bg-slate-50/30">
                <div>
                    <h3 class="text-xl font-black text-slate-900 uppercase tracking-tighter">Bill Details</h3>
                    <p id="modal_bill_no" class="text-[10px] font-black text-slate-400 uppercase tracking-widest"></p>
                </div>
                <button onclick="closeBillModal()"
                    class="w-10 h-10 rounded-xl bg-white text-slate-400 hover:text-red-500 transition-all shadow-sm">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="p-10 space-y-8">
                <div class="grid grid-cols-2 gap-8">
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase mb-1">Supplier</p>
                        <p id="modal_supplier" class="text-lg font-black text-slate-900"></p>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] font-black text-slate-400 uppercase mb-1">Total Amount</p>
                        <p id="modal_amount" class="text-2xl font-black text-emerald-600"></p>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4 border-y border-slate-50 py-6">
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase">Due Date</p>
                        <p id="modal_due" class="text-sm font-bold text-slate-700"></p>
                    </div>
                    <div class="text-center">
                        <p class="text-[10px] font-black text-slate-400 uppercase">Status</p>
                        <span id="modal_status"
                            class="px-3 py-1 rounded-full text-[9px] font-black uppercase bg-amber-100 text-amber-700"></span>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] font-black text-slate-400 uppercase">Created On</p>
                        <p id="modal_created" class="text-sm font-bold text-slate-700"></p>
                    </div>
                </div>

                <div class="flex gap-3">
                    <button id="modal_pay_btn"
                        class="flex-1 bg-slate-900 text-white py-4 rounded-2xl font-black uppercase tracking-widest hover:bg-emerald-600 transition-all">
                        Mark as Paid
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openBillModal(bill) {
            // Set Values
            document.getElementById('modal_bill_no').innerText = "Reference: #" + bill.bill_number;
            document.getElementById('modal_supplier').innerText = bill.supplier_name;
            document.getElementById('modal_amount').innerText = "₹" + parseFloat(bill.total_amount).toLocaleString('en-IN', { minimumFractionDigits: 2 });
            document.getElementById('modal_due').innerText = bill.due_date;
            document.getElementById('modal_status').innerText = bill.payment_status;
            document.getElementById('modal_created').innerText = bill.created_at;

            // Set Pay Button Action
            document.getElementById('modal_pay_btn').onclick = function () {
                markAsPaid(bill.bill_id);
            };

            // Show Modal
            const modal = document.getElementById('billViewModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeBillModal() {
            const modal = document.getElementById('billViewModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }



    </script>
</main>

<script>
    function markAsPaid(billId) {
        if (confirm("Confirm that this bill has been paid in full? This will update your accounts.")) {
            window.location.href = `Make Payment.php?bill_id=${billId}`;
        }
    }
</script>