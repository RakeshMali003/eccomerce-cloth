<?php
session_start();
$base_path = $_SERVER['DOCUMENT_ROOT'] . '/ecommerce-website/';
require_once $base_path . 'config/database.php';

include $base_path . 'includes/admin-header.php';
include $base_path . 'includes/sidebar.php';
include $base_path . 'includes/notifications.php';

$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();
$suppliers = $pdo->query("SELECT supplier_id, name FROM suppliers WHERE status = 1 ORDER BY name ASC")->fetchAll();

// Handle Category Add
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    $cat_name = trim($_POST['category_name']);
    $cat_desc = trim($_POST['category_desc'] ?? '');

    if (!empty($cat_name)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
            $stmt->execute([$cat_name, $cat_desc]);
            $_SESSION['toast'] = ['msg' => 'Category added successfully!', 'type' => 'success'];
            header("Location: add-product.php");
            exit();
        } catch (Exception $e) {
            $_SESSION['toast'] = ['msg' => 'Error adding category', 'type' => 'error'];
        }
    }
}
?>

<style>
    .input-premium {
        @apply w-full bg-slate-50 border-2 border-transparent focus:border-orange-500 rounded-2xl p-4 text-sm font-bold outline-none transition-all;
    }

    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(4px);
        z-index: 100;
        display: none;
        justify-content: center;
        align-items: center;
    }

    .modal-overlay.active {
        display: flex;
    }

    .modal-content {
        background: white;
        border-radius: 2.5rem;
        padding: 2.5rem;
        width: 100%;
        max-width: 500px;
        margin: 1rem;
        transform: scale(0.9);
        opacity: 0;
        transition: all 0.3s ease;
    }

    .modal-overlay.active .modal-content {
        transform: scale(1);
        opacity: 1;
    }
</style>

