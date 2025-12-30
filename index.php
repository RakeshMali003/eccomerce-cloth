<?php
session_start();
require_once 'config/database.php';
require_once 'config/constants.php';
require_once 'includes/functions.php';

// Logic to determine which page to load
// Example: index.php?page=product-list
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

// Basic Routing Logic
include 'includes/header.php';
?>

<main class="bg-[#fafafa] pb-20">
    
    <section class="bg-orange-600 py-3 relative overflow-hidden">
        <div class="container mx-auto px-4 flex flex-col md:flex-row justify-center items-center gap-4 text-white">
            <span class="flex items-center gap-2 font-bold tracking-widest text-sm">
                <i class="fas fa-bolt animate-pulse"></i> FLASH SALE ENDS IN:
            </span>
            <div class="flex gap-4 font-mono text-xl font-black">
                <div class="flex flex-col items-center leading-none">02<span class="text-[8px] uppercase mt-1">Hrs</span></div>
                <span>:</span>
                <div class="flex flex-col items-center leading-none">45<span class="text-[8px] uppercase mt-1">Min</span></div>
                <span>:</span>
                <div class="flex flex-col items-center leading-none">12<span class="text-[8px] uppercase mt-1">Sec</span></div>
            </div>
            <a href="#" class="bg-white text-orange-600 px-6 py-1 rounded-full text-xs font-bold hover:bg-black hover:text-white transition-all uppercase">Shop Now</a>
        </div>
    </section>

    <div class="container mx-auto px-4 mt-8">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
            
            <div class="md:col-span-8 relative rounded-[2.5rem] overflow-hidden h-[500px] md:h-[600px] group shadow-xl">
                <img src="https://images.unsplash.com/photo-1610030469915-9a88edc1c59a?auto=format&fit=crop&q=80&w=1200" 
                     class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110" alt="Saree Sale">
                
                <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-transparent p-8 md:p-16 flex flex-col justify-end">
                    <div class="mb-4 bg-orange-600 text-white w-max px-4 py-1 rounded-md text-sm font-bold animate-bounce">
                        SAVE UP TO 60% OFF
                    </div>
                    <h1 class="text-white text-4xl md:text-7xl font-serif mb-4 leading-tight">
                        Wedding <br>Season <span class="italic font-light">Special</span>
                    </h1>
                    <p class="text-gray-300 text-lg mb-8 max-w-md">Premium Banarasi & Kanjivaram Silk Sarees at wholesale prices for a limited time.</p>
                    <div class="flex gap-4">
                        <button class="bg-white text-black px-10 py-4 rounded-full font-bold hover:bg-orange-600 hover:text-white transition-all transform hover:-translate-y-1">
                            CLAIM OFFER
                        </button>
                    </div>
                </div>
            </div>

            <div class="md:col-span-4 flex flex-col gap-6">
                
                <div class="h-1/2 relative rounded-[2.5rem] overflow-hidden group bg-zinc-900">
                    <img src="https://images.unsplash.com/photo-1594938298603-c8148c4dae35?auto=format&fit=crop&q=80&w=600" 
                         class="w-full h-full object-cover opacity-60 group-hover:opacity-40 transition-all duration-500" alt="Men's Wear">
                    <div class="absolute inset-0 p-8 flex flex-col justify-between">
                        <div class="text-right">
                            <span class="text-white font-black text-3xl italic tracking-tighter">BOGO</span>
                            <p class="text-orange-500 text-xs font-bold uppercase tracking-widest">Buy 1 Get 1 Free</p>
                        </div>
                        <div>
                            <h3 class="text-white text-2xl font-bold">Premium <br>Menswear</h3>
                            <a href="#" class="inline-block mt-3 text-orange-500 font-bold border-b border-orange-500 pb-1">Shop Collection</a>
                        </div>
                    </div>
                </div>

                <div class="h-1/2 relative rounded-[2.5rem] overflow-hidden group border-2 border-dashed border-zinc-200 bg-white">
                    <div class="absolute inset-0 p-8 flex flex-col justify-center items-center text-center">
                        <div class="w-16 h-16 bg-orange-100 text-orange-600 rounded-full flex items-center justify-center mb-4 text-2xl">
                            <i class="fas fa-tags"></i>
                        </div>
                        <h3 class="text-2xl font-black text-zinc-900 mb-2">KURTI UNDER <br>₹499/-</h3>
                        <p class="text-zinc-500 text-sm mb-4">Daily wear & Office collections</p>
                        <button class="bg-zinc-900 text-white px-6 py-2 rounded-full text-sm font-bold hover:bg-orange-600 transition-colors">
                            VIEW ALL
                        </button>
                    </div>
                </div>

            </div>
        </div>

        <div class="mt-12 grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white p-6 rounded-2xl flex items-center gap-4 shadow-sm border border-gray-100">
                <div class="text-orange-600 text-2xl"><i class="fas fa-award"></i></div>
                <div>
                    <h5 class="text-sm font-bold">Silk Certified</h5>
                    <p class="text-[10px] text-gray-400 uppercase tracking-tighter">100% Genuine Yarn</p>
                </div>
            </div>
            <div class="bg-white p-6 rounded-2xl flex items-center gap-4 shadow-sm border border-gray-100">
                <div class="text-orange-600 text-2xl"><i class="fas fa-handshake"></i></div>
                <div>
                    <h5 class="text-sm font-bold">Wholesale Price</h5>
                    <p class="text-[10px] text-gray-400 uppercase tracking-tighter">Direct Factory Rates</p>
                </div>
            </div>
            <div class="bg-white p-6 rounded-2xl flex items-center gap-4 shadow-sm border border-gray-100">
                <div class="text-orange-600 text-2xl"><i class="fas fa-globe"></i></div>
                <div>
                    <h5 class="text-sm font-bold">Global Export</h5>
                    <p class="text-[10px] text-gray-400 uppercase tracking-tighter">Shipping to 20+ Countries</p>
                </div>
            </div>
            <div class="bg-white p-6 rounded-2xl flex items-center gap-4 shadow-sm border border-gray-100">
                <div class="text-orange-600 text-2xl"><i class="fas fa-undo"></i></div>
                <div>
                    <h5 class="text-sm font-bold">Easy Returns</h5>
                    <p class="text-[10px] text-gray-400 uppercase tracking-tighter">7-Day Return Policy</p>
                </div>
            </div>
        </div>
    </div>
