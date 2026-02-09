<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/ecommerce-website/config/database.php';

/* ----------------------------
   ADMIN AUTH CHECK
---------------------------- */
if (!isset($_SESSION['admin_id'])) {
    die("Unauthorized Access");
}

/* ----------------------------
   ADD USER
---------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $status = $_POST['status'] ?? 'active';

    $password = password_hash('User@123', PASSWORD_BCRYPT);

    try {
        $stmt = $pdo->prepare(
            "INSERT INTO users (name, email, phone, password, status, role)
             VALUES (?, ?, ?, ?, ?, 'user')"
        );
        $stmt->execute([$name, $email, $phone, $password, $status]);

        header("Location: users.php?msg=added");
        exit();

    } catch (PDOException $e) {
        $error = ($e->getCode() == 23000) ? 'Email already exists' : $e->getMessage();
        header("Location: users.php?msg=error&error=" . urlencode($error));
        exit();
    }
}

/* ----------------------------
   EDIT USER
---------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit') {

    $user_id = (int) $_POST['user_id'];
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $status = $_POST['status'] ?? 'active';

    try {
        $stmt = $pdo->prepare(
            "UPDATE users 
             SET name = ?, email = ?, phone = ?, status = ?
             WHERE user_id = ? AND role = 'user'"
        );
        $stmt->execute([$name, $email, $phone, $status, $user_id]);

        header("Location: users.php?msg=updated");
        exit();

    } catch (PDOException $e) {
        $error = ($e->getCode() == 23000) ? 'Email already exists' : $e->getMessage();
        header("Location: users.php?msg=error&error=" . urlencode($error));
        exit();
    }
}

/* ----------------------------
   DELETE USER
---------------------------- */
/* ----------------------------
   DELETE USER
---------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {

    // Helper to check permission
    if (($_SESSION['role'] ?? '') !== 'admin') {
        die("Unauthorized Action");
    }

    $delete_id = (int) $_POST['delete_id'];

    try {
        $stmt = $pdo->prepare(
            "DELETE FROM users WHERE user_id = ? AND role = 'user'"
        );
        $stmt->execute([$delete_id]);

        if ($stmt->rowCount() > 0) {
            header("Location: users.php?msg=deleted");
        } else {
            header("Location: users.php?msg=error&error=User not found");
        }
        exit();

    } catch (PDOException $e) {
        header("Location: users.php?msg=error&error=" . urlencode($e->getMessage()));
        exit();
    }
}

/* ----------------------------
   BULK DELETE
---------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'bulk_delete') {

    if (($_SESSION['role'] ?? '') !== 'admin') {
        die("Unauthorized Action");
    }

    $ids = $_POST['delete_ids'] ?? [];

    if (!empty($ids)) {
        try {
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $stmt = $pdo->prepare("DELETE FROM users WHERE user_id IN ($placeholders) AND role = 'user'");
            $stmt->execute($ids);

            header("Location: users.php?msg=deleted");
            exit();
        } catch (PDOException $e) {
            header("Location: users.php?msg=error&error=" . urlencode($e->getMessage()));
            exit();
        }
    } else {
        header("Location: users.php");
        exit();
    }
}

/* ----------------------------
   FETCH USERS
---------------------------- */
$users = $pdo->query(
    "SELECT * FROM users WHERE role='user' ORDER BY created_at DESC"
)->fetchAll(PDO::FETCH_ASSOC);
?>

<!-------------------------------
  ALERT MESSAGE SECTION
-------------------------------->
<?php
if (isset($_GET['msg'])) {

    if ($_GET['msg'] === 'added') {
        echo "<script>alert('✅ User added successfully');</script>";
    }

    if ($_GET['msg'] === 'updated') {
        echo "<script>alert('✏️ User updated successfully');</script>";
    }

    if ($_GET['msg'] === 'deleted') {
        echo "<script>alert('🗑️ User deleted successfully');</script>";
    }

    if ($_GET['msg'] === 'error') {
        $error = $_GET['error'] ?? 'Something went wrong';
        echo "<script>alert('❌ Error: $error');</script>";
    }
}
?>

<!-- ALERT REPEAT FIX -->
<script>
    if (window.location.search.includes('msg=')) {
        window.history.replaceState({}, document.title, window.location.pathname);
    }
</script>