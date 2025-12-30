

<?php
$base_path = $_SERVER['DOCUMENT_ROOT'] . '/ecommerce-website/';
require_once $base_path . 'config/database.php';


include $base_path . 'includes/sidebar.php'; 
include $base_path . 'includes/notifications.php'; 

// Fetch Products
$query = "SELECT p.*, c.name as category_name, s.name as supplier_name 
          FROM products p 
          LEFT JOIN categories c ON p.category_id = c.category_id 
          LEFT JOIN suppliers s ON p.preferred_supplier_id = s.supplier_id 
          ORDER BY p.created_at DESC";
$products = $pdo->query($query)->fetchAll();

// Fetch Dropdowns
$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();
$suppliers = $pdo->query("SELECT supplier_id, name FROM suppliers WHERE status = 1 ORDER BY name ASC")->fetchAll();
?>
<main class="p-6 lg:p-12">
    <div class="mb-10 flex justify-between items-center">
        <div>
            <h2 class="text-3xl font-black tracking-tighter text-slate-900">Master Catalog<span class="text-orange-600">.</span></h2>
            <p class="text-slate-400 text-sm font-medium">Manage your products, pricing, and media.</p>
        </div>
        
        <button onclick="openActionModal('add')" class="bg-slate-900 text-white px-8 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-orange-600 transition-all shadow-xl shadow-slate-200">
            + Add New Product
        </button>
   
    </div>

    <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="bg-slate-50 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                <tr>
                    <th class="px-8 py-6">Product</th>
                    <th class="px-6 py-6">Category</th>
                    <th class="px-6 py-6">Stock</th>
                    <th class="px-6 py-6 text-right">Price</th>
                    <th class="px-8 py-6 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php foreach($products as $p): ?>
                <tr class="hover:bg-slate-50 transition-all group">
                    <td class="px-8 py-5 flex items-center gap-4">
                        <img src="../../assets/images/products/<?= $p['main_image'] ?: 'default.png' ?>" class="w-12 h-12 rounded-xl object-cover border border-slate-100">
                        <div>
                            <p class="text-sm font-black text-slate-900"><?= htmlspecialchars($p['name']) ?></p>
                            <p class="text-[10px] text-slate-400 font-bold uppercase"><?= $p['sku'] ?></p>
                        </div>
                    </td>
                    <td class="px-6 py-5 text-xs font-bold text-slate-500"><?= $p['category_name'] ?: 'N/A' ?></td>
                    <td class="px-6 py-5">
                        <span class="text-sm font-black <?= ($p['stock'] <= $p['min_stock_level']) ? 'text-red-500' : 'text-slate-900' ?>">
                            <?= $p['stock'] ?>
                        </span>
                    </td>
                    <td class="px-6 py-5 text-right font-black text-slate-900">₹<?= number_format($p['price'], 2) ?></td>
                  <td class="px-8 py-5">
     <div class="flex justify-center gap-2">
                              <button onclick='openVariantModal(<?= htmlspecialchars(json_encode($p), ENT_QUOTES, "UTF-8") ?>)' 
                class="w-9 h-9 rounded-lg bg-purple-50 text-purple-600 hover:bg-purple-600 hover:text-white transition-all shadow-sm"
                title="Manage Variants">
            <i class="fas fa-layer-group text-xs"></i>
        </button>

                            <button onclick='openActionModal("view", <?= htmlspecialchars(json_encode($p), ENT_QUOTES, "UTF-8") ?>)' 
                                class="w-9 h-9 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-all">
                                <i class="fas fa-eye text-xs"></i>
                            </button>
                            <button onclick='openActionModal("edit", <?= htmlspecialchars(json_encode($p), ENT_QUOTES, "UTF-8") ?>)' 
                                class="w-9 h-9 rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-900 hover:text-white transition-all">
                                <i class="fas fa-pencil text-xs"></i>
                            </button>
                            <button onclick="deleteProduct(<?= $p['product_id'] ?>)" class="w-9 h-9 rounded-lg bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition-all">
                                <i class="fas fa-trash text-xs"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>

<div id="actionModal" class="fixed inset-0 z-[110] hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeActionModal()"></div>
    
    <form id="productForm" action="process_product.php" method="POST" enctype="multipart/form-data" 
          class="bg-[#f8fafc] w-full max-w-6xl rounded-[3rem] shadow-2xl relative z-10 overflow-hidden flex flex-col max-h-[95vh]">
        
        <input type="hidden" name="action" id="formAction" value="add">
        <input type="hidden" name="product_id" id="editProductId">

        <div class="p-8 border-b border-slate-100 flex justify-between items-center bg-white">
            <div>
                <h3 id="modalTitle" class="text-2xl font-black text-slate-900 uppercase tracking-tighter">Register New Product</h3>
            </div>
            <button type="button" onclick="closeActionModal()" class="w-12 h-12 rounded-2xl bg-slate-50 flex items-center justify-center text-slate-400 hover:bg-red-50 hover:text-red-500 transition-all">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <div class="p-8 overflow-y-auto space-y-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <div class="lg:col-span-2 space-y-8">
                    <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm space-y-6">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Basic Information</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Product Name</label>
                                <input type="text" name="name" id="p_name" required class="input-premium w-full">
                            </div>
                            <div>
                                <label class="text-[10px] font-black uppercase text-slate-400 ml-2">SKU</label>
                                <input type="text" name="sku" id="p_sku" required class="input-premium w-full">
                            </div>
                            <div>
                                <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Category</label>
                                <select name="category_id" id="p_cat" required class="input-premium w-full">
                                    <option value="">Select Category</option>
                                    <?php foreach($categories as $cat): ?>
                                        <option value="<?= $cat['category_id'] ?>"><?= $cat['name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <textarea name="description" id="p_desc" rows="3" class="input-premium w-full" placeholder="Description..."></textarea>
                    </div>

                    <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div><label class="text-[10px] font-black uppercase text-slate-400">Price</label><input type="number" name="price" id="p_price" step="0.01" class="input-premium"></div>
                            <div><label class="text-[10px] font-black uppercase text-slate-400">Wholesale</label><input type="number" name="wholesale_price" id="p_wprice" step="0.01" class="input-premium"></div>
                            <div><label class="text-[10px] font-black uppercase text-slate-400">W-Qty</label><input type="number" name="min_wholesale_qty" id="p_wqty" class="input-premium"></div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div><label class="text-[10px] font-black uppercase text-slate-400">GST %</label><input type="number" name="gst_percent" id="p_gst" class="input-premium"></div>
                            <div><label class="text-[10px] font-black uppercase text-slate-400">Discount %</label><input type="number" name="discount_percent" id="p_disc" class="input-premium"></div>
                        </div>
                    </div>
                </div>

                <div class="space-y-8">
                    <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm space-y-6">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Media</p>
                        <div class="w-full h-40 bg-slate-50 border-2 border-dashed border-slate-100 rounded-[2rem] relative overflow-hidden flex items-center justify-center">
                            <img id="main_preview" class="absolute inset-0 w-full h-full object-cover hidden">
                            <i class="fas fa-cloud-upload text-slate-200 text-3xl"></i>
                            <input type="file" name="main_image" accept="image/*" onchange="previewImg(this, 'main_preview')" class="absolute inset-0 opacity-0 cursor-pointer">
                        </div>
                        <div class="grid grid-cols-4 gap-2">
                             <?php for($i=1; $i<=4; $i++): ?>
                                <div class="w-full aspect-square bg-slate-50 rounded-xl relative overflow-hidden border border-slate-100 flex items-center justify-center">
                                    <img id="prev_<?= $i ?>" class="absolute inset-0 w-full h-full object-cover hidden">
                                    <span class="text-[8px] font-black text-slate-300"><?= $i ?></span>
                                    <input type="file" name="image_<?= $i ?>" accept="image/*" onchange="previewImg(this, 'prev_<?= $i ?>')" class="absolute inset-0 opacity-0 cursor-pointer">
                                </div>
                             <?php endfor; ?>
                        </div>
                    </div>

                    <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm space-y-6">
                        <div><label class="text-[10px] font-black">Stock</label><input type="number" name="stock" id="p_stock" class="input-premium"></div>
                        <div><label class="text-[10px] font-black">Min Alert</label><input type="number" name="min_stock_level" id="p_min" class="input-premium"></div>
                        <div><label class="text-[10px] font-black">Supplier</label>
                            <select name="preferred_supplier_id" id="p_sup" class="input-premium">
                                <?php foreach($suppliers as $s): ?><option value="<?= $s['supplier_id'] ?>"><?= $s['name'] ?></option><?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" id="submitBtn" class="w-full bg-slate-900 text-white py-6 rounded-[2rem] font-black uppercase tracking-[0.2em] hover:bg-orange-600 transition-all shadow-xl">Commit Product</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<div id="variantModal" class="fixed inset-0 z-[130] hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeVariantModal()"></div>
    <div class="bg-white w-full max-w-3xl rounded-[3rem] shadow-2xl relative z-10 overflow-hidden flex flex-col max-h-[85vh]">
        <div class="p-8 border-b border-slate-100 flex justify-between items-center bg-purple-50/30">
            <div>
                <h3 id="var_productName" class="text-xl font-black text-slate-900 uppercase">Manage Variants</h3>
                <p id="var_productSku" class="text-[10px] font-bold text-slate-400"></p>
            </div>
            <button onclick="closeVariantModal()" class="w-10 h-10 rounded-xl bg-white text-slate-400 hover:text-red-500 transition-all"><i class="fas fa-times"></i></button>
        </div>

        <div class="p-8 overflow-y-auto">
            <form id="addVariantForm" class="grid grid-cols-4 gap-4 mb-8 p-6 bg-slate-50 rounded-[2rem]">
                <input type="hidden" name="product_id" id="var_pid">
                <div>
                    <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Size</label>
                    <input type="text" name="size" placeholder="XL, 42..." class="input-premium w-full">
                </div>
                <div>
                    <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Color</label>
                    <input type="text" name="color" placeholder="Red, Blue..." class="input-premium w-full">
                </div>
                <div>
                    <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Stock</label>
                    <input type="number" name="stock_qty" value="0" class="input-premium w-full">
                </div>
                <div class="flex items-end">
                    <button type="button" onclick="saveVariant()" class="w-full bg-purple-600 text-white py-4 rounded-2xl font-black uppercase text-[10px] hover:bg-slate-900 transition-all">Add</button>
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

    // 1. Reset everything
    form.reset();
    document.getElementById('editProductId').value = '';
    
    // Hide all image previews
    document.getElementById('main_preview').classList.add('hidden');
    for (let i = 1; i <= 4; i++) {
        const prev = document.getElementById('prev_' + i);
        if(prev) prev.classList.add('hidden');
    }

    // 2. Populate if Edit or View
    if ((mode === 'edit' || mode === 'view') && data) {
        title.innerText = mode === 'edit' ? "Edit Product" : "Product Details";
        document.getElementById('formAction').value = "edit";
        document.getElementById('editProductId').value = data.product_id;

        // Map data to fields (Ensure IDs match your HTML)
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
            if(el) el.value = fields[id] || '';
        });

        // Main Image Preview
        if (data.main_image) {
            const m = document.getElementById('main_preview');
            m.src = "../../assets/images/products/" + data.main_image;
            m.classList.remove('hidden');
        }

        // Gallery Previews
        for (let i = 1; i <= 4; i++) {
            if (data['image_' + i]) {
                const img = document.getElementById('prev_' + i);
                if(img) {
                    img.src = "../../assets/images/products/" + data['image_' + i];
                    img.classList.remove('hidden');
                }
            }
        }

        // Mode Restriction
        if (mode === 'view') {
            inputs.forEach(el => el.disabled = true);
            if(submitBtn) submitBtn.classList.add('hidden');
        } else {
            inputs.forEach(el => el.disabled = false);
            if(submitBtn) submitBtn.classList.remove('hidden');
        }
    } else {
        // Add Mode
        title.innerText = "Register New Product";
        document.getElementById('formAction').value = "add";
        inputs.forEach(el => el.disabled = false);
        if(submitBtn) submitBtn.classList.remove('hidden');
    }

    modal.classList.replace('hidden', 'flex');
}

function closeActionModal() {
    document.getElementById('actionModal').classList.replace('flex', 'hidden');
}

// --- VARIANT MANAGEMENT LOGIC ---


function openVariantModal(product) {
    document.getElementById('var_productName').innerText = product.name;
    document.getElementById('var_productSku').innerText = "Parent SKU: " + product.sku;
    document.getElementById('var_pid').value = product.product_id;
    
    loadVariants(product.product_id);
    document.getElementById('variantModal').classList.replace('hidden', 'flex');
}

function loadVariants(pid) {
    fetch(`get_variants.php?product_id=${pid}`)
        .then(response => response.json())
        .then(data => {
            const tbody = document.getElementById('variantTableBody');
            if (!data || data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="4" class="p-10 text-center text-slate-300">No variants found</td></tr>';
                return;
            }
            tbody.innerHTML = data.map(v => `
                <tr class="text-xs font-bold text-slate-600 border-b border-slate-50">
                    <td class="px-6 py-4">${v.size || '-'}</td>
                    <td class="px-6 py-4">${v.color || '-'}</td>
                    <td class="px-6 py-4 text-center">${v.stock_qty}</td>
                    <td class="px-6 py-4 text-right">
                        <button onclick="deleteVariant(${v.variant_id}, ${pid})" class="text-red-400 hover:text-red-600 transition-colors">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `).join('');
        })
        .catch(err => console.error("Error loading variants:", err));
}

function saveVariant() {
    const form = document.getElementById('addVariantForm');
    const formData = new FormData(form);
    const pid = formData.get('product_id');

    if(!pid) return alert("Product ID missing");

    fetch('save_variant.php', { 
        method: 'POST', 
        body: formData 
    })
    .then(res => res.text()) // Use text() first to debug PHP errors
    .then(() => {
        form.reset();
        document.getElementById('var_pid').value = pid; // Re-set PID after reset
        loadVariants(pid);
    })
    .catch(err => console.error("Error saving variant:", err));
}

function deleteVariant(vid, pid) {
    if(confirm("Remove this variant?")) {
        fetch(`delete_variant.php?variant_id=${vid}`)
            .then(() => loadVariants(pid))
            .catch(err => console.error("Error deleting variant:", err));
    }
}

function closeVariantModal() {
    document.getElementById('variantModal').classList.replace('flex', 'hidden');
}

// --- UTILITIES ---
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
    if(confirm("Permanently delete this product? This cannot be undone.")) {
        window.location.href = `process_product.php?action=delete&id=${id}`;
    }
}
</script>