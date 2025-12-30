<?php include '../../includes/header.php'; ?>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');
    
    :root { --brand-orange: #FF6F1E; }
    body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; color: #0f172a; }

    /* Card Transitions */
    .wish-item { transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1); }
    .wish-item.removing { opacity: 0; transform: scale(0.9) translateY(20px); }

    /* Custom Scrollbar */
    .custom-scroll::-webkit-scrollbar { width: 4px; }
    .custom-scroll::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }

    /* Toast Animation */
    #wish-toast { transform: translateY(100px); transition: transform 0.4s ease; }
    #wish-toast.show { transform: translateY(0); }
</style>

<body class="antialiased">

<div id="deleteModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-md z-[1000] hidden items-center justify-center p-4">
    <div id="modalBox" class="bg-white rounded-[3rem] p-10 max-w-sm w-full shadow-2xl scale-95 opacity-0 transition-all duration-300">
        <div class="w-20 h-20 bg-orange-50 text-orange-600 rounded-full flex items-center justify-center mx-auto mb-6 text-3xl">
            <i class="fas fa-heart-broken"></i>
        </div>
        <h3 class="text-2xl font-extrabold text-center mb-2">Remove Item?</h3>
        <p class="text-slate-500 text-center text-sm mb-10 leading-relaxed">This article will be removed from your curated list. You can add it back from the catalog later.</p>
        <div class="flex gap-4">
            <button onclick="closeModal()" class="flex-1 py-4 rounded-2xl font-bold text-slate-400 hover:bg-slate-50 transition">Cancel</button>
            <button id="confirmDeleteBtn" class="flex-1 py-4 bg-slate-900 text-white rounded-2xl font-bold hover:bg-red-600 transition-all">Remove</button>
        </div>
    </div>
</div>

<div class="container mx-auto px-4 lg:px-12 py-10 lg:py-20">
    
    <header class="mb-12 flex flex-col md:flex-row justify-between items-end gap-6 border-b border-slate-200 pb-10">
        <div>
            <h1 class="text-4xl lg:text-5xl font-extrabold tracking-tighter">Your Curation <span class="text-slate-300 font-light" id="countBadge">(0)</span></h1>
            <p class="text-slate-400 text-xs font-bold uppercase tracking-[0.3em] mt-3">Gurukrupa Saved Articles</p>
        </div>
        
        <div class="flex gap-4">
            <button onclick="moveAllToBag()" class="px-8 py-4 bg-orange-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-900 transition-all shadow-xl shadow-orange-200 active:scale-95">
                Add All to Bag
            </button>
        </div>
    </header>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8" id="wishlistGrid">
        
        <div class="wish-item bg-white rounded-[2.5rem] border border-slate-100 overflow-hidden group shadow-sm hover:shadow-xl transition-all" id="item-101">
            <div class="relative aspect-[3/4] overflow-hidden">
                <img src="https://images.unsplash.com/photo-1596755094514-f87e34085b2c?auto=format&fit=crop&w=600" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                <button onclick="triggerDelete('item-101')" class="absolute top-5 right-5 w-10 h-10 bg-white/80 backdrop-blur-md rounded-full flex items-center justify-center text-slate-400 hover:text-red-500 transition-all opacity-0 group-hover:opacity-100">
                    <i class="fas fa-times"></i>
                </button>
                <div class="absolute bottom-4 left-4">
                    <span class="px-3 py-1 bg-green-500 text-white text-[8px] font-black uppercase rounded-lg shadow-lg">In Stock</span>
                </div>
            </div>
            <div class="p-6">
                <h3 class="font-bold text-slate-900 truncate">Azure Oxford Cotton</h3>
                <div class="flex justify-between items-center mt-4 pt-4 border-t border-slate-50">
                    <div>
                        <p class="text-[8px] font-bold text-slate-300 uppercase">Retail Price</p>
                        <p class="text-sm font-bold text-slate-400 line-through">₹1,299</p>
                    </div>
                    <div class="text-right">
                        <p class="text-[8px] font-bold text-orange-500 uppercase">Dealer Slab</p>
                        <p class="text-xl font-black text-slate-900 tracking-tighter">₹850</p>
                    </div>
                </div>
                <button class="w-full mt-6 py-4 bg-slate-50 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-900 hover:text-white transition-all">Move to Bag</button>
            </div>
        </div>

        <div class="wish-item bg-white rounded-[2.5rem] border border-slate-100 overflow-hidden group shadow-sm hover:shadow-xl transition-all" id="item-102">
            <div class="relative aspect-[3/4] overflow-hidden">
                <img src="https://images.unsplash.com/photo-1583391733956-6c78276477e2?auto=format&fit=crop&w=600" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                <button onclick="triggerDelete('item-102')" class="absolute top-5 right-5 w-10 h-10 bg-white/80 backdrop-blur-md rounded-full flex items-center justify-center text-slate-400 hover:text-red-500 transition-all opacity-0 group-hover:opacity-100">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-6">
                <h3 class="font-bold text-slate-900 truncate">Maroon Linen Kurta</h3>
                <div class="flex justify-between items-center mt-4 pt-4 border-t border-slate-50">
                    <div><p class="text-[8px] font-bold text-slate-300 uppercase">Retail Price</p><p class="text-sm font-bold text-slate-400 line-through">₹1,899</p></div>
                    <div class="text-right"><p class="text-[8px] font-bold text-orange-500 uppercase">Dealer Slab</p><p class="text-xl font-black text-slate-900 tracking-tighter">₹1,150</p></div>
                </div>
                <button class="w-full mt-6 py-4 bg-slate-50 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-900 hover:text-white transition-all">Move to Bag</button>
            </div>
        </div>

    </div>

    <div id="emptyState" class="hidden flex-col items-center justify-center py-32 text-center">
        <div class="w-24 h-24 bg-slate-100 rounded-full flex items-center justify-center text-slate-300 text-4xl mb-6 animate-pulse">
            <i class="far fa-heart"></i>
        </div>
        <h2 class="text-3xl font-black text-slate-900 tracking-tight">Your wishlist is resting.</h2>
        <p class="text-slate-400 text-sm mt-3 font-medium">Save your favorite factory designs to see them here.</p>
        <a href="<?php echo $base_url; ?>pages/products/product-list.php" class="mt-10 px-10 py-5 bg-orange-600 text-white rounded-[2rem] text-[10px] font-black uppercase tracking-[0.2em] shadow-2xl shadow-orange-200 hover:scale-105 active:scale-95 transition-all">Start Exploring</a>
    </div>

