<?php
require_once '../../config/database.php';
include '../../includes/header.php';

$is_logged_in = isset($_SESSION['user_id']);
$user_id = $is_logged_in ? $_SESSION['user_id'] : null;

// Fetch cart: DB for logged-in users, session for guests
if ($is_logged_in) {
    $stmt = $pdo->prepare("
        SELECT c.product_id, c.quantity, c.variant_id, c.purchase_type,
               p.name, p.price, p.wholesale_price, p.min_wholesale_qty, p.stock, p.main_image
        FROM cart c 
        JOIN products p ON c.product_id = p.product_id 
        WHERE c.user_id = ?
    ");
    $stmt->execute([$user_id]);
    $db_cart = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $cart = [];
    foreach ($db_cart as $item) {
        $cart[$item['product_id']] = [
            'name' => $item['name'],
            'price' => (float) $item['price'],
            'wholesale_price' => (float) $item['wholesale_price'],
            'min_wholesale_qty' => (int) $item['min_wholesale_qty'],
            'stock' => (int) $item['stock'],
            'main_image' => $item['main_image'],
            'variant_id' => $item['variant_id'],
            'purchase_type' => $item['purchase_type'],
            'quantity' => (int) $item['quantity']
        ];
    }
} else {
    $cart = $_SESSION['cart'] ?? [];
}

// Fetch user profile data for pre-filling checkout form
$user_data = null;
if ($is_logged_in) {
    $user_stmt = $pdo->prepare("SELECT name, phone, address, city, pincode FROM users WHERE user_id = ?");
    $user_stmt->execute([$user_id]);
    $user_data = $user_stmt->fetch(PDO::FETCH_ASSOC);
}

if (empty($cart)) {
    header("Location: cart.php");
    exit();
}

$subtotal = 0;
$total_items = 0;
$isWholesale = false;

foreach ($cart as $item) {
    if ($item['quantity'] >= $item['min_wholesale_qty']) {
        $subtotal += $item['wholesale_price'] * $item['quantity'];
        $isWholesale = true;
    } else {
        $subtotal += $item['price'] * $item['quantity'];
    }
    $total_items += $item['quantity'];
}

$gst = round($subtotal * 0.12);
$grand_total = $subtotal + $gst;
?>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap');

    :root {
        --brand-orange: #FF6F1E;
    }

    body {
        font-family: 'Plus Jakarta Sans', sans-serif;
        background-color: #f8fafc;
        color: #0f172a;
    }

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

    #dynamic-payment-details {
        transition: all 0.4s ease;
    }

    .fade-in {
        animation: fadeIn 0.3s ease forwards;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

<body class="antialiased">

    <div class="container mx-auto px-4 lg:px-12 py-10">
        <form action="process-order.php" method="POST"
            class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-10">

            <div class="lg:col-span-7 space-y-8">
                <h1 class="text-3xl font-extrabold tracking-tight">Checkout <span class="text-orange-600">.</span></h1>

                <div class="bg-white rounded-[2.5rem] p-8 border border-slate-100 shadow-sm">
                    <div class="flex items-center gap-4 mb-6">
                        <div
                            class="w-10 h-10 bg-slate-900 text-white rounded-full flex items-center justify-center text-sm font-bold">
                            1</div>
                        <h2 class="text-xl font-bold">Shipping Information</h2>
                    </div>
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <input type="text" name="customer_name" placeholder="Full Name" required
                                value="<?php echo htmlspecialchars($user_data['name'] ?? ''); ?>"
                                class="w-full bg-slate-50 border-none rounded-2xl px-5 py-4 text-sm font-medium">
                            <input type="text" name="customer_phone" placeholder="Phone Number" required
                                value="<?php echo htmlspecialchars($user_data['phone'] ?? ''); ?>"
                                class="w-full bg-slate-50 border-none rounded-2xl px-5 py-4 text-sm font-medium">
                        </div>
                        <textarea name="shipping_address" placeholder="Full Address with Pincode" required rows="3"
                            class="w-full bg-slate-50 border-none rounded-2xl px-5 py-4 text-sm font-medium"><?php echo htmlspecialchars($user_data['address'] ?? ''); ?></textarea>
                        <div class="grid grid-cols-2 gap-4">
                            <input type="text" name="city" placeholder="City" required
                                value="<?php echo htmlspecialchars($user_data['city'] ?? ''); ?>"
                                class="w-full bg-slate-50 border-none rounded-2xl px-5 py-4 text-sm font-medium">
                            <input type="text" name="pincode" placeholder="Pincode" required
                                value="<?php echo htmlspecialchars($user_data['pincode'] ?? ''); ?>"
                                class="w-full bg-slate-50 border-none rounded-2xl px-5 py-4 text-sm font-medium">
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-[2.5rem] p-8 border border-slate-100 shadow-sm">
                    <div class="flex items-center gap-4 mb-8">
                        <div
                            class="w-10 h-10 bg-slate-900 text-white rounded-full flex items-center justify-center text-sm font-bold">
                            2</div>
                        <h2 class="text-xl font-bold">Payment Method</h2>
                    </div>

                    <div class="space-y-3" id="payment-options">
                        <input type="hidden" name="payment_method" id="payment_method_input" value="upi">

                        <div class="payment-card active" onclick="selectPayment(this, 'upi')">
                            <div class="flex items-center gap-4">
                                <div
                                    class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center text-xl">
                                    <i class="fas fa-mobile-screen-button"></i>
                                </div>
                                <div>
                                    <p class="font-bold text-sm">UPI / Instant Pay</p>
                                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">PhonePe,
                                        GooglePay, WhatsApp</p>
                                </div>
                            </div>
                            <div class="radio-circle"></div>
                        </div>

                        <div class="payment-card" onclick="selectPayment(this, 'bank')">
                            <div class="flex items-center gap-4">
                                <div
                                    class="w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center text-xl">
                                    <i class="fas fa-building-columns"></i>
                                </div>
                                <div>
                                    <p class="font-bold text-sm">NEFT / RTGS (Wholesale)</p>
                                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Get 1%
                                        Cash Discount on Bulk</p>
                                </div>
                            </div>
                            <div class="radio-circle"></div>
                        </div>

                        <div class="payment-card" onclick="selectPayment(this, 'cod')">
                            <div class="flex items-center gap-4">
                                <div
                                    class="w-12 h-12 bg-green-50 text-green-600 rounded-2xl flex items-center justify-center text-xl">
                                    <i class="fas fa-hand-holding-dollar"></i>
                                </div>
                                <div>
                                    <p class="font-bold text-sm">Cash on Delivery</p>
                                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Retail
                                        Orders Only</p>
                                </div>
                            </div>
                            <div class="radio-circle"></div>
                        </div>
                    </div>

                    <div id="dynamic-payment-details"
                        class="mt-8 p-6 bg-slate-50 rounded-3xl border border-slate-100 min-h-[100px]">
                    </div>
                </div>
            </div>

            <div class="lg:col-span-5">
                <div class="sticky top-10">
                    <div class="bg-slate-900 rounded-[3rem] p-10 text-white shadow-2xl shadow-slate-300">
                        <h3 class="text-xl font-bold mb-8 border-b border-slate-800 pb-6">Order Recap</h3>

                        <div class="space-y-5 text-sm mb-10">
                            <div class="flex justify-between text-slate-400">
                                <span>Subtotal (<?php echo $total_items; ?> Items)</span>
                                <span class="text-white font-bold">₹<?php echo number_format($subtotal); ?></span>
                            </div>
                            <div class="flex justify-between text-slate-400">
                                <span>GST (12%)</span>
                                <span class="text-white font-bold"
                                    id="live-gst">₹<?php echo number_format($gst); ?></span>
                            </div>
                            <div id="cod-fee-row" class="hidden flex justify-between text-orange-400">
                                <span>COD Handling Fee</span>
                                <span class="font-bold">₹150</span>
                            </div>
                            <div id="bank-discount-row" class="hidden flex justify-between text-green-400">
                                <span>B2B Bank Discount (1%)</span>
                                <span class="font-bold">-₹<?php echo number_format($subtotal * 0.01); ?></span>
                            </div>
                            <div class="pt-8 border-t border-slate-800 flex justify-between items-end">
                                <div>
                                    <p class="text-[10px] font-bold text-slate-500 uppercase mb-1">Grand Total</p>
                                    <p class="text-4xl font-black text-orange-500 tracking-tighter" id="live-total">
                                        ₹<?php echo number_format($grand_total); ?></p>
                                </div>
                            </div>
                        </div>

                        <button type="submit"
                            class="w-full bg-orange-600 py-6 rounded-2xl font-black uppercase text-xs tracking-[0.2em] shadow-xl hover:scale-[1.02] transition-all active:scale-95">
                            Confirm & Place Order
                        </button>

                        <div
                            class="mt-8 flex justify-center gap-4 opacity-30 grayscale hover:opacity-100 transition-opacity">
                            <i class="fab fa-cc-visa text-2xl"></i>
                            <i class="fab fa-cc-mastercard text-2xl"></i>
                            <i class="fab fa-google-pay text-2xl"></i>
                            <i class="fas fa-shield-halved text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        const baseSubtotal = <?php echo $subtotal; ?>;
        const gstRate = 0.12;

        function selectPayment(element, type) {
            document.querySelectorAll('.payment-card').forEach(card => card.classList.remove('active'));
            element.classList.add('active');
            document.getElementById('payment_method_input').value = type;

            const detailsBox = document.getElementById('dynamic-payment-details');
            const codRow = document.getElementById('cod-fee-row');
            const bankRow = document.getElementById('bank-discount-row');

            let finalTotal = baseSubtotal + (baseSubtotal * gstRate);

            codRow.classList.add('hidden');
            bankRow.classList.add('hidden');

            if (type === 'upi') {
                detailsBox.innerHTML = `
                <div class="fade-in">
                    <p class="text-xs font-bold text-slate-800 mb-2">Preferred UPI App</p>
                    <div class="flex gap-3">
                        <button type="button" class="bg-white border p-3 rounded-xl flex-1 text-[10px] font-bold hover:border-orange-500 transition-all">GPay</button>
                        <button type="button" class="bg-white border p-3 rounded-xl flex-1 text-[10px] font-bold hover:border-orange-500 transition-all">PhonePe</button>
                        <button type="button" class="bg-white border p-3 rounded-xl flex-1 text-[10px] font-bold hover:border-orange-500 transition-all">WhatsApp Pay</button>
                    </div>
                </div>`;
            } else if (type === 'bank') {
                bankRow.classList.remove('hidden');
                finalTotal -= (baseSubtotal * 0.01);
                detailsBox.innerHTML = `
                <div class="fade-in">
                    <p class="text-xs font-bold text-slate-800 mb-2">Joshi Electricals Bank Details</p>
                    <p class="text-[11px] text-slate-500 leading-relaxed font-medium">HDFC Bank, Surat Main Branch<br>A/C: 502000XXXXXXX<br>IFSC: HDFC0000123</p>
                    <p class="text-[9px] text-orange-600 font-bold mt-3 uppercase tracking-tighter">*Order processed after verify</p>
                </div>`;
            } else if (type === 'cod') {
                codRow.classList.remove('hidden');
                finalTotal += 150;
                detailsBox.innerHTML = `
                <div class="fade-in">
                    <p class="text-[11px] text-slate-500 font-medium leading-relaxed">Please pay cash to the courier. Ensure contact number is reachable.</p>
                </div>`;
            }

            document.getElementById('live-total').innerText = '₹' + Math.round(finalTotal).toLocaleString('en-IN');
        }

        selectPayment(document.querySelector('.payment-card'), 'upi');
    </script>

    <?php include '../../includes/footer.php'; ?>