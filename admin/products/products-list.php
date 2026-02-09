<?php
$base_path = $_SERVER['DOCUMENT_ROOT'] . '/ecommerce-website/';
require_once $base_path . 'config/database.php';

include $base_path . 'includes/admin-header.php';
include $base_path . 'includes/sidebar.php';
include $base_path . 'includes/notifications.php';

// 1. Pagination & Filter Config
$current_category = isset($_GET['category']) ? $_GET['category'] : 'all';

if (!has_permission('products')) {
    echo "<script>alert('Access Denied'); window.location.href='../dashboard.php';</script>";
    exit;
}

// Fetch Categories for Filtert = ($page - 1) * $limit;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;
$search = $_GET['search'] ?? '';
$cat_id = $_GET['category'] ?? '';

$where = ["1=1"];
$params = [];

if ($search) {
    $where[] = "(p.name LIKE ? OR p.sku LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($cat_id) {
    $where[] = "p.category_id = ?";
    $params[] = $cat_id;
}

$where_sql = implode(" AND ", $where);

// 2. Fetch Total Count
$count_stmt = $pdo->prepare("SELECT COUNT(*) FROM products p WHERE $where_sql");
$count_stmt->execute($params);
$total_rows = $count_stmt->fetchColumn();
$total_pages = ceil($total_rows / $limit);

// 3. Fetch Products
$query = "SELECT p.*, c.name as category_name, s.name as supplier_name 
          FROM products p 
          LEFT JOIN categories c ON p.category_id = c.category_id 
          LEFT JOIN suppliers s ON p.preferred_supplier_id = s.supplier_id 
          WHERE $where_sql
          ORDER BY p.created_at DESC 
          LIMIT $limit OFFSET $offset";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll();