<main class="p-6 lg:p-12 bg-[#f8fafc] min-h-screen">
    <div class="mb-10 flex justify-between items-end">
        <div>
            <h2 class="text-3xl font-black tracking-tighter text-slate-900">Add New Product<span
                    class="text-orange-600">.</span></h2>
            <p class="text-slate-400 text-sm font-medium">Register a new item into your master catalog.</p>
        </div>
        <a href="products-list.php" class="text-slate-400 font-bold text-sm hover:text-slate-900 transition-all">
            <i class="fas fa-arrow-left mr-2"></i> Back to Products
        </a>
    </div>

    <form action="process_product.php" method="POST" enctype="multipart/form-data" class="space-y-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <div class="lg:col-span-2 space-y-8">
                <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm space-y-6">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4">General Information
                    </p>

                    <div>
                        <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Product Name</label>
                        <input type="text" name="name" required placeholder="e.g., Premium Cotton Fabric"
                            class="input-premium">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-[10px] font-black uppercase text-slate-400 ml-2">SKU (Stock Keeping
                                Unit)</label>
                            <input type="text" name="sku" required placeholder="GK-1024" class="input-premium">
                        </div>
                        <div>
                            <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Category</label>
                            <select name="category_id" required class="input-premium appearance-none">
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['category_id'] ?>"><?= htmlspecialchars($cat['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Description</label>
                        <textarea name="description" rows="3" class="input-premium"
                            placeholder="Detail product specifications..."></textarea>
                    </div>
                </div>

                <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-6">Commercials &
                        Pricing</p>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Retail Price (₹)</label>
                            <input type="number" name="price" step="0.01" required class="input-premium"
                                placeholder="0.00">
                        </div>
                        <div>
                            <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Wholesale (₹)</label>
                            <input type="number" name="wholesale_price" step="0.01" class="input-premium"
                                placeholder="0.00">
                        </div>
                        <div>
                            <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Min Wholesale
                                Qty</label>
                            <input type="number" name="min_wholesale_qty" value="1" class="input-premium">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4 mt-4">
                        <div>
                            <label class="text-[10px] font-black uppercase text-slate-400 ml-2">GST (%)</label>
                            <input type="number" name="gst_percent" value="0" class="input-premium">
                        </div>
                        <div>
                            <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Max Discount (%)</label>
                            <input type="number" name="discount_percent" value="0" class="input-premium">
                        </div>
                    </div>
                </div>

                <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-6">Product Media</p>

                    <div class="space-y-6">
                        <div class="p-6 border-2 border-dashed border-slate-100 rounded-3xl bg-slate-50/50">
                            <label class="text-[10px] font-black uppercase text-slate-400 block mb-2 text-center">Main
                                Display Image (Required)</label>
                            <input type="file" name="main_image" accept="image/*" required
                                class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-slate-900 file:text-white hover:file:bg-orange-600 transition-all cursor-pointer">
                        </div>

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <?php for ($i = 1; $i <= 4; $i++): ?>
                                <div class="p-4 border border-slate-100 rounded-3xl bg-white text-center">
                                    <label class="text-[8px] font-black uppercase text-slate-400 block mb-2">Gallery
                                        <?= $i ?></label>
                                    <input type="file" name="image_<?= $i ?>" accept="image/*"
                                        class="w-full text-[8px] text-slate-400 file:hidden cursor-pointer">
                                </div>
                            <?php endfor; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-8">
                <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm space-y-6">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4">Inventory Info</p>
                    <div>
                        <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Current Stock</label>
                        <input type="number" name="stock" value="0" class="input-premium">
                    </div>
                    <div>
                        <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Low Stock Alert
                            Level</label>
                        <input type="number" name="min_stock_level" value="5" class="input-premium">
                    </div>
                </div>

                <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm space-y-6">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4">Vendor Link</p>
                    <div>
                        <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Preferred Supplier</label>
                        <select name="preferred_supplier_id" class="input-premium appearance-none">
                            <option value="">None / Multiple</option>
                            <?php foreach ($suppliers as $sup): ?>
                                <option value="<?= $sup['supplier_id'] ?>"><?= htmlspecialchars($sup['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Status</label>
                        <select name="status" class="input-premium appearance-none">
                            <option value="1">Active (Visible)</option>
                            <option value="0">Draft / Suspended</option>
                        </select>
                    </div>
                </div>

                <button type="submit"
                    class="w-full bg-slate-900 text-white py-6 rounded-[2rem] font-black uppercase tracking-[0.2em] shadow-xl hover:bg-orange-600 transition-all hover:-translate-y-1 active:scale-95">
                    Create Product Record
                </button>
            </div>
        </div>
    </form>
</main>

<!-- Category Modal -->
<div id="categoryModal" class="modal-overlay" onclick="closeCategoryModal(event)">
    <div class="modal-content" onclick="event.stopPropagation()">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-black text-slate-900">Add New Category</h3>
            <button onclick="closeCategoryModal()"
                class="w-10 h-10 rounded-full bg-slate-100 text-slate-400 hover:bg-slate-200 transition-all">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form action="add-product.php" method="POST">
            <input type="hidden" name="add_category" value="1">

            <div class="space-y-4">
                <div>
                    <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Category Name <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="category_name" required placeholder="e.g., Silk Sarees"
                        class="input-premium">
                </div>
                <div>
                    <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Description (Optional)</label>
                    <textarea name="category_desc" rows="2" class="input-premium"
                        placeholder="Category description..."></textarea>
                </div>
            </div>

            <div class="flex gap-3 mt-6">
                <button type="button" onclick="closeCategoryModal()"
                    class="flex-1 py-4 bg-slate-100 text-slate-600 rounded-2xl font-bold hover:bg-slate-200 transition-all">
                    Cancel
                </button>
                <button type="submit"
                    class="flex-1 py-4 bg-orange-600 text-white rounded-2xl font-bold hover:bg-orange-700 transition-all">
                    <i class="fas fa-plus mr-2"></i> Add Category
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openCategoryModal() {
        document.getElementById('categoryModal').classList.add('active');
    }

    function closeCategoryModal(event) {
        if (event && event.target !== event.currentTarget) return;
        document.getElementById('categoryModal').classList.remove('active');
    }

    // Close modal on Escape key
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeCategoryModal();
    });
</script>

<?php include $base_path . 'includes/admin-footer.php'; ?>