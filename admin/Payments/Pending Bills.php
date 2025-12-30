<?php
$base_path = $_SERVER['DOCUMENT_ROOT'] . '/ecommerce-website/';

require_once "../../config/database.php";
$sql = "SELECT b.*, s.name 
        FROM supplier_bills b 
        JOIN suppliers s ON b.supplier_id = s.supplier_id 
        WHERE b.payment_status != 'Paid' 
        ORDER BY b.due_date ASC";
$pending = $pdo->query($sql)->fetchAll();
?>
<td class="<?= (strtotime($b['due_date']) < time()) ? 'text-red-600 font-black' : '' ?>">
    <?= $b['due_date'] ?>
</td>