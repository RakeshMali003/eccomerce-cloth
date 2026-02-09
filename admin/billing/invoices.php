<?php
$base_path = $_SERVER['DOCUMENT_ROOT'] . '/ecommerce-website/';
require_once "../../config/database.php";

include $base_path . 'includes/admin-header.php';

$invoice_id = $_GET['id'] ?? null;

if (!$invoice_id) {
    // Instead of dying, show a clean message and search box
    include $base_path . 'includes/sidebar.php';
    echo '
   <main class="p-6 lg:p-12 bg-[#f8fafc] min-h-screen">
        <div class="text-center bg-white p-12 rounded-[3rem] shadow-xl max-w-md border border-slate-100">
            <i class="fas fa-file-invoice-dollar text-slate-200 text-6xl mb-6"></i>
            <h2 class="text-xl font-black text-slate-900 uppercase">Selection Required</h2>
            <p class="text-slate-400 text-sm mt-2 mb-8">Please enter an Invoice ID manually or return to the order list.</p>
            <form action="sales-invoice.php" method="GET" class="flex gap-2">
                <input type="text" name="id" placeholder="Ex: 15" class="w-full bg-slate-50 p-4 rounded-2xl outline-none font-bold">
                <button type="submit" class="bg-slate-900 text-white px-6 rounded-2xl font-black uppercase text-[10px]">Open</button>
            </form>
            <a href="order-list.php" class="inline-block mt-6 text-xs font-bold text-blue-600 underline">Back to Order List</a>
        </div>
    </main>';
    include $base_path . "includes/admin-footer.php";
    exit();
}


// 1. Fetch Invoice, Order, and Customer Data in one join
$sql = "SELECT i.*, o.order_type, o.payment_method, o.payment_status as p_status, 
               u.name as cust_name, u.phone, u.address, u.city, u.pincode, u.gst_number as cust_gst
        FROM invoices i
        JOIN orders o ON i.order_id = o.order_id
        JOIN users u ON o.user_id = u.user_id
        WHERE i.invoice_id = ?";

$stmt = $pdo->prepare($sql);
$stmt->execute([$invoice_id]);
$inv = $stmt->fetch();

if (!$inv)
    die("Invoice record not found in database.");

// 2. Fetch Itemized Breakdown
$item_sql = "SELECT oi.*, p.name as prod_name, p.sku 
             FROM order_items oi
             JOIN products p ON oi.product_id = p.product_id
             WHERE oi.order_id = ?";
$items_stmt = $pdo->prepare($item_sql);
$items_stmt->execute([$inv['order_id']]);
$line_items = $items_stmt->fetchAll();
include $base_path . 'includes/sidebar.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice_<?= $inv['invoice_number'] ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @media print {
            .no-print {
                display: none !important;
            }

            body {
                background: white !important;
                padding: 0 !important;
            }

            .invoice-card {
                box-shadow: none !important;
                border: 1px solid #eee !important;
                border-radius: 0 !important;
            }
        }
    </style>
</head>

