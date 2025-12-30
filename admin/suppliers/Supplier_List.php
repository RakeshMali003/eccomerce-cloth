<?php

$base_path = $_SERVER['DOCUMENT_ROOT'] . '/ecommerce-website/';

require_once "../../config/database.php";

// --- 1. LOGIC: CHECK IF WE HAVE AN ID ---
$supplier_id = isset($_GET['id']) ? filter_var($_GET['id'], FILTER_VALIDATE_INT) : null;

// --- 2. LOGIC: FETCH DATA BASED ON STATE ---
if ($supplier_id) {
    // STATE A: Show the actual Ledger (Same code as before)
    $stmt = $pdo->prepare("SELECT name FROM suppliers WHERE supplier_id = ?");
    $stmt->execute([$supplier_id]);
    $supplier = $stmt->fetch();
    
    if (!$supplier) { $supplier_id = null; } // Reset if ID is invalid
} 

if (!$supplier_id) {
    // STATE B: Prepare the Picker (Searchable List)
    $suppliers = $pdo->query("SELECT supplier_id, name, city, phone FROM suppliers WHERE status = 1 ORDER BY name ASC")->fetchAll();
}

include $base_path . 'includes/sidebar.php'; 
?>


    <main class="p-4 md:p-10 flex-1">

    <?php if (!$supplier_id): ?>
        <div class="max-w-4xl">
            <div class="mb-10">
                <h2 class="text-4xl font-black tracking-tighter text-slate-900">Select Supplier Ledger<span class="text-orange-600">.</span></h2>
                <p class="text-slate-400 text-sm font-medium">Search through your 50+ vendors to view detailed statements.</p>
            </div>

            <div class="relative mb-8">
                <i class="fas fa-search absolute left-6 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" id="ledgerSearch" placeholder="Search by name, city or contact..." 
                       class="w-full bg-white p-6 pl-16 rounded-[2rem] shadow-sm border border-slate-100 outline-none focus:border-orange-500 transition-all font-bold">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4" id="supplierGrid">
                <?php foreach($suppliers as $s): ?>
                <a href="Supplier_Ledger.php?id=<?= $s['supplier_id'] ?>" 
                   class="supplier-card bg-white p-6 rounded-3xl border border-slate-100 hover:border-orange-500 hover:shadow-xl hover:shadow-orange-100/50 transition-all group">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-slate-900 rounded-2xl flex items-center justify-center text-white font-black text-lg group-hover:bg-orange-600 transition-colors">
                                <?= strtoupper(substr($s['name'], 0, 1)) ?>
                            </div>
                            <div>
                                <h4 class="font-black text-slate-900 supplier-name"><?= $s['name'] ?></h4>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest"><?= $s['city'] ?> • <?= $s['phone'] ?></p>
                            </div>
                        </div>
                        <i class="fas fa-chevron-right text-slate-200 group-hover:text-orange-500 transition-colors"></i>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>

    <?php else: ?>
        <div class="mb-10 flex justify-between items-end">
            <div>
                <a href="Supplier_Ledger.php" class="text-[10px] font-black uppercase text-orange-600 hover:text-slate-900 transition-all mb-2 block">
                    <i class="fas fa-arrow-left mr-1"></i> Back to Selection
                </a>
                <h2 class="text-3xl font-black tracking-tighter text-slate-900"><?= htmlspecialchars($supplier['name']) ?></h2>
            </div>
            <button onclick="window.print()" class="bg-slate-900 text-white px-8 py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-orange-600 transition-all">
                Print Statement
            </button>
        </div>

        <?php endif; ?>

</main>

<script>
// Instant Search Logic for large lists
document.getElementById('ledgerSearch')?.addEventListener('input', function(e) {
    const term = e.target.value.toLowerCase();
    const cards = document.querySelectorAll('.supplier-card');
    
    cards.forEach(card => {
        const name = card.querySelector('.supplier-name').innerText.toLowerCase();
        if(name.includes(term)) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
});
</script>

</body>
</html>