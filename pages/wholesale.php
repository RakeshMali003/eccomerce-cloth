<?php include '../includes/header.php';
require_once '../includes/cms_helper.php';
?>

<header class="relative bg-zinc-900 text-white pt-32 pb-20 px-6 overflow-hidden">
    <div class="absolute inset-0 opacity-20">
        <img src="<?= get_cms_content('wholesale', 'hero_image', 'https://images.unsplash.com/photo-1621905251189-08b45d6a269e?q=80&w=2069&auto=format&fit=crop') ?>"
            class="w-full h-full object-cover">
    </div>
    <div class="container mx-auto relative z-10 text-center">
        <div class="flex justify-center gap-4 mb-6">
            <span
                class="bg-white/10 backdrop-blur-md px-4 py-1 rounded-full text-[10px] font-bold tracking-widest uppercase border border-white/20">Authorized
                Distributor</span>
            <span
                class="bg-white/10 backdrop-blur-md px-4 py-1 rounded-full text-[10px] font-bold tracking-widest uppercase border border-white/20">GST
                Verified</span>
            <span
                class="bg-white/10 backdrop-blur-md px-4 py-1 rounded-full text-[10px] font-bold tracking-widest uppercase border border-white/20">Genuine
                Products</span>
        </div>
        <h1 class="text-4xl md:text-7xl font-sans font-bold mb-6 leading-tight">
            <?= get_cms_content('wholesale', 'hero_title', 'Electrical Wholesale Supplier<br><span class="text-yellow-500">Bulk Orders at Best Rates</span>') ?>
        </h1>
        <p class="text-zinc-400 max-w-2xl mx-auto text-lg mb-10">
            <?= get_cms_content('wholesale', 'hero_desc', 'Premium quality HPL, Anchor, Havells products for retailers, electricians & contractors. Pan-India delivery.') ?>
        </p>

        <div class="flex flex-wrap justify-center gap-4">
            <a href="#catalogue"
                class="bg-yellow-500 text-black hover:bg-white px-8 py-4 rounded-xl font-bold transition-all flex items-center gap-2">
                <i class="fas fa-file-pdf"></i> Request Pricelist
            </a>
            <a href="https://wa.me/919956510247"
                class="bg-zinc-800 hover:bg-zinc-700 text-white px-8 py-4 rounded-xl font-bold transition-all flex items-center gap-2">
                <i class="fab fa-whatsapp"></i> Contact Sales
            </a>
            <a href="#register"
                class="bg-white text-black hover:bg-yellow-500 px-8 py-4 rounded-xl font-bold transition-all">Become
                a Dealer</a>
        </div>
    </div>
</header>

<section class="py-20 container mx-auto px-6">
    <div
        class="bg-white rounded-[2.5rem] p-8 md:p-16 shadow-xl border border-zinc-100 flex flex-col md:flex-row items-center gap-12">
        <div class="md:w-1/2">
            <h2 class="text-3xl md:text-5xl font-sans font-bold mb-6 text-zinc-900">Who We Supply To?</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="flex items-center gap-3 p-4 bg-zinc-50 rounded-xl border-l-4 border-yellow-500">
                    <i class="fas fa-store text-yellow-600"></i> <span class="font-bold text-sm">Retail Shop
                        Owners</span>
                </div>
                <div class="flex items-center gap-3 p-4 bg-zinc-50 rounded-xl border-l-4 border-yellow-500">
                    <i class="fas fa-hard-hat text-yellow-600"></i> <span class="font-bold text-sm">Civil
                        Contractors</span>
                </div>
                <div class="flex items-center gap-3 p-4 bg-zinc-50 rounded-xl border-l-4 border-yellow-500">
                    <i class="fas fa-bolt text-yellow-600"></i> <span class="font-bold text-sm">Electricians</span>
                </div>
                <div class="flex items-center gap-3 p-4 bg-zinc-50 rounded-xl border-l-4 border-yellow-500">
                    <i class="fas fa-building text-yellow-600"></i> <span class="font-bold text-sm">Builders &
                        Developers</span>
                </div>
            </div>
            <p class="mt-6 text-xs text-zinc-400 font-bold uppercase tracking-widest"><i
                    class="fas fa-info-circle mr-2"></i> Valid business details required for wholesale pricing.</p>
        </div>
        <div class="md:w-1/2 grid grid-cols-2 gap-4">
            <div class="p-6 bg-yellow-50 rounded-3xl text-center">
                <h4 class="text-3xl font-black text-yellow-600">Bulk</h4>
                <p class="text-[10px] font-bold text-zinc-500 uppercase">Quantity Discounts</p>
            </div>
            <div class="p-6 bg-yellow-50 rounded-3xl text-center">
                <h4 class="text-3xl font-black text-yellow-600">GST</h4>
                <p class="text-[10px] font-bold text-zinc-500 uppercase">Input Credit Available</p>
            </div>
        </div>
    </div>
