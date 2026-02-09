<?php
$base_path = $_SERVER['DOCUMENT_ROOT'] . '/ecommerce-website/';
require_once "../../config/database.php";

$invoice_id = $_GET['id'] ?? null;

if (!$invoice_id) {
    die("Invoice ID is required.");
}

// 1. Fetch Invoice & Order Details
$query = "SELECT i.*, o.order_type, o.payment_method, u.name as customer_name, u.phone, u.address, u.city, u.pincode
          FROM invoices i
          JOIN orders o ON i.order_id = o.order_id
          JOIN users u ON o.user_id = u.user_id
          WHERE i.invoice_id = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$invoice_id]);
$inv = $stmt->fetch();

if (!$inv)
    die("Invoice not found.");

// 2. Fetch Order Items
$item_query = "SELECT oi.*, p.name as product_name, pv.size, pv.color 
               FROM order_items oi
               JOIN products p ON oi.product_id = p.product_id
               LEFT JOIN product_variants pv ON oi.variant_id = pv.variant_id
               WHERE oi.order_id = ?";
$stmt_items = $pdo->prepare($item_query);
$stmt_items->execute([$inv['order_id']]);
$items = $stmt_items->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Invoice - <?= $inv['invoice_number'] ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @media print {
            .no-print {
                display: none;
            }

            body {
                background: white;
            }

            .print-shadow-none {
                shadow: none;
                border: none;
            }
        }
    </style>
</head>

<body class="bg-slate-50 p-4 md:p-10">

    <div class="max-w-4xl mx-auto mb-6 flex justify-between items-center no-print">
        <a href="order-list.php" class="text-slate-500 font-bold text-sm hover:text-slate-900">
            <i class="fas fa-arrow-left mr-2"></i> Back to Orders
        </a>
        <button onclick="window.print()"
            class="bg-slate-900 text-white px-6 py-3 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-orange-600 transition-all shadow-lg">
            <i class="fas fa-print mr-2"></i> Print Invoice
        </button>
    </div>

    <div
        class="max-w-4xl mx-auto bg-white rounded-[2.5rem] shadow-xl overflow-hidden print-shadow-none border border-slate-100">

        <div class="p-10 bg-slate-900 text-white flex justify-between items-start">
            <div>
                <h1 class="text-3xl font-black tracking-tighter">JOSHI ELECTRICALS<span class="text-orange-500">.</span>
                </h1>
                <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest mt-1">Premium Clothing Store
                </p>
            </div>
            <div class="text-right">
                <h2 class="text-xl font-black uppercase italic opacity-50">Tax Invoice</h2>
                <p class="text-sm font-bold mt-2"><?= $inv['invoice_number'] ?></p>
                <p class="text-xs text-slate-400"><?= date('d M, Y', strtotime($inv['invoice_date'])) ?></p>
            </div>
        </div>

        <div class="p-10">
            <div class="grid grid-cols-2 gap-10 mb-10 pb-10 border-b border-slate-50">
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Bill To:</p>
                    <h4 class="text-lg font-black text-slate-900"><?= htmlspecialchars($inv['customer_name']) ?></h4>
                    <p class="text-sm text-slate-500 leading-relaxed mt-2">
                        <?= htmlspecialchars($inv['address']) ?><br>
                        <?= htmlspecialchars($inv['city']) ?> - <?= $inv['pincode'] ?><br>
                        <span class="font-bold">Mob:</span> <?= $inv['phone'] ?>
                    </p>
                </div>
                <div class="text-right">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Shipment Detail:</p>
                    <p class="text-sm text-slate-500">
                        <span class="font-bold text-slate-900">Payment:</span>
                        <?= strtoupper($inv['payment_method']) ?><br>
                        <span class="font-bold text-slate-900">Order Type:</span> <?= ucfirst($inv['order_type']) ?><br>
                    </p>
                </div>
            </div>

            <table class="w-full mb-10">
                <thead>
                    <tr
                        class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100">
                        <th class="py-4 text-left">Description</th>
                        <th class="py-4 text-center">Qty</th>
                        <th class="py-4 text-right">Price</th>
                        <th class="py-4 text-right">GST</th>
                        <th class="py-4 text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td class="py-5">
                                <p class="text-sm font-black text-slate-900"><?= $item['product_name'] ?></p>
                                <?php if ($item['size'] || $item['color']): ?>
                                    <p class="text-[10px] text-slate-400 font-bold uppercase">Size: <?= $item['size'] ?> |
                                        Color: <?= $item['color'] ?></p>
                                <?php endif; ?>
                            </td>
                            <td class="py-5 text-center text-sm font-bold text-slate-600"><?= $item['quantity'] ?></td>
                            <td class="py-5 text-right text-sm font-bold text-slate-600">
                                ₹<?= number_format($item['unit_price'], 2) ?></td>
                            <td class="py-5 text-right text-sm font-bold text-slate-600"><?= $item['gst_percent'] ?>%</td>
                            <td class="py-5 text-right text-sm font-black text-slate-900">
                                ₹<?= number_format($item['total_price'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="flex justify-end">
                <div class="w-full max-w-xs space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-400 font-bold uppercase text-[10px]">Subtotal</span>
                        <span class="font-bold text-slate-700">₹<?= number_format($inv['subtotal'], 2) ?></span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-400 font-bold uppercase text-[10px]">GST Total</span>
                        <span class="font-bold text-slate-700">₹<?= number_format($inv['gst_total'], 2) ?></span>
                    </div>
                    <div class="flex justify-between items-center pt-3 border-t border-slate-100">
                        <span class="text-slate-900 font-black uppercase text-xs">Grand Total</span>
                        <span
                            class="text-2xl font-black text-slate-900">₹<?= number_format($inv['total_amount'], 2) ?></span>
                    </div>
                </div>
            </div>

            <div class="mt-20 pt-10 border-t border-slate-100 text-center">
                <p class="text-[10px] font-black text-slate-300 uppercase tracking-[0.3em]">Thank you for shopping at
                    Joshi Electricals</p>
                <p class="text-[9px] text-slate-400 mt-2 italic">This is a computer generated invoice and does not
                    require a physical signature.</p>
            </div>
        </div>
    </div>

</body>

</html>