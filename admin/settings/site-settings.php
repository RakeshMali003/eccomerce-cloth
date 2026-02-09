<?php
$base_path = $_SERVER['DOCUMENT_ROOT'] . '/ecommerce-website/';
require_once "../../config/database.php";
require_once "../../includes/functions.php";

include $base_path . 'includes/admin-header.php';
include $base_path . 'includes/sidebar.php';
include $base_path . 'includes/notifications.php';

if (!has_permission('settings')) {
    echo "<script>alert('Access Denied'); window.location.href='../dashboard.php';</script>";
    exit;
}

// Helper to get setting
function get_setting($pdo, $key)
{
    $stmt = $pdo->prepare("SELECT setting_value FROM site_settings WHERE setting_key = ?");
    $stmt->execute([$key]);
    return $stmt->fetchColumn() ?: '';
}

// Handle Save
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $settings = [
        'site_name',
        'site_phone',
        'site_email',
        'site_address',
        'map_embed_url',
        'about_us_text',
        'wholesale_details',
        'gst_number'
    ];

    foreach ($settings as $key) {
        if (isset($_POST[$key])) {
            $val = $_POST[$key];
            $sql = "INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?";
            $pdo->prepare($sql)->execute([$key, $val, $val]);
        }
    }

    // Handle File Uploads (Logo, Banner)
    $uploads = ['site_logo', 'home_banner'];
    foreach ($uploads as $key) {
        if (isset($_FILES[$key]) && $_FILES[$key]['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES[$key]['name'], PATHINFO_EXTENSION);
            $filename = $key . '_' . time() . '.' . $ext;
            $target = $base_path . 'assets/uploads/' . $filename;

            if (move_uploaded_file($_FILES[$key]['tmp_name'], $target)) {
                $val = $filename;
                $sql = "INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?";
                $pdo->prepare($sql)->execute([$key, $val, $val]);
            }
        }
    }

    echo "<script>alert('Settings Saved Successfully'); window.location.href='site-settings.php';</script>";
}
?>

