<?php include '../includes/header.php'; ?>

<body class="bg-slate-50 text-slate-900">

 <section class="bg-gray-50 py-20 px-4 md:px-10">
    <div class="container mx-auto">
        
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-5xl font-serif text-zinc-900 mb-4 italic">Get In Touch</h2>
            <p class="text-zinc-500 max-w-xl mx-auto">Have questions about our collections or wholesale pricing? Our team is here to assist you.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-start">
            
            <div class="space-y-8">
                <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-zinc-100">
                    <h3 class="text-xl font-bold mb-6 text-zinc-800 uppercase tracking-widest">Store Information</h3>
                    
                    <div class="space-y-6">
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 bg-orange-50 text-orange-600 rounded-full flex items-center justify-center shrink-0">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-sm">Our Warehouse</h4>
                                <p class="text-gray-500 text-sm">123 Textile Market, Ring Road, Surat, Gujarat - 395002</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 bg-orange-50 text-orange-600 rounded-full flex items-center justify-center shrink-0">
                                <i class="fas fa-phone-alt"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-sm">Call / WhatsApp</h4>
                                <p class="text-gray-500 text-sm">+91 98765 43210</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 bg-orange-50 text-orange-600 rounded-full flex items-center justify-center shrink-0">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-sm">Email Support</h4>
                                <p class="text-gray-500 text-sm">contact@gurukrupa.com</p>
                            </div>
                        </div>
                    </div>

                    <a href="https://wa.me/919876543210" class="mt-8 flex items-center justify-center gap-2 bg-[#25D366] text-white py-4 rounded-xl font-bold hover:opacity-90 transition-all">
                        <i class="fab fa-whatsapp text-xl"></i> Chat with us on WhatsApp
                    </a>
                </div>

                <div class="rounded-[2rem] overflow-hidden h-[300px] shadow-sm border border-zinc-100 grayscale hover:grayscale-0 transition-all duration-500">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d119066.41709470125!2d72.73989472621941!3d21.159340299132174!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3be04e59411d1563%3A0xfe4558290938b042!2sSurat%2C%20Gujarat!5e0!3m2!1sen!2sin!4v1700000000000" 
                    width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                </div>
            </div>

            <div class="bg-white p-8 md:p-12 rounded-[2.5rem] shadow-xl border border-zinc-100">
                <h3 class="text-2xl font-bold mb-2 text-zinc-900">Send a Message</h3>
                <p class="text-gray-500 mb-8 text-sm">Fill out the form below and our team will get back to you within 24 hours.</p>
                
                <form action="#" class="space-y-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="space-y-2">
                            <label class="text-[10px] font-bold uppercase text-gray-400 tracking-widest">Full Name</label>
                            <input type="text" placeholder="Enter your name" class="w-full bg-gray-50 border-none rounded-xl px-5 py-4 focus:ring-2 focus:ring-orange-600 transition-all">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-bold uppercase text-gray-400 tracking-widest">Phone Number</label>
                            <input type="tel" placeholder="+91 00000 00000" class="w-full bg-gray-50 border-none rounded-xl px-5 py-4 focus:ring-2 focus:ring-orange-600 transition-all">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-bold uppercase text-gray-400 tracking-widest">Email Address</label>
                        <input type="email" placeholder="example@gmail.com" class="w-full bg-gray-50 border-none rounded-xl px-5 py-4 focus:ring-2 focus:ring-orange-600 transition-all">
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-bold uppercase text-gray-400 tracking-widest">Inquiry Type</label>
                        <select class="w-full bg-gray-50 border-none rounded-xl px-5 py-4 focus:ring-2 focus:ring-orange-600 transition-all text-gray-500">
                            <option>General Inquiry</option>
                            <option>Wholesale Order</option>
                            <option>Retail Order Support</option>
                            <option>Custom Stitching</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-bold uppercase text-gray-400 tracking-widest">Your Message</label>
                        <textarea rows="4" placeholder="How can we help you today?" class="w-full bg-gray-50 border-none rounded-xl px-5 py-4 focus:ring-2 focus:ring-orange-600 transition-all"></textarea>
                    </div>

                    <button type="submit" class="w-full bg-zinc-900 text-white py-5 rounded-xl font-bold hover:bg-orange-600 transition-all uppercase tracking-widest shadow-lg">
                        Submit Message
                    </button>
                </form>
            </div>

        </div>
    </div>
</section>
   

    <?php include '../includes/footer.php'; ?>