<body class="bg-slate-100 py-10 px-4">

    <div class="max-w-4xl mx-auto mb-6 flex justify-between no-print">
        <a href="..\orders\orders-list.php"
            class="inline-flex items-center text-slate-500 font-black text-[10px] uppercase tracking-widest hover:text-slate-900 transition-all">
            <i class="fas fa-arrow-left mr-2"></i> Return to Orders
        </a>
        <div class="flex gap-3">
            <button onclick="window.print()"
                class="bg-slate-900 text-white px-8 py-3 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-orange-600 transition-all shadow-lg">
                <i class="fas fa-print mr-2"></i> Print Invoice
            </button>
        </div>
    </div>

    <div
        class="invoice-card max-w-4xl mx-auto bg-white rounded-[3rem] shadow-2xl overflow-hidden border border-slate-100">

        <div class="p-12 bg-slate-900 text-white flex justify-between items-center">
            <div>
                <h1 class="text-4xl font-black tracking-tighter">JOSHI ELECTRICALS<span class="text-orange-500">.</span>
                </h1>
                <p class="text-slate-400 text-[9px] font-black uppercase tracking-[0.3em] mt-2">Premium Retail &
                    Wholesale</p>
            </div>
            <div class="text-right">
                <h2 class="text-2xl font-black uppercase italic opacity-20">Tax Invoice</h2>
                <div class="mt-4 space-y-1">
                    <p class="text-sm font-black"><?= $inv['invoice_number'] ?></p>
                    <p class="text-[10px] text-slate-400 font-bold uppercase">
                        <?= date('d F, Y', strtotime($inv['invoice_date'])) ?></p>
                </div>
            </div>
        </div>

        <div class="p-12">
            <div class="grid grid-cols-2 gap-12 mb-12">
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Customer Details</p>
                    <h4 class="text-xl font-black text-slate-900"><?= htmlspecialchars($inv['cust_name']) ?></h4>
                    <div class="text-sm text-slate-500 mt-3 leading-relaxed font-medium">
                        <?= htmlspecialchars($inv['address']) ?><br>
                        <?= htmlspecialchars($inv['city']) ?> - <?= $inv['pincode'] ?><br>
                        <span class="text-slate-900 font-bold">Contact:</span> <?= $inv['phone'] ?><br>
                        <?php if ($inv['cust_gst']): ?>
                            <span class="text-slate-900 font-bold">GSTIN:</span> <?= $inv['cust_gst'] ?>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Transaction Info</p>
                    <div class="space-y-2">
                        <p class="text-sm font-bold text-slate-700">Payment: <span
                                class="uppercase text-slate-900"><?= $inv['payment_method'] ?></span></p>
                        <p class="text-sm font-bold text-slate-700">Status:
                            <span
                                class="px-3 py-1 rounded-full text-[9px] font-black uppercase <?= $inv['p_status'] == 'paid' ? 'bg-emerald-100 text-emerald-600' : 'bg-amber-100 text-amber-600' ?>">
                                <?= $inv['p_status'] ?>
                            </span>
                        </p>
                        <p class="text-sm font-bold text-slate-700">Type: <span
                                class="capitalize text-slate-900"><?= $inv['order_type'] ?></span></p>
                    </div>
                </div>
            </div>

            <table class="w-full mb-10">
                <thead>
                    <tr
                        class="border-b-2 border-slate-900 text-[10px] font-black text-slate-900 uppercase tracking-widest">
                        <th class="py-4 text-left">Product & SKU</th>
                        <th class="py-4 text-center">Qty</th>
                        <th class="py-4 text-right">Rate</th>
                        <th class="py-4 text-right">Tax (%)</th>
                        <th class="py-4 text-right">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($line_items as $item): ?>
                        <tr>
                            <td class="py-6">
                                <p class="text-sm font-black text-slate-900"><?= htmlspecialchars($item['prod_name']) ?></p>
                                <p class="text-[9px] text-slate-400 font-bold uppercase tracking-tighter">
                                    <?= $item['sku'] ?></p>
                            </td>
                            <td class="py-6 text-center text-sm font-black text-slate-600"><?= $item['quantity'] ?></td>
                            <td class="py-6 text-right text-sm font-bold text-slate-600">
                                ₹<?= number_format($item['unit_price'], 2) ?></td>
                            <td class="py-6 text-right text-sm font-bold text-slate-600">
                                <?= number_format($item['gst_percent'], 1) ?>%</td>
                            <td class="py-6 text-right text-sm font-black text-slate-900">
                                ₹<?= number_format($item['total_price'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="flex justify-end pt-6 border-t border-slate-100">
                <div class="w-full max-w-xs space-y-4">
                    <div class="flex justify-between">
                        <span class="text-[10px] font-black text-slate-400 uppercase">Subtotal (Taxable)</span>
                        <span class="text-sm font-bold text-slate-700">₹<?= number_format($inv['subtotal'], 2) ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-[10px] font-black text-slate-400 uppercase">Total GST</span>
                        <span
                            class="text-sm font-bold text-slate-700">₹<?= number_format($inv['gst_total'], 2) ?></span>
                    </div>
                    <div class="flex justify-between items-center py-4 px-6 bg-slate-900 rounded-2xl">
                        <span class="text-xs font-black text-white uppercase">Invoice Total</span>
                        <span
                            class="text-xl font-black text-white">₹<?= number_format($inv['total_amount'], 2) ?></span>
                    </div>
                </div>
            </div>

            <div class="mt-20 flex justify-between items-end border-t border-slate-50 pt-10">
                <div class="text-[10px] text-slate-300 font-bold uppercase tracking-widest leading-loose">
                    Terms & Conditions:<br>
                    1. Goods once sold cannot be returned.<br>
                    2. Subject to local jurisdiction.
                </div>
                <div class="text-center">
                    <div class="h-16 w-40 border-b border-slate-200 mb-2 mx-auto"></div>
                    <p class="text-[10px] font-black text-slate-900 uppercase tracking-widest">Authorized Signatory</p>
                </div>
            </div>
        </div>
    </div>
    </main>
    <?php include $base_path . "includes/admin-footer.php"; ?>
</body>

</html>