<main class="p-6 lg:p-12 bg-[#f8fafc] min-h-screen">
    <div class="mb-10">
        <h2 class="text-3xl font-black tracking-tighter text-slate-900">System Configuration<span
                class="text-orange-600">.</span></h2>
        <p class="text-slate-400 text-sm font-medium">Manage global website attributes and content</p>
    </div>

    <form method="POST" enctype="multipart/form-data" class="max-w-4xl space-y-8">

        <!-- General Identity -->
        <div class="bg-white p-8 rounded-[3rem] shadow-sm border border-slate-100">
            <h3 class="text-xl font-bold mb-6">Brand Identity</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-1">
                    <label class="text-[9px] font-black uppercase text-slate-400 ml-2">Website Name</label>
                    <input type="text" name="site_name" value="<?= htmlspecialchars(get_setting($pdo, 'site_name')) ?>"
                        class="w-full px-6 py-4 bg-slate-50 rounded-2xl border-none focus:ring-2 focus:ring-orange-500/20 font-bold outline-none">
                </div>
                <div class="space-y-1">
                    <label class="text-[9px] font-black uppercase text-slate-400 ml-2">GST Number</label>
                    <input type="text" name="gst_number"
                        value="<?= htmlspecialchars(get_setting($pdo, 'gst_number')) ?>"
                        class="w-full px-6 py-4 bg-slate-50 rounded-2xl border-none focus:ring-2 focus:ring-orange-500/20 font-bold outline-none">
                </div>
                <div class="space-y-1">
                    <label class="text-[9px] font-black uppercase text-slate-400 ml-2">Logo Upload</label>
                    <input type="file" name="site_logo"
                        class="w-full px-6 py-4 bg-slate-50 rounded-2xl file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100">
                    <?php if ($logo = get_setting($pdo, 'site_logo')): ?>
                        <img src="../../assets/uploads/<?= $logo ?>" class="h-12 mt-2 ml-4">
                    <?php endif; ?>
                </div>
                <div class="space-y-1">
                    <label class="text-[9px] font-black uppercase text-slate-400 ml-2">Home Banner</label>
                    <input type="file" name="home_banner"
                        class="w-full px-6 py-4 bg-slate-50 rounded-2xl file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100">
                    <?php if ($banner = get_setting($pdo, 'home_banner')): ?>
                        <img src="../../assets/uploads/<?= $banner ?>" class="h-12 mt-2 ml-4 object-cover rounded-lg">
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Contact Info -->
        <div class="bg-white p-8 rounded-[3rem] shadow-sm border border-slate-100">
            <h3 class="text-xl font-bold mb-6">Contact Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-1">
                    <label class="text-[9px] font-black uppercase text-slate-400 ml-2">Phone Number</label>
                    <input type="text" name="site_phone"
                        value="<?= htmlspecialchars(get_setting($pdo, 'site_phone')) ?>"
                        class="w-full px-6 py-4 bg-slate-50 rounded-2xl border-none focus:ring-2 focus:ring-orange-500/20 font-bold outline-none">
                </div>
                <div class="space-y-1">
                    <label class="text-[9px] font-black uppercase text-slate-400 ml-2">Email Address</label>
                    <input type="email" name="site_email"
                        value="<?= htmlspecialchars(get_setting($pdo, 'site_email')) ?>"
                        class="w-full px-6 py-4 bg-slate-50 rounded-2xl border-none focus:ring-2 focus:ring-orange-500/20 font-bold outline-none">
                </div>
                <div class="col-span-1 md:col-span-2 space-y-1">
                    <label class="text-[9px] font-black uppercase text-slate-400 ml-2">Physical Address</label>
                    <textarea name="site_address" rows="2"
                        class="w-full px-6 py-4 bg-slate-50 rounded-2xl border-none focus:ring-2 focus:ring-orange-500/20 font-medium outline-none"><?= htmlspecialchars(get_setting($pdo, 'site_address')) ?></textarea>
                </div>
                <div class="col-span-1 md:col-span-2 space-y-1">
                    <label class="text-[9px] font-black uppercase text-slate-400 ml-2">Google Map Embed Link (iframe
                        src)</label>
                    <input type="text" name="map_embed_url"
                        value="<?= htmlspecialchars(get_setting($pdo, 'map_embed_url')) ?>"
                        class="w-full px-6 py-4 bg-slate-50 rounded-2xl border-none focus:ring-2 focus:ring-orange-500/20 font-medium outline-none text-xs">
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="bg-white p-8 rounded-[3rem] shadow-sm border border-slate-100">
            <h3 class="text-xl font-bold mb-6">Page Content</h3>
            <div class="space-y-6">
                <div class="space-y-1">
                    <label class="text-[9px] font-black uppercase text-slate-400 ml-2">About Us Text</label>
                    <textarea name="about_us_text" rows="5"
                        class="w-full px-6 py-4 bg-slate-50 rounded-2xl border-none focus:ring-2 focus:ring-orange-500/20 font-medium outline-none"><?= htmlspecialchars(get_setting($pdo, 'about_us_text')) ?></textarea>
                </div>
                <div class="space-y-1">
                    <label class="text-[9px] font-black uppercase text-slate-400 ml-2">Wholesale Details</label>
                    <textarea name="wholesale_details" rows="5"
                        class="w-full px-6 py-4 bg-slate-50 rounded-2xl border-none focus:ring-2 focus:ring-orange-500/20 font-medium outline-none"><?= htmlspecialchars(get_setting($pdo, 'wholesale_details')) ?></textarea>
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit"
                class="bg-slate-900 text-white px-10 py-5 rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-orange-600 transition-all shadow-xl">
                Save Configuration
            </button>
        </div>

    </form>
</main>
<?php include $base_path . "includes/admin-footer.php"; ?>