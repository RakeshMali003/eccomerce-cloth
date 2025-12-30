<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/ecommerce-website/config/database.php';

// 1. Security Check
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$success_msg = "";
$error_msg = "";
$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'personal';

// 2. LOGIC: PROFILE UPDATE
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $name     = $_POST['name'];
    $phone    = $_POST['phone'];
    $address  = $_POST['address'];
    $city     = $_POST['city'];
    $pincode  = $_POST['pincode'];

    $sql = "UPDATE users SET name = ?, phone = ?, address = ?, city = ?, pincode = ?, updated_at = NOW() WHERE user_id = ?";
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute([$name, $phone, $address, $city, $pincode, $user_id])) {
        $success_msg = "Profile synchronized successfully!";
        $_SESSION['user_name'] = $name;
    } else {
        $error_msg = "Database sync failed.";
    }
}

// 3. LOGIC: PASSWORD UPDATE
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    $stmt = $pdo->prepare("SELECT password FROM users WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $user_data = $stmt->fetch();

    if ($user_data && password_verify($current_password, $user_data['password'])) {
        if ($new_password === $confirm_password && strlen($new_password) >= 8) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_stmt = $pdo->prepare("UPDATE users SET password = ?, updated_at = NOW() WHERE user_id = ?");
            
            if ($update_stmt->execute([$hashed_password, $user_id])) {
                $success_msg = "Security credentials updated successfully!";
                $active_tab = 'security';
            }
        } else {
            $error_msg = "New passwords must match and be at least 8 characters.";
            $active_tab = 'security';
        }
    } else {
        $error_msg = "Current password verification failed.";
        $active_tab = 'security';
    }
}

// 4. FETCH CURRENT STATE
$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
?>

<?php include '../../includes/header.php'; ?>

