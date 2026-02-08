<?php
$base_path = $_SERVER['DOCUMENT_ROOT'] . '/ecommerce-website/';
require_once "../../config/database.php";

include $base_path . 'includes/admin-header.php';
include $base_path . 'includes/sidebar.php';
include $base_path . 'includes/notifications.php';

// Fetch current deals
$stmt = $pdo->query("SELECT * FROM discounts ORDER BY created_at DESC");
$discounts = $stmt->fetchAll();
?>

<main class="p-6 lg:p-12 bg-[#f8fafc] min-h-screen">
    <div class="mb-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
        <div>
            <h2 class="text-3xl font-black tracking-tighter text-slate-900">Discount Engines<span
                    class="text-indigo-600">.</span></h2>
            <p class="text-slate-400 text-sm font-medium">Create and manage viral promo codes and seasonal offers.</p>
        </div>

        <button onclick="openPromoModal('add')"
            class="bg-slate-900 text-white px-8 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-indigo-600 transition-all shadow-xl shadow-slate-200">
            + New Promo Code
        </button>
    </div>

    <div class="bg-white rounded-[3rem] border border-slate-100 shadow-sm overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-slate-50 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                <tr>
                    <th class="px-8 py-6">Campaign Code</th>
                    <th class="px-6 py-6 text-center">Value</th>
                    <th class="px-6 py-6 text-center">Validity Period</th>
                    <th class="px-6 py-6 text-center">Uses Left</th>
                    <th class="px-6 py-6 text-center">Status</th>
                    <th class="px-8 py-6 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php foreach ($discounts as $d):
                    $isExpired = strtotime($d['end_date']) < time();
                    $usageExceeded = $d['usage_limit'] <= 0;
                    ?>
                    <tr class="hover:bg-slate-50 transition-all group">
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-500 flex items-center justify-center text-xs">
                                    <i class="fas fa-tag"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-black text-slate-900 tracking-widest">
                                        <?= htmlspecialchars($d['code']) ?>
                                    </p>
                                    <p class="text-[9px] text-slate-400 font-bold uppercase">
                                        <?= $d['type'] ?>
                                    </p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <p class="text-sm font-black text-slate-900">
                                <?= $d['type'] == 'percentage' ? $d['value'] . '%' : '₹' . number_format($d['value'], 2) ?>
                            </p>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <p class="text-[10px] font-bold text-slate-600">
                                <?= date('d M', strtotime($d['start_date'])) ?> -
                                <?= date('d M, Y', strtotime($d['end_date'])) ?>
                            </p>
                            <?php if ($isExpired): ?>
                                <span class="text-[8px] font-black text-red-500 uppercase">Expired</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-5 text-center text-xs font-bold text-slate-900">
                            <?= $d['usage_limit'] ?> Slots
                        </td>
                        <td class="px-6 py-5 text-center">
                            <span
                                class="px-3 py-1 rounded-lg text-[9px] font-black uppercase <?= ($d['status'] == 'active' && !$isExpired) ? 'bg-emerald-100 text-emerald-600' : 'bg-slate-100 text-slate-400' ?>">
                                <?= $d['status'] ?>
                            </span>
                        </td>
                        <td class="px-8 py-5 text-right">
                            <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-all">
                                <button
                                    onclick='openPromoModal("edit", <?= htmlspecialchars(json_encode($d), ENT_QUOTES, "UTF-8") ?>)'
                                    class="w-9 h-9 rounded-lg bg-slate-50 text-slate-400 hover:text-slate-900 transition-all">
                                    <i class="fas fa-pencil text-xs"></i>
                                </button>
                                <button onclick="deletePromo(<?= $d['discount_id'] ?>)"
                                    class="w-9 h-9 rounded-lg bg-red-50 text-red-300 hover:text-red-500 transition-all">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($discounts)): ?>
                    <tr>
                        <td colspan="6" class="px-8 py-20 text-center">
                            <i class="fas fa-ticket-alt text-slate-100 text-6xl mb-4"></i>
                            <p class="text-sm font-black text-slate-300 uppercase tracking-widest">No promo campaigns
                                configured</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

