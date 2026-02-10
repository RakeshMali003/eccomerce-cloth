<?php
$base_path = __DIR__ . '/../../';
require_once "../../config/database.php";


include $base_path . 'includes/sidebar.php';

// 1. Get Month/Year from URL or default to current
$selected_month = $_GET['month'] ?? date('m');
$selected_year = $_GET['year'] ?? date('Y');

// --- DYNAMIC SALES GST (Output Tax) ---
// Based on your 'invoices' table: invoice_date, gst_total, subtotal
$sales_sql = "SELECT 
                SUM(subtotal) as taxable_sales, 
                SUM(gst_total) as output_gst, 
                SUM(total_amount) as gross_sales 
              FROM invoices 
              WHERE MONTH(invoice_date) = :m AND YEAR(invoice_date) = :y";
$sales_stmt = $pdo->prepare($sales_sql);
$sales_stmt->execute(['m' => $selected_month, 'y' => $selected_year]);
$sales_data = $sales_stmt->fetch();

// --- DYNAMIC PURCHASE GST (Input Tax Credit) ---
// Based on your 'supplier_bills' table: bill_date, total_amount
// Since your table doesn't have a separate GST column, we calculate 18% reverse (standard for clothing)
// Formula: GST = Total - (Total / 1.18)
$purchase_sql = "SELECT 
                    SUM(total_amount / 1.18) as taxable_purchase,
                    SUM(total_amount - (total_amount / 1.18)) as input_gst,
                    SUM(total_amount) as gross_purchase
                 FROM supplier_bills 
                 WHERE MONTH(bill_date) = :m AND YEAR(bill_date) = :y";
$purchase_stmt = $pdo->prepare($purchase_sql);
$purchase_stmt->execute(['m' => $selected_month, 'y' => $selected_year]);
$purchase_data = $purchase_stmt->fetch();

// --- CALCULATIONS ---
$out_gst = $sales_data['output_gst'] ?? 0;
$in_gst  = $purchase_data['input_gst'] ?? 0;
$net_payable = $out_gst - $in_gst;
?>

<main class="p-6 lg:p-12 bg-[#f8fafc] min-h-screen">
    <div class="mb-10 flex flex-col md:flex-row justify-between items-center gap-6">
        <div>
            <h2 class="text-3xl font-black tracking-tighter text-slate-900">Tax Intelligence<span class="text-indigo-600">.</span></h2>
            <p class="text-slate-400 text-sm font-medium">Dynamic GST report for <?= date('F Y', mktime(0, 0, 0, $selected_month, 1, $selected_year)) ?></p>
        </div>

        <form method="GET" class="flex gap-2 bg-white p-2 rounded-2xl shadow-sm border border-slate-100">
            <select name="month" onchange="this.form.submit()" class="bg-transparent p-2 text-xs font-bold outline-none">
                <?php for($i=1; $i<=12; $i++) echo "<option value='$i' ".($selected_month==$i?'selected':'').">".date('F', mktime(0,0,0,$i,1))."</option>"; ?>
            </select>
            <select name="year" onchange="this.form.submit()" class="bg-transparent p-2 text-xs font-bold outline-none">
                <option value="2025" <?= $selected_year == '2025' ? 'selected' : '' ?>>2025</option>
                <option value="2024" <?= $selected_year == '2024' ? 'selected' : '' ?>>2024</option>
            </select>
            <button type="button" onclick="window.print()" class="bg-slate-900 text-white px-4 py-2 rounded-xl hover:bg-indigo-600 transition-all">
                <i class="fas fa-print"></i>
            </button>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Output GST</p>
            <h3 class="text-3xl font-black text-red-500">₹<?= number_format($out_gst, 2) ?></h3>
            <div class="mt-4 pt-4 border-t border-slate-50 flex justify-between text-[10px] font-bold">
                <span class="text-slate-400">TAXABLE SALES</span>
                <span class="text-slate-900 text-xs">₹<?= number_format($sales_data['taxable_sales'] ?? 0, 2) ?></span>
            </div>
        </div>

        <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Available ITC</p>
            <h3 class="text-3xl font-black text-emerald-500">₹<?= number_format($in_gst, 2) ?></h3>
            <div class="mt-4 pt-4 border-t border-slate-50 flex justify-between text-[10px] font-bold">
                <span class="text-slate-400">PURCHASE VALUE</span>
                <span class="text-slate-900 text-xs">₹<?= number_format($purchase_data['taxable_purchase'] ?? 0, 2) ?></span>
            </div>
        </div>

        <div class="bg-indigo-600 p-8 rounded-[2.5rem] shadow-xl shadow-indigo-100 text-white">
            <p class="text-[10px] font-black uppercase tracking-widest opacity-70 mb-1">Net GST Payable</p>
            <h3 class="text-3xl font-black">₹<?= number_format(max(0, $net_payable), 2) ?></h3>
            <p class="text-[10px] mt-4 font-bold"><?= $net_payable < 0 ? "EXCESS CREDIT: ₹".number_format(abs($net_payable), 2) : "DUE THIS MONTH" ?></p>
        </div>
    </div>

    <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-slate-50 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                <tr>
                    <th class="px-8 py-5">Classification</th>
                    <th class="px-6 py-5 text-right">Taxable Amount</th>
                    <th class="px-6 py-5 text-right">CGST (9%)</th>
                    <th class="px-6 py-5 text-right">SGST (9%)</th>
                    <th class="px-8 py-5 text-right">Total Tax</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50 text-sm font-bold text-slate-600">
                <tr>
                    <td class="px-8 py-6">Outward Supplies (Sales)</td>
                    <td class="px-6 py-6 text-right">₹<?= number_format($sales_data['taxable_sales'] ?? 0, 2) ?></td>
                    <td class="px-6 py-6 text-right">₹<?= number_format($out_gst / 2, 2) ?></td>
                    <td class="px-6 py-6 text-right">₹<?= number_format($out_gst / 2, 2) ?></td>
                    <td class="px-8 py-6 text-right text-red-500 font-black">₹<?= number_format($out_gst, 2) ?></td>
                </tr>
                <tr>
                    <td class="px-8 py-6">Inward Supplies (Purchases)</td>
                    <td class="px-6 py-6 text-right">₹<?= number_format($purchase_data['taxable_purchase'] ?? 0, 2) ?></td>
                    <td class="px-6 py-6 text-right">₹<?= number_format($in_gst / 2, 2) ?></td>
                    <td class="px-6 py-6 text-right">₹<?= number_format($in_gst / 2, 2) ?></td>
                    <td class="px-8 py-6 text-right text-emerald-500 font-black">₹<?= number_format($in_gst, 2) ?></td>
                </tr>
            </tbody>
        </table>
    </div>
</main>