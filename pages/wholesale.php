<?php include '../includes/header.php'; ?>

<body class="bg-slate-50 text-slate-900">

    <header class="relative bg-zinc-900 text-white pt-32 pb-20 px-6 overflow-hidden">
        <div class="absolute inset-0 opacity-20">
            <img src="https://images.unsplash.com/photo-1556740749-887f6717d7e4?auto=format&fit=crop&q=80" class="w-full h-full object-cover">
        </div>
        <div class="container mx-auto relative z-10 text-center">
            <div class="flex justify-center gap-4 mb-6">
                <span class="bg-white/10 backdrop-blur-md px-4 py-1 rounded-full text-[10px] font-bold tracking-widest uppercase border border-white/20">Quality Checked</span>
                <span class="bg-white/10 backdrop-blur-md px-4 py-1 rounded-full text-[10px] font-bold tracking-widest uppercase border border-white/20">GST Verified</span>
                <span class="bg-white/10 backdrop-blur-md px-4 py-1 rounded-full text-[10px] font-bold tracking-widest uppercase border border-white/20">Manufacturer</span>
            </div>
            <h1 class="text-4xl md:text-7xl font-serif mb-6 leading-tight">Wholesale Clothing Supplier<br><span class="italic font-light text-brand">Bulk Orders at Best Prices</span></h1>
            <p class="text-zinc-400 max-w-2xl mx-auto text-lg mb-10">Premium quality garments for retailers, boutiques & resellers. Factory prices with Pan-India delivery.</p>
            
            <div class="flex flex-wrap justify-center gap-4">
                <a href="#catalogue" class="bg-brand hover:bg-white hover:text-black px-8 py-4 rounded-xl font-bold transition-all flex items-center gap-2">
                    <i class="fas fa-file-pdf"></i> Request Catalogue
                </a>
                <a href="https://wa.me/919876543210" class="bg-zinc-800 hover:bg-zinc-700 px-8 py-4 rounded-xl font-bold transition-all flex items-center gap-2">
                    <i class="fab fa-whatsapp"></i> Contact Sales
                </a>
                <a href="#register" class="bg-white text-black hover:bg-brand hover:text-white px-8 py-4 rounded-xl font-bold transition-all">Become a Dealer</a>
            </div>
        </div>
    </header>

    <section class="py-20 container mx-auto px-6">
        <div class="bg-white rounded-[2.5rem] p-8 md:p-16 shadow-xl border border-slate-100 flex flex-col md:flex-row items-center gap-12">
            <div class="md:w-1/2">
                <h2 class="text-3xl md:text-5xl font-serif mb-6 italic text-zinc-800">Who Can Buy Wholesale?</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="flex items-center gap-3 p-4 bg-slate-50 rounded-xl border-l-4 border-brand">
                        <i class="fas fa-store text-brand"></i> <span class="font-bold text-sm">Retail Shop Owners</span>
                    </div>
                    <div class="flex items-center gap-3 p-4 bg-slate-50 rounded-xl border-l-4 border-brand">
                        <i class="fas fa-globe text-brand"></i> <span class="font-bold text-sm">Online Sellers</span>
                    </div>
                    <div class="flex items-center gap-3 p-4 bg-slate-50 rounded-xl border-l-4 border-brand">
                        <i class="fas fa-cut text-brand"></i> <span class="font-bold text-sm">Boutique Owners</span>
                    </div>
                    <div class="flex items-center gap-3 p-4 bg-slate-50 rounded-xl border-l-4 border-brand">
                        <i class="fas fa-user-tie text-brand"></i> <span class="font-bold text-sm">Corporate Buyers</span>
                    </div>
                </div>
                <p class="mt-6 text-xs text-zinc-400 font-bold uppercase tracking-widest"><i class="fas fa-info-circle mr-2"></i> Valid business details required for wholesale pricing.</p>
            </div>
            <div class="md:w-1/2 grid grid-cols-2 gap-4">
                <div class="p-6 bg-brand/5 rounded-3xl text-center">
                    <h4 class="text-3xl font-black text-brand">10-50</h4>
                    <p class="text-[10px] font-bold text-zinc-500 uppercase">Minimum Order Quantity</p>
                </div>
                <div class="p-6 bg-brand/5 rounded-3xl text-center">
                    <h4 class="text-3xl font-black text-brand">60%</h4>
                    <p class="text-[10px] font-bold text-zinc-500 uppercase">Up To Off Factory Rates</p>
                </div>
            </div>
        </div>
    </section>

    <section class="py-20 bg-white">
        <div class="container mx-auto px-6">
            <div class="flex justify-between items-end mb-12">
                <h2 class="text-4xl font-serif italic">Wholesale Categories</h2>
                <p class="text-brand font-bold border-b-2 border-brand pb-1">Custom Stitching & Private Labeling Available</p>
            </div>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="group relative aspect-[3/4] rounded-[2rem] overflow-hidden bg-slate-200">
                    <img src="https://images.unsplash.com/photo-1617137984095-74e4e5e3613f?auto=format&w=600" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 flex flex-col justify-end p-6">
                        <h4 class="text-white font-bold text-xl uppercase tracking-tighter">Men's Wear</h4>
                        <p class="text-white/60 text-[10px] uppercase tracking-widest">Shirts, Jeans, Track Pants</p>
                    </div>
                </div>
                <div class="group relative aspect-[3/4] rounded-[2rem] overflow-hidden bg-slate-200">
                    <img src="https://images.unsplash.com/photo-1583391733956-6c78276477e2?auto=format&w=600" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 flex flex-col justify-end p-6">
                        <h4 class="text-white font-bold text-xl uppercase tracking-tighter">Women's Wear</h4>
                        <p class="text-white/60 text-[10px] uppercase tracking-widest">Kurtis, Sarees, Dresses</p>
                    </div>
                </div>
                <div class="group relative aspect-[3/4] rounded-[2rem] overflow-hidden bg-slate-200">
                    <img src="https://images.unsplash.com/photo-1519702221971-4713833d739e?auto=format&w=600" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 flex flex-col justify-end p-6">
                        <h4 class="text-white font-bold text-xl uppercase tracking-tighter">Kids Wear</h4>
                        <p class="text-white/60 text-[10px] uppercase tracking-widest">Boys & Girls Collections</p>
                    </div>
                </div>
                <div class="group relative aspect-[3/4] rounded-[2rem] overflow-hidden bg-slate-200">
                    <img src="https://images.unsplash.com/photo-1595332245752-16718024f5e2?auto=format&w=600" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 flex flex-col justify-end p-6">
                        <h4 class="text-white font-bold text-xl uppercase tracking-tighter">Ethnic Specials</h4>
                        <p class="text-white/60 text-[10px] uppercase tracking-widest">Celebration & Festive</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-24 bg-slate-900 text-white">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-20">
                <div>
                    <h3 class="text-4xl font-serif italic mb-8">Professional Order Process</h3>
                    <div class="space-y-6">
                        <div class="flex gap-4">
                            <span class="w-10 h-10 rounded-full border border-brand flex items-center justify-center font-bold text-brand">01</span>
                            <div><h5 class="font-bold">Browse Catalogue</h5><p class="text-sm text-zinc-500">Pick designs from our latest seasonal PDF or website.</p></div>
                        </div>
                        <div class="flex gap-4">
                            <span class="w-10 h-10 rounded-full border border-brand flex items-center justify-center font-bold text-brand">02</span>
                            <div><h5 class="font-bold">Send Enquiry</h5><p class="text-sm text-zinc-500">Add bulk order details and confirm MOQ per design.</p></div>
                        </div>
                        <div class="flex gap-4">
                            <span class="w-10 h-10 rounded-full border border-brand flex items-center justify-center font-bold text-brand">03</span>
                            <div><h5 class="font-bold">Production & QC</h5><p class="text-sm text-zinc-500">We manufacture and conduct 100% quality checks.</p></div>
                        </div>
                        <div class="flex gap-4">
                            <span class="w-10 h-10 rounded-full border border-brand flex items-center justify-center font-bold text-brand">04</span>
                            <div><h5 class="font-bold">Dispatch</h5><p class="text-sm text-zinc-500">Packaging and delivery within 5–10 working days.</p></div>
                        </div>
                    </div>
                </div>
                <div class="bg-white/5 backdrop-blur-md p-10 rounded-3xl border border-white/10">
                    <h4 class="text-2xl font-bold mb-6">MOQ & Pricing Guide</h4>
                    <ul class="space-y-4 mb-8">
                        <li class="flex justify-between border-b border-white/10 pb-2"><span class="text-zinc-400">Min Order</span><span class="font-bold">10–50 Pcs/Design</span></li>
                        <li class="flex justify-between border-b border-white/10 pb-2"><span class="text-zinc-400">Mixed Sizes</span><span class="font-bold">Allowed</span></li>
                        <li class="flex justify-between border-b border-white/10 pb-2"><span class="text-zinc-400">Samples</span><span class="font-bold">Paid/Refundable</span></li>
                        <li class="flex justify-between border-b border-white/10 pb-2"><span class="text-zinc-400">Billing</span><span class="font-bold">GST Invoicing</span></li>
                    </ul>
                    <div class="p-6 bg-brand rounded-2xl text-center">
                        <p class="text-xs font-bold uppercase tracking-[0.2em] mb-2">Ready to Start?</p>
                        <h5 class="text-xl font-black">Get Factory Quotation</h5>
                        <a href="tel:+919876543210" class="mt-4 inline-block bg-black px-6 py-2 rounded-lg font-bold">Call Now</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="register" class="py-24 container mx-auto px-6">
        <div class="max-w-4xl mx-auto bg-white rounded-[3rem] shadow-2xl overflow-hidden border border-slate-100">
            <div class="flex flex-col md:flex-row">
                <div class="md:w-1/3 bg-brand p-12 text-white">
                    <h3 class="text-3xl font-serif italic mb-6">Apply for Wholesale Account</h3>
                    <p class="text-sm opacity-90 mb-8">Join our network of 500+ successful clothing dealers across India.</p>
                    <div class="space-y-4 text-xs font-bold uppercase">
                        <p><i class="fas fa-check mr-2"></i> Priority Production</p>
                        <p><i class="fas fa-check mr-2"></i> Direct Factory Rates</p>
                        <p><i class="fas fa-check mr-2"></i> Custom Branding</p>
                    </div>
                </div>
                <div class="md:w-2/3 p-12">
                    <form class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="sm:col-span-2">
                            <label class="text-[10px] font-bold uppercase text-zinc-400 block mb-2">Business Name</label>
                            <input type="text" class="w-full bg-slate-50 border-none rounded-xl px-4 py-3 focus:ring-2 focus:ring-brand">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold uppercase text-zinc-400 block mb-2">Owner Name</label>
                            <input type="text" class="w-full bg-slate-50 border-none rounded-xl px-4 py-3 focus:ring-2 focus:ring-brand">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold uppercase text-zinc-400 block mb-2">Mobile Number</label>
                            <input type="tel" class="w-full bg-slate-50 border-none rounded-xl px-4 py-3 focus:ring-2 focus:ring-brand">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold uppercase text-zinc-400 block mb-2">City & State</label>
                            <input type="text" class="w-full bg-slate-50 border-none rounded-xl px-4 py-3 focus:ring-2 focus:ring-brand">
                        </div>
                        <div>
                            <label class="text-[10px] font-bold uppercase text-zinc-400 block mb-2">GST Number (Optional)</label>
                            <input type="text" class="w-full bg-slate-50 border-none rounded-xl px-4 py-3 focus:ring-2 focus:ring-brand">
                        </div>
                        <div class="sm:col-span-2">
                            <button type="submit" class="w-full bg-zinc-900 text-white py-4 rounded-xl font-bold hover:bg-brand transition-all uppercase tracking-widest">Apply for Wholesale Account</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <section class="py-24 bg-slate-50">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-16">
                <div>
                    <h4 class="text-3xl font-serif italic mb-8 underline decoration-brand">Wholesale FAQ</h4>
                    <div class="space-y-4">
                        <details class="group bg-white rounded-2xl p-4 shadow-sm border border-slate-100">
                            <summary class="font-bold cursor-pointer flex justify-between">Do you provide samples? <i class="fas fa-chevron-down group-open:rotate-180"></i></summary>
                            <p class="text-sm text-zinc-500 mt-2">Yes, samples are available at retail prices (Paid). This cost is adjustable/refundable on your first bulk order.</p>
                        </details>
                        <details class="group bg-white rounded-2xl p-4 shadow-sm border border-slate-100">
                            <summary class="font-bold cursor-pointer flex justify-between">Is GST mandatory? <i class="fas fa-chevron-down group-open:rotate-180"></i></summary>
                            <p class="text-sm text-zinc-500 mt-2">GST is required for formal B2B invoicing. For small resellers, we can provide simplified billing options.</p>
                        </details>
                        <details class="group bg-white rounded-2xl p-4 shadow-sm border border-slate-100">
                            <summary class="font-bold cursor-pointer flex justify-between">Can I mix designs? <i class="fas fa-chevron-down group-open:rotate-180"></i></summary>
                            <p class="text-sm text-zinc-500 mt-2">Design-wise MOQ is mandatory for factory rates, but you can mix sizes within those designs.</p>
                        </details>
                    </div>
                </div>
                <div class="flex flex-col justify-center">
                    <div class="p-10 bg-white rounded-[2rem] shadow-xl border border-slate-100 relative">
                        <i class="fas fa-quote-left text-4xl text-brand/20 absolute top-6 left-6"></i>
                        <p class="text-lg italic text-zinc-600 mb-6 leading-relaxed">"Great quality, timely delivery, and excellent wholesale pricing. The direct factory access has allowed us to increase our profit margins by 35% compared to local distributors."</p>
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full bg-slate-200"></div>
                            <div><h6 class="font-bold">Retail Partner</h6><p class="text-[10px] text-zinc-400 uppercase">Mumbai, India</p></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

   

    <?php include '../includes/footer.php'; ?>
