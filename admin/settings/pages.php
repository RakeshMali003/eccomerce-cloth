<?php
$base_path = __DIR__ . '/../../';
require_once "../../config/database.php";
require_once "../../includes/functions.php";
require_once "../../includes/cms_config.php";

include $base_path . 'includes/admin-header.php';
include $base_path . 'includes/sidebar.php';
include $base_path . 'includes/notifications.php';

// Auth Check
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../index.php");
    exit;
}

$selected_page = $_GET['page'] ?? 'about';
$page_config = $cms_config[$selected_page] ?? [];
$msg = '';

// Handle Save
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($page_config as $key => $config) {
        $value = $_POST[$key] ?? '';

        // Handle Image Upload
        if ($config['type'] === 'image') {
            if (isset($_FILES[$key]) && $_FILES[$key]['error'] === UPLOAD_ERR_OK) {
                $uploadDir = $base_path . 'assets/images/cms/';
                if (!is_dir($uploadDir))
                    mkdir($uploadDir, 0777, true);

                $fileName = time() . '_' . basename($_FILES[$key]['name']);
                $targetPath = $uploadDir . $fileName;

                if (move_uploaded_file($_FILES[$key]['tmp_name'], $targetPath)) {
                    $value = 'assets/images/cms/' . $fileName;
                }
            } else {
                // Keep existing value if no new file uploaded
                $value = $_POST['existing_' . $key] ?? '';
            }
        }

        // Insert or Update
        $stmt = $pdo->prepare("INSERT INTO page_content (page_name, section_key, content_value, content_type) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE content_value = ?");
        $stmt->execute([$selected_page, $key, $value, $config['type'], $value]);
    }
    $msg = "Content updated successfully!";
}

// Fetch Current Content
$stmt = $pdo->prepare("SELECT section_key, content_value FROM page_content WHERE page_name = ?");
$stmt->execute([$selected_page]);
$current_content = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

?>

<main class="p-6 lg:p-12 bg-[#f8fafc] min-h-screen">
    <div class="max-w-4xl mx-auto">
        <div class="mb-10 flex justify-between items-center">
            <div>
                <h2 class="text-3xl font-black tracking-tighter text-slate-900">Content Manager<span
                        class="text-orange-600">.</span></h2>
                <p class="text-slate-400 text-sm font-medium">Edit website pages content</p>
            </div>
        </div>

        <?php if ($msg): ?>
            <div
                class="bg-emerald-100 text-emerald-700 p-4 rounded-xl mb-6 font-bold text-center border-l-4 border-emerald-500">
                <?= $msg ?>
            </div>
        <?php endif; ?>

        <!-- Page Tabs -->
        <div class="flex gap-4 mb-8 overflow-x-auto pb-2">
            <?php foreach (array_keys($cms_config) as $page): ?>
                <a href="?page=<?= $page ?>"
                    class="px-6 py-3 rounded-xl font-bold uppercase text-xs tracking-widest transition-all <?= $selected_page === $page ? 'bg-slate-900 text-white shadow-lg' : 'bg-white text-slate-500 hover:bg-slate-50' ?>">
                    <?= ucfirst($page) ?>
                </a>
            <?php endforeach; ?>
        </div>

        <div class="bg-white p-10 rounded-[2.5rem] shadow-xl border border-slate-100">
            <h3 class="text-xl font-black text-slate-800 mb-8 uppercase tracking-widest border-b pb-4">Editing: <span
                    class="text-orange-600">
                    <?= ucfirst($selected_page) ?>
                </span></h3>

            <form method="POST" enctype="multipart/form-data" class="space-y-8">
                <?php foreach ($page_config as $key => $config):
                    $val = $current_content[$key] ?? '';
                    ?>
                    <div class="space-y-2">
                        <label class="text-[11px] font-black uppercase text-slate-400 ml-1 tracking-wider block">
                            <?= $config['label'] ?>
                        </label>

                        <?php if ($config['type'] === 'textarea'): ?>
                            <textarea name="<?= $key ?>" rows="4"
                                class="w-full px-6 py-4 bg-slate-50 rounded-2xl border-none focus:ring-2 focus:ring-orange-500/20 font-medium outline-none text-slate-900 transition-all"><?= htmlspecialchars($val) ?></textarea>

                        <?php elseif ($config['type'] === 'image'): ?>
                            <div class="flex items-center gap-6">
                                <?php if ($val): ?>
                                    <div class="relative group">
                                        <img src="<?= $base_path . $val ?>"
                                            class="w-24 h-24 object-cover rounded-xl border-2 border-slate-100">
                                        <input type="hidden" name="existing_<?= $key ?>" value="<?= htmlspecialchars($val) ?>">
                                    </div>
                                <?php endif; ?>
                                <input type="file" name="<?= $key ?>" accept="image/*"
                                    class="block w-full text-sm text-slate-500 file:mr-4 file:py-3 file:px-6 file:rounded-xl file:border-0 file:text-xs file:font-black file:uppercase file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 transition-all">
                            </div>

                        <?php else: ?>
                            <input type="text" name="<?= $key ?>" value="<?= htmlspecialchars($val) ?>"
                                class="w-full px-6 py-4 bg-slate-50 rounded-2xl border-none focus:ring-2 focus:ring-orange-500/20 font-bold outline-none text-slate-900 transition-all">
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>

                <div class="pt-6 border-t border-slate-50">
                    <button type="submit"
                        class="w-full bg-slate-900 text-white py-5 rounded-2xl font-black uppercase tracking-widest hover:bg-orange-600 transition-all shadow-lg hover:shadow-orange-200">
                        Update Content
                    </button>
                    <p class="text-center text-[10px] text-slate-400 font-bold uppercase mt-4">Changes reflect
                        immediately on frontend</p>
                </div>
            </form>
        </div>
    </div>
</main>
<?php include $base_path . "includes/admin-footer.php"; ?>