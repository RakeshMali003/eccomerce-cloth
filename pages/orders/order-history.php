<?php include '../../includes/header.php'; ?>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
    body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }

    /* Ultra-smooth Accordion Logic */
    .expandable-grid {
        display: grid;
        grid-template-rows: 0fr;
        transition: grid-template-rows 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .order-card.active .expandable-grid { grid-template-rows: 1fr; }
    .content-overflow { overflow: hidden; }

    /* Card Styling */
    .order-card { 
        @apply bg-white rounded-[1.5rem] md:rounded-[2.5rem] border border-slate-200 mb-6 transition-all duration-300;
        box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.05);
    }
    .order-card:hover { @apply border-orange-300 shadow-xl shadow-orange-900/5; }
    .order-card.active { @apply ring-2 ring-orange-500 border-transparent shadow-2xl shadow-orange-900/10; }

    /* Micro-interactions */
    .arrow-icon { transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
    .order-card.active .arrow-icon { transform: rotate(180deg); color: #FF6F1E; }

    /* Custom Status Badges */
    .badge { @apply px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-tighter border; }
    .badge-blue { @apply bg-blue-50 text-blue-600 border-blue-100; }
    .badge-green { @apply bg-emerald-50 text-emerald-600 border-emerald-100; }
    .badge-orange { @apply bg-orange-50 text-orange-600 border-orange-100; }
    .badge-grey { @apply bg-slate-100 text-slate-500 border-slate-200; }
</style>

<body class="antialiased">

<div class="container mx-auto px-4 lg:px-20 py-10 lg:py-16">
    
    <header class="mb-12 flex flex-col md:flex-row justify-between items-end gap-6">
        <div>
            <h1 class="text-3xl lg:text-4xl font-extrabold tracking-tight text-slate-900">Track Orders</h1>
            <p class="text-slate-400 text-sm font-medium mt-1 uppercase tracking-widest text-[10px]">Manage your inventory & retail purchases</p>
        </div>
        <div class="flex gap-2 bg-slate-200/50 p-1.5 rounded-2xl">
            <button class="px-6 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest bg-white shadow-sm text-slate-900">Active</button>
            <button class="px-6 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest text-slate-500 hover:text-slate-900 transition">History</button>
        </div>
    </header>

    <div class="max-w-5xl mx-auto">

        <div class="order-card" id="card-wholesale">
            <div class="p-6 md:p-10 cursor-pointer flex items-center justify-between" onclick="toggle('card-wholesale')">
                <div class="flex flex-1 items-center gap-6 md:gap-12">
                    <div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center text-xl shrink-0 shadow-inner">
                        <i class="fas fa-boxes-stacked"></i>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-12 flex-1">
                        <div>
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Order ID</p>
                            <p class="text-sm font-bold text-slate-900">#GK-9920</p>
                        </div>
                        <div class="hidden md:block">
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Order Type</p>
                            <span class="badge badge-blue">Wholesale</span>
                        </div>
                        <div>
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Grand Total</p>
                            <p class="text-sm font-black text-orange-600">₹84,500</p>
                        </div>
                        <div class="hidden md:block">
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Status</p>
                            <div class="flex items-center text-blue-600 font-bold text-xs uppercase">
                                <span class="w-2 h-2 rounded-full bg-blue-600 animate-ping mr-2"></span> In Transit
                            </div>
                        </div>
                    </div>
                </div>
                <i class="fas fa-chevron-down ml-6 text-slate-300 arrow-icon"></i>
            </div>

            <div class="expandable-grid">
                <div class="content-overflow">
                    <div class="px-6 pb-10 md:px-10 border-t border-slate-50 bg-slate-50/50">
                        <div class="py-12 px-4">
                            <div class="relative flex justify-between items-center max-w-2xl mx-auto">
                                <div class="absolute h-0.5 bg-slate-200 w-full top-1/2 -translate-y-1/2"></div>
                                <div class="absolute h-0.5 bg-blue-600 w-2/3 top-1/2 -translate-y-1/2"></div>
                                <div class="z-10 bg-blue-600 text-white w-8 h-8 rounded-full flex items-center justify-center text-xs shadow-lg"><i class="fas fa-check"></i></div>
                                <div class="z-10 bg-blue-600 text-white w-8 h-8 rounded-full flex items-center justify-center text-xs shadow-lg shadow-blue-200"><i class="fas fa-truck"></i></div>
                                <div class="z-10 bg-white text-slate-300 border-2 border-slate-200 w-8 h-8 rounded-full flex items-center justify-center text-xs"><i class="fas fa-house"></i></div>
                            </div>
                            <div class="flex justify-between max-w-2xl mx-auto mt-4 px-2">
                                <span class="text-[10px] font-black uppercase text-slate-400">Order Confirmed</span>
                                <span class="text-[10px] font-black uppercase text-blue-600">Dispatched</span>
                                <span class="text-[10px] font-black uppercase text-slate-300">Delivered</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
                            <div class="space-y-4">
                                <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b pb-2">Shipment Contents (120 Pcs)</h4>
                                <div class="flex items-center gap-4 bg-white p-3 rounded-2xl border border-slate-100">
                                    <img src="https://images.unsplash.com/photo-1596755094514-f87e34085b2c?auto=format&fit=crop&w=100" class="w-12 h-16 rounded-xl object-cover shadow-sm">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-bold text-slate-900 truncate">Azure Oxford Cotton</p>
                                        <p class="text-[10px] text-slate-400 font-bold mt-1">S: 20 | M: 40 | L: 20</p>
                                    </div>
                                    <p class="text-xs font-black text-slate-900">₹55,920</p>
                                </div>
                                <button class="w-full text-center text-[10px] font-black text-orange-600 uppercase tracking-widest hover:underline">+ View 3 Other Articles</button>
                            </div>
                            
                            <div class="flex flex-col justify-between bg-slate-900 rounded-[2rem] p-8 text-white">
                                <div>
                                    <p class="text-[9px] font-black text-slate-500 uppercase tracking-widest mb-3">Logistics Partner</p>
                                    <h5 class="text-base font-bold">TCI Express (Bulk Transport)</h5>
                                    <p class="text-[11px] text-slate-400 mt-1">Kamala Mills Warehouse → Kolkata City Hub</p>
                                    <p class="text-[11px] text-orange-500 font-black mt-4 uppercase">AWB: 2209118374</p>
                                </div>
                                <div class="flex gap-3 mt-8">
                                    <button class="flex-1 bg-white text-slate-900 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-orange-600 hover:text-white transition-all">Download Invoice</button>
                                    <button class="w-12 h-12 flex items-center justify-center bg-white/10 rounded-xl hover:bg-white/20 transition-all"><i class="fas fa-headset text-xs"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="order-card" id="card-retail">
            <div class="p-6 md:p-10 cursor-pointer flex items-center justify-between" onclick="toggle('card-retail')">
                <div class="flex flex-1 items-center gap-6 md:gap-12">
                    <div class="w-14 h-14 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center text-xl shrink-0 shadow-inner">
                        <i class="fas fa-bag-shopping"></i>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-12 flex-1">
                        <div>
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Order ID</p>
                            <p class="text-sm font-bold text-slate-900">#GK-4412</p>
                        </div>
                        <div class="hidden md:block">
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Order Type</p>
                            <span class="badge badge-green">Retail</span>
                        </div>
                        <div>
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Paid Via</p>
                            <p class="text-sm font-bold text-slate-900 italic">UPI / GPay</p>
                        </div>
                        <div class="hidden md:block">
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Status</p>
                            <div class="flex items-center text-emerald-600 font-bold text-xs uppercase">
                                <i class="fas fa-check-circle mr-2"></i> Confirmed
                            </div>
                        </div>
                    </div>
                </div>
                <i class="fas fa-chevron-down ml-6 text-slate-300 arrow-icon"></i>
            </div>
            <div class="expandable-grid">
                <div class="content-overflow"><div class="p-10 border-t border-slate-50 text-center text-slate-400 text-xs italic uppercase tracking-[0.2em]">Package is being sanitized and packed...</div></div>
            </div>
        </div>

        <div class="order-card opacity-60 border-dashed border-slate-300 shadow-none hover:shadow-none" id="card-sample">
            <div class="p-6 md:p-10 cursor-pointer flex items-center justify-between" onclick="toggle('card-sample')">
                <div class="flex flex-1 items-center gap-6 md:gap-12">
                    <div class="w-14 h-14 bg-slate-100 text-slate-500 rounded-2xl flex items-center justify-center text-xl shrink-0">
                        <i class="fas fa-flask"></i>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-12 flex-1">
                        <div>
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Order ID</p>
                            <p class="text-sm font-bold text-slate-900">#SK-1102</p>
                        </div>
                        <div class="hidden md:block">
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Order Type</p>
                            <span class="badge badge-grey">Sample Kit</span>
                        </div>
                        <div>
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Current Task</p>
                            <p class="text-sm font-bold text-slate-900">Awaiting QC</p>
                        </div>
                        <div class="hidden md:block">
                            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Status</p>
                            <div class="flex items-center text-slate-400 font-bold text-xs uppercase">
                                <i class="fas fa-clock mr-2"></i> Pending
                            </div>
                        </div>
                    </div>
                </div>
                <i class="fas fa-chevron-down ml-6 text-slate-200 arrow-icon"></i>
            </div>
            <div class="expandable-grid">
                <div class="content-overflow"><div class="p-10 border-t border-slate-50 text-center text-slate-400 text-xs italic uppercase tracking-[0.2em]">Our QC team is verifying fabric swatches...</div></div>
            </div>
        </div>

    </div>
</div>

<script>
    function toggle(id) {
        const card = document.getElementById(id);
        const isActive = card.classList.contains('active');

        // Close all others for a cleaner UX
        document.querySelectorAll('.order-card').forEach(c => c.classList.remove('active'));

        if (!isActive) {
            card.classList.add('active');
        }
    }
</script>

<?php include '../../includes/footer.php'; ?>