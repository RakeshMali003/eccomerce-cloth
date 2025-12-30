<?php include '../../includes/header.php'; ?>

<body class="bg-gray-50">

    <div class="container mx-auto px-4 py-10 lg:px-10">
        <div class="flex flex-col lg:flex-row gap-10">
            
            <aside class="hidden lg:block w-64 shrink-0">
                <div class="sticky top-28 space-y-8">
                    <div>
                        <h3 class="text-xs font-black uppercase tracking-widest text-zinc-400 mb-6">Collections</h3>
                        <ul class="space-y-4 text-sm font-semibold">
                            <li><a href="#" class="text-orange-600 border-l-2 border-orange-600 pl-4">Men's Apparel</a></li>
                            <li><a href="#" class="text-zinc-500 hover:text-orange-600 pl-4 transition-all">Women's Ethnic</a></li>
                            <li><a href="#" class="text-zinc-500 hover:text-orange-600 pl-4 transition-all">Kids Boutique</a></li>
                            <li><a href="#" class="text-zinc-500 hover:text-orange-600 pl-4 transition-all">Heritage Sarees</a></li>
                        </ul>
                    </div>
                    
                    <div class="bg-zinc-900 text-white p-6 rounded-3xl">
                        <h4 class="text-sm font-bold mb-2">Wholesale?</h4>
                        <p class="text-[10px] text-zinc-400 mb-4 uppercase tracking-widest">Get Factory Rates</p>
                        <a href="#" class="block text-center bg-orange-600 py-2 rounded-xl text-xs font-bold">Dealer Login</a>
                    </div>
                </div>
            </aside>

            <div class="flex-1">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                    
                    <div class="space-y-4">
                        <div class="zoom-container relative aspect-[4/5] rounded-[2rem] overflow-hidden bg-white border border-zinc-100 shadow-sm">
                            <img id="mainImage" src="https://images.unsplash.com/photo-1596755094514-f87e34085b2c?auto=format&fit=crop&w=800" class="w-full h-full object-cover">
                            <span class="absolute top-6 left-6 bg-white px-3 py-1 rounded-full text-[10px] font-black uppercase shadow-sm">New Arrival</span>
                        </div>
                        
                        <div class="grid grid-cols-4 gap-4">
                            <button onclick="changeImg(this.src)" class="aspect-square rounded-2xl overflow-hidden border-2 border-orange-600">
                                <img src="https://images.unsplash.com/photo-1596755094514-f87e34085b2c?auto=format&w=200" class="w-full h-full object-cover">
                            </button>
                            <button onclick="changeImg(this.src)" class="aspect-square rounded-2xl overflow-hidden border border-zinc-200">
                                <img src="https://images.unsplash.com/photo-1621072156002-e2fcced0b170?auto=format&w=200" class="w-full h-full object-cover">
                            </button>
                            <button onclick="changeImg(this.src)" class="aspect-square rounded-2xl overflow-hidden border border-zinc-200">
                                <img src="https://images.unsplash.com/photo-1598033129183-c4f50c7176c8?auto=format&w=200" class="w-full h-full object-cover">
                            </button>
                            <div class="aspect-square rounded-2xl overflow-hidden bg-zinc-100 flex items-center justify-center text-xs font-bold text-zinc-400 uppercase text-center p-2">
                                Fabric Zoom
                            </div>
                        </div>
                    </div>

                    <div class="space-y-8">
                        <div>
                            <div class="flex items-center gap-2 mb-2">
                                <div class="flex text-orange-500 text-xs">
                                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i>
                                </div>
                                <span class="text-[10px] font-bold text-zinc-400 uppercase">(128 Reviews)</span>
                            </div>
                            <h1 class="text-4xl font-serif italic text-zinc-900 mb-2">Men Cotton Casual Shirt – Slim Fit</h1>
                            <p class="text-zinc-400 text-sm tracking-widest uppercase font-bold">Style Code: GK-SH-2025</p>
                        </div>

                        <div class="flex items-end gap-4">
                            <span class="text-3xl font-black text-zinc-900">₹1,299</span>
                            <span class="text-lg text-zinc-400 line-through">₹2,499</span>
                            <span class="text-orange-600 font-bold text-sm bg-orange-50 px-3 py-1 rounded-full">48% OFF</span>
                        </div>
                        <p class="text-[10px] text-zinc-400 font-bold uppercase tracking-widest -mt-4 italic">Incl. all taxes (GST Verified)</p>

                        <ul class="space-y-2 text-zinc-600 text-sm">
                            <li class="flex items-center gap-2"><i class="fas fa-check text-orange-600"></i> 100% Premium Breathable Cotton</li>
                            <li class="flex items-center gap-2"><i class="fas fa-check text-orange-600"></i> Slim Fit Silhouette for Modern Look</li>
                            <li class="flex items-center gap-2"><i class="fas fa-check text-orange-600"></i> Color-Fast & Shrinkage Tested</li>
                        </ul>

                        <div class="border-t border-zinc-100 pt-8 space-y-6">
                            <div>
                                <div class="flex justify-between mb-4">
                                    <span class="text-xs font-black uppercase">Select Size</span>
                                    <a href="#" class="text-xs text-orange-600 font-bold underline">Size Guide</a>
                                </div>
                                <div class="flex gap-3">
                                    <button class="w-12 h-12 rounded-xl border-2 border-zinc-900 flex items-center justify-center font-bold text-sm">S</button>
                                    <button class="w-12 h-12 rounded-xl border border-zinc-200 flex items-center justify-center font-bold text-sm hover:border-zinc-900">M</button>
                                    <button class="w-12 h-12 rounded-xl border border-zinc-200 flex items-center justify-center font-bold text-sm hover:border-zinc-900">L</button>
                                    <button class="w-12 h-12 rounded-xl border border-zinc-200 flex items-center justify-center font-bold text-sm hover:border-zinc-900">XL</button>
                                </div>
                            </div>

                            <div>
                                <span class="text-xs font-black uppercase block mb-4">Color: Sky Blue</span>
                                <div class="flex gap-3">
                                    <div class="w-8 h-8 rounded-full bg-sky-200 ring-2 ring-offset-2 ring-zinc-900 cursor-pointer"></div>
                                    <div class="w-8 h-8 rounded-full bg-zinc-100 border cursor-pointer hover:ring-2 hover:ring-zinc-400"></div>
                                    <div class="w-8 h-8 rounded-full bg-pink-100 border cursor-pointer hover:ring-2 hover:ring-zinc-400"></div>
                                </div>
                            </div>

                            <div class="flex gap-4 mobile-sticky-cta">
                                <div class="flex items-center border border-zinc-200 rounded-xl px-4 bg-white">
                                    <button class="p-2 text-zinc-400 hover:text-black font-bold">-</button>
                                    <input type="text" value="1" class="w-10 text-center font-bold border-none outline-none">
                                    <button class="p-2 text-zinc-400 hover:text-black font-bold">+</button>
                                </div>
                                <button class="flex-1 bg-zinc-900 text-white py-4 rounded-xl font-black text-xs tracking-widest uppercase hover:bg-orange-600 transition-all">
                                    Add to Cart
                                </button>
                                <button class="flex-1 bg-orange-600 text-white py-4 rounded-xl font-black text-xs tracking-widest uppercase hover:bg-black transition-all">
                                    Buy Now
                                </button>
                            </div>

                            <div class="p-6 bg-zinc-50 rounded-2xl border border-zinc-100 space-y-4">
                                <div class="flex gap-3">
                                    <input type="text" placeholder="Enter Pincode" class="flex-1 bg-white border border-zinc-200 px-4 py-2 rounded-lg text-sm">
                                    <button class="text-orange-600 font-bold text-xs uppercase">Check</button>
                                </div>
                                <div class="flex items-center gap-3 text-xs font-bold text-zinc-500">
                                    <i class="fas fa-truck text-orange-600"></i> Estimated Delivery by Dec 30
                                </div>
                            </div>

                            <div class="grid grid-cols-3 gap-2 py-4">
                                <div class="text-center p-2 border-r border-zinc-100">
                                    <i class="fas fa-shield-alt text-lg text-zinc-300 mb-2"></i>
                                    <p class="text-[8px] font-bold uppercase text-zinc-400">Quality Checked</p>
                                </div>
                                <div class="text-center p-2 border-r border-zinc-100">
                                    <i class="fas fa-undo text-lg text-zinc-300 mb-2"></i>
                                    <p class="text-[8px] font-bold uppercase text-zinc-400">7 Day Exchange</p>
                                </div>
                                <div class="text-center p-2">
                                    <i class="fas fa-lock text-lg text-zinc-300 mb-2"></i>
                                    <p class="text-[8px] font-bold uppercase text-zinc-400">Secure Payment</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-20 border-t border-zinc-100 pt-16">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-16">
                        <div class="lg:col-span-2 space-y-12">
                            <div>
                                <h3 class="text-2xl font-serif italic mb-6">Product Story</h3>
                                <p class="text-zinc-500 leading-relaxed">
                                    Crafted for the modern professional, our Premium Cotton Casual Shirt blends timeless ethnic weaving techniques with a sharp slim-fit silhouette. The breathable fabric ensures all-day comfort, whether you're at a business meeting or a festive gathering.
                                </p>
                            </div>

                            <div>
                                <h4 class="text-xs font-black uppercase tracking-widest mb-6">Specifications</h4>
                                <table class="w-full text-sm text-left border-collapse">
                                    <tr class="border-b border-zinc-50">
                                        <th class="py-3 font-bold text-zinc-400 uppercase text-[10px]">Fabric</th>
                                        <td class="py-3 text-zinc-600">100% Cotton</td>
                                    </tr>
                                    <tr class="border-b border-zinc-50">
                                        <th class="py-3 font-bold text-zinc-400 uppercase text-[10px]">Fit</th>
                                        <td class="py-3 text-zinc-600">Slim Fit</td>
                                    </tr>
                                    <tr class="border-b border-zinc-50">
                                        <th class="py-3 font-bold text-zinc-400 uppercase text-[10px]">Pattern</th>
                                        <td class="py-3 text-zinc-600">Solid / Plain</td>
                                    </tr>
                                    <tr>
                                        <th class="py-3 font-bold text-zinc-400 uppercase text-[10px]">Wash Care</th>
                                        <td class="py-3 text-zinc-600">Cold Machine Wash</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="bg-orange-600 p-10 rounded-[3rem] text-white">
                            <h4 class="text-xl font-serif italic mb-4">Wholesale Partnership</h4>
                            <p class="text-sm opacity-80 mb-8 leading-relaxed">Grow your business with Gurukrupa. Get tiered pricing and early access to seasonal stock.</p>
                            <ul class="text-xs font-bold space-y-4 mb-10">
                                <li><i class="fas fa-check-circle mr-2"></i> MOQ: 20 Pieces</li>
                                <li><i class="fas fa-check-circle mr-2"></i> Custom Labeling Available</li>
                                <li><i class="fas fa-check-circle mr-2"></i> Paid Sample Kits</li>
                            </ul>
                            <button class="w-full bg-white text-orange-600 py-4 rounded-xl font-bold uppercase tracking-widest text-[10px]">Request B2B Quote</button>
                        </div>
                    </div>
                </div>

                <div class="mt-32">
                    <div class="flex justify-between items-end mb-12">
                        <h3 class="text-3xl font-serif italic">You May Also Like</h3>
                        <a href="#" class="text-xs font-bold border-b border-zinc-900 pb-1">View All</a>
                    </div>
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
                        <div class="group">
                            <div class="aspect-[3/4] rounded-2xl overflow-hidden bg-zinc-100 mb-4">
                                <img src="https://images.unsplash.com/photo-1598033129183-c4f50c7176c8?auto=format&w=600" class="w-full h-full object-cover group-hover:scale-110 transition-all duration-500">
                            </div>
                            <h4 class="font-bold text-sm">Men's Formal White Shirt</h4>
                            <p class="text-orange-600 font-bold text-xs">₹1,499</p>
                        </div>
                        <div class="group">
                            <div class="aspect-[3/4] rounded-2xl overflow-hidden bg-zinc-100 mb-4">
                                <img src="https://images.unsplash.com/photo-1621072156002-e2fcced0b170?auto=format&w=600" class="w-full h-full object-cover group-hover:scale-110 transition-all duration-500">
                            </div>
                            <h4 class="font-bold text-sm">Oxford Blue Premium Kurta</h4>
                            <p class="text-orange-600 font-bold text-xs">₹1,899</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

 
    <script>
        function changeImg(src) {
            document.getElementById('mainImage').src = src;
        }
    </script>
   <?php include '../../includes/footer.php'; ?>
