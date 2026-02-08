<?php
$base_path = $_SERVER['DOCUMENT_ROOT'] . '/ecommerce-website/';
require_once "../../config/database.php";

include $base_path . 'includes/admin-header.php';
include $base_path . 'includes/sidebar.php';
include $base_path . 'includes/notifications.php';
?>

<main class="p-6 lg:p-12 bg-[#f8fafc] min-h-screen">
    <div class="mb-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
        <div>
            <h2 class="text-3xl font-black tracking-tighter text-slate-900">Inventory Sprints<span
                    class="text-orange-600">.</span></h2>
            <p class="text-slate-400 text-sm font-medium">Bulk import products via CSV to scale your catalog in seconds.
            </p>
        </div>
        <a href="sample_catalog.csv"
            class="bg-white border border-slate-200 px-6 py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-50 transition-all shadow-sm">
            <i class="fas fa-file-download mr-2"></i> Download Template
        </a>
    </div>

    <div class="max-w-4xl space-y-10">
        <form action="process_bulk_upload.php" method="POST" enctype="multipart/form-data">
            <div
                class="bg-white p-12 rounded-[3.5rem] border-2 border-dashed border-slate-200 hover:border-orange-500 hover:bg-orange-50/30 transition-all group flex flex-col items-center justify-center text-center cursor-pointer relative">
                <input type="file" name="csv_file" required
                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                <div
                    class="w-20 h-20 bg-slate-50 rounded-3xl flex items-center justify-center text-slate-300 group-hover:bg-orange-100 group-hover:text-orange-600 transition-all mb-6">
                    <i class="fas fa-file-csv text-3xl"></i>
                </div>
                <h3 class="text-xl font-black text-slate-900 mb-2">Select Catalog Manifest</h3>
                <p class="text-sm text-slate-400 font-medium max-w-xs mx-auto">Drop your CSV file here or click to
                    browse. Ensure headers match the template format.</p>
            </div>

            <div class="mt-10 bg-white p-10 rounded-[3rem] border border-slate-100 shadow-sm">
                <h4 class="text-[10px] font-black uppercase text-slate-400 tracking-[0.3em] mb-6">Import Strategy</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div
                        class="relative p-6 rounded-3xl bg-slate-50 border-2 border-transparent hover:border-orange-500 transition-all">
                        <input type="radio" name="strategy" value="skip" checked
                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                        <h5 class="text-sm font-black text-slate-900 mb-1">Skip Duplicates</h5>
                        <p class="text-[10px] text-slate-400 font-medium">Keep existing products, only add new unique
                            ones.</p>
                    </div>
                    <div
                        class="relative p-6 rounded-3xl bg-slate-50 border-2 border-transparent hover:border-orange-500 transition-all">
                        <input type="radio" name="strategy" value="update"
                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                        <h5 class="text-sm font-black text-slate-900 mb-1">Overwrite / Sync</h5>
                        <p class="text-[10px] text-slate-400 font-medium">Update prices and stock for existing SKU
                            matches.</p>
                    </div>
                </div>
            </div>

            <button type="submit"
                class="w-full bg-slate-900 text-white py-8 rounded-[3rem] font-black uppercase tracking-[0.3em] shadow-2xl shadow-slate-200 hover:bg-orange-600 transition-all mt-10 group">
                Commence Bulk Sync <i class="fas fa-bolt ml-4 group-hover:text-amber-300 transition-colors"></i>
            </button>
        </form>

        <div class="bg-indigo-900 p-10 rounded-[3rem] text-white overflow-hidden relative">
            <div class="relative z-10">
                <h4 class="text-[10px] font-black uppercase tracking-[0.3em] text-indigo-300 mb-4">Pro Tips</h4>
                <ul class="space-y-4">
                    <li class="flex items-start gap-4">
                        <div class="w-6 h-6 rounded-lg bg-indigo-800 flex items-center justify-center text-[10px]"><i
                                class="fas fa-check"></i></div>
                        <p class="text-xs font-medium text-indigo-100">Ensure category names match existing ones in the
                            Master Catalog.</p>
                    </li>
                    <li class="flex items-start gap-4">
                        <div class="w-6 h-6 rounded-lg bg-indigo-800 flex items-center justify-center text-[10px]"><i
                                class="fas fa-check"></i></div>
                        <p class="text-xs font-medium text-indigo-100">Image URLs must be publicly accessible for
                            auto-download.</p>
                    </li>
                </ul>
            </div>
            <i
                class="fas fa-cloud-upload-alt absolute -right-10 -bottom-10 text-[12rem] text-indigo-800 opacity-50 rotate-12"></i>
        </div>
    </div>
</main>
<?php include $base_path . 'includes/admin-footer.php'; ?>
</body>

</html>