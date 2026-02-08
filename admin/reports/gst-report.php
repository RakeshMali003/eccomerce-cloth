<?php
$base_path = $_SERVER['DOCUMENT_ROOT'] . '/ecommerce-website/';
require_once "../../config/database.php";

include $base_path . 'includes/admin-header.php';
include $base_path . 'includes/sidebar.php';
include $base_path . 'includes/notifications.php';

$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date = $_GET['end_date'] ?? date('Y-m-t');

$sql = "SELECT i.*, o.order_id, u.name as customer_name, u.gst_number
        FROM invoices i
        JOIN orders o ON i.order_id = o.order_id
        JOIN users u ON o.user_id = u.user_id
        WHERE i.invoice_date BETWEEN ? AND ?
        ORDER BY i.invoice_date DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute([$start_date, $end_date]);
$invoices = $stmt->fetchAll();

$totals = [
    'taxable' => 0,
    'gst' => 0,
    'total' => 0
];

foreach ($invoices as $inv) {
    $totals['taxable'] += $inv['subtotal'];
    $totals['gst'] += $inv['gst_total'];
    $totals['total'] += $inv['total_amount'];
}
?>

<main class="p-6 lg:p-12 bg-[#f8fafc] min-h-screen">
    <div class="mb-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
        <div>
            <h2 class="text-3xl font-black tracking-tighter text-slate-900">Tax Intelligence: GST Report<span
                    class="text-orange-600">.</span></h2>
            <p class="text-slate-400 text-sm font-medium">Summarized tax data for legal compliance and filing</p>
        </div>
        <div class="flex gap-3">
            <button onclick="window.print()"
                class="bg-white border border-slate-200 px-6 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-50 transition-all shadow-sm">
                <i class="fas fa-file-pdf mr-2"></i> Export PDF
            </button>
        </div>
    </div>

    <!-- Date Filter -->
    <div class="bg-white p-8 rounded-[3rem] border border-slate-100 shadow-sm mb-10">
        <form method="GET" class="flex flex-wrap items-end gap-6">
            <div class="space-y-2">
                <label class="text-[10px] font-black uppercase text-slate-400 ml-4">Start Period</label>
                <input type="date" name="start_date" value="<?= $start_date ?>"
                    class="bg-slate-50 px-6 py-4 rounded-2xl text-xs font-bold outline-none border-2 border-transparent focus:border-orange-500 transition-all">
            </div>
            <div class="space-y-2">
                <label class="text-[10px] font-black uppercase text-slate-400 ml-4">End Period</label>
                <input type="date" name="end_date" value="<?= $end_date ?>"
                    class="bg-slate-50 px-6 py-4 rounded-2xl text-xs font-bold outline-none border-2 border-transparent focus:border-orange-500 transition-all">
            </div>
            <button type="submit"
                class="bg-slate-900 text-white px-10 py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-orange-600 transition-all shadow-xl">
                Generate View
            </button>
        </form>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <div class="bg-white p-8 rounded-[3rem] border border-slate-100 shadow-sm">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Taxable Value</p>
            <h3 class="text-2xl font-black text-slate-900">₹
                <?= number_format($totals['taxable'], 2) ?>
            </h3>
        </div>
        <div class="bg-white p-8 rounded-[3rem] border border-slate-100 shadow-sm">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Total GST Collected (Output)
            </p>
            <h3 class="text-2xl font-black text-orange-600">₹
                <?= number_format($totals['gst'], 2) ?>
            </h3>
        </div>
        <div class="bg-white p-8 rounded-[3rem] border border-slate-900 shadow-xl shadow-slate-200 text-slate-900">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Gross Sales (Incl. Tax)</p>
            <h3 class="text-2xl font-black text-slate-900">₹
                <?= number_format($totals['total'], 2) ?>
            </h3>
        </div>
    </div>

    <!-- Detailed Table -->
    <div class="bg-white rounded-[3rem] border border-slate-100 shadow-sm overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-slate-50 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                <tr>
                    <th class="px-8 py-6">Invoice Date</th>
                    <th class="px-6 py-6">Invoice No.</th>
                    <th class="px-6 py-6">Customer / GSTIN</th>
                    <th class="px-6 py-6 text-right">Taxable Amt</th>
                    <th class="px-6 py-6 text-right">GST Total</th>
                    <th class="px-8 py-6 text-right">Total Payable</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php foreach ($invoices as $inv): ?>
                    <tr class="hover:bg-slate-50 transition-all">
                        <td class="px-8 py-5 text-xs font-bold text-slate-500">
                            <?= date('d M, Y', strtotime($inv['invoice_date'])) ?>
                        </td>
                        <td class="px-6 py-5 text-xs font-black text-slate-900">
                            <?= $inv['invoice_number'] ?>
                        </td>
                        <td class="px-6 py-5">
                            <p class="text-xs font-black text-slate-900">
                                <?= htmlspecialchars($inv['customer_name']) ?>
                            </p>
                            <p class="text-[9px] text-slate-400 font-bold italic">
                                <?= $inv['gst_number'] ?: 'Unregistered' ?>
                            </p>
                        </td>
                        <td class="px-6 py-5 text-right text-xs font-bold text-slate-500">₹
                            <?= number_format($inv['subtotal'], 2) ?>
                        </td>
                        <td class="px-6 py-5 text-right text-xs font-black text-orange-600">₹
                            <?= number_format($inv['gst_total'], 2) ?>
                        </td>
                        <td class="px-8 py-5 text-right text-sm font-black text-slate-900">₹
                            <?= number_format($inv['total_amount'], 2) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($invoices)): ?>
                    <tr>
                        <td colspan="6" class="px-8 py-20 text-center">
                            <i class="fas fa-file-invoice text-slate-100 text-6xl mb-4"></i>
                            <p class="text-sm font-black text-slate-300 uppercase tracking-widest">No transaction found in
                                this period</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>
<?php include $base_path . "includes/admin-footer.php"; ?>