// 4. Fetch Dropdowns
$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();
$suppliers = $pdo->query("SELECT supplier_id, name FROM suppliers WHERE status = 1 ORDER BY name ASC")->fetchAll();
?>
<main class="p-6 lg:p-12bg-[#f8fafc] min-h-screen">
    <div class="mb-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
        <div>
            <h2 class="text-3xl font-black tracking-tighter text-slate-900">Master Catalog<span
                    class="text-orange-600">.</span></h2>
            <p class="text-slate-400 text-sm font-medium">Manage <?= $total_rows ?> products across your stores.</p>
        </div>

        <div class="flex flex-wrap gap-3">
            <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
                <button onclick="submitBulkDelete()" id="bulkDeleteBtn" style="display:none;"
                    class="bg-red-500 text-white px-6 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-red-600 transition-all shadow-sm">
                    <i class="fas fa-trash mr-2"></i> Delete Selected
                </button>
            <?php endif; ?>
            <button onclick="bulkEditPrices()"
                class="bg-white border border-slate-200 px-6 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-slate-50 transition-all shadow-sm">
                <i class="fas fa-tags mr-2"></i> Bulk Price Adjust
            </button>
            <a href="add-product.php"
                class="bg-slate-900 text-white px-8 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-orange-600 transition-all shadow-xl shadow-slate-200 flex items-center">
                + Add New Unit
            </a>
        </div>
    </div>

    <!-- Filters & Search (Keep as is) -->
    <div
        class="bg-white p-8 rounded-[3rem] border border-slate-100 shadow-sm mb-10 flex flex-col lg:flex-row justify-between items-center gap-6">
        <!-- ... (Keep Search Form) ... -->
        <form method="GET" class="flex flex-wrap items-center gap-4 w-full lg:w-auto">
            <div class="relative w-full md:w-72">
                <i class="fas fa-search absolute left-5 top-1/2 -translate-y-1/2 text-slate-300"></i>
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>"
                    placeholder="Search SKU or Name..."
                    class="w-full bg-slate-50 pl-14 pr-6 py-4 rounded-2xl text-xs font-bold outline-none border-2 border-transparent focus:border-orange-500 transition-all">
            </div>
            <select name="category" onchange="this.form.submit()"
                class="bg-slate-50 px-6 py-4 rounded-2xl text-xs font-bold outline-none border-2 border-transparent focus:border-orange-500 transition-all">
                <option value="">All Categories</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['category_id'] ?>" <?= ($cat_id == $cat['category_id']) ? 'selected' : '' ?>>
                        <?= $cat['name'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if ($search || $cat_id): ?>
                <a href="products-list.php"
                    class="text-[10px] font-black uppercase text-slate-400 hover:text-red-500 transition-all ml-2 underline">Clear
                    Filters</a>
            <?php endif; ?>
        </form>

        <div class="flex items-center gap-4 text-[10px] font-black uppercase tracking-widest text-slate-400">
            <span>Page <?= $page ?> of <?= $total_pages ?></span>
            <div class="flex gap-2">
                <?php if ($page > 1): ?>
                    <a href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>&category=<?= $cat_id ?>"
                        class="w-10 h-10 bg-slate-50 rounded-xl flex items-center justify-center hover:bg-slate-900 hover:text-white transition-all"><i
                            class="fas fa-chevron-left"></i></a>
                <?php endif; ?>
                <?php if ($page < $total_pages): ?>
                    <a href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>&category=<?= $cat_id ?>"
                        class="w-10 h-10 bg-slate-50 rounded-xl flex items-center justify-center hover:bg-slate-900 hover:text-white transition-all"><i
                            class="fas fa-chevron-right"></i></a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-[3rem] border border-slate-100 shadow-sm overflow-hidden overflow-x-auto">
        <form id="bulkDeleteForm" action="process_product.php" method="POST">
            <input type="hidden" name="action" value="bulk_delete">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                    <tr>
                        <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
                            <th class="px-8 py-6 w-10">
                                <input type="checkbox" id="selectAll" onclick="toggleSelectAll()"
                                    class="w-4 h-4 rounded text-orange-600 focus:ring-orange-500 cursor-pointer">
                            </th>
                        <?php endif; ?>
                        <th class="px-8 py-6">Product</th>
                        <th class="px-6 py-6">Category</th>
                        <th class="px-6 py-6 text-center">Stock</th>
                        <th class="px-6 py-6 text-right">Base Price</th>
                        <th class="px-8 py-6 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php foreach ($products as $p): ?>
                        <tr class="hover:bg-slate-50 transition-all group">
                            <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
                                <td class="px-8 py-5">
                                    <input type="checkbox" name="delete_ids[]" value="<?= $p['product_id'] ?>"
                                        onclick="checkSelection()"
                                        class="row-checkbox w-4 h-4 rounded text-orange-600 focus:ring-orange-500 cursor-pointer">
                                </td>
                            <?php endif; ?>
                            <td class="px-8 py-5 flex items-center gap-4">
                                <img src="../../assets/images/products/<?= $p['main_image'] ?: 'default.png' ?>"
                                    class="w-12 h-12 rounded-xl object-cover border border-slate-100">
                                <div>
                                    <p class="text-sm font-black text-slate-900 italic"><?= htmlspecialchars($p['name']) ?>
                                    </p>
                                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-tight">
                                        <?= $p['sku'] ?>
                                    </p>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <span
                                    class="px-3 py-1 bg-slate-100 text-slate-500 rounded-lg text-[9px] font-black uppercase"><?= $p['category_name'] ?: 'N/A' ?></span>
                            </td>
                            <td class="px-6 py-5 text-center">
                                <span
                                    class="text-sm font-black <?= ($p['stock'] <= $p['min_stock_level']) ? 'text-red-500' : 'text-slate-900' ?>">
                                    <?= $p['stock'] ?>
                                </span>
                            </td>
                            <td class="px-6 py-5 text-right font-black text-slate-900">
                                ₹<?= number_format($p['price'] ?? 0, 2) ?></td>
                            <td class="px-8 py-5">
                                <div class="flex justify-center gap-2 opacity-0 group-hover:opacity-100 transition-all">
                                    <button type="button"
                                        onclick='openVariantModal(<?= htmlspecialchars(json_encode($p), ENT_QUOTES, "UTF-8") ?>)'
                                        class="w-9 h-9 rounded-lg bg-purple-50 text-purple-600 hover:bg-purple-600 hover:text-white transition-all shadow-sm"
                                        title="Manage Variants">
                                        <i class="fas fa-layer-group text-xs"></i>
                                    </button>
                                    <a href="add-product.php?id=<?= $p['product_id'] ?>"
                                        class="w-9 h-9 rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-900 hover:text-white transition-all flex items-center justify-center">
                                        <i class="fas fa-pencil text-xs"></i>
                                    </a>
                                    <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
                                        <button type="button" onclick="deleteProduct(<?= $p['product_id'] ?>)"
                                            class="w-9 h-9 rounded-lg bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition-all">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($products)): ?>
                        <tr>
                            <td colspan="6" class="px-8 py-20 text-center">
                                <i class="fas fa-search text-slate-100 text-6xl mb-4"></i>
                                <p class="text-sm font-black text-slate-300 uppercase tracking-widest">No products found for
                                    this query</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </form>
    </div>
