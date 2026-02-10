<?php
$base_path = __DIR__ . '/../../';
require_once $base_path . 'config/database.php';
include $base_path . 'includes/admin-header.php';
include $base_path . 'includes/sidebar.php';
include $base_path . 'includes/notifications.php';
include $base_path . 'admin/includes/navbar.php';

if (!has_permission('products')) {
    echo "<script>alert('Access Denied'); window.location.href='products-list.php?error=access_denied';</script>";
    exit;
}

$product_id = $_GET['id'] ?? null;
$product = null;
$action = 'add';
$title = 'Initialize New Stock Unit';
$subtitle = 'Add a new product to your inventory catalog';

if ($product_id) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE product_id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        $action = 'edit';
        $title = 'Modify Product Datasheet';
        $subtitle = 'Update existing inventory specifications';
    }
}

// Fetch Dropdowns
$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();
$suppliers = $pdo->query("SELECT * FROM suppliers WHERE status = 1 ORDER BY name ASC")->fetchAll();
?>

<main class="p-6 lg:p-10 bg-slate-50/50 min-h-screen">
    
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-6">
            <div>
                <a href="products-list.php" class="text-xs font-bold text-slate-400 hover:text-orange-600 transition-colors uppercase tracking-widest mb-2 block">
                    <i class="fas fa-arrow-left mr-2"></i> Return to Catalog
                </a>
                <h2 class="text-3xl font-black tracking-tight text-slate-800"><?php echo $title; ?><span class="text-orange-600">.</span></h2>
                <p class="text-slate-500 font-medium text-sm"><?php echo $subtitle; ?></p>
            </div>
            
            <button form="productForm" type="submit" 
                class="bg-slate-900 text-white px-10 py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-orange-600 transition-all shadow-xl shadow-slate-200 flex items-center gap-3">
                <i class="fas fa-save text-lg"></i> Save Changes
            </button>
        </div>

        <form id="productForm" action="process_product.php" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <input type="hidden" name="action" value="<?php echo $action; ?>">
            <?php if($product_id): ?>
                <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
            <?php endif; ?>

            <!-- LEFT COLUMN (Main Data) -->
            <div class="lg:col-span-2 space-y-8">
                
                <!-- Core Info -->
                <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm relative group overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-orange-50 rounded-bl-[100%] -mr-16 -mt-16 transition-all group-hover:bg-orange-100"></div>
                    
                    <h4 class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-6 relative z-10">Core Specifications</h4>
                    
                    <div class="space-y-6 relative z-10">
                        <div>
                            <label class="block text-[10px] font-bold uppercase text-slate-400 mb-2 ml-2">Product Name</label>
                            <input type="text" name="name" value="<?php echo htmlspecialchars($product['name'] ?? ''); ?>" required
                                class="w-full bg-slate-50 border-none rounded-2xl px-6 py-4 text-sm font-bold text-slate-700 outline-none focus:ring-2 focus:ring-orange-500/20 transition-all placeholder:text-slate-300"
                                placeholder="e.g. Industrial Circuit Breaker 500V">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-[10px] font-bold uppercase text-slate-400 mb-2 ml-2">SKU / Identifier</label>
                                <input type="text" name="sku" value="<?php echo htmlspecialchars($product['sku'] ?? ''); ?>" required
                                    class="w-full bg-slate-50 border-none rounded-2xl px-6 py-4 text-sm font-bold text-slate-700 outline-none focus:ring-2 focus:ring-orange-500/20 transition-all placeholder:text-slate-300"
                                    placeholder="e.g. EL-2024-001">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold uppercase text-slate-400 mb-2 ml-2">Category</label>
                                <select name="category_id" required
                                    class="w-full bg-slate-50 border-none rounded-2xl px-6 py-4 text-sm font-bold text-slate-700 outline-none focus:ring-2 focus:ring-orange-500/20 transition-all cursor-pointer">
                                    <option value="">Select Classification</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?php echo $cat['category_id']; ?>" <?php echo ($product['category_id'] ?? '') == $cat['category_id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($cat['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="button" onclick="openCategoryModal()" class="mt-2 text-xs font-bold text-orange-600 hover:text-orange-700 flex items-center gap-1">
                                    <i class="fas fa-plus-circle"></i> Quick Add Category
                                </button>
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold uppercase text-slate-400 mb-2 ml-2">Technical Description</label>
                            <textarea name="description" rows="5"
                                class="w-full bg-slate-50 border-none rounded-2xl px-6 py-4 text-sm font-medium text-slate-600 outline-none focus:ring-2 focus:ring-orange-500/20 transition-all placeholder:text-slate-300 resize-none"
                                placeholder="Detailed technical specifications and features..."><?php echo htmlspecialchars($product['description'] ?? ''); ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Pricing & Financials -->
                <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm relative group overflow-hidden">
                    <h4 class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-6">Financial Architecture</h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-[10px] font-bold uppercase text-slate-400 mb-2 ml-2">Retail Price (₹)</label>
                            <input type="number" step="0.01" name="price" value="<?php echo $product['price'] ?? ''; ?>" required
                                class="w-full bg-slate-50 border-none rounded-2xl px-6 py-4 text-lg font-black text-slate-700 outline-none focus:ring-2 focus:ring-orange-500/20 transition-all">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold uppercase text-slate-400 mb-2 ml-2">Wholesale (₹)</label>
                            <input type="number" step="0.01" name="wholesale_price" value="<?php echo $product['wholesale_price'] ?? ''; ?>"
                                class="w-full bg-slate-50 border-none rounded-2xl px-6 py-4 text-lg font-black text-slate-700 outline-none focus:ring-2 focus:ring-orange-500/20 transition-all">
                        </div>
                         <div>
                            <label class="block text-[10px] font-bold uppercase text-slate-400 mb-2 ml-2">Min Wholesale Qty</label>
                             <input type="number" name="min_wholesale_qty" value="<?php echo $product['min_wholesale_qty'] ?? 1; ?>"
                                class="w-full bg-slate-50 border-none rounded-2xl px-6 py-4 text-lg font-black text-slate-700 outline-none focus:ring-2 focus:ring-orange-500/20 transition-all">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                         <div>
                            <label class="block text-[10px] font-bold uppercase text-slate-400 mb-2 ml-2">GST Rate (%)</label>
                             <input type="number" step="0.01" name="gst_percent" value="<?php echo $product['gst_percent'] ?? 18; ?>"
                                class="w-full bg-slate-50 border-none rounded-2xl px-6 py-4 text-sm font-bold text-slate-700 outline-none focus:ring-2 focus:ring-orange-500/20 transition-all">
                        </div>
                         <div>
                            <label class="block text-[10px] font-bold uppercase text-slate-400 mb-2 ml-2">Discount (%)</label>
                             <input type="number" step="0.01" name="discount_percent" value="<?php echo $product['discount_percent'] ?? 0; ?>"
                                class="w-full bg-slate-50 border-none rounded-2xl px-6 py-4 text-sm font-bold text-slate-700 outline-none focus:ring-2 focus:ring-orange-500/20 transition-all">
                        </div>
                    </div>
                </div>

                <!-- Media Gallery -->
                <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm relative group">
                    <h4 class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-6">Visual Assets</h4>
                    
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                         <!-- Main Image (Leading) -->
                         <div class="col-span-2 md:col-span-4 mb-4">
                            <label class="block text-[10px] font-bold uppercase text-slate-400 mb-2 ml-2">Primary Cover Image</label>
                             <div class="h-64 rounded-[2rem] border-2 border-dashed border-slate-200 bg-slate-50/50 flex flex-col items-center justify-center relative overflow-hidden group/upload cursor-pointer hover:border-orange-400 transition-all">
                                <input type="file" name="main_image" accept="image/*" onchange="previewImage(this, 'preview_main')" class="absolute inset-0 opacity-0 cursor-pointer z-20">
                                
                                <img id="preview_main" src="<?php echo ($product['main_image'] ?? '') ? '../../assets/images/products/'.$product['main_image'] : ''; ?>" 
                                    class="<?php echo ($product['main_image'] ?? '') ? '' : 'hidden'; ?> absolute inset-0 w-full h-full object-cover z-10">
                                
                                <div class="text-center p-6 transition-opacity group-hover/upload:opacity-0 <?php echo ($product['main_image'] ?? '') ? 'hidden' : ''; ?>">
                                    <div class="w-16 h-16 rounded-full bg-white shadow-lg flex items-center justify-center mx-auto mb-4 text-orange-500">
                                        <i class="fas fa-camera text-2xl"></i>
                                    </div>
                                    <p class="text-xs font-bold text-slate-500">Drop main image or click to browse</p>
                                </div>
                            </div>
                         </div>

                         <!-- Additional Images 1-4 -->
                         <?php for($i=1; $i<=4; $i++): ?>
                         <div class="relative">
                            <label class="block text-[10px] font-bold uppercase text-slate-400 mb-2 ml-2">Gallery <?php echo $i; ?></label>
                             <div class="h-32 rounded-3xl border-2 border-dashed border-slate-200 bg-slate-50/50 flex flex-col items-center justify-center relative overflow-hidden group/upload cursor-pointer hover:border-orange-400 transition-all">
                                <input type="file" name="image_<?php echo $i; ?>" accept="image/*" onchange="previewImage(this, 'preview_<?php echo $i; ?>')" class="absolute inset-0 opacity-0 cursor-pointer z-20">
                                
                                <img id="preview_<?php echo $i; ?>" src="<?php echo ($product['image_'.$i] ?? '') ? '../../assets/images/products/'.$product['image_'.$i] : ''; ?>" 
                                    class="<?php echo ($product['image_'.$i] ?? '') ? '' : 'hidden'; ?> absolute inset-0 w-full h-full object-cover z-10">
                                
                                <i class="fas fa-plus text-slate-300 text-xl <?php echo ($product['image_'.$i] ?? '') ? 'hidden' : ''; ?>"></i>
                            </div>
                         </div>
                         <?php endfor; ?>
                    </div>
                </div>

            </div>

            <!-- RIGHT COLUMN (Inventory & Status) -->
            <div class="space-y-8">
                
                <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm">
                    <h4 class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-6">Inventory Control</h4>
                    
                    <div class="space-y-6">
                        <div>
                            <label class="block text-[10px] font-bold uppercase text-slate-400 mb-2 ml-2">Current Stock</label>
                            <input type="number" name="stock" value="<?php echo $product['stock'] ?? 0; ?>" required
                                class="w-full bg-slate-50 border-none rounded-2xl px-6 py-4 text-xl font-black text-slate-700 outline-none focus:ring-2 focus:ring-orange-500/20 transition-all">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold uppercase text-slate-400 mb-2 ml-2">Low Stock Threshold</label>
                            <input type="number" name="min_stock_level" value="<?php echo $product['min_stock_level'] ?? 5; ?>"
                                class="w-full bg-slate-50 border-none rounded-2xl px-6 py-4 text-sm font-bold text-slate-700 outline-none focus:ring-2 focus:ring-orange-500/20 transition-all">
                        </div>
                    </div>
                </div>

                <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm">
                    <h4 class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-6">Supply Chain</h4>
                    
                     <div class="space-y-6">
                        <div>
                            <label class="block text-[10px] font-bold uppercase text-slate-400 mb-2 ml-2">Preferred Supplier</label>
                             <select name="preferred_supplier_id"
                                class="w-full bg-slate-50 border-none rounded-2xl px-6 py-4 text-sm font-bold text-slate-700 outline-none focus:ring-2 focus:ring-orange-500/20 transition-all cursor-pointer">
                                <option value="">Select Partner</option>
                                <?php foreach ($suppliers as $s): ?>
                                    <option value="<?php echo $s['supplier_id']; ?>" <?php echo ($product['preferred_supplier_id'] ?? '') == $s['supplier_id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($s['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                         <div>
                            <label class="block text-[10px] font-bold uppercase text-slate-400 mb-2 ml-2">Visibility Status</label>
                             <select name="status"
                                class="w-full bg-slate-50 border-none rounded-2xl px-6 py-4 text-sm font-bold text-slate-700 outline-none focus:ring-2 focus:ring-orange-500/20 transition-all cursor-pointer">
                                <option value="1" <?php echo ($product['status'] ?? 1) == 1 ? 'selected' : ''; ?>>Active (Visible)</option>
                                <option value="0" <?php echo ($product['status'] ?? 1) == 0 ? 'selected' : ''; ?>>Draft (Hidden)</option>
                            </select>
                        </div>
                    </div>
                </div>

            </div>
        </form>
    </div>
</main>

<script>
    function previewImage(input, previewId) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                var preview = document.getElementById(previewId);
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                
                // If it's the main image, hide the placeholder
                if(previewId === 'preview_main') {
                    const placeholder = input.parentElement.querySelector('.text-center');
                    if(placeholder) placeholder.classList.add('hidden');
                } else {
                     const icon = input.parentElement.querySelector('.fas.fa-plus');
                    if(icon) icon.classList.add('hidden');
                }
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Category Modal Logic
    function openCategoryModal() {
        document.getElementById('categoryModal').classList.remove('hidden');
    }

    function closeCategoryModal() {
        document.getElementById('categoryModal').classList.add('hidden');
    }

    async function saveCategory() {
        const name = document.getElementById('new_cat_name').value;
        const desc = document.getElementById('new_cat_desc').value;
        const btn = document.getElementById('saveCatBtn');

        if (!name) {
            alert('Category name is required');
            return;
        }

        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

        try {
            const formData = new FormData();
            formData.append('name', name);
            formData.append('description', desc);

            const response = await fetch('ajax_add_category.php', {
                method: 'POST',
                body: formData
            });

            const text = await response.text();
            let result;
            try {
                result = JSON.parse(text);
            } catch (e) {
                console.error('Server Error:', text);
                alert('Server returned an unexpected response. Check console for details.');
                throw new Error('Invalid JSON response');
            }

            if (result.success) {
                // Add to dropdown
                const select = document.querySelector('select[name="category_id"]');
                const option = new Option(result.category.name, result.category.id);
                option.selected = true;
                select.add(option);
                
                // Close & Reset
                closeCategoryModal();
                document.getElementById('new_cat_name').value = '';
                document.getElementById('new_cat_desc').value = '';
                
                alert('Category added successfully!');
            } else {
                alert(result.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred while saving the category.');
        } finally {
            btn.disabled = false;
            btn.innerHTML = 'Save Category';
        }
    }
</script>

<!-- New Category Modal -->
<div id="categoryModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden z-[100] flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50">
            <h3 class="font-bold text-slate-800">Add New Category</h3>
            <button type="button" onclick="closeCategoryModal()" class="text-slate-400 hover:text-slate-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="p-6 space-y-4">
            <div>
                <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Category Name <span class="text-red-500">*</span></label>
                <input type="text" id="new_cat_name" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-bold focus:outline-none focus:border-orange-500 transition-colors" placeholder="e.g. Smart Switches">
            </div>
            <div>
                <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Description</label>
                <textarea id="new_cat_desc" rows="3" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-medium focus:outline-none focus:border-orange-500 transition-colors" placeholder="Optional description..."></textarea>
            </div>
        </div>
        <div class="p-6 border-t border-slate-100 bg-slate-50 flex justify-end gap-3">
            <button type="button" onclick="closeCategoryModal()" class="px-4 py-2 text-sm font-bold text-slate-500 hover:text-slate-700">Cancel</button>
            <button type="button" id="saveCatBtn" onclick="saveCategory()" class="px-6 py-2 bg-slate-900 text-white rounded-xl text-sm font-bold hover:bg-orange-600 transition-colors shadow-lg shadow-slate-200">Save Category</button>
        </div>
    </div>
</div>

<?php include $base_path . 'includes/admin-footer.php'; ?>