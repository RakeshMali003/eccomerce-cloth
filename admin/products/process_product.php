<?php
session_start();
require_once "../../config/database.php";

$action = $_REQUEST['action'] ?? '';

// --- DELETE LOGIC ---
if ($action === 'delete' && isset($_GET['id'])) {
    try {
        // Check if product is linked to any purchase items before deleting
        $stmt = $pdo->prepare("DELETE FROM products WHERE product_id = ?");
        $stmt->execute([$_GET['id']]);
        $_SESSION['toast'] = ['msg' => 'Product removed!', 'type' => 'success'];
    } catch (Exception $e) {
        $_SESSION['toast'] = ['msg' => 'Cannot delete: Product is linked to transactions.', 'type' => 'error'];
    }
    header("Location: products-list.php");
    exit();
}

// --- ADD / EDIT LOGIC ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pid = $_POST['product_id'] ?? null;
    $upload_dir = "../../assets/images/products/";

    // Helper function for multiple image uploads
    function handleUpload($file_key, $dir)
    {
        if (!empty($_FILES[$file_key]['name'])) {
            $ext = pathinfo($_FILES[$file_key]['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . "_" . time() . "." . $ext;
            if (move_uploaded_file($_FILES[$file_key]['tmp_name'], $dir . $filename)) {
                return $filename;
            }
        }
        return null;
    }

    // Capture Images
    $main_img = handleUpload('main_image', $upload_dir);
    $img1 = handleUpload('image_1', $upload_dir);
    $img2 = handleUpload('image_2', $upload_dir);
    $img3 = handleUpload('image_3', $upload_dir);
    $img4 = handleUpload('image_4', $upload_dir);

    // Prepare Base Parameters - Handle both 'retail_price' and 'price' field names
    $price = $_POST['price'] ?? $_POST['retail_price'] ?? 0;
    $params = [
        'sid' => $_POST['preferred_supplier_id'] ?: null,
        'cid' => $_POST['category_id'],
        'psid' => $_POST['preferred_supplier_id'] ?: null,
        'name' => $_POST['name'],
        'sku' => $_POST['sku'],
        'desc' => $_POST['description'] ?? '',
        'price' => !empty($price) ? (float) $price : 0.00,
        'w_price' => !empty($_POST['wholesale_price']) ? (float) $_POST['wholesale_price'] : 0.00,
        'w_qty' => $_POST['min_wholesale_qty'] ?? 1,
        'gst' => $_POST['gst_percent'] ?? 0,
        'disc' => $_POST['discount_percent'] ?? 0,
        'stock' => $_POST['stock'] ?? 0,
        'min_s' => $_POST['min_stock_level'] ?? 5,
        'status' => $_POST['status'] ?? 1
    ];

    try {
        if ($action === 'edit') {
            $sql = "UPDATE products SET 
                    supplier_id=:sid, category_id=:cid, preferred_supplier_id=:psid, 
                    name=:name, sku=:sku, description=:desc, price=:price, 
                    wholesale_price=:w_price, min_wholesale_qty=:w_qty, 
                    gst_percent=:gst, discount_percent=:disc, stock=:stock, 
                    min_stock_level=:min_s, status=:status";

            // Add images to query only if new ones were uploaded
            if ($main_img) {
                $sql .= ", main_image = :m_img";
                $params['m_img'] = $main_img;
            }
            if ($img1) {
                $sql .= ", image_1 = :i1";
                $params['i1'] = $img1;
            }
            if ($img2) {
                $sql .= ", image_2 = :i2";
                $params['i2'] = $img2;
            }
            if ($img3) {
                $sql .= ", image_3 = :i3";
                $params['i3'] = $img3;
            }
            if ($img4) {
                $sql .= ", image_4 = :i4";
                $params['i4'] = $img4;
            }

            $sql .= " WHERE product_id = :pid";
            $params['pid'] = $pid;

            $pdo->prepare($sql)->execute($params);
            $msg = "Product updated successfully!";
        } else {
            // INSERT LOGIC
            $sql = "INSERT INTO products (
                        supplier_id, category_id, preferred_supplier_id, name, sku, 
                        description, price, wholesale_price, min_wholesale_qty, 
                        gst_percent, discount_percent, stock, min_stock_level, 
                        status, main_image, image_1, image_2, image_3, image_4, created_at
                    ) VALUES (
                        :sid, :cid, :psid, :name, :sku, :desc, :price, :w_price, :w_qty, 
                        :gst, :disc, :stock, :min_s, :status, :m_img, :i1, :i2, :i3, :i4, NOW()
                    )";

            $params['m_img'] = $main_img;
            $params['i1'] = $img1;
            $params['i2'] = $img2;
            $params['i3'] = $img3;
            $params['i4'] = $img4;

            $pdo->prepare($sql)->execute($params);
            $msg = "Product added to catalog!";
        }
        $_SESSION['toast'] = ['msg' => $msg, 'type' => 'success'];
    } catch (Exception $e) {
        $_SESSION['toast'] = ['msg' => 'System Error: ' . $e->getMessage(), 'type' => 'error'];
    }
    header("Location: products-list.php");
    exit();
}