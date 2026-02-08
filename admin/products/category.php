<?php
$base_path = $_SERVER['DOCUMENT_ROOT'] . '/ecommerce-website/';
require_once $base_path . 'config/database.php';


include $base_path . 'includes/sidebar.php'; 
include $base_path . 'includes/notifications.php'; 

// Change your query to this:
$query = "SELECT * FROM categories ORDER BY name ASC";
$categories = $pdo->query($query)->fetchAll();
?>

<main class="p-6 lg:p-12">
    <div class="mb-10 flex justify-between items-center">
        <div>
            <h2 class="text-3xl font-black tracking-tighter text-slate-900">Categories<span class="text-orange-600">.</span></h2>
            <p class="text-slate-400 text-sm font-medium">Group your products for better organization.</p>
        </div>
        <button onclick="openCategoryModal('add')" class="bg-slate-900 text-white px-8 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-orange-600 transition-all shadow-xl">
            + Create Category
        </button>
    </div>

    <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="bg-slate-50 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                <tr>
                    <th class="px-8 py-6">ID</th>
                    <th class="px-6 py-6">Category Name</th>
                    <th class="px-6 py-6">Description</th>
                    <th class="px-6 py-6">Created At</th>
                    <th class="px-8 py-6 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php foreach($categories as $cat): ?>
                <tr class="hover:bg-slate-50 transition-all group">
                    <td class="px-8 py-5 text-xs font-bold text-slate-400">#<?= $cat['category_id'] ?></td>
                    <td class="px-6 py-5">
                        <p class="text-sm font-black text-slate-900"><?= htmlspecialchars($cat['name']) ?></p>
                    </td>
                    <td class="px-6 py-5">
                        <p class="text-xs text-slate-500 truncate max-w-xs"><?= htmlspecialchars($cat['description'] ?: 'No description provided') ?></p>
                    </td>
                    <td class="px-6 py-5 text-xs text-slate-400 font-medium">
                        <?= date('d M, Y', strtotime($cat['created_at'])) ?>
                    </td>
                    <td class="px-8 py-5">
                        <div class="flex justify-center gap-2">
                            <button onclick='openCategoryModal("edit", <?= htmlspecialchars(json_encode($cat), ENT_QUOTES, "UTF-8") ?>)' 
                                    class="w-9 h-9 rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-900 hover:text-white transition-all">
                                <i class="fas fa-pencil text-xs"></i>
                            </button>
                            <button onclick="deleteCategory(<?= $cat['category_id'] ?>)" 
                                    class="w-9 h-9 rounded-lg bg-red-50 text-red-600 hover:bg-red-600 hover:text-white transition-all">
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

<div id="categoryModal" class="fixed inset-0 z-[110] hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeCategoryModal()"></div>
    <form id="categoryForm" action="process_category.php" method="POST" class="bg-white w-full max-w-lg rounded-[2.5rem] shadow-2xl relative z-10 overflow-hidden">
        <input type="hidden" name="action" id="formAction" value="add">
        <input type="hidden" name="category_id" id="editCategoryId">

        <div class="p-8 border-b border-slate-50 flex justify-between items-center">
            <h3 id="modalTitle" class="text-xl font-black text-slate-900 uppercase">New Category</h3>
            <button type="button" onclick="closeCategoryModal()" class="text-slate-300 hover:text-red-500 transition-all"><i class="fas fa-times"></i></button>
        </div>

        <div class="p-8 space-y-6">
            <div>
                <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Category Name</label>
                <input type="text" name="name" id="c_name" required class="input-premium w-full mt-2" placeholder="e.g., Electronics">
            </div>
            <div>
                <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Description</label>
                <textarea name="description" id="c_desc" rows="3" class="input-premium w-full mt-2" placeholder="Briefly describe this category..."></textarea>
            </div>
            <button type="submit" id="submitBtn" class="w-full bg-slate-900 text-white py-5 rounded-2xl font-black uppercase tracking-widest hover:bg-orange-600 transition-all shadow-lg">Save Category</button>
        </div>
    </form>
</div>

<script>
function openCategoryModal(mode, data = null) {
    const modal = document.getElementById('categoryModal');
    const form = document.getElementById('categoryForm');
    const title = document.getElementById('modalTitle');
    const actionInput = document.getElementById('formAction');

    form.reset();

    if(mode === 'edit' && data) {
        title.innerText = "Edit Category";
        actionInput.value = "edit";
        document.getElementById('editCategoryId').value = data.category_id;
        document.getElementById('c_name').value = data.name;
        document.getElementById('c_desc').value = data.description;
    } else {
        title.innerText = "New Category";
        actionInput.value = "add";
        document.getElementById('editCategoryId').value = '';
    }

    modal.classList.replace('hidden', 'flex');
}

function closeCategoryModal() {
    document.getElementById('categoryModal').classList.replace('flex', 'hidden');
}

function deleteCategory(id) {
    if(confirm("Are you sure? Products assigned to this category may lose their reference.")) {
        window.location.href = `process_category.php?action=delete&id=${id}`;
    }
}
</script>