</section>

<section class="py-20 bg-white">
    <div class="container mx-auto px-6">
        <div class="flex justify-between items-end mb-12">
            <h2 class="text-4xl font-sans font-bold text-zinc-900">Wholesale Categories</h2>
            <p class="text-yellow-600 font-bold border-b-2 border-yellow-500 pb-1">Project Supply & Bulk Deals
                Available
            </p>
        </div>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="group relative aspect-[3/4] rounded-[2rem] overflow-hidden bg-zinc-100">
                <div class="absolute inset-0 flex items-center justify-center bg-zinc-200">
                    <i class="fas fa-lightbulb text-6xl text-zinc-400 group-hover:scale-110 transition-transform"></i>
                </div>
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 flex flex-col justify-end p-6">
                    <h4 class="text-white font-bold text-xl uppercase tracking-tighter">lighting</h4>
                    <p class="text-white/60 text-[10px] uppercase tracking-widest">LED Bulbs, Tubes, Panels</p>
                </div>
            </div>
            <div class="group relative aspect-[3/4] rounded-[2rem] overflow-hidden bg-zinc-100">
                <div class="absolute inset-0 flex items-center justify-center bg-zinc-200">
                    <i class="fas fa-toggle-on text-6xl text-zinc-400 group-hover:scale-110 transition-transform"></i>
                </div>
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 flex flex-col justify-end p-6">
                    <h4 class="text-white font-bold text-xl uppercase tracking-tighter">Switches</h4>
                    <p class="text-white/60 text-[10px] uppercase tracking-widest">Modular, Sockets, Plates</p>
                </div>
            </div>
            <div class="group relative aspect-[3/4] rounded-[2rem] overflow-hidden bg-zinc-100">
                <div class="absolute inset-0 flex items-center justify-center bg-zinc-200">
                    <i class="fas fa-fan text-6xl text-zinc-400 group-hover:scale-110 transition-transform"></i>
                </div>
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 flex flex-col justify-end p-6">
                    <h4 class="text-white font-bold text-xl uppercase tracking-tighter">Appliances</h4>
                    <p class="text-white/60 text-[10px] uppercase tracking-widest">Fans, Geysers, Irons</p>
                </div>
            </div>
            <div class="group relative aspect-[3/4] rounded-[2rem] overflow-hidden bg-zinc-100">
                <div class="absolute inset-0 flex items-center justify-center bg-zinc-200">
                    <i class="fas fa-plug text-6xl text-zinc-400 group-hover:scale-110 transition-transform"></i>
                </div>
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 flex flex-col justify-end p-6">
                    <h4 class="text-white font-bold text-xl uppercase tracking-tighter">Wires</h4>
                    <p class="text-white/60 text-[10px] uppercase tracking-widest">Cables, MCBs, DB Boxes</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-24 bg-zinc-900 text-white">
    <div class="container mx-auto px-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-20">
            <div>
                <h3 class="text-4xl font-sans font-bold mb-8">Professional Order Process</h3>
                <div class="space-y-6">
                    <div class="flex gap-4">
                        <span
                            class="w-10 h-10 rounded-full border border-yellow-500 flex items-center justify-center font-bold text-yellow-500">01</span>
                        <div>
                            <h5 class="font-bold">Check Product List</h5>
                            <p class="text-sm text-zinc-400">Browse our wide range of electrical items on the website.
                            </p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <span
                            class="w-10 h-10 rounded-full border border-yellow-500 flex items-center justify-center font-bold text-yellow-500">02</span>
                        <div>
                            <h5 class="font-bold">Send Requirement</h5>
                            <p class="text-sm text-zinc-400">Send us your list via WhatsApp or Inquiry Form.</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <span
                            class="w-10 h-10 rounded-full border border-yellow-500 flex items-center justify-center font-bold text-yellow-500">03</span>
                        <div>
                            <h5 class="font-bold">Get Quotation</h5>
                            <p class="text-sm text-zinc-400">We will provide the best wholesale rates and availability.
                            </p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <span
                            class="w-10 h-10 rounded-full border border-yellow-500 flex items-center justify-center font-bold text-yellow-500">04</span>
                        <div>
                            <h5 class="font-bold">Dispatch & Delivery</h5>
                            <p class="text-sm text-zinc-400">Secure packaging and fast delivery to your location.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-white/5 backdrop-blur-md p-10 rounded-3xl border border-white/10">
                <h4 class="text-2xl font-bold mb-6">Terms & Pricing</h4>
                <ul class="space-y-4 mb-8">
                    <li class="flex justify-between border-b border-white/10 pb-2"><span class="text-zinc-400">Min
                            Order</span><span class="font-bold">Negotiable</span></li>
                    <li class="flex justify-between border-b border-white/10 pb-2"><span
                            class="text-zinc-400">Payment</span><span class="font-bold">Advance / COD (Partial)</span>
                    </li>
                    <li class="flex justify-between border-b border-white/10 pb-2"><span
                            class="text-zinc-400">Returns</span><span class="font-bold">Replacement Warranty</span></li>
                    <li class="flex justify-between border-b border-white/10 pb-2"><span
                            class="text-zinc-400">Billing</span><span class="font-bold">GST Invoice</span></li>
                </ul>
                <div class="p-6 bg-yellow-500 text-black rounded-2xl text-center">
                    <p class="text-xs font-bold uppercase tracking-[0.2em] mb-2">Ready to Start?</p>
                    <h5 class="text-xl font-black">Get Dealer Price Check</h5>
                    <a href="tel:6386517300"
                        class="mt-4 inline-block bg-black text-white px-6 py-2 rounded-lg font-bold hover:bg-white hover:text-black transition-all">Call
                        Now</a>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="register" class="py-24 container mx-auto px-6">
    <div class="max-w-4xl mx-auto bg-white rounded-[3rem] shadow-2xl overflow-hidden border border-zinc-100">
        <div class="flex flex-col md:flex-row">
            <div class="md:w-1/3 bg-zinc-900 p-12 text-white">
                <h3 class="text-3xl font-sans font-bold mb-6">Register as Dealer</h3>
                <p class="text-sm opacity-90 mb-8">Join the network of successful electrical dealers with Joshi
                    Electrical.
                </p>
                <div class="space-y-4 text-xs font-bold uppercase">
                    <p><i class="fas fa-check mr-2 text-yellow-500"></i> Best Margins</p>
                    <p><i class="fas fa-check mr-2 text-yellow-500"></i> HPL Authorized</p>
                    <p><i class="fas fa-check mr-2 text-yellow-500"></i> Marketing Support</p>
                </div>
            </div>
            <div class="md:w-2/3 p-12">
                <form class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="sm:col-span-2">
                        <label class="text-[10px] font-bold uppercase text-zinc-400 block mb-2">Shop/Business
                            Name</label>
                        <input type="text"
                            class="w-full bg-zinc-50 border-none rounded-xl px-4 py-3 focus:ring-2 focus:ring-yellow-500">
                    </div>
                    <div>
                        <label class="text-[10px] font-bold uppercase text-zinc-400 block mb-2">Owner Name</label>
                        <input type="text"
                            class="w-full bg-zinc-50 border-none rounded-xl px-4 py-3 focus:ring-2 focus:ring-yellow-500">
                    </div>
                    <div>
                        <label class="text-[10px] font-bold uppercase text-zinc-400 block mb-2">Mobile Number</label>
                        <input type="tel"
                            class="w-full bg-zinc-50 border-none rounded-xl px-4 py-3 focus:ring-2 focus:ring-yellow-500">
                    </div>
                    <div>
                        <label class="text-[10px] font-bold uppercase text-zinc-400 block mb-2">City & State</label>
                        <input type="text"
                            class="w-full bg-zinc-50 border-none rounded-xl px-4 py-3 focus:ring-2 focus:ring-yellow-500">
                    </div>
                    <div>
                        <label class="text-[10px] font-bold uppercase text-zinc-400 block mb-2">GST Number
                            (Optional)</label>
                        <input type="text"
                            class="w-full bg-zinc-50 border-none rounded-xl px-4 py-3 focus:ring-2 focus:ring-yellow-500">
                    </div>
                    <div class="sm:col-span-2">
                        <button type="submit"
                            class="w-full bg-yellow-500 text-black py-4 rounded-xl font-bold hover:bg-zinc-900 hover:text-white transition-all uppercase tracking-widest">Apply
                            Now</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<section class="py-24 bg-zinc-50">
    <div class="container mx-auto px-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-16">
            <div>
                <h4 class="text-3xl font-sans font-bold mb-8 underline decoration-yellow-500">Wholesale FAQ</h4>
                <div class="space-y-4">
                    <details class="group bg-white rounded-2xl p-4 shadow-sm border border-zinc-100">
                        <summary class="font-bold cursor-pointer flex justify-between">Do you provide GST Bill? <i
                                class="fas fa-chevron-down group-open:rotate-180"></i></summary>
                        <p class="text-sm text-zinc-500 mt-2">Yes, we provide proper GST invoices for all wholesale
                            purchases for input credit.</p>
                    </details>
                    <details class="group bg-white rounded-2xl p-4 shadow-sm border border-zinc-100">
                        <summary class="font-bold cursor-pointer flex justify-between">Is there a minimum order? <i
                                class="fas fa-chevron-down group-open:rotate-180"></i></summary>
                        <p class="text-sm text-zinc-500 mt-2">For wholesale rates, a minimum quantity is expected, but
                            we support small shop owners with starter packs too.</p>
                    </details>
                    <details class="group bg-white rounded-2xl p-4 shadow-sm border border-zinc-100">
                        <summary class="font-bold cursor-pointer flex justify-between">Do you have HPL products? <i
                                class="fas fa-chevron-down group-open:rotate-180"></i></summary>
                        <p class="text-sm text-zinc-500 mt-2">Yes, we are Authorized Distributors for HPL and stock
                            their full range of switches, MCBs and lights.</p>
                    </details>
                </div>
            </div>
            <div class="flex flex-col justify-center">
                <div class="p-10 bg-white rounded-[2rem] shadow-xl border border-zinc-100 relative">
                    <i class="fas fa-quote-left text-4xl text-yellow-500/20 absolute top-6 left-6"></i>
                    <p class="text-lg italic text-zinc-600 mb-6 leading-relaxed">"Best rates in the market for HPL
                        products. Joshi Electrical provided excellent service for our ongoing construction project."</p>
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full bg-zinc-200 flex items-center justify-center"><i
                                class="fas fa-user"></i></div>
                        <div>
                            <h6 class="font-bold">Rahul Singh</h6>
                            <p class="text-[10px] text-zinc-400 uppercase">Civil Contractor</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>