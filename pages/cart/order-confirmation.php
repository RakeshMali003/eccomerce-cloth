<?php
require_once '../../config/database.php';
include '../../includes/header.php';

$order_id = $_GET['id'] ?? 0;
?>

<body class="antialiased bg-slate-50">

    <div class="container mx-auto px-4 py-20">
        <div class="max-w-2xl mx-auto text-center">
            <div
                class="w-24 h-24 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-8 shadow-xl shadow-green-50">
                <i class="fas fa-check text-4xl"></i>
            </div>

            <h1 class="text-4xl font-black tracking-tight text-slate-900 mb-4">Order Placed Successfully!</h1>
            <p class="text-slate-500 text-lg mb-10">Thank you for shopping with Gurukrupa. Your order <span
                    class="font-bold text-slate-900">#ORD-
                    <?php echo $order_id; ?>
                </span> is now being processed.</p>

            <div class="bg-white p-8 rounded-[3rem] border border-slate-100 shadow-sm mb-10 text-left">
                <h3 class="text-xs font-black uppercase tracking-widest text-slate-400 mb-6 border-b pb-4">Next Steps
                </h3>
                <ul class="space-y-4">
                    <li class="flex items-start gap-4">
                        <div
                            class="w-8 h-8 bg-orange-100 text-orange-600 rounded-full flex items-center justify-center shrink-0 font-bold text-xs">
                            1</div>
                        <div>
                            <p class="font-bold text-sm">Order Confirmation</p>
                            <p class="text-xs text-slate-500">We've sent a detailed receipt to your registered email.
                            </p>
                        </div>
                    </li>
                    <li class="flex items-start gap-4">
                        <div
                            class="w-8 h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center shrink-0 font-bold text-xs">
                            2</div>
                        <div>
                            <p class="font-bold text-sm">Quality Check</p>
                            <p class="text-xs text-slate-500">Our team is hand-inspecting each item for any defects.</p>
                        </div>
                    </li>
                    <li class="flex items-start gap-4">
                        <div
                            class="w-8 h-8 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center shrink-0 font-bold text-xs">
                            3</div>
                        <div>
                            <p class="font-bold text-sm">Shipment Dispatched</p>
                            <p class="text-xs text-slate-500">You'll receive a tracking number via SMS within 24 hours.
                            </p>
                        </div>
                    </li>
                </ul>
            </div>

            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="../orders/order-tracking.php?id=<?php echo $order_id; ?>"
                    class="px-10 py-5 bg-slate-900 text-white rounded-2xl font-black uppercase tracking-widest hover:brightness-110 transition-all flex items-center justify-center gap-2">
                    <i class="fas fa-truck-fast"></i> Track Order
                </a>
                <a href="../products/product-list.php"
                    class="px-10 py-5 bg-white border border-slate-200 text-slate-900 rounded-2xl font-black uppercase tracking-widest hover:bg-slate-50 transition-all flex items-center justify-center">
                    Continue Shopping
                </a>
            </div>
        </div>
    </div>

    <?php include '../../includes/footer.php'; ?>