</div>

<div id="wish-toast" class="fixed bottom-10 left-1/2 -translate-x-1/2 z-[2000] bg-slate-900 text-white px-8 py-4 rounded-full shadow-2xl border border-slate-800 flex items-center gap-4">
    <div class="w-2 h-2 rounded-full bg-orange-500"></div>
    <span id="toastMsg" class="text-[10px] font-black uppercase tracking-widest">Item Removed</span>
</div>

<script>
    let itemToDelete = null;

    function triggerDelete(id) {
        itemToDelete = id;
        const modal = document.getElementById('deleteModal');
        const box = document.getElementById('modalBox');
        
        modal.classList.replace('hidden', 'flex');
        setTimeout(() => {
            box.classList.remove('scale-95', 'opacity-0');
            box.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeModal() {
        const box = document.getElementById('modalBox');
        const modal = document.getElementById('deleteModal');
        
        box.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modal.classList.replace('flex', 'hidden');
            itemToDelete = null;
        }, 300);
    }

    document.getElementById('confirmDeleteBtn').addEventListener('click', () => {
        if(itemToDelete) {
            const item = document.getElementById(itemToDelete);
            item.classList.add('removing');
            
            setTimeout(() => {
                item.remove();
                updateUI();
                showToast("Item removed from your list");
            }, 500);
        }
        closeModal();
    });

    function updateUI() {
        const grid = document.getElementById('wishlistGrid');
        const items = document.querySelectorAll('.wish-item');
        const countBadge = document.getElementById('countBadge');
        
        countBadge.innerText = `(${items.length})`;

        if(items.length === 0) {
            grid.classList.add('hidden');
            document.getElementById('emptyState').classList.replace('hidden', 'flex');
        }
    }

    function moveAllToBag() {
        showToast("Moving all designs to bag...");
        // Logic to connect to Backend Cart Session
    }

    function showToast(msg) {
        const toast = document.getElementById('wish-toast');
        document.getElementById('toastMsg').innerText = msg;
        toast.classList.add('show');
        setTimeout(() => toast.classList.remove('show'), 3000);
    }

    // Initialize UI
    updateUI();
</script>

<?php include '../../includes/footer.php'; ?>