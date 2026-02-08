<?php
$base_path = $_SERVER['DOCUMENT_ROOT'] . '/ecommerce-website/';
require_once "../../config/database.php";

include $base_path . 'includes/admin-header.php';
include $base_path . 'includes/sidebar.php';
include $base_path . 'includes/notifications.php';

// Fetch roles (Simplified demonstration)
$roles = [
    ['id' => 1, 'name' => 'Super Admin', 'desc' => 'Full system power with infrastructure access.'],
    ['id' => 2, 'name' => 'Sales Manager', 'desc' => 'Manage orders, invoices, and customer support.'],
    ['id' => 3, 'name' => 'Inventory Head', 'desc' => 'Manage stock, products, and procurement.'],
    ['id' => 4, 'name' => 'Junior Staff', 'desc' => 'View only access to orders and products.']
];
?>

<main class="p-6 lg:p-12 bg-[#f8fafc] min-h-screen">
    <div class="mb-10 flex items-center gap-4">
        <a href="site-settings.php"
            class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-slate-400 hover:text-slate-900 transition-all shadow-sm">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h2 class="text-3xl font-black tracking-tighter text-slate-900">Access Control Hub<span
                    class="text-indigo-600">.</span></h2>
            <p class="text-slate-400 text-sm font-medium">Define responsibilities and restrict sensitive data access
                across the team.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-5xl">
        <?php foreach ($roles as $r): ?>
            <div
                class="bg-white p-8 rounded-[3rem] border border-slate-100 shadow-sm hover:shadow-xl hover:scale-[1.02] transition-all group">
                <div class="flex items-center justify-between mb-6">
                    <div
                        class="w-14 h-14 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl">
                        <i class="fas <?= $r['id'] == 1 ? 'fa-user-shield' : 'fa-user-tag' ?>"></i>
                    </div>
                    <span
                        class="bg-slate-50 text-slate-400 px-4 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-widest">Role
                        ID: #
                        <?= $r['id'] ?>
                    </span>
                </div>

                <h3 class="text-xl font-black text-slate-900 mb-2">
                    <?= $r['name'] ?>
                </h3>
                <p class="text-sm text-slate-400 font-medium leading-relaxed mb-6">
                    <?= $r['desc'] ?>
                </p>

                <div class="pt-6 border-t border-slate-50 flex items-center justify-between">
                    <div class="flex -space-x-2">
                        <div
                            class="w-8 h-8 rounded-full bg-slate-100 border-2 border-white flex items-center justify-center text-[10px] font-bold text-slate-400">
                            AJ</div>
                        <div
                            class="w-8 h-8 rounded-full bg-slate-100 border-2 border-white flex items-center justify-center text-[10px] font-bold text-slate-400">
                            SM</div>
                    </div>
                    <button
                        class="text-indigo-600 text-[10px] font-black uppercase tracking-widest hover:text-slate-900 transition-all">Configure
                        Permissions <i class="fas fa-chevron-right ml-1"></i></button>
                </div>
            </div>
        <?php endforeach; ?>

        <button
            class="bg-dashed border-2 border-dashed border-slate-200 p-8 rounded-[3rem] flex flex-col items-center justify-center gap-4 hover:border-indigo-500 hover:bg-white transition-all group">
            <div
                class="w-16 h-16 rounded-full bg-slate-50 flex items-center justify-center text-slate-300 group-hover:bg-indigo-50 group-hover:text-indigo-600 transition-all">
                <i class="fas fa-plus text-xl"></i>
            </div>
            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 group-hover:text-slate-900">
                Define Novel Role</p>
        </button>
    </div>
</main>
<?php include $base_path . 'includes/admin-footer.php'; ?>
<style>
    .bg-dashed {
        background-image: url("data:image/svg+xml,%3csvg width='100%25' height='100%25' xmlns='http://www.w3.org/2000/svg'%3e%3crect width='100%25' height='100%25' fill='none' rx='48' ry='48' stroke='%23CBD5E1' stroke-width='4' stroke-dasharray='12%2c 12' stroke-dashoffset='0' stroke-linecap='square'/%3e%3c/svg%3e");
    }
</style>
</body>

</html>