<script src="https://cdn.tailwindcss.com"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');
    
    body { 
        font-family: 'Plus Jakarta Sans', sans-serif; 
        background-color: #fcfcfc; 
        color: #0f172a;
    }

    /* Bento Grid Cards */
    .nav-card, .content-card {
        background: white;
        border-radius: 2rem;
        border: 1px solid #f1f5f9;
        padding: 2rem;
        box-shadow: 0 4px 20px -5px rgba(0,0,0,0.05);
    }

    /* Navigation Items */
    .nav-item {
        width: 100%;
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 1rem 1.5rem;
        border-radius: 1.25rem;
        font-size: 0.875rem;
        font-weight: 700;
        transition: all 0.3s ease;
        margin-bottom: 0.5rem;
    }

    .nav-item-active {
        background: #0f172a;
        color: white;
        box-shadow: 0 10px 15px -3px rgba(15, 23, 42, 0.2);
    }

    .nav-item-inactive {
        color: #64748b;
    }

    .nav-item-inactive:hover {
        background: #f8fafc;
        color: #0f172a;
    }

    /* Premium Inputs */
    .input-premium {
        width: 100%;
        padding: 1rem 1.25rem;
        background: #f8fafc;
        border: 2px solid transparent;
        border-radius: 1.25rem;
        font-size: 0.875rem;
        font-weight: 600;
        transition: all 0.3s ease;
        outline: none;
    }

    .input-premium:focus {
        background: white;
        border-color: #FF6F1E;
        box-shadow: 0 0 0 4px rgba(255, 111, 30, 0.1);
    }

    .label-premium {
        display: block;
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: #94a3b8;
        margin-bottom: 0.75rem;
        padding-left: 0.5rem;
    }

    /* Buttons */
    .btn-sync {
        background: #FF6F1E;
        color: white;
        padding: 1rem 2.5rem;
        border-radius: 1.25rem;
        font-weight: 800;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 1px;
        transition: all 0.3s ease;
    }

    .btn-sync:hover {
        background: #0f172a;
        transform: translateY(-2px);
        box-shadow: 0 15px 30px -5px rgba(255, 111, 30, 0.3);
    }

    /* Animation */
    .animate-fadeIn {
        animation: fadeIn 0.4s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<div class="container mx-auto px-4 lg:px-12 py-10 lg:py-16">
    <div class="flex flex-col lg:row gap-10 lg:flex-row">
        
        <div class="w-full lg:w-1/3 space-y-6">
            <div class="nav-card text-center">
                <div class="relative inline-block mb-6">
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($user['name']); ?>&background=FF6F1E&color=fff&size=128" 
                         class="w-28 h-28 rounded-[2.5rem] shadow-2xl border-4 border-white">
                    <div class="absolute -bottom-2 -right-2 w-8 h-8 bg-slate-900 text-white rounded-xl flex items-center justify-center text-xs shadow-lg">
                        <i class="fas fa-camera"></i>
                    </div>
                </div>
                <h2 class="text-2xl font-extrabold tracking-tight"><?php echo htmlspecialchars($user['name']); ?></h2>
                <p class="text-slate-400 text-sm font-medium mb-4 italic"><?php echo htmlspecialchars($user['email']); ?></p>
                <span class="px-4 py-1.5 bg-orange-50 text-orange-600 rounded-full text-[9px] font-black uppercase tracking-widest border border-orange-100">
                    <?php echo htmlspecialchars($user['role']); ?> Member
                </span>
            </div>

            <div class="nav-card">
                <div class="flex flex-col gap-2">
                    <button onclick="window.location.href='?tab=personal'" class="nav-item <?php echo $active_tab == 'personal' ? 'nav-item-active' : 'nav-item-inactive'; ?>">
                        <i class="fas fa-user-circle w-5"></i> Personal Info
                    </button>
                    <button onclick="window.location.href='?tab=security'" class="nav-item <?php echo $active_tab == 'security' ? 'nav-item-active' : 'nav-item-inactive'; ?>">
                        <i class="fas fa-shield-alt w-5"></i> Security Settings
                    </button>
                    <a href="../auth/logout.php" class="nav-item text-red-500 hover:bg-red-50">
                        <i class="fas fa-sign-out-alt w-5"></i> Secure Logout
                    </a>
                </div>
            </div>
        </div>

        <div class="w-full lg:w-2/3">
            
            <?php if($success_msg): ?>
                <div class="mb-8 p-5 bg-emerald-500 text-white rounded-[1.5rem] flex items-center gap-4">
                    <i class="fas fa-check-circle"></i>
                    <span class="text-[10px] font-black uppercase tracking-widest"><?php echo $success_msg; ?></span>
                </div>
            <?php endif; ?>

            <?php if($error_msg): ?>
                <div class="mb-8 p-5 bg-red-500 text-white rounded-[1.5rem] flex items-center gap-4">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span class="text-[10px] font-black uppercase tracking-widest"><?php echo $error_msg; ?></span>
                </div>
            <?php endif; ?>

            <?php if($active_tab == 'personal'): ?>
            <div class="content-card animate-fadeIn">
                <h3 class="text-xl font-extrabold tracking-tight mb-10 border-b border-slate-50 pb-6">Account Synchronization</h3>
                <form action="?tab=personal" method="POST" class="space-y-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <label class="label-premium">Display Name</label>
                            <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" class="input-premium">
                        </div>
                        <div>
                            <label class="label-premium">Contact Number</label>
                            <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" class="input-premium">
                        </div>
                        <div class="md:col-span-2">
                            <label class="label-premium">Street Address</label>
                            <textarea name="address" rows="3" class="input-premium resize-none"><?php echo htmlspecialchars($user['address']); ?></textarea>
                        </div>
                        <div>
                            <label class="label-premium">City</label>
                            <input type="text" name="city" value="<?php echo htmlspecialchars($user['city']); ?>" class="input-premium">
                        </div>
                        <div>
                            <label class="label-premium">Zip Code</label>
                            <input type="text" name="pincode" value="<?php echo htmlspecialchars($user['pincode']); ?>" class="input-premium">
                        </div>
                    </div>
                    <div class="flex justify-end pt-6 border-t border-slate-50">
                        <button type="submit" name="update_profile" class="btn-sync">Synchronize Profile</button>
                    </div>
                </form>
            </div>
            <?php endif; ?>

            <?php if($active_tab == 'security'): ?>
            <div class="content-card animate-fadeIn">
                <div class="flex items-center gap-4 mb-10 border-b border-slate-50 pb-6">
                    <div class="w-10 h-10 bg-orange-50 text-orange-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-lock"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-extrabold tracking-tight">Access Control</h3>
                        <p class="text-slate-400 text-xs font-medium">Manage your vault password</p>
                    </div>
                </div>

                <form action="?tab=security" method="POST" class="space-y-8 max-w-xl">
                    <div>
                        <label class="label-premium">Current Password</label>
                        <input type="password" name="current_password" required class="input-premium" placeholder="••••••••">
                    </div>
                    <div class="space-y-6">
                        <div>
                            <label class="label-premium">New Password</label>
                            <input type="password" name="new_password" required class="input-premium" placeholder="Min 8 characters">
                        </div>
                        <div>
                            <label class="label-premium">Confirm New Password</label>
                            <input type="password" name="confirm_password" required class="input-premium" placeholder="Re-type new password">
                        </div>
                    </div>
                    <div class="pt-6">
                        <button type="submit" name="change_password" class="btn-sync w-full md:w-auto">Update Access Keys</button>
                    </div>
                </form>
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>