</main>













<section class="bg-[#FCFBFA] py-20 overflow-hidden">
    <div class="container mx-auto px-4 md:px-10">
        
        <div class="relative rounded-[3rem] overflow-hidden bg-zinc-900 mb-32 h-[500px] md:h-[600px] group">
            <img src="https://images.unsplash.com/photo-1583391733956-6c78276477e2?auto=format&fit=crop&q=80&w=1800" 
                 class="w-full h-full object-cover opacity-70 transition-transform duration-1000 group-hover:scale-110" alt="Ethnic Collection">
            
            <div class="absolute inset-0 flex flex-col items-center justify-center text-center p-6">
                <span class="text-orange-500 font-bold tracking-[0.5em] uppercase text-xs mb-4 animate-fade-in">New Season</span>
                <h2 class="text-white text-5xl md:text-8xl font-serif italic mb-6">Elevated <br>Ethnic Wear</h2>
                <p class="text-gray-300 max-w-xl mx-auto text-lg md:text-xl font-light mb-10 leading-relaxed">
                    Discover our new collection of ethnic wear, crafted with passion and designed to make you shine.
                </p>
                <a href="#" class="group relative px-10 py-4 bg-white text-black font-bold rounded-full overflow-hidden transition-all hover:pr-14">
                    <span class="relative z-10 uppercase tracking-widest text-sm">Shop The Latest</span>
                    <i class="fas fa-arrow-right absolute right-6 top-1/2 -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-all"></i>
                </a>
            </div>
        </div>

        <div class="mb-24">
            <div class="text-center mb-12">
                <h3 class="text-3xl md:text-5xl font-serif text-zinc-900 mb-4">Shop By Category</h3>
                <p class="text-zinc-500">Explore our curated collections for everyone and every occasion.</p>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-8">
                <a href="#" class="group block text-center">
                    <div class="relative aspect-[3/4] rounded-3xl overflow-hidden mb-4 bg-zinc-100">
                        <img src="https://images.unsplash.com/photo-1617137984095-74e4e5e3613f?auto=format&fit=crop&q=80&w=600" class="w-full h-full object-cover group-hover:scale-105 transition-all duration-500" alt="Men's Wear">
                        <div class="absolute inset-0 bg-black/10 group-hover:bg-black/0 transition-all"></div>
                    </div>
                    <span class="font-bold uppercase tracking-widest text-zinc-800">Men's Wear</span>
                </a>
                <a href="#" class="group block text-center">
                    <div class="relative aspect-[3/4] rounded-3xl overflow-hidden mb-4 bg-zinc-100">
                        <img src="https://images.unsplash.com/photo-1610030469668-8386343513b2?auto=format&fit=crop&q=80&w=600" class="w-full h-full object-cover group-hover:scale-105 transition-all duration-500" alt="Women's Wear">
                    </div>
                    <span class="font-bold uppercase tracking-widest text-zinc-800">Women's Wear</span>
                </a>
                <a href="#" class="group block text-center">
                    <div class="relative aspect-[3/4] rounded-3xl overflow-hidden mb-4 bg-zinc-100">
                        <img src="https://images.unsplash.com/photo-1519702221971-4713833d739e?auto=format&fit=crop&q=80&w=600" class="w-full h-full object-cover group-hover:scale-105 transition-all duration-500" alt="Kids Wear">
                    </div>
                    <span class="font-bold uppercase tracking-widest text-zinc-800">Kids Wear</span>
                </a>
                <a href="#" class="group block text-center">
                    <div class="relative aspect-[3/4] rounded-3xl overflow-hidden mb-4 bg-zinc-100">
                        <img src="https://images.unsplash.com/photo-1606760227091-3dd870d97f1d?auto=format&fit=crop&q=80&w=600" class="w-full h-full object-cover group-hover:scale-105 transition-all duration-500" alt="Accessories">
                    </div>
                    <span class="font-bold uppercase tracking-widest text-zinc-800">Accessories</span>
                </a>
            </div>
        </div>

        <div class="mb-32">
            <div class="flex flex-col md:flex-row justify-between items-end mb-12 gap-4">
                <div>
                    <h3 class="text-4xl font-serif text-zinc-900 italic">New Arrivals</h3>
                    <p class="text-zinc-500">Fresh styles just for you</p>
                </div>
                <a href="#" class="text-zinc-900 font-bold border-b-2 border-orange-600 pb-1 hover:text-orange-600 transition-all">View All Products</a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10">
                <div class="group">
                    <div class="relative aspect-[4/5] overflow-hidden rounded-2xl bg-zinc-100 mb-6">
                        <img src="https://images.unsplash.com/photo-1521572267360-ee0c2909d518?auto=format&fit=crop&q=80&w=600" class="w-full h-full object-cover">
                        <button class="absolute bottom-6 left-1/2 -translate-x-1/2 w-[80%] bg-white py-3 rounded-full font-bold shadow-xl translate-y-12 opacity-0 group-hover:translate-y-0 group-hover:opacity-100 transition-all duration-300">
                            Add to cart
                        </button>
                    </div>
                    <h4 class="text-zinc-800 font-medium">Men's Casual T-Shirt</h4>
                    <p class="text-zinc-400 font-bold">₹15.99</p>
                </div>
                <div class="group">
                    <div class="relative aspect-[4/5] overflow-hidden rounded-2xl bg-zinc-100 mb-6">
                        <img src="https://images.unsplash.com/photo-1596755094514-f87e34085b2c?auto=format&fit=crop&q=80&w=600" class="w-full h-full object-cover">
                        <button class="absolute bottom-6 left-1/2 -translate-x-1/2 w-[80%] bg-white py-3 rounded-full font-bold shadow-xl translate-y-12 opacity-0 group-hover:translate-y-0 group-hover:opacity-100 transition-all duration-300">
                            Add to cart
                        </button>
                    </div>
                    <h4 class="text-zinc-800 font-medium">Men's Formal Shirt</h4>
                    <p class="text-zinc-400 font-bold">₹39.99</p>
                </div>
                <div class="group">
                    <div class="relative aspect-[4/5] overflow-hidden rounded-2xl bg-zinc-100 mb-6">
                        <img src="https://images.unsplash.com/photo-1542272604-787c3835535d?auto=format&fit=crop&q=80&w=600" class="w-full h-full object-cover">
                        <button class="absolute bottom-6 left-1/2 -translate-x-1/2 w-[80%] bg-white py-3 rounded-full font-bold shadow-xl translate-y-12 opacity-0 group-hover:translate-y-0 group-hover:opacity-100 transition-all duration-300">
                            Add to cart
                        </button>
                    </div>
                    <h4 class="text-zinc-800 font-medium">Men's Classic Jeans</h4>
                    <p class="text-zinc-400 font-bold">₹49.99</p>
                </div>
                <div class="group">
                    <div class="relative aspect-[4/5] overflow-hidden rounded-2xl bg-zinc-100 mb-6">
                        <img src="https://images.unsplash.com/photo-1589410884333-662589574163?auto=format&fit=crop&q=80&w=600" class="w-full h-full object-cover">
                        <button class="absolute bottom-6 left-1/2 -translate-x-1/2 w-[80%] bg-white py-3 rounded-full font-bold shadow-xl translate-y-12 opacity-0 group-hover:translate-y-0 group-hover:opacity-100 transition-all duration-300">
                            Add to cart
                        </button>
                    </div>
                    <h4 class="text-zinc-800 font-medium">Women's Floral Kurti</h4>
                    <p class="text-zinc-400 font-bold">₹29.99</p>
                </div>
            </div>
        </div>

        <div class="relative rounded-[3rem] overflow-hidden bg-orange-600 p-8 md:p-20 text-center text-white">
            <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/pinstriped-suit.png')] opacity-20"></div>
            <div class="relative z-10 flex flex-col items-center">
                <div class="bg-black/20 backdrop-blur-md px-6 py-2 rounded-full text-xs font-black tracking-widest mb-6 uppercase">
                    Seasonal Offer
                </div>
                <h2 class="text-6xl md:text-9xl font-black mb-4 tracking-tighter">Fashion Sale</h2>
                <h3 class="text-3xl md:text-5xl font-serif italic mb-8 underline decoration-white/30">Up to 60% OFF</h3>
                <p class="max-w-xl text-lg opacity-80 mb-10">Limited time offer on trending styles. Don't miss out on the biggest sale of the season.</p>
                <a href="#" class="bg-white text-orange-600 px-12 py-5 rounded-full font-black text-lg hover:scale-105 transition-transform shadow-2xl uppercase tracking-widest">
                    Shop Sale
                </a>
            </div>
        </div>

    </div>
