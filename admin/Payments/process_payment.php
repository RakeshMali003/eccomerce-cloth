<?php
session_start();
require_once "../../config/database.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $supplier_id = $_POST['supplier_id'];
    $bill_id = $_POST['bill_id'];
    $amount_paid = (float) $_POST['amount'];
    $payment_mode = $_POST['payment_mode'];
    $transaction_id = $_POST['transaction_id'] ?? null;
    $payment_date = date('Y-m-d');

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
            // Fetch current bill details to ensure accurate calculation
            $stmtBill = $pdo->prepare("SELECT total_amount, paid_amount FROM supplier_bills WHERE bill_id = ? FOR UPDATE");
            $stmtBill->execute([$bill_id]);
            $bill = $stmtBill->fetch();

            if ($bill) {
                $new_paid = (float) $bill['paid_amount'] + $amount_paid;
                $total = (float) $bill['total_amount'];
                $new_balance = $total - $new_paid;

                // Determine Status
                if ($new_paid >= $total) {
                    $new_status = 'PAID';
                    $new_balance = 0; // Prevent negative balance
                } elseif ($new_paid > 0) {
                    $new_status = 'PARTIALLY_PAID';
                } else {
                    $new_status = 'Pending';
                }

                $stmtUpdateBill = $pdo->prepare("UPDATE supplier_bills SET 
                    paid_amount = ?, 
                    balance_amount = ?,
                    payment_status = ?
                    WHERE bill_id = ?");
                $stmtUpdateBill->execute([$new_paid, $new_balance, $new_status, $bill_id]);
            }
        }

        $pdo->commit();
        $_SESSION['toast'] = ['msg' => 'Payment of ₹' . number_format($amount_paid, 2) . ' recorded.', 'type' => 'success'];
    } catch (Exception $e) {
        if ($pdo->inTransaction())
            $pdo->rollBack();
        $_SESSION['toast'] = ['msg' => 'Error: ' . $e->getMessage(), 'type' => 'error'];
    }

    header("Location: Make Payment.php");
    exit();
}