<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
$base_path = $_SERVER['DOCUMENT_ROOT'] . '/ecommerce-website/';

include $base_path . 'includes/sidebar.php'; 
include $base_path . 'includes/notifications.php'; 
require_once "../../config/database.php";

// ✅ FIX 1: Safety Guard - Check if 'id' exists in the URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>alert('Please select a supplier first.'); window.location.href='Supplier_List.php';</script>";
    exit();
}

$supplier_id = filter_var($_GET['id'], FILTER_VALIDATE_INT);

// ✅ FIX 2: Check if ID is a valid integer
if (!$supplier_id) {
    die("Invalid Supplier ID.");
}

// 1. Fetch Supplier Details
$stmt = $pdo->prepare("SELECT name FROM suppliers WHERE supplier_id = ?");
$stmt->execute([$supplier_id]);
$supplier = $stmt->fetch();

// ✅ FIX 3: Check if Supplier actually exists in DB
if (!$supplier) {
    die("Supplier not found in database.");
}

// 2. The Master Ledger Query (Combines Bills and Payments)

$query = "
    (SELECT 
        bill_date as date, 
        CONCAT('Purchase: ', bill_number) as description, 
        bill_number as reference, 
        total_amount as credit, 
        0 as debit,
        'BILL' as doc_type
     FROM supplier_bills 
     WHERE supplier_id = :sid1)
    
    UNION ALL
    
    (SELECT 
        payment_date as date, 
        CONCAT('Payment: ', payment_mode) as description, 
        transaction_id as reference, 
        0 as credit, 
        amount as debit,
        'PAY' as doc_type
     FROM supplier_payments 
     WHERE supplier_id = :sid2 AND status = 'Cleared')
    
    ORDER BY date ASC, doc_type DESC";

$stmt = $pdo->prepare($query);

// Bind both placeholders to the same ID
$stmt->execute([
    'sid1' => $supplier_id,
    'sid2' => $supplier_id
]); 
$ledger_entries = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<main class="p-6 lg:p-12">
 
    <div class="flex justify-between items-end mb-10">
        <div>
            <h2 class="text-3xl font-black tracking-tighter"><?= htmlspecialchars($supplier['name']) ?></h2>
            <p class="text-[10px] font-black uppercase text-slate-400 tracking-widest">Statement of Account</p>
        </div>
        <button onclick="window.print()" class="bg-slate-900 text-white px-6 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-orange-600 transition-all">
            <i class="fas fa-print mr-2"></i> Print Ledger
        </button>
    </div>

    <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                    <th class="px-8 py-6">Date</th>
                    <th class="px-6 py-6">Transaction Detail</th>
                    <th class="px-6 py-6 text-right">Credit (+)</th>
                    <th class="px-6 py-6 text-right">Debit (-)</th>
                    <th class="px-8 py-6 text-right">Balance</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php 
                $running_balance = 0;
                if (empty($ledger_entries)): ?>
                    <tr><td colspan="5" class="p-20 text-center text-slate-300 font-bold uppercase text-xs">No transactions recorded for this vendor.</td></tr>
                <?php else: 
                    foreach($ledger_entries as $entry): 
                        // The Core Math: Purchases increase balance, Payments decrease it
                        $running_balance += ($entry['credit'] - $entry['debit']);
                ?>
                <tr class="hover:bg-slate-50 transition-all group">
                    <td class="px-8 py-5 text-xs font-bold text-slate-500">
                        <?= date('d M, Y', strtotime($entry['date'])) ?>
                    </td>
                    <td class="px-6 py-5">
                        <p class="text-xs font-black text-slate-900"><?= htmlspecialchars($entry['description']) ?></p>
                        <p class="text-[9px] font-bold text-slate-400">Ref: <?= htmlspecialchars($entry['reference'] ?: 'N/A') ?></p>
                    </td>
                    <td class="px-6 py-5 text-right font-bold text-slate-900">
                        <?= $entry['credit'] > 0 ? '₹' . number_format($entry['credit'], 2) : '-' ?>
                    </td>
                    <td class="px-6 py-5 text-right font-bold text-emerald-600">
                        <?= $entry['debit'] > 0 ? '₹' . number_format($entry['debit'], 2) : '-' ?>
                    </td>
                    <td class="px-8 py-5 text-right">
                        <span class="px-4 py-2 rounded-xl font-black text-xs <?= $running_balance > 0 ? 'bg-orange-50 text-orange-700' : 'bg-emerald-50 text-emerald-700' ?>">
                            ₹<?= number_format(abs($running_balance), 2) ?> 
                            <?= $running_balance > 0 ? 'Cr' : 'Dr' ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</main>