</section>






<section class="bg-white py-16 overflow-hidden">
    <div class="container mx-auto px-4 mb-10 text-center">
        <h2 class="text-xs font-bold tracking-[0.4em] text-orange-600 uppercase mb-2">Our Production Line</h2>
        <p class="text-3xl md:text-5xl font-serif italic text-zinc-900">Crafting Elegance Daily</p>
    </div>

    <div class="relative flex overflow-x-hidden border-y border-zinc-100 py-6 mb-4 bg-zinc-50">
        <div class="animate-marquee-fast flex whitespace-nowrap gap-12 items-center">
            <span class="text-4xl md:text-6xl font-black text-zinc-200 uppercase">Banarasi Silk</span>
            <i class="fas fa-certificate text-orange-600"></i>
            <span class="text-4xl md:text-6xl font-black text-zinc-200 uppercase">Cotton Kurtas</span>
            <i class="fas fa-certificate text-orange-600"></i>
            <span class="text-4xl md:text-6xl font-black text-zinc-200 uppercase">Designer Lehengas</span>
            <i class="fas fa-certificate text-orange-600"></i>
            <span class="text-4xl md:text-6xl font-black text-zinc-200 uppercase">Wedding Sherwanis</span>
            <i class="fas fa-certificate text-orange-600"></i>
        </div>
        <div class="animate-marquee-fast flex whitespace-nowrap gap-12 items-center ml-12">
            <span class="text-4xl md:text-6xl font-black text-zinc-200 uppercase">Banarasi Silk</span>
            <i class="fas fa-certificate text-orange-600"></i>
            <span class="text-4xl md:text-6xl font-black text-zinc-200 uppercase">Cotton Kurtas</span>
            <i class="fas fa-certificate text-orange-600"></i>
            <span class="text-4xl md:text-6xl font-black text-zinc-200 uppercase">Designer Lehengas</span>
            <i class="fas fa-certificate text-orange-600"></i>
            <span class="text-4xl md:text-6xl font-black text-zinc-200 uppercase">Wedding Sherwanis</span>
            <i class="fas fa-certificate text-orange-600"></i>
        </div>
    </div>

    <div class="relative flex overflow-x-hidden py-4">
        <div class="animate-marquee-slow flex whitespace-nowrap gap-6">
            <div class="w-64 h-80 rounded-2xl overflow-hidden bg-zinc-100 flex-shrink-0">
                <img src="https://images.unsplash.com/photo-1610189012906-40008538302b?auto=format&fit=crop&q=80&w=400" class="w-full h-full object-cover hover:scale-110 transition-transform duration-500" alt="Saree Production">
            </div>
            <div class="w-64 h-80 rounded-2xl overflow-hidden bg-zinc-100 flex-shrink-0">
                <img src="https://images.unsplash.com/photo-1594938384824-022ef609ade3?auto=format&fit=crop&q=80&w=400" class="w-full h-full object-cover hover:scale-110 transition-transform duration-500" alt="Menswear Production">
            </div>
            <div class="w-64 h-80 rounded-2xl overflow-hidden bg-zinc-100 flex-shrink-0">
                <img src="https://images.unsplash.com/photo-1589410884333-662589574163?auto=format&fit=crop&q=80&w=400" class="w-full h-full object-cover hover:scale-110 transition-transform duration-500" alt="Kurti Production">
            </div>
            <div class="w-64 h-80 rounded-2xl overflow-hidden bg-zinc-100 flex-shrink-0">
                <img src="https://images.unsplash.com/photo-1610030469668-8386343513b2?auto=format&fit=crop&q=80&w=400" class="w-full h-full object-cover hover:scale-110 transition-transform duration-500" alt="Fabric Production">
            </div>
        </div>
        <div class="animate-marquee-slow flex whitespace-nowrap gap-6 ml-6">
            <div class="w-64 h-80 rounded-2xl overflow-hidden bg-zinc-100 flex-shrink-0">
                <img src="https://images.unsplash.com/photo-1610189012906-40008538302b?auto=format&fit=crop&q=80&w=400" class="w-full h-full object-cover">
            </div>
            <div class="w-64 h-80 rounded-2xl overflow-hidden bg-zinc-100 flex-shrink-0">
                <img src="https://images.unsplash.com/photo-1594938384824-022ef609ade3?auto=format&fit=crop&q=80&w=400" class="w-full h-full object-cover">
            </div>
            <div class="w-64 h-80 rounded-2xl overflow-hidden bg-zinc-100 flex-shrink-0">
                <img src="https://images.unsplash.com/photo-1589410884333-662589574163?auto=format&fit=crop&q=80&w=400" class="w-full h-full object-cover">
            </div>
            <div class="w-64 h-80 rounded-2xl overflow-hidden bg-zinc-100 flex-shrink-0">
                <img src="https://images.unsplash.com/photo-1610030469668-8386343513b2?auto=format&fit=crop&q=80&w=400" class="w-full h-full object-cover">
            </div>
        </div>
    </div>

    <div class="relative flex overflow-x-hidden py-6 mt-4">
        <div class="animate-marquee-reverse flex whitespace-nowrap gap-12 items-center">
            <span class="text-2xl font-light text-zinc-400 tracking-widest uppercase">Direct From Factory</span>
            <span class="text-2xl font-black text-orange-600 uppercase">Wholesale Experts</span>
            <span class="text-2xl font-light text-zinc-400 tracking-widest uppercase">Ethical Labor</span>
            <span class="text-2xl font-black text-orange-600 uppercase">Quality Checked</span>
        </div>
        <div class="animate-marquee-reverse flex whitespace-nowrap gap-12 items-center ml-12">
            <span class="text-2xl font-light text-zinc-400 tracking-widest uppercase">Direct From Factory</span>
            <span class="text-2xl font-black text-orange-600 uppercase">Wholesale Experts</span>
            <span class="text-2xl font-light text-zinc-400 tracking-widest uppercase">Ethical Labor</span>
            <span class="text-2xl font-black text-orange-600 uppercase">Quality Checked</span>
        </div>
    </div>
