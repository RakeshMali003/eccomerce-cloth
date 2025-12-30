<?php
session_start();
require_once "../../config/database.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $supplier_id    = $_POST['supplier_id'];
    $bill_id        = $_POST['bill_id'];
    $amount_paid    = (float)$_POST['amount'];
    $payment_mode   = $_POST['payment_mode'];
    $transaction_id = $_POST['transaction_id'] ?? null;
    $payment_date   = date('Y-m-d');

    try {
        $pdo->beginTransaction();

        // 1. RECORD THE PAYMENT
        $stmtPay = $pdo->prepare("INSERT INTO supplier_payments 
            (supplier_id, bill_id, payment_date, amount, payment_mode, transaction_id, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?)");
        
        // If Cheque, set status to Pending, otherwise Cleared
        $p_status = ($payment_mode === 'Cheque') ? 'Pending' : 'Cleared';
        $stmtPay->execute([$supplier_id, $bill_id, $payment_date, $amount_paid, $payment_mode, $transaction_id, $p_status]);

        // 2. UPDATE BILL TOTALS (Only if not a pending cheque)
        if ($p_status === 'Cleared') {
            $stmtUpdateBill = $pdo->prepare("UPDATE supplier_bills SET 
                paid_amount = paid_amount + ?, 
                balance_amount = total_amount - (paid_amount + ?),
                payment_status = CASE 
                    WHEN (paid_amount + ?) >= total_amount THEN 'Paid'
                    WHEN (paid_amount + ?) > 0 THEN 'Partial'
                    ELSE 'Pending'
                END
                WHERE bill_id = ?");
            $stmtUpdateBill->execute([$amount_paid, $amount_paid, $amount_paid, $amount_paid, $bill_id]);
        }

        $pdo->commit();
        $_SESSION['toast'] = ['msg' => 'Payment recorded successfully', 'type' => 'success'];
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['toast'] = ['msg' => 'Error: ' . $e->getMessage(), 'type' => 'error'];
    }

    header("Location: Make Payment.php");
    exit();
}