<!-- Action Modal -->
<div id="promoModal" class="fixed inset-0 z-[110] hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closePromoModal()"></div>
    <form id="promoForm" action="process_promo.php" method="POST"
        class="bg-white w-full max-w-xl rounded-[3rem] shadow-2xl relative z-10 overflow-hidden transform transition-all">
        <input type="hidden" name="action" id="formAction" value="add">
        <input type="hidden" name="discount_id" id="promoId">

        <div class="p-8 border-b border-slate-50 flex justify-between items-center bg-slate-50/30">
            <div>
                <h3 id="modalTitle" class="text-xl font-black text-slate-900 uppercase tracking-tighter">Issue New Promo
                </h3>
            </div>
            <button type="button" onclick="closePromoModal()"
                class="w-10 h-10 rounded-xl bg-white text-slate-300 hover:text-red-500 transition-all shadow-sm">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="p-10 space-y-6">
            <div class="space-y-2">
                <label class="text-[10px] font-black uppercase text-slate-400 ml-4">Promo Code (Unique)</label>
                <input type="text" name="code" id="p_code" required placeholder="Ex: WINTER50"
                    class="input-premium w-full text-indigo-600 font-black tracking-widest p-5 rounded-[2rem] bg-slate-50 border-2 border-transparent focus:border-indigo-500 outline-none uppercase">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase text-slate-400 ml-4">Discount Type</label>
                    <select name="type" id="p_type" required
                        class="w-full bg-slate-50 px-6 py-5 rounded-[2rem] text-sm font-black outline-none border-2 border-transparent focus:border-indigo-500 appearance-none">
                        <option value="percentage">Percentage (%)</option>
                        <option value="fixed">Fixed Amount (₹)</option>
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase text-slate-400 ml-4">Value</label>
                    <input type="number" name="value" id="p_value" step="0.01" required placeholder="0.00"
                        class="w-full bg-slate-50 px-6 py-5 rounded-[2rem] text-sm font-black outline-none border-2 border-transparent focus:border-indigo-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase text-slate-400 ml-4">Valid From</label>
                    <input type="date" name="start_date" id="p_start" required
                        class="w-full bg-slate-50 px-6 py-5 rounded-[2rem] text-sm font-black outline-none border-2 border-transparent focus:border-indigo-500">
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase text-slate-400 ml-4">Valid Until</label>
                    <input type="date" name="end_date" id="p_end" required
                        class="w-full bg-slate-50 px-6 py-5 rounded-[2rem] text-sm font-black outline-none border-2 border-transparent focus:border-indigo-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase text-slate-400 ml-4">Usage Limit</label>
                    <input type="number" name="usage_limit" id="p_limit" value="100"
                        class="w-full bg-slate-50 px-6 py-5 rounded-[2rem] text-sm font-black outline-none border-2 border-transparent focus:border-indigo-500">
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase text-slate-400 ml-4">Initial Status</label>
                    <select name="status" id="p_status"
                        class="w-full bg-slate-50 px-6 py-5 rounded-[2rem] text-sm font-black outline-none border-2 border-transparent focus:border-indigo-500 appearance-none">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>

            <button type="submit"
                class="w-full bg-slate-900 text-white py-6 rounded-[2rem] font-black uppercase tracking-[0.2em] shadow-xl hover:bg-indigo-600 transition-all mt-4">
                Launch Campaign
            </button>
        </div>
    </form>
</div>

<script>
    function openPromoModal(mode, data = null) {
        const modal = document.getElementById('promoModal');
        const form = document.getElementById('promoForm');
        const title = document.getElementById('modalTitle');

        form.reset();
        document.getElementById('promoId').value = "";

        if (mode === 'edit' && data) {
            title.innerText = "Refine Campaign";
            document.getElementById('formAction').value = "edit";
            document.getElementById('promoId').value = data.discount_id;

            document.getElementById('p_code').value = data.code;
            document.getElementById('p_type').value = data.type;
            document.getElementById('p_value').value = data.value;
            document.getElementById('p_start').value = data.start_date.split(' ')[0];
            document.getElementById('p_end').value = data.end_date.split(' ')[0];
            document.getElementById('p_limit').value = data.usage_limit;
            document.getElementById('p_status').value = data.status;
        } else {
            title.innerText = "Issue New Promo";
            document.getElementById('formAction').value = "add";
        }

        modal.classList.replace('hidden', 'flex');
    }

    function closePromoModal() {
        document.getElementById('promoModal').classList.replace('flex', 'hidden');
    }

    function deletePromo(id) {
        if (confirm("Exclude this promo campaign permanently?")) {
            window.location.href = `process_promo.php?action=delete&id=${id}`;
        }
    }
</script>
<?php include $base_path . 'includes/admin-footer.php'; ?>
</body>

</html>