</section>














<section class="bg-white py-20">
    <div class="container mx-auto px-4 md:px-10">
        
        <div class="relative rounded-[3rem] overflow-hidden bg-zinc-900 mb-24 h-[500px] md:h-[600px] group">
            <img src="https://images.unsplash.com/photo-1595332245752-16718024f5e2?auto=format&fit=crop&q=80&w=1800" 
                 class="w-full h-full object-cover opacity-80 group-hover:scale-105 transition-transform duration-1000">
            <div class="absolute inset-0 flex flex-col items-center justify-center text-center p-6 bg-black/20">
                <h2 class="text-white text-5xl md:text-8xl font-serif italic mb-4">Elevated <br>Ethnic Wear</h2>
                <p class="text-gray-200 max-w-lg mx-auto text-lg mb-8 font-light">
                    Discover our new collection of ethnic wear, crafted with passion and designed to make you shine.
                </p>
                <a href="#" class="bg-white text-black px-10 py-4 rounded-full font-bold tracking-widest hover:bg-orange-600 hover:text-white transition-all">
                    SHOP THE LATEST
                </a>
            </div>
        </div>
</div>
       







<section class="bg-zinc-50 py-24 px-4 md:px-10 border-t border-zinc-200">
    <div class="container mx-auto">
        
        <div class="text-center mb-20">
            <h2 class="text-4xl md:text-6xl font-serif italic text-zinc-900 mb-4">Visit Us or Get In Touch</h2>
            <p class="text-zinc-500 max-w-2xl mx-auto">We're here to help with all your clothing needs — retail or wholesale.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-start">
            
            <div class="space-y-12">
                <div>
                    <h3 class="text-2xl font-bold text-zinc-900 mb-8 uppercase tracking-widest border-b pb-4">Store Location & Details</h3>
                    
                    <div class="space-y-8">
                        <div class="flex gap-6">
                            <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-sm text-orange-600 shrink-0">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-zinc-900">Store Address:</h4>
                                <p class="text-zinc-600 leading-relaxed mt-1">
                                    Gurukrupa Wholesale Depot<br>
                                    123 Fashion Street, Textile Market<br>
                                    Mumbai, Maharashtra - 400001
                                </p>
                            </div>
                        </div>

                        <div class="flex gap-6">
                            <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-sm text-orange-600 shrink-0">
                                <i class="fas fa-phone-alt"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-zinc-900">Phone / WhatsApp:</h4>
                                <p class="text-zinc-600 mt-1">+91 98765 43210</p>
                                <p class="text-zinc-600">support@gurukrupafashion.com</p>
                            </div>
                        </div>

                        <div class="flex gap-6">
                            <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-sm text-orange-600 shrink-0">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-zinc-900">Working Hours:</h4>
                                <p class="text-zinc-600 mt-1">Mon - Sat: 10:00 AM - 8:00 PM</p>
                                <p class="text-zinc-600">Sunday: 11:00 AM - 6:00 PM</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <a href="https://wa.me/919876543210" class="flex items-center justify-center gap-3 bg-[#25D366] text-white py-4 rounded-xl font-bold hover:opacity-90 transition-all">
                        <i class="fab fa-whatsapp text-xl"></i> Chat on WhatsApp
                    </a>
                    <a href="#" class="flex items-center justify-center gap-3 bg-zinc-900 text-white py-4 rounded-xl font-bold hover:bg-orange-600 transition-all">
                        <i class="fas fa-directions"></i> Get Directions
                    </a>
                </div>
            </div>

            <div class="bg-white p-8 md:p-12 rounded-[2.5rem] shadow-xl shadow-zinc-200/50 border border-zinc-100">
                <h3 class="text-2xl font-bold text-zinc-900 mb-8">Send us a Message</h3>
                <form action="#" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-zinc-400 uppercase tracking-widest mb-2">Full Name</label>
                            <input type="text" placeholder="Your Name" class="w-full bg-zinc-50 border-none rounded-xl px-4 py-4 focus:ring-2 focus:ring-orange-600 transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-zinc-400 uppercase tracking-widest mb-2">Email Address</label>
                            <input type="email" placeholder="email@example.com" class="w-full bg-zinc-50 border-none rounded-xl px-4 py-4 focus:ring-2 focus:ring-orange-600 transition-all">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-zinc-400 uppercase tracking-widest mb-2">Phone Number</label>
                            <input type="tel" placeholder="+91 00000 00000" class="w-full bg-zinc-50 border-none rounded-xl px-4 py-4 focus:ring-2 focus:ring-orange-600 transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-zinc-400 uppercase tracking-widest mb-2">Inquiry Type</label>
                            <select class="w-full bg-zinc-50 border-none rounded-xl px-4 py-4 focus:ring-2 focus:ring-orange-600 transition-all text-zinc-500">
                                <option>Select inquiry type</option>
                                <option>Retail Inquiry</option>
                                <option>Wholesale Inquiry</option>
                                <option>Support</option>
                                <option>Other</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-zinc-400 uppercase tracking-widest mb-2">Message</label>
                        <textarea rows="4" placeholder="How can we help you?" class="w-full bg-zinc-50 border-none rounded-xl px-4 py-4 focus:ring-2 focus:ring-orange-600 transition-all"></textarea>
                    </div>

                    <button type="submit" class="w-full bg-orange-600 text-white py-5 rounded-xl font-black text-lg shadow-lg hover:bg-zinc-900 transition-all uppercase tracking-widest">
                        Send Message
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>


