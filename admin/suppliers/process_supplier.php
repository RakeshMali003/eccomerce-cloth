<?php
session_start();
require_once "../../config/database.php";

// 1. GLOBAL SECURITY CHECK
if (!isset($_SESSION['admin_id'])) {
    header('Content-Type: application/json');
    die(json_encode(['success' => false, 'message' => 'Access Denied']));
}

// ---------------------------------------------------------
// 🔍 FETCH LOGIC (For View/Edit via AJAX)
// ---------------------------------------------------------
// 🔍 UPDATED FETCH LOGIC
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['fetch_id'])) {
    $id = filter_var($_GET['fetch_id'], FILTER_VALIDATE_INT);
    
    // Get Supplier Details
    $stmt = $pdo->prepare("SELECT * FROM suppliers WHERE supplier_id = ?");
    $stmt->execute([$id]);
    $supplier = $stmt->fetch(PDO::FETCH_ASSOC);

    if($supplier) {
        // Get Purchase History (Linked via supplier_id)
        $pStmt = $pdo->prepare("SELECT po_id as id, order_date as date, total_amount as amount, status 
                                FROM purchase_orders WHERE supplier_id = ? ORDER BY created_at DESC");
        $pStmt->execute([$id]);
        $supplier['purchases'] = $pStmt->fetchAll(PDO::FETCH_ASSOC);

        // Get Outstanding Balance (Sum of unpaid bills)
        $bStmt = $pdo->prepare("SELECT SUM(total_amount) FROM purchase_orders WHERE supplier_id = ? AND status != 'received'");
        $bStmt->execute([$id]);
        $supplier['outstanding'] = $bStmt->fetchColumn() ?: 0;
    }

    header('Content-Type: application/json');
    echo json_encode($supplier ?: ['error' => 'Not found']);
    exit();
}

// ---------------------------------------------------------
// 💾 SAVE/UPDATE LOGIC
// ---------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    // Sanitize Data
    $data = [
        'id'      => filter_var($_POST['supplier_id'] ?? 0, FILTER_VALIDATE_INT),
        'name'    => htmlspecialchars(trim($_POST['name'])),
        'contact' => htmlspecialchars(trim($_POST['contact_person'])),
        'email'   => filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL),
        'phone'   => preg_replace('/[^0-9+]/', '', $_POST['phone']),
        'gstin'   => strtoupper(htmlspecialchars(trim($_POST['gstin']))),
        'terms'   => $_POST['payment_terms'] ?? 'COD',
        'address' => htmlspecialchars(trim($_POST['address'])),
        'city'    => htmlspecialchars(trim($_POST['city'])),
        'state'   => htmlspecialchars(trim($_POST['state'])),
        'pincode' => preg_replace('/[^0-9]/', '', $_POST['pincode']),
        'status'  => ($_POST['status'] === 'active') ? 'active' : 'inactive'
    ];

    try {
        if ($action === 'add') {
            $sql = "INSERT INTO suppliers (name, contact_person, email, phone, gstin, payment_terms, address, city, state, pincode, status) 
                    VALUES (:name, :contact, :email, :phone, :gstin, :terms, :address, :city, :state, :pincode, :status)";
            $stmt = $pdo->prepare($sql);
            
            // Remove 'id' from data for INSERT
            $insertData = $data; unset($insertData['id']);
            $stmt->execute($insertData);
            
            $_SESSION['toast'] = ['msg' => 'Supplier onboarded successfully!', 'type' => 'success'];
        } 
        
        elseif ($action === 'update') {
            $sql = "UPDATE suppliers SET 
                    name=:name, contact_person=:contact, email=:email, phone=:phone, gstin=:gstin, 
                    payment_terms=:terms, address=:address, city=:city, state=:state, pincode=:pincode, 
                    status=:status, updated_at=NOW() 
                    WHERE supplier_id=:id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($data);
            
            $_SESSION['toast'] = ['msg' => 'Supplier details updated.', 'type' => 'success'];
        }

    } catch (PDOException $e) {
        $_SESSION['toast'] = ['msg' => 'Database Error: ' . $e->getCode(), 'type' => 'error'];
    }
    
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}

// ---------------------------------------------------------
// ❌ DELETE LOGIC
// ---------------------------------------------------------

if (isset($_GET['delete_id'])) {
    $id = filter_var($_GET['delete_id'], FILTER_VALIDATE_INT);
    
    try {
        $stmt = $pdo->prepare("DELETE FROM suppliers WHERE supplier_id = ?");
        $stmt->execute([$id]);
        
        $_SESSION['toast'] = [
            'msg' => 'Supplier removed permanently.', 
            'type' => 'warning'
        ];
    } catch (PDOException $e) {
        $_SESSION['toast'] = [
            'msg' => 'Cannot delete: Supplier is linked to existing orders.', 
            'type' => 'error'
        ];
    }
    
    // REDIRECT BACK TO THE SAME PAGE
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}