</main>

<script>
    function toggleSelectAll() {
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.row-checkbox');
        checkboxes.forEach(cb => cb.checked = selectAll.checked);
        checkSelection();
    }

    function checkSelection() {
        const checkboxes = document.querySelectorAll('.row-checkbox:checked');
        const bulkBtn = document.getElementById('bulkDeleteBtn');
        if (bulkBtn) {
            bulkBtn.style.display = checkboxes.length > 0 ? 'block' : 'none'; // changed to block for button
        }
    }

    function submitBulkDelete() {
        if (confirm('Are you sure you want to delete selected products? This action cannot be undone.')) {
            document.getElementById('bulkDeleteForm').submit();
        }
    }
</script>

<div id="actionModal" class="fixed inset-0 z-[110] hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeActionModal()"></div>

    <form id="productForm" action="process_product.php" method="POST" enctype="multipart/form-data"
        class="bg-[#f8fafc] w-full max-w-6xl rounded-[3rem] shadow-2xl relative z-10 overflow-hidden flex flex-col max-h-[95vh]">

        <input type="hidden" name="action" id="formAction" value="add">
        <input type="hidden" name="product_id" id="editProductId">

        <div class="p-8 border-b border-slate-100 flex justify-between items-center bg-white">
            <div>
                <h3 id="modalTitle" class="text-2xl font-black text-slate-900 uppercase tracking-tighter">Register New
                    Product</h3>
            </div>
            <button type="button" onclick="closeActionModal()"
                class="w-12 h-12 rounded-2xl bg-slate-50 flex items-center justify-center text-slate-400 hover:bg-red-50 hover:text-red-500 transition-all">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <div class="p-8 overflow-y-auto space-y-8 sidebar-scroll">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <div class="lg:col-span-2 space-y-8">
                    <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm space-y-6">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Product Core</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Product
                                    Title</label>
                                <input type="text" name="name" id="p_name" required class="input-premium w-full">
                            </div>
                            <div>
                                <label class="text-[10px] font-black uppercase text-slate-400 ml-2">SKU / ID</label>
                                <input type="text" name="sku" id="p_sku" required class="input-premium w-full">
                            </div>
                            <div>
                                <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Store
                                    Category</label>
                                <select name="category_id" id="p_cat" required class="input-premium w-full p-4">
                                    <option value="">Select Category</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= $cat['category_id'] ?>"><?= $cat['name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <textarea name="description" id="p_desc" rows="3" class="input-premium w-full"
                            placeholder="Internal product description or details..."></textarea>
                    </div>

                    <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm space-y-6">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Pricing Architecture
                            (₹)</p>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div><label class="text-[10px] font-black uppercase text-slate-400 ml-2">Retail
                                    Price</label><input type="number" name="price" id="p_price" step="0.01"
                                    class="input-premium"></div>
                            <div><label
                                    class="text-[10px] font-black uppercase text-slate-400 ml-2">Wholesale</label><input
                                    type="number" name="wholesale_price" id="p_wprice" step="0.01"
                                    class="input-premium"></div>
                            <div><label class="text-[10px] font-black uppercase text-slate-400 ml-2">Min
                                    Qty</label><input type="number" name="min_wholesale_qty" id="p_wqty"
                                    class="input-premium"></div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div><label class="text-[10px] font-black uppercase text-slate-400 ml-2">GST Rate
                                    %</label><input type="number" name="gst_percent" id="p_gst" class="input-premium">
                            </div>
                            <div><label class="text-[10px] font-black uppercase text-slate-400 ml-2">Discount
                                    %</label><input type="number" name="discount_percent" id="p_disc"
                                    class="input-premium"></div>
                        </div>
                    </div>
                </div>

                <div class="space-y-8">
                    <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm space-y-6">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Cover Image</p>
                        <div
                            class="w-full h-48 bg-slate-50 border-2 border-dashed border-slate-100 rounded-[2rem] relative overflow-hidden flex items-center justify-center">
                            <img id="main_preview" class="absolute inset-0 w-full h-full object-cover hidden">
                            <i class="fas fa-cloud-upload text-slate-200 text-3xl"></i>
                            <input type="file" name="main_image" accept="image/*"
                                onchange="previewImg(this, 'main_preview')"
                                class="absolute inset-0 opacity-0 cursor-pointer">
                        </div>
                    </div>

                    <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm space-y-6">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Inventory Hub</p>
                        <div><label class="text-[10px] font-black uppercase text-slate-400 ml-2">Base
                                Stock</label><input type="number" name="stock" id="p_stock" class="input-premium"></div>
                        <div><label class="text-[10px] font-black uppercase text-slate-400 ml-2">Low Stock
                                Alert</label><input type="number" name="min_stock_level" id="p_min"
                                class="input-premium"></div>
                        <div><label class="text-[10px] font-black uppercase text-slate-400 ml-2">Pref. Supplier</label>
                            <select name="preferred_supplier_id" id="p_sup" class="input-premium p-4">
                                <?php foreach ($suppliers as $s): ?>
                                    <option value="<?= $s['supplier_id'] ?>"><?= $s['name'] ?></option><?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" id="submitBtn"
                            class="w-full bg-slate-900 text-white py-6 rounded-[2rem] font-black uppercase tracking-[0.2em] hover:bg-cyan-600 transition-all shadow-xl">Commit
                            Catalog Entity</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Variant Modal preserved from original -->