<section class="bg-white">
    <div class="relative w-full h-[450px] md:h-[650px] overflow-hidden group cursor-pointer">
        <video 
            autoplay 
            muted 
            loop 
            playsinline 
            class="absolute inset-0 w-full h-full object-cover">
            <source src="https://player.vimeo.com/external/494251291.sd.mp4?s=98d97537b819f7988358485293444a7f0e34015f&profile_id=164&oauth2_token_id=57447761" type="video/mp4">
            Your browser does not support the video tag.
        </video>

        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/50 transition-all duration-700 flex flex-col items-center justify-center">
            <div class="text-center opacity-0 group-hover:opacity-100 translate-y-12 group-hover:translate-y-0 transition-all duration-700 ease-out p-6">
                <span class="text-orange-500 font-bold tracking-[0.4em] uppercase text-xs mb-3 block">Premium Production</span>
                <h2 class="text-white text-4xl md:text-7xl font-serif italic mb-8">Elegance in Motion</h2>
                
                <a href="#products" class="inline-flex items-center gap-4 bg-white text-black px-12 py-5 rounded-full font-black hover:bg-orange-600 hover:text-white transition-all transform hover:scale-110 shadow-[0_0_30px_rgba(255,255,255,0.3)]">
                    SHOP THE COLLECTION <i class="fas fa-arrow-right text-sm"></i>
                </a>
            </div>
        </div>

        <div class="absolute top-8 right-8 bg-white/10 backdrop-blur-md border border-white/20 text-white px-4 py-2 rounded-full text-[10px] font-bold tracking-widest uppercase">
            Live Lookbook
        </div>
    </div>

    <div class="w-full h-[500px] relative">
        <iframe 
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d241317.116099006!2d72.7410992383182!3d19.08219783935293!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3be7c6306644edc1%3A0x5ad4ed877a66e30!2sMumbai%2C%20Maharashtra!5e0!3m2!1sen!2sin!4v1690000000000!5m2!1sen!2sin" 
            width="100%" 
            height="100%" 
            style="border:0;" 
            allowfullscreen="" 
            loading="lazy" 
            class="grayscale-map"
            referrerpolicy="no-referrer-when-downgrade">
        </iframe>

        <div class="absolute top-12 left-6 md:left-12 bg-white p-8 rounded-[2rem] shadow-2xl border border-zinc-100 hidden md:block max-w-sm transition-transform hover:-translate-y-2">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest">Store Now Open</span>
            </div>
            <h4 class="font-black text-2xl text-zinc-900 mb-2">Gurukrupa Depot</h4>
            <p class="text-zinc-500 text-sm leading-relaxed mb-6">
                123 Fashion Street, Textile Market, Mumbai. <br>
                Wholesale & Retail Counter.
            </p>
            <a href="https://goo.gl/maps/xyz" target="_blank" class="bg-zinc-900 text-white px-6 py-3 rounded-xl font-bold text-xs inline-flex items-center gap-2 hover:bg-orange-600 transition-colors">
                GET DIRECTIONS <i class="fas fa-location-arrow"></i>
            </a>
        </div>
    </div>
</section>



<?php

include 'includes/footer.php';
?>