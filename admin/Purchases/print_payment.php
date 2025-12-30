<?php
require_once "../../config/database.php";

if(!isset($_GET['id'])) die("Invalid Request");

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT sp.*, s.name as supplier_name, s.phone, s.address 
                       FROM supplier_payments sp 
                       JOIN suppliers s ON sp.supplier_id = s.supplier_id 
                       WHERE sp.payment_id = ?");
$stmt->execute([$id]);
$payment = $stmt->fetch();

if(!$payment) die("Payment not found");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment_Receipt_<?= $payment['transaction_id'] ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none; }
            body { background: white; padding: 0; }
        }
    </style>
</head>
<body class="bg-slate-50 p-10">
    <div class="max-w-2xl mx-auto bg-white p-12 rounded-[2rem] shadow-sm border border-slate-100">
        <div class="flex justify-between items-start mb-12">
            <div>
                <h1 class="text-2xl font-black tracking-tighter uppercase">Payment Advice</h1>
                <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest">Transaction Proof</p>
            </div>
            <div class="text-right">
                <h2 class="font-black text-lg">YOUR COMPANY NAME</h2>
                <p class="text-[10px] text-slate-400 font-bold uppercase">GSTIN: 24XXXXX0000X1Z</p>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-10 mb-12">
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase mb-2">Paid To:</p>
                <h3 class="font-black text-slate-900"><?= $payment['supplier_name'] ?></h3>
                <p class="text-xs text-slate-500"><?= $payment['address'] ?></p>
                <p class="text-xs text-slate-500"><?= $payment['phone'] ?></p>
            </div>
            <div class="text-right">
                <p class="text-[10px] font-black text-slate-400 uppercase mb-1">Receipt Date</p>
                <p class="text-sm font-bold mb-4"><?= date('d M, Y', strtotime($payment['payment_date'])) ?></p>
                
                <p class="text-[10px] font-black text-slate-400 uppercase mb-1">Payment Mode</p>
                <p class="text-sm font-bold"><?= $payment['payment_mode'] ?></p>
            </div>
        </div>

        <div class="bg-slate-50 rounded-3xl p-8 mb-10">
            <div class="flex justify-between items-center">
                <span class="text-[10px] font-black text-slate-400 uppercase">Reference / TXN ID</span>
                <span class="font-bold text-slate-900"><?= $payment['transaction_id'] ?: 'N/A' ?></span>
            </div>
            <hr class="my-4 border-slate-200">
            <div class="flex justify-between items-center">
                <span class="text-sm font-black text-slate-900">Total Amount Paid</span>
                <span class="text-2xl font-black text-slate-900">₹<?= number_format($payment['amount'], 2) ?></span>
            </div>
        </div>

        <div class="border-t border-dashed border-slate-200 pt-8 text-center">
            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-[0.2em]">This is a computer generated document and does not require a signature.</p>
        </div>

        <div class="mt-10 flex gap-4 no-print">
            <button onclick="window.print()" class="flex-1 bg-slate-900 text-white py-4 rounded-2xl font-black uppercase text-[10px] tracking-widest">Print Now</button>
            <button onclick="window.close()" class="flex-1 bg-slate-100 text-slate-400 py-4 rounded-2xl font-black uppercase text-[10px] tracking-widest">Close Window</button>
        </div>
    </div>
</body>
</html>