<div id="variantModal" class="fixed inset-0 z-[130] hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeVariantModal()"></div>
    <div
        class="bg-white w-full max-w-3xl rounded-[3rem] shadow-2xl relative z-10 overflow-hidden flex flex-col max-h-[85vh]">
        <div class="p-8 border-b border-slate-100 flex justify-between items-center bg-purple-50/30">
            <div>
                <h3 id="var_productName" class="text-xl font-black text-slate-900 uppercase">Manage Variants</h3>
                <p id="var_productSku" class="text-[10px] font-bold text-slate-400"></p>
            </div>
            <button onclick="closeVariantModal()"
                class="w-10 h-10 rounded-xl bg-white text-slate-400 hover:text-red-500 transition-all"><i
                    class="fas fa-times"></i></button>
        </div>

        <div class="p-8 overflow-y-auto">
            <form id="addVariantForm" class="grid grid-cols-4 gap-4 mb-8 p-6 bg-slate-50 rounded-[2rem]">
                <input type="hidden" name="product_id" id="var_pid">
                <div>
                    <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Size</label>
                    <input type="text" name="size" placeholder="XL, 42..." class="input-premium w-full font-bold p-4">
                </div>
                <div>
                    <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Color</label>
                    <input type="text" name="color" placeholder="Red, Blue..."
                        class="input-premium w-full font-bold p-4">
                </div>
                <div>
                    <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Stock</label>
                    <input type="number" name="stock_qty" value="0" class="input-premium w-full font-bold p-4">
                </div>
                <div class="flex items-end">
                    <button type="button" onclick="saveVariant()"
                        class="w-full bg-purple-600 text-white py-4 rounded-2xl font-black uppercase text-[10px] hover:bg-slate-900 transition-all">Add</button>
                </div>
            </form>

            <div class="rounded-3xl border border-slate-100 overflow-hidden">
                <table class="w-full text-left">
                    <thead class="bg-slate-50 text-[10px] font-black text-slate-400 uppercase">
                        <tr>
                            <th class="px-6 py-4">Size</th>
                            <th class="px-6 py-4">Color</th>
                            <th class="px-6 py-4 text-center">Stock</th>
                            <th class="px-6 py-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody id="variantTableBody" class="divide-y divide-slate-50">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    // --- PRODUCT MODAL LOGIC (ADD/EDIT/VIEW) ---
    function openActionModal(mode, data = null) {
        const modal = document.getElementById('actionModal');
        const form = document.getElementById('productForm');
        const title = document.getElementById('modalTitle');
        const submitBtn = document.getElementById('submitBtn');
        const inputs = form.querySelectorAll('input, select, textarea');

        form.reset();
        document.getElementById('editProductId').value = '';
        document.getElementById('main_preview').classList.add('hidden');

        if ((mode === 'edit') && data) {
            title.innerText = "Refine Product Data";
            document.getElementById('formAction').value = "edit";
            document.getElementById('editProductId').value = data.product_id;

            const fields = {
                'p_name': data.name,
                'p_sku': data.sku,
                'p_cat': data.category_id,
                'p_price': data.price,
                'p_wprice': data.wholesale_price,
                'p_wqty': data.min_wholesale_qty,
                'p_gst': data.gst_percent,
                'p_disc': data.discount_percent,
                'p_desc': data.description,
                'p_stock': data.stock,
                'p_min': data.min_stock_level,
                'p_sup': data.preferred_supplier_id
            };

            Object.keys(fields).forEach(id => {
                const el = document.getElementById(id);
                if (el) el.value = fields[id] || '';
            });

            if (data.main_image) {
                const m = document.getElementById('main_preview');
                m.src = "../../assets/images/products/" + data.main_image;
                m.classList.remove('hidden');
            }
        } else {
            title.innerText = "Register New Asset";
            document.getElementById('formAction').value = "add";
        }

        modal.classList.replace('hidden', 'flex');
    }

    function closeActionModal() {
        document.getElementById('actionModal').classList.replace('flex', 'hidden');
    }

    function openVariantModal(product) {
        document.getElementById('var_productName').innerText = product.name;
        document.getElementById('var_productSku').innerText = "Chain ID: " + product.sku;
        document.getElementById('var_pid').value = product.product_id;
        loadVariants(product.product_id);
        document.getElementById('variantModal').classList.replace('hidden', 'flex');
    }

    function loadVariants(pid) {
        fetch(`get_variants.php?product_id=${pid}`)
            .then(res => res.json())
            .then(data => {
                const tbody = document.getElementById('variantTableBody');
                if (!data || data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="4" class="p-10 text-center text-slate-300 italic">No variants active</td></tr>';
                    return;
                }
                tbody.innerHTML = data.map(v => `
                <tr class="text-xs font-bold text-slate-600 border-b border-slate-50">
                    <td class="px-6 py-4 uppercase">${v.size || '-'}</td>
                    <td class="px-6 py-4">${v.color || '-'}</td>
                    <td class="px-6 py-4 text-center">${v.stock_qty}</td>
                    <td class="px-6 py-4 text-right">
                        <button onclick="deleteVariant(${v.variant_id}, ${pid})" class="w-8 h-8 rounded-lg bg-red-50 text-red-400 hover:bg-red-500 hover:text-white transition-all">
                            <i class="fas fa-trash text-[10px]"></i>
                        </button>
                    </td>
                </tr>
            `).join('');
            });
    }

    function saveVariant() {
        const form = document.getElementById('addVariantForm');
        const formData = new FormData(form);
        const pid = formData.get('product_id');
        fetch('save_variant.php', { method: 'POST', body: formData })
            .then(() => { form.reset(); document.getElementById('var_pid').value = pid; loadVariants(pid); });
    }

    function deleteVariant(vid, pid) {
        if (confirm("Exclude this variant?")) {
            fetch(`delete_variant.php?variant_id=${vid}`).then(() => loadVariants(pid));
        }
    }

    function closeVariantModal() {
        document.getElementById('variantModal').classList.replace('flex', 'hidden');
    }

    function previewImg(input, targetId) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = (e) => {
                const img = document.getElementById(targetId);
                img.src = e.target.result;
                img.classList.remove('hidden');
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function deleteProduct(id) {
        if (confirm("Permanently purge this unit from catalog?")) {
            window.location.href = `process_product.php?action=delete&id=${id}`;
        }
    }

    function bulkEditPrices() {
        alert("Bulk Price Adjustment module coming soon in Phase 6!");
    }
</script>

<style>
    .input-premium {
        @apply bg-slate-50 border-2 border-transparent focus:border-cyan-500 rounded-2xl p-4 text-sm font-bold outline-none transition-all;
    }

    .sidebar-scroll::-webkit-scrollbar {
        width: 4px;
    }

    .sidebar-scroll::-webkit-scrollbar-thumb {
        background: #e2e8f0;
        border-radius: 10px;
    }
</style>
<?php include $base_path . 'includes/admin-footer.php'; ?>