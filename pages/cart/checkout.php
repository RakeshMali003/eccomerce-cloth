<?php include '../../includes/header.php'; ?>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap');
    
    :root { --brand-orange: #FF6F1E; }
    body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; color: #0f172a; }

    /* Real-time Selection Highlight */
    .payment-card {
        @apply cursor-pointer border-2 border-transparent bg-white p-5 rounded-3xl transition-all duration-300 shadow-sm flex items-center justify-between;
    }
    .payment-card.active {
        @apply border-orange-500 bg-orange-50 shadow-md;
    }
    .payment-card.active .radio-circle {
        @apply border-[6px] border-orange-500 bg-white;
    }
    .radio-circle {
        @apply w-5 h-5 rounded-full border-2 border-slate-200 transition-all;
    }

    /* Dynamic UI Transitions */
    #dynamic-payment-details { transition: all 0.4s ease; }
    .fade-in { animation: fadeIn 0.3s ease forwards; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>

<body class="antialiased">

<div class="container mx-auto px-4 lg:px-12 py-10">
    <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-10">
        
        <div class="lg:col-span-7 space-y-8">
            <h1 class="text-3xl font-extrabold tracking-tight">Checkout <span class="text-orange-600">.</span></h1>

            <div class="bg-white rounded-[2.5rem] p-8 border border-slate-100 shadow-sm">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-10 h-10 bg-slate-900 text-white rounded-full flex items-center justify-center text-sm font-bold">1</div>
                    <h2 class="text-xl font-bold">Shipping Destination</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-4 rounded-2xl border-2 border-orange-500 bg-orange-50/50">
                        <p class="font-bold text-sm">Arjun Mehta (Default)</p>
                        <p class="text-xs text-slate-500 mt-1">Kamala Mills, Lower Parel, Mumbai...</p>
                    </div>
                    <button class="p-4 rounded-2xl border-2 border-dashed border-slate-200 text-slate-400 text-xs font-bold hover:border-orange-500 hover:text-orange-600 transition-all">
                        + Add New Address
                    </button>
                </div>
            </div>

            <div class="bg-white rounded-[2.5rem] p-8 border border-slate-100 shadow-sm">
                <div class="flex items-center gap-4 mb-8">
                    <div class="w-10 h-10 bg-slate-900 text-white rounded-full flex items-center justify-center text-sm font-bold">2</div>
                    <h2 class="text-xl font-bold">Payment Method</h2>
                </div>

                <div class="space-y-3" id="payment-options">
                    <div class="payment-card active" onclick="selectPayment(this, 'upi')">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center text-xl">
                                <i class="fas fa-mobile-screen-button"></i>
                            </div>
                            <div>
                                <p class="font-bold text-sm">UPI / Instant Pay</p>
                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">PhonePe, GooglePay, WhatsApp</p>
                            </div>
                        </div>
                        <div class="radio-circle"></div>
                    </div>

                    <div class="payment-card" onclick="selectPayment(this, 'bank')">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center text-xl">
                                <i class="fas fa-building-columns"></i>
                            </div>
                            <div>
                                <p class="font-bold text-sm">NEFT / RTGS (Wholesale)</p>
                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Get 1% Cash Discount on Bulk</p>
                            </div>
                        </div>
                        <div class="radio-circle"></div>
                    </div>

                    <div class="payment-card" onclick="selectPayment(this, 'cod')">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-green-50 text-green-600 rounded-2xl flex items-center justify-center text-xl">
                                <i class="fas fa-hand-holding-dollar"></i>
                            </div>
                            <div>
                                <p class="font-bold text-sm">Cash on Delivery</p>
                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Retail Orders Only</p>
                            </div>
                        </div>
                        <div class="radio-circle"></div>
                    </div>
                </div>

                <div id="dynamic-payment-details" class="mt-8 p-6 bg-slate-50 rounded-3xl border border-slate-100">
                    </div>
            </div>
        </div>

        <div class="lg:col-span-5">
            <div class="sticky top-10">
                <div class="bg-slate-900 rounded-[3rem] p-10 text-white shadow-2xl shadow-slate-300">
                    <h3 class="text-xl font-bold mb-8 border-b border-slate-800 pb-6">Final Order Recap</h3>
                    
                    <div class="space-y-5 text-sm mb-10">
                        <div class="flex justify-between text-slate-400">
                            <span>Subtotal (50 Units)</span>
                            <span class="text-white font-bold">₹42,500</span>
                        </div>
                        <div class="flex justify-between text-slate-400">
                            <span>GST (12%)</span>
                            <span class="text-white font-bold" id="live-gst">₹5,100</span>
                        </div>
                        <div id="cod-fee-row" class="hidden flex justify-between text-orange-400">
                            <span>COD Handling Fee</span>
                            <span class="font-bold">₹150</span>
                        </div>
                        <div id="bank-discount-row" class="hidden flex justify-between text-green-400">
                            <span>B2B Bank Discount (1%)</span>
                            <span class="font-bold">-₹425</span>
                        </div>
                        <div class="pt-8 border-t border-slate-800 flex justify-between items-end">
                            <div>
                                <p class="text-[10px] font-bold text-slate-500 uppercase mb-1">Grand Total</p>
                                <p class="text-4xl font-black text-orange-500 tracking-tighter" id="live-total">₹47,600</p>
                            </div>
                        </div>
                    </div>

                    <button class="w-full bg-orange-600 py-6 rounded-2xl font-black uppercase text-xs tracking-[0.2em] shadow-xl hover:scale-[1.02] transition-all active:scale-95">
                        Place Order Now
                    </button>
                    
                    <div class="mt-8 flex justify-center gap-4 opacity-30 grayscale hover:opacity-100 transition-opacity">
                        <i class="fab fa-cc-visa text-2xl"></i>
                        <i class="fab fa-cc-mastercard text-2xl"></i>
                        <i class="fab fa-google-pay text-2xl"></i>
                        <i class="fas fa-shield-halved text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const baseSubtotal = 42500;
    const gstRate = 0.12;

    function selectPayment(element, type) {
        // Update UI Selection
        document.querySelectorAll('.payment-card').forEach(card => card.classList.remove('active'));
        element.classList.add('active');

        const detailsBox = document.getElementById('dynamic-payment-details');
        const codRow = document.getElementById('cod-fee-row');
        const bankRow = document.getElementById('bank-discount-row');
        
        let finalTotal = baseSubtotal + (baseSubtotal * gstRate);
        
        // Reset rows
        codRow.classList.add('hidden');
        bankRow.classList.add('hidden');

        // Dynamic Content & Calculation Logic
        if(type === 'upi') {
            detailsBox.innerHTML = `
                <div class="fade-in">
                    <p class="text-xs font-bold text-slate-800 mb-2">Preferred UPI App</p>
                    <div class="flex gap-3">
                        <button class="bg-white border p-2 rounded-xl flex-1 text-[10px] font-bold">GPay</button>
                        <button class="bg-white border p-2 rounded-xl flex-1 text-[10px] font-bold">PhonePe</button>
                        <button class="bg-white border p-2 rounded-xl flex-1 text-[10px] font-bold">Paytm</button>
                    </div>
                </div>`;
        } else if(type === 'bank') {
            bankRow.classList.remove('hidden');
            finalTotal -= 425; // 1% discount
            detailsBox.innerHTML = `
                <div class="fade-in">
                    <p class="text-xs font-bold text-slate-800 mb-2">Gurukrupa Bank Details</p>
                    <p class="text-[11px] text-slate-500 leading-relaxed font-medium">HDFC Bank, Surat Main Branch<br>A/C: 502000XXXXXXX<br>IFSC: HDFC0000123</p>
                    <p class="text-[9px] text-orange-600 font-bold mt-3 uppercase tracking-tighter">*Upload screenshot after payment</p>
                </div>`;
        } else if(type === 'cod') {
            codRow.classList.remove('hidden');
            finalTotal += 150;
            detailsBox.innerHTML = `
                <div class="fade-in">
                    <p class="text-[11px] text-slate-500 font-medium">Please pay cash to the delivery partner upon arrival. Ensure someone is available at the Boutique address.</p>
                </div>`;
        }

        // Update Summary Figures
        document.getElementById('live-total').innerText = '₹' + Math.round(finalTotal).toLocaleString('en-IN');
    }

    // Initialize with UPI
    selectPayment(document.querySelector('.payment-card'), 'upi');
</script>

<?php include '../../includes/footer.php'; ?>