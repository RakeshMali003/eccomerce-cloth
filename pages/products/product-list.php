<?php include '../../includes/header.php'; ?>

<body class="bg-[#FBFBFB]">

    <div class="container mx-auto px-4 lg:px-6 py-12">
        <div class="flex flex-col lg:flex-row gap-8">
            
            <aside class="w-full lg:w-72 shrink-0">
                <div class="sticky top-28 space-y-8 h-[calc(100vh-140px)] overflow-y-auto no-scrollbar pb-10">
                    <div class="p-6 bg-zinc-900 rounded-[2rem] text-white shadow-xl shadow-zinc-200">
                        <h4 class="text-sm font-bold mb-2">Wholesale Mode</h4>
                        <p class="text-[10px] text-zinc-400 mb-4 uppercase tracking-widest">Showing Factory Rates</p>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" checked>
                            <div class="w-11 h-6 bg-zinc-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-orange-600"></div>
                        </label>
                    </div>

                    <div>
                        <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-5">Main Categories</h3>
                        <ul class="space-y-3 text-sm font-bold text-zinc-600">
                            <li><a href="#" class="text-orange-600 flex justify-between items-center">Men's Apparel <span>(120)</span></a></li>
                            <li><a href="#" class="hover:text-orange-600 flex justify-between items-center transition">Women's Ethnic <span>(85)</span></a></li>
                            <li><a href="#" class="hover:text-orange-600 flex justify-between items-center transition">Kids Collection <span>(45)</span></a></li>
                            <li><a href="#" class="hover:text-orange-600 flex justify-between items-center transition">B2B Specials <span>(12)</span></a></li>
                            <li><a href="#" class="hover:text-orange-600 flex justify-between items-center transition">Seasonal <span>(30)</span></a></li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-5">Filter by Type</h3>
                        <div class="space-y-3">
                            <label class="flex items-center text-xs font-bold text-zinc-600 cursor-pointer">
                                <input type="checkbox" class="rounded border-zinc-300 text-orange-600 mr-3"> Casual Shirts
                            </label>
                            <label class="flex items-center text-xs font-bold text-zinc-600 cursor-pointer">
                                <input type="checkbox" class="rounded border-zinc-300 text-orange-600 mr-3"> Formal Trousers
                            </label>
                            <label class="flex items-center text-xs font-bold text-zinc-600 cursor-pointer">
                                <input type="checkbox" class="rounded border-zinc-300 text-orange-600 mr-3"> Designer Kurtas
                            </label>
                            <label class="flex items-center text-xs font-bold text-zinc-600 cursor-pointer">
                                <input type="checkbox" class="rounded border-zinc-300 text-orange-600 mr-3"> Winter Jackets
                            </label>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-5">Fabric Material</h3>
                        <div class="space-y-3">
                            <label class="flex items-center text-xs font-bold text-zinc-600 cursor-pointer">
                                <input type="checkbox" class="rounded border-zinc-300 text-orange-600 mr-3"> 100% Pure Cotton
                            </label>
                            <label class="flex items-center text-xs font-bold text-zinc-600 cursor-pointer">
                                <input type="checkbox" class="rounded border-zinc-300 text-orange-600 mr-3"> Premium Silk
                            </label>
                            <label class="flex items-center text-xs font-bold text-zinc-600 cursor-pointer">
                                <input type="checkbox" class="rounded border-zinc-300 text-orange-600 mr-3"> Linen Blend
                            </label>
                        </div>
                    </div>
                </div>
            </aside>

            <main class="flex-1">
                <div class="flex justify-between items-center mb-8 pb-6 border-b border-zinc-100">
                    <h2 class="text-2xl font-serif italic text-zinc-900">New Arrivals <span class="text-xs font-sans font-bold text-zinc-400 not-italic ml-2 uppercase tracking-widest">(245 Products)</span></h2>
                    <div class="flex items-center gap-4">
                        <span class="text-[10px] font-bold uppercase text-zinc-400">Sort By</span>
                        <select class="bg-transparent text-xs font-bold uppercase tracking-widest outline-none border-none cursor-pointer text-zinc-900">
                            <option>Newest First</option>
                            <option>Price: Low to High</option>
                            <option>Best Seller</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-x-4 gap-y-12">
                    
                    <div class="product-card group cursor-pointer">
                        <div class="relative aspect-[3/4] rounded-[2rem] overflow-hidden bg-zinc-100 mb-5 shadow-sm">
                            <img src="https://images.unsplash.com/photo-1596755094514-f87e34085b2c?auto=format&fit=crop&w=600" class="w-full h-full object-cover">
                            <div class="absolute top-4 left-4 flex flex-col gap-2">
                                <span class="badge-zoom bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full text-[8px] font-black uppercase tracking-tighter text-zinc-900 shadow-sm">Pure Cotton</span>
                            </div>
                            <div class="action-bar absolute bottom-3 left-3 right-3 flex gap-2 translate-y-4 opacity-0 transition-all duration-300">
                                <button class="flex-1 bg-white text-black py-2 rounded-xl text-[9px] font-black uppercase hover:bg-orange-600 hover:text-white transition-all">Quick View</button>
    <button 
  class="wishlist-btn w-10 bg-white text-black py-2 rounded-xl 
         hover:bg-zinc-900 hover:text-white transition-all"
  data-product-id="123">
  
  <i class="far fa-heart"></i>
</button>

                            </div>
                        </div>
                        <div class="space-y-1.5 px-1">
                            <div class="flex justify-between items-start">
                                <h3 class="font-bold text-zinc-900 text-[13px] leading-tight truncate pr-2">Azure Cotton Slim Fit</h3>
                                <span class="text-xs font-extrabold text-orange-600">₹1,299</span>
                            </div>
                            <div class="bg-zinc-50 p-2.5 rounded-xl border border-zinc-100">
                                <div class="flex justify-between items-center text-[9px] font-bold uppercase text-zinc-500">
                                    <span>Bulk Rate</span>
                                    <span class="text-zinc-900 font-extrabold">₹850</span>
                                </div>
                                <div class="w-full bg-zinc-200 h-1 rounded-full mt-1.5 overflow-hidden">
                                    <div class="bg-orange-600 h-full w-2/3"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="product-card group cursor-pointer">
                        <div class="relative aspect-[3/4] rounded-[2rem] overflow-hidden bg-zinc-100 mb-5 shadow-sm">
                            <img src="https://images.unsplash.com/photo-1583391733956-6c78276477e2?auto=format&fit=crop&w=600" class="w-full h-full object-cover">
                            <div class="action-bar absolute bottom-3 left-3 right-3 flex gap-2 translate-y-4 opacity-0 transition-all duration-300">
                                <button class="flex-1 bg-white text-black py-2 rounded-xl text-[9px] font-black uppercase hover:bg-orange-600 hover:text-white transition-all">Quick View</button>
                            </div>
                        </div>
                        <div class="space-y-1.5 px-1">
                            <div class="flex justify-between items-start">
                                <h3 class="font-bold text-zinc-900 text-[13px] truncate pr-2">Indigo Heritage Kurta</h3>
                                <span class="text-xs font-extrabold text-orange-600">₹1,899</span>
                            </div>
                            <div class="bg-zinc-50 p-2.5 rounded-xl border border-zinc-100 text-[9px] font-bold text-zinc-500">
                                <span class="uppercase">Wholesale: ₹1,150</span>
                                <p class="text-zinc-400 font-medium tracking-tighter">Min: 20 pcs</p>
                            </div>
                        </div>
                    </div>

                    <div class="product-card group cursor-pointer">
                        <div class="relative aspect-[3/4] rounded-[2rem] overflow-hidden bg-zinc-100 mb-5 shadow-sm">
                            <img src="https://images.unsplash.com/photo-1617137984095-74e4e5e3613f?auto=format&fit=crop&w=600" class="w-full h-full object-cover">
                            <div class="action-bar absolute bottom-3 left-3 right-3 flex gap-2 translate-y-4 opacity-0 transition-all duration-300">
                                <button class="flex-1 bg-white text-black py-2 rounded-xl text-[9px] font-black uppercase hover:bg-orange-600 hover:text-white transition-all">Quick View</button>
                            </div>
                        </div>
                        <div class="space-y-1.5 px-1">
                            <div class="flex justify-between items-start">
                                <h3 class="font-bold text-zinc-900 text-[13px] truncate pr-2">Midnight Formal Pants</h3>
                                <span class="text-xs font-extrabold text-orange-600">₹2,199</span>
                            </div>
                            <div class="bg-zinc-50 p-2.5 rounded-xl border border-zinc-100 text-[9px] font-bold text-zinc-500">
                                <span class="uppercase">Wholesale: ₹1,400</span>
                                <p class="text-zinc-400 font-medium tracking-tighter">Min: 12 sets</p>
                            </div>
                        </div>
                    </div>

                    <div class="product-card group cursor-pointer">
                        <div class="relative aspect-[3/4] rounded-[2rem] overflow-hidden bg-zinc-100 mb-5 shadow-sm">
                            <img src="https://images.unsplash.com/photo-1618333234977-67092dc09372?auto=format&fit=crop&w=600" class="w-full h-full object-cover">
                            <div class="absolute top-4 left-4 flex flex-col gap-2">
                                <span class="badge-zoom bg-orange-600 text-white px-3 py-1 rounded-full text-[8px] font-black uppercase tracking-tighter shadow-sm">Hot Sale</span>
                            </div>
                            <div class="action-bar absolute bottom-3 left-3 right-3 flex gap-2 translate-y-4 opacity-0 transition-all duration-300">
                                <button class="flex-1 bg-white text-black py-2 rounded-xl text-[9px] font-black uppercase hover:bg-orange-600 hover:text-white transition-all">Quick View</button>
                            </div>
                        </div>
                        <div class="space-y-1.5 px-1">
                            <div class="flex justify-between items-start">
                                <h3 class="font-bold text-zinc-900 text-[13px] truncate pr-2">Royal Saree Collection</h3>
                                <span class="text-xs font-extrabold text-orange-600">₹3,499</span>
                            </div>
                            <div class="bg-zinc-50 p-2.5 rounded-xl border border-zinc-100 text-[9px] font-bold text-zinc-500">
                                <span class="uppercase">Wholesale: ₹2,200</span>
                                <p class="text-zinc-400 font-medium tracking-tighter">Min: 10 pcs</p>
                            </div>
                        </div>
                    </div>

                    </div>

                <div class="mt-24 flex flex-col items-center">
                    <p class="text-xs font-bold text-zinc-400 uppercase tracking-widest mb-6">Viewing 12 of 245 Designs</p>
                    <div class="w-64 h-1 bg-zinc-200 rounded-full mb-8 overflow-hidden">
                        <div class="bg-zinc-900 h-full w-12 transition-all"></div>
                    </div>
                    <button class="px-12 py-4 border-2 border-zinc-900 rounded-full text-xs font-black uppercase tracking-widest hover:bg-zinc-900 hover:text-white transition-all transform active:scale-95 shadow-lg shadow-zinc-100">
                        Load More Designs
                    </button>
                </div>
            </main>
        </div>
    </div>
   <?php include '../../includes/footer.php'; ?>
