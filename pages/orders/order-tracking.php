<?php include '../../includes/header.php'; ?>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');
    
    :root { --brand-orange: #FF6F1E; }
    body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; color: #0f172a; }

    /* Vertical Timeline Logic */
    .step-line { position: absolute; left: 15px; top: 30px; bottom: -10px; width: 2px; background: #e2e8f0; }
    .step-active .step-line { background: var(--brand-orange); }
    .step-dot { position: relative; z-index: 10; width: 32px; h-32px; @apply rounded-full border-4 border-white shadow-sm transition-all duration-500; }
    
    .step-complete .step-dot { background: var(--brand-orange); border-color: #ffedd5; }
    .step-current .step-dot { background: white; border-color: var(--brand-orange); }
    .step-pending .step-dot { background: #f1f5f9; border-color: #e2e8f0; }

    .pulse-orange { box-shadow: 0 0 0 0 rgba(255, 111, 30, 0.7); animation: pulse 2s infinite; }
    @keyframes pulse {
        70% { box-shadow: 0 0 0 10px rgba(255, 111, 30, 0); }
        100% { box-shadow: 0 0 0 0 rgba(255, 111, 30, 0); }
    }
</style>

<body class="antialiased">

<div class="container mx-auto px-4 lg:px-12 py-10 lg:py-20">
    
    <div class="max-w-4xl mx-auto mb-12">
        <h1 class="text-4xl font-extrabold tracking-tighter mb-4">Track Your Shipment <span class="text-orange-600">.</span></h1>
        <div class="bg-white p-2 rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-100 flex flex-col md:flex-row gap-2">
            <input type="text" placeholder="Enter Order ID or AWB Number (e.g. GK-99283)" class="flex-1 px-6 py-4 bg-transparent border-none outline-none font-bold text-sm">
            <button class="px-10 py-4 bg-slate-900 text-white rounded-2xl font-black uppercase tracking-widest hover:bg-orange-600 transition-all active:scale-95">Track Order</button>
        </div>
    </div>

    <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <div class="lg:col-span-7 space-y-6">
            <div class="bg-white rounded-[2.5rem] p-8 lg:p-10 border border-slate-100 shadow-sm">
                <div class="flex justify-between items-start mb-10 pb-6 border-b border-slate-50">
                    <div>
                        <p class="text-[10px] font-black uppercase text-slate-400 tracking-widest">Order ID: #GK-22409</p>
                        <h2 class="text-xl font-bold mt-1">Status: <span class="text-orange-600">In Transit</span></h2>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] font-black uppercase text-slate-400">Estimated Delivery</p>
                        <p class="text-lg font-black text-slate-900">Dec 30, 2025</p>
                    </div>
                </div>

                <div class="space-y-12 pl-2">
                    <div class="relative flex gap-6 step-complete step-active">
                        <div class="step-line"></div>
                        <div class="step-dot flex items-center justify-center text-white text-[10px]"><i class="fas fa-check"></i></div>
                        <div>
                            <h4 class="font-bold text-slate-900">Order Placed</h4>
                            <p class="text-xs text-slate-400 mt-1 uppercase font-medium">Dec 25 • 10:30 AM</p>
                            <p class="text-sm text-slate-500 mt-2">Order received and sent to the Gurukrupa warehouse hub.</p>
                        </div>
                    </div>

                    <div class="relative flex gap-6 step-complete step-active">
                        <div class="step-line"></div>
                        <div class="step-dot flex items-center justify-center text-white text-[10px]"><i class="fas fa-check"></i></div>
                        <div>
                            <h4 class="font-bold text-slate-900">Quality Check & Packaging</h4>
                            <p class="text-xs text-slate-400 mt-1 uppercase font-medium">Dec 26 • 02:15 PM</p>
                            <p class="text-sm text-slate-500 mt-2">Verified fabric GSM and stitching quality. Manifest generated.</p>
                        </div>
                    </div>

                    <div class="relative flex gap-6 step-current">
                        <div class="step-line" style="background: #e2e8f0;"></div>
                        <div class="step-dot pulse-orange"></div>
                        <div>
                            <h4 class="font-bold text-slate-900">Shipped via TCI Express</h4>
                            <p class="text-xs text-orange-600 mt-1 uppercase font-bold tracking-widest">In Transit • Dec 27 • 09:00 AM</p>
                            <p class="text-sm text-slate-500 mt-2">Handed over to bulk transport. Currently at Mumbai Logistics Hub.</p>
                            <a href="#" class="inline-block mt-4 px-4 py-2 bg-slate-50 rounded-lg text-[10px] font-black uppercase text-slate-400 border border-slate-100">Live AWB: 2209118374</a>
                        </div>
                    </div>

                    <div class="relative flex gap-6 step-pending">
                        <div class="step-dot"></div>
                        <div>
                            <h4 class="font-bold text-slate-300">Out for Delivery</h4>
                            <p class="text-sm text-slate-300 mt-2 italic">Awaiting arrival at destination city hub.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-5 space-y-6">
            
            <div class="bg-white rounded-[2.5rem] p-8 border border-slate-100 shadow-sm">
                <h3 class="text-sm font-black uppercase tracking-widest text-slate-400 mb-6">Delivery Address</h3>
                <div class="flex gap-4">
                    <div class="w-12 h-12 bg-slate-50 rounded-2xl flex items-center justify-center text-slate-400 shrink-0">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div>
                        <p class="font-bold text-slate-900">Royal Boutique & Textiles</p>
                        <p class="text-sm text-slate-500 mt-1 leading-relaxed">
                            B-402, Trade World Building,<br>
                            Kamala Mills Compound, Lower Parel,<br>
                            Mumbai, MH - 400013
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-slate-900 rounded-[2.5rem] p-8 text-white shadow-2xl shadow-slate-300">
                <h3 class="text-sm font-bold uppercase tracking-widest text-slate-500 mb-6 italic">Shipment Documents</h3>
                <div class="space-y-4">
                    <button class="w-full flex items-center justify-between p-4 bg-white/5 rounded-2xl border border-white/10 hover:bg-white/10 transition-all">
                        <span class="flex items-center gap-3 text-sm font-medium"><i class="fas fa-file-invoice text-orange-500"></i> GST Tax Invoice</span>
                        <i class="fas fa-download text-xs opacity-40"></i>
                    </button>
                    <button class="w-full flex items-center justify-between p-4 bg-white/5 rounded-2xl border border-white/10 hover:bg-white/10 transition-all">
                        <span class="flex items-center gap-3 text-sm font-medium"><i class="fas fa-box text-orange-500"></i> Packing List (Bulk)</span>
                        <i class="fas fa-download text-xs opacity-40"></i>
                    </button>
                </div>
            </div>

            <div class="bg-orange-50 rounded-[2.5rem] p-8 border border-orange-100">
                <p class="text-[10px] font-black uppercase text-orange-600 tracking-widest mb-4">Need Assistance?</p>
                <h4 class="text-lg font-bold text-slate-900 mb-4">Contact Logistics Desk</h4>
                <div class="flex gap-3">
                    <a href="#" class="flex-1 bg-white p-3 rounded-xl text-center shadow-sm hover:scale-105 transition-transform">
                        <i class="fab fa-whatsapp text-green-500 text-xl"></i>
                    </a>
                    <a href="#" class="flex-1 bg-white p-3 rounded-xl text-center shadow-sm hover:scale-105 transition-transform">
                        <i class="fas fa-phone-alt text-slate-900 text-xl"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>