<?php include '../../includes/header.php'; ?>

<style>
    /* Custom Design Fixes */
    .card { background: white; border-radius: 2rem; border: 1px solid #f3f4f6; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1); }
    .accent-bg { background-color: #FF6F1E; }
    .accent-text { color: #FF6F1E; }
    
    /* State Management for Wholesale/Retail */
    .is-wholesale .retail-only { display: none !important; }
    #cartApp:not(.is-wholesale) .wholesale-only { display: none !important; }
    
    /* Input Styling */
    .qty-input {
        width: 100%; border: 1px solid #e5e7eb; border-radius: 0.5rem; 
        padding: 0.5rem; text-align: center; font-weight: 700; font-size: 0.75rem;
    }
    .qty-input:focus { outline: 2px solid #FF6F1E; border-color: transparent; }

    /* Modal Animation */
    #modalBox.show { transform: scale(1); opacity: 1; }
</style>

<body class="antialiased bg-gray-50/50">

<div id="deleteModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[999] hidden items-center justify-center p-4">
    <div class="bg-white rounded-[2rem] p-6 md:p-8 max-w-sm w-full shadow-2xl transform scale-95 opacity-0 transition-all duration-300" id="modalBox">
        <div class="w-16 h-16 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-trash-alt text-red-500 text-xl"></i>
        </div>
        <h3 class="text-xl font-bold text-center mb-2" id="modalTitle">Remove Article?</h3>
        <p class="text-gray-500 text-center text-sm mb-6" id="modalDesc">Are you sure you want to remove this item from your bag?</p>
        <div class="flex gap-3">
            <button onclick="closeDeleteModal()" class="flex-1 py-3 rounded-xl font-bold text-gray-400 hover:bg-gray-100 transition">Cancel</button>
            <button id="confirmDeleteBtn" class="flex-1 py-3 bg-red-500 text-white rounded-xl font-bold hover:bg-red-600 transition shadow-lg shadow-red-200">Remove</button>
        </div>
    </div>
</div>

<div id="cartApp" class="container mx-auto px-4 lg:px-10 py-6 lg:py-12 is-wholesale">
    
    <header class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4 border-b pb-6">
        <div>
            <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight">Your Bag <span class="text-gray-400 font-light" id="topItemCount">(0)</span></h1>
            <p class="text-[10px] font-bold uppercase tracking-widest text-orange-600 mt-1">Gurukrupa Wholesale Hub</p>
        </div>
        
        <div class="flex items-center gap-4">
            <button onclick="openDeleteModal('ALL')" class="text-[10px] font-bold uppercase text-gray-400 hover:text-red-500 transition-colors">
                <i class="fas fa-trash-sweep mr-1"></i> Clear Bag
            </button>

            <div class="flex items-center gap-3 bg-white p-1.5 rounded-xl border border-gray-100 shadow-sm">
                <span class="text-[9px] font-bold uppercase ml-2 text-gray-400">Buying Mode</span>
                <button onclick="toggleMode()" class="px-5 py-2 rounded-lg text-[10px] font-black uppercase tracking-widest bg-zinc-900 text-white shadow-md transition-all active:scale-95" id="modeBtn">
                    Wholesale
                </button>
            </div>
        </div>
    </header>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <div class="lg:col-span-8 space-y-4" id="cartItemsContainer">
            
            <div class="card p-4 md:p-6 cart-item" data-retail="1299" data-tier1="850" data-tier2="799" data-tier3="749" data-id="item1">
                <div class="flex gap-4 md:gap-6">
                    <div class="w-24 md:w-32 shrink-0 aspect-[3/4] rounded-xl overflow-hidden bg-gray-50 border">
                        <img src="https://images.unsplash.com/photo-1596755094514-f87e34085b2c?auto=format&fit=crop&w=400" class="w-full h-full object-cover">
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-sm md:text-base font-bold truncate">Premium Azure Oxford Shirt</h3>
                                <p class="text-[9px] font-bold text-gray-400 uppercase">SKU: GK-101 | Sky Blue</p>
                            </div>
                            <button onclick="openDeleteModal('item1')" class="text-gray-300 hover:text-red-500 transition-colors"><i class="fas fa-times"></i></button>
                        </div>

                        <div class="wholesale-only mt-3">
                            <div class="grid grid-cols-4 gap-2 mb-3">
                                <div><label class="text-[8px] font-bold text-gray-400 block mb-1">S</label><input type="number" value="5" onchange="updateCalculations()" class="qty-input"></div>
                                <div><label class="text-[8px] font-bold text-gray-400 block mb-1">M</label><input type="number" value="10" onchange="updateCalculations()" class="qty-input"></div>
                                <div><label class="text-[8px] font-bold text-gray-400 block mb-1">L</label><input type="number" value="5" onchange="updateCalculations()" class="qty-input"></div>
                                <div><label class="text-[8px] font-bold text-gray-400 block mb-1">XL</label><input type="number" value="0" onchange="updateCalculations()" class="qty-input"></div>
                            </div>
                        </div>

                        <div class="retail-only flex items-center gap-4 mt-4">
                            <div class="flex items-center bg-gray-100 rounded-lg p-0.5">
                                <button onclick="changeQty(this, -1)" class="w-8 h-8 flex items-center justify-center text-xs"><i class="fas fa-minus"></i></button>
                                <input type="number" value="1" readonly class="w-8 bg-transparent text-center font-bold text-xs retail-qty">
                                <button onclick="changeQty(this, 1)" class="w-8 h-8 flex items-center justify-center text-xs"><i class="fas fa-plus"></i></button>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-t flex justify-between items-center">
                            <div class="moq-status wholesale-only"></div>
                            <p class="text-lg font-black accent-text item-subtotal-display ml-auto">₹0</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card p-4 md:p-6 cart-item" data-retail="1899" data-tier1="1150" data-tier2="1050" data-tier3="999" data-id="item2">
                <div class="flex gap-4 md:gap-6">
                    <div class="w-24 md:w-32 shrink-0 aspect-[3/4] rounded-xl overflow-hidden bg-gray-50 border">
                        <img src="https://images.unsplash.com/photo-1583391733956-6c78276477e2?auto=format&fit=crop&w=400" class="w-full h-full object-cover">
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-sm md:text-base font-bold truncate">Heritage Maroon Linen Kurta</h3>
                                <p class="text-[9px] font-bold text-gray-400 uppercase">SKU: GK-202 | Maroon</p>
                            </div>
                            <button onclick="openDeleteModal('item2')" class="text-gray-300 hover:text-red-500 transition-colors"><i class="fas fa-times"></i></button>
                        </div>

                        <div class="wholesale-only mt-3">
                            <div class="grid grid-cols-4 gap-2 mb-3">
                                <div><label class="text-[8px] font-bold text-gray-400 block mb-1">S</label><input type="number" value="10" onchange="updateCalculations()" class="qty-input"></div>
                                <div><label class="text-[8px] font-bold text-gray-400 block mb-1">M</label><input type="number" value="10" onchange="updateCalculations()" class="qty-input"></div>
                                <div><label class="text-[8px] font-bold text-gray-400 block mb-1">L</label><input type="number" value="10" onchange="updateCalculations()" class="qty-input"></div>
                                <div><label class="text-[8px] font-bold text-gray-400 block mb-1">XL</label><input type="number" value="5" onchange="updateCalculations()" class="qty-input"></div>
                            </div>
                        </div>

                        <div class="retail-only flex items-center gap-4 mt-4">
                            <div class="flex items-center bg-gray-100 rounded-lg p-0.5">
                                <button onclick="changeQty(this, -1)" class="w-8 h-8 flex items-center justify-center text-xs"><i class="fas fa-minus"></i></button>
                                <input type="number" value="1" readonly class="w-8 bg-transparent text-center font-bold text-xs retail-qty">
                                <button onclick="changeQty(this, 1)" class="w-8 h-8 flex items-center justify-center text-xs"><i class="fas fa-plus"></i></button>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-t flex justify-between items-center">
                            <div class="moq-status wholesale-only"></div>
                            <p class="text-lg font-black accent-text item-subtotal-display ml-auto">₹0</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-4">
            <div class="sticky top-6 space-y-4">
                <div class="card p-6 md:p-8">
                    <h2 class="text-lg font-bold mb-6 border-b pb-4">Order Summary</h2>
                    
                    <div class="wholesale-only mb-6 p-4 bg-zinc-50 rounded-2xl border border-dashed border-gray-200">
                        <div class="flex justify-between text-[9px] font-bold uppercase mb-2">
                            <span>Bulk Slab Progress</span>
                            <span id="meterPerc">0%</span>
                        </div>
                        <div class="w-full h-1 bg-gray-200 rounded-full overflow-hidden">
                            <div id="meterBar" class="h-full accent-bg transition-all duration-500" style="width: 0%"></div>
                        </div>
                        <p id="nextSlabText" class="text-[8px] font-bold text-orange-600 mt-2 uppercase"></p>
                    </div>

                    <div class="space-y-3 text-xs">
                        <div class="flex justify-between text-gray-500"><span>Bag Total</span><span class="font-bold text-gray-900" id="summarySubtotal">₹0</span></div>
                        <div class="flex justify-between text-gray-500 wholesale-only"><span>Tier Savings</span><span class="text-green-600 font-bold" id="summaryDiscount">- ₹0</span></div>
                        <div class="flex justify-between text-gray-500"><span>GST (12%)</span><span class="font-bold text-gray-900" id="summaryGST">₹0</span></div>
                        <div class="flex justify-between text-gray-500"><span>Shipping</span><span class="text-green-600 font-bold uppercase">Free</span></div>
                        
                        <div class="pt-4 border-t flex justify-between items-center">
                            <span class="text-sm font-bold">Payable Amount</span>
                            <span class="text-2xl font-black accent-text" id="summaryNet">₹0</span>
                        </div>
                    </div>

                    <button class="w-full accent-bg text-white py-4 rounded-xl font-bold uppercase text-[10px] tracking-widest mt-8 shadow-lg shadow-orange-200 hover:brightness-110 active:scale-95 transition-all">
                        Checkout Securely
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let currentItemToDelete = null;

    function toggleMode() {
        const app = document.getElementById('cartApp');
        const btn = document.getElementById('modeBtn');
        const isWholesale = app.classList.contains('is-wholesale');
        app.classList.toggle('is-wholesale');
        btn.innerText = isWholesale ? "Retail Mode" : "Wholesale Mode";
        btn.style.backgroundColor = isWholesale ? "#FF6F1E" : "#18181b";
        updateCalculations();
    }

    function changeQty(btn, delta) {
        const input = btn.parentElement.querySelector('input');
        let val = parseInt(input.value) + delta;
        if(val < 1) val = 1;
        input.value = val;
        updateCalculations();
    }

    function updateCalculations() {
        const app = document.getElementById('cartApp');
        const isWholesale = app.classList.contains('is-wholesale');
        let totalFinal = 0, totalRetailVal = 0, totalArticles = 0;

        const items = document.querySelectorAll('.cart-item');
        
        items.forEach(item => {
            const retail = parseInt(item.dataset.retail);
            const tiers = [parseInt(item.dataset.tier1), parseInt(item.dataset.tier2), parseInt(item.dataset.tier3)];
            let qty = 0;

            if (isWholesale) {
                item.querySelectorAll('.qty-input').forEach(i => qty += parseInt(i.value || 0));
            } else {
                qty = parseInt(item.querySelector('.retail-qty').value);
            }

            let price = retail;
            if (isWholesale) {
                if (qty >= 100) price = tiers[2];
                else if (qty >= 50) price = tiers[1];
                else if (qty >= 20) price = tiers[0];
                
                const moqBox = item.querySelector('.moq-status');
                moqBox.innerHTML = qty < 20 
                    ? `<span class="text-[8px] font-bold text-red-500 bg-red-50 px-2 py-1 rounded-md border border-red-100 uppercase">Short ${20-qty} for B2B</span>` 
                    : `<span class="text-[8px] font-bold text-green-600 bg-green-50 px-2 py-1 rounded-md border border-green-100 uppercase">Slab Unlocked</span>`;
            }

            const sub = qty * price;
            item.querySelector('.item-subtotal-display').innerText = `₹${sub.toLocaleString()}`;
            totalFinal += sub;
            totalRetailVal += (qty * retail);
            totalArticles += qty;
        });

        // Summary Calculations
        const gst = Math.round(totalFinal * 0.12);
        document.getElementById('topItemCount').innerText = `(${totalArticles})`;
        document.getElementById('summarySubtotal').innerText = `₹${totalFinal.toLocaleString()}`;
        document.getElementById('summaryDiscount').innerText = `- ₹${(totalRetailVal - totalFinal).toLocaleString()}`;
        document.getElementById('summaryGST').innerText = `₹${gst.toLocaleString()}`;
        document.getElementById('summaryNet').innerText = `₹${(totalFinal + gst).toLocaleString()}`;

        // Progress Bar logic
        if(isWholesale) {
            let next = totalArticles < 20 ? 20 : (totalArticles < 50 ? 50 : 100);
            let p = Math.min((totalArticles / next) * 100, 100);
            document.getElementById('meterBar').style.width = p + '%';
            document.getElementById('meterPerc').innerText = Math.round(p) + '%';
            document.getElementById('nextSlabText').innerText = totalArticles >= 100 ? 'Max Discount Applied' : `Add ${next - totalArticles} more for next slab`;
        }
    }

    function openDeleteModal(id) {
        currentItemToDelete = id;
        const modal = document.getElementById('deleteModal');
        const box = document.getElementById('modalBox');
        
        // Update text for "Delete All" vs Single
        if(id === 'ALL') {
            document.getElementById('modalTitle').innerText = "Clear Bag?";
            document.getElementById('modalDesc').innerText = "Are you sure you want to remove all items from your bag?";
        } else {
            document.getElementById('modalTitle').innerText = "Remove Article?";
            document.getElementById('modalDesc').innerText = "Are you sure you want to remove this item from your bag?";
        }

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        setTimeout(() => box.classList.add('show'), 10);
    }

    function closeDeleteModal() {
        const modal = document.getElementById('deleteModal');
        const box = document.getElementById('modalBox');
        box.classList.remove('show');
        setTimeout(() => modal.classList.add('hidden'), 300);
    }

    document.getElementById('confirmDeleteBtn').addEventListener('click', () => {
        if(currentItemToDelete === 'ALL') {
            document.getElementById('cartItemsContainer').innerHTML = `
                <div class="text-center py-20 bg-white rounded-[2rem] border-2 border-dashed border-gray-100">
                    <p class="text-gray-400 font-bold">Your bag is empty</p>
                    <button onclick="location.reload()" class="mt-4 accent-text text-xs font-bold uppercase tracking-widest">Restart Demo</button>
                </div>
            `;
        } else {
            document.querySelector(`[data-id="${currentItemToDelete}"]`).remove();
        }
        
        updateCalculations();
        closeDeleteModal();
    });

    // Initial run
    updateCalculations();
</script>

<?php include '../../includes/footer.php'; ?>