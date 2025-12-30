<?php
$base_path = $_SERVER['DOCUMENT_ROOT'] . '/ecommerce-website/';
require_once $base_path . 'config/database.php';


include $base_path . 'includes/sidebar.php'; 
include $base_path . 'includes/notifications.php'; 

$categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();
$suppliers = $pdo->query("SELECT supplier_id, name FROM suppliers WHERE status = 1 ORDER BY name ASC")->fetchAll();
?>
<main class="p-6 lg:p-12">
    <div class="mb-10">
        <h2 class="text-3xl font-black tracking-tighter text-slate-900">Add New Product<span class="text-orange-600">.</span></h2>
        <p class="text-slate-400 text-sm font-medium">Register a new item into your master catalog.</p>
    </div>

    <form action="process_product.php" method="POST" enctype="multipart/form-data" class="space-y-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="lg:col-span-2 space-y-8">
                <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm space-y-6">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4">General Information</p>
                    
                    <div>
                        <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Product Name</label>
                        <input type="text" name="name" required placeholder="e.g., Premium Cotton Fabric" class="input-premium">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-[10px] font-black uppercase text-slate-400 ml-2">SKU (Stock Keeping Unit)</label>
                            <input type="text" name="sku" required placeholder="GK-1024" class="input-premium">
                        </div>
                        <div>
                            <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Category</label>
                            <select name="category_id" required class="input-premium appearance-none">
                                <option value="">Select Category</option>
                                <?php foreach($categories as $cat): ?>
                                    <option value="<?= $cat['category_id'] ?>"><?= $cat['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Description</label>
                        <textarea name="description" rows="3" class="input-premium" placeholder="Detail product specifications..."></textarea>
                    </div>
                </div>

                <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-6">Commercials & Pricing</p>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Retail Price (₹)</label>
                            <input type="number" name="price" step="0.01" required class="input-premium" placeholder="0.00">
                        </div>
                        <div>
                            <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Wholesale (₹)</label>
                            <input type="number" name="wholesale_price" step="0.01" class="input-premium" placeholder="0.00">
                        </div>
                        <div>
                            <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Min Wholesale Qty</label>
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
                            <label class="text-[10px] font-black uppercase text-slate-400 block mb-2 text-center">Main Display Image (Required)</label>
                            <input type="file" name="main_image" accept="image/*" required class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-slate-900 file:text-white hover:file:bg-orange-600 transition-all cursor-pointer">
                        </div>

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <?php for($i=1; $i<=4; $i++): ?>
                            <div class="p-4 border border-slate-100 rounded-3xl bg-white text-center">
                                <label class="text-[8px] font-black uppercase text-slate-400 block mb-2">Gallery <?= $i ?></label>
                                <input type="file" name="image_<?= $i ?>" accept="image/*" class="w-full text-[8px] text-slate-400 file:hidden cursor-pointer">
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
                        <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Low Stock Alert Level</label>
                        <input type="number" name="min_stock_level" value="5" class="input-premium">
                    </div>
                </div>

                <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm space-y-6">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4">Vendor Link</p>
                    <div>
                        <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Preferred Supplier</label>
                        <select name="preferred_supplier_id" class="input-premium appearance-none">
                            <option value="">None / Multiple</option>
                            <?php foreach($suppliers as $sup): ?>
                                <option value="<?= $sup['supplier_id'] ?>"><?= $sup['name'] ?></option>
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

                <button type="submit" class="w-full bg-slate-900 text-white py-6 rounded-[2rem] font-black uppercase tracking-[0.2em] shadow-xl hover:bg-orange-600 transition-all hover:-translate-y-1 active:scale-95">
                    Create Product Record
                </button>
            </div>
        </div>
    </form>
</main>