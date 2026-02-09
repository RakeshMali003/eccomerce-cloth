<?php include '../../includes/header.php'; ?>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');

    :root {
        --brand-orange: #FF6F1E;
    }

    body {
        font-family: 'Plus Jakarta Sans', sans-serif;
        background-color: #f8fafc;
        color: #0f172a;
    }

    /* Vertical Timeline Logic */
    .step-line {
        position: absolute;
        left: 15px;
        top: 30px;
        bottom: -10px;
        width: 2px;
        background: #e2e8f0;
    }

    .step-active .step-line {
        background: var(--brand-orange);
    }

    .step-dot {
        position: relative;
        z-index: 10;
        width: 32px;
        h-32px;
        @apply rounded-full border-4 border-white shadow-sm transition-all duration-500;
    }

    .step-complete .step-dot {
        background: var(--brand-orange);
        border-color: #ffedd5;
    }

    .step-current .step-dot {
        background: white;
        border-color: var(--brand-orange);
    }

    .step-pending .step-dot {
        background: #f1f5f9;
        border-color: #e2e8f0;
    }

    .pulse-orange {
        box-shadow: 0 0 0 0 rgba(255, 111, 30, 0.7);
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        70% {
            box-shadow: 0 0 0 10px rgba(255, 111, 30, 0);
        }

        100% {
            box-shadow: 0 0 0 0 rgba(255, 111, 30, 0);
        }
    }
</style>

<body class="antialiased">

    <div class="container mx-auto px-4 lg:px-12 py-10 lg:py-20">

        <div class="max-w-4xl mx-auto mb-12">
            <h1 class="text-4xl font-extrabold tracking-tighter mb-4">Track Your Shipment <span
                    class="text-orange-600">.</span></h1>
            <form action="" method="GET"
                class="bg-white p-2 rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-100 flex flex-col md:flex-row gap-2">
                <input type="text" name="id" value="<?= htmlspecialchars($_GET['id'] ?? '') ?>"
                    placeholder="Enter Order ID (e.g. 1)"
                    class="flex-1 px-6 py-4 bg-transparent border-none outline-none font-bold text-sm">
                <button type="submit"
                    class="px-10 py-4 bg-slate-900 text-white rounded-2xl font-black uppercase tracking-widest hover:bg-orange-600 transition-all active:scale-95">Track
                    Order</button>
            </form>
        </div>

        <?php
        $order_id = $_GET['id'] ?? null;
        $order = null;

        if ($order_id) {
            $stmt = $pdo->prepare("
            SELECT o.*, u.name as customer_name, u.email, u.phone, u.address, u.city, u.pincode 
            FROM orders o 
            JOIN users u ON o.user_id = u.user_id 
            WHERE o.order_id = ?
        ");
            $stmt->execute([$order_id]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        if ($order_id && !$order): ?>
            <div class="text-center py-20">
                <div class="text-6xl mb-4">🔍</div>
                <h3 class="text-2xl font-bold text-slate-900">Order Not Found</h3>
                <p class="text-slate-500">We couldn't find an order with ID #<?= htmlspecialchars($order_id) ?></p>
            </div>
        <?php elseif ($order):
            // Determine steps status
            $status = strtolower($order['order_status']); // pending, processing, shipped, delivered, cancelled
        
            $steps = [
                'pending' => ['label' => 'Order Placed', 'icon' => 'fa-clipboard-check', 'desc' => 'Order received and sent to the warehouse.'],
                'processing' => ['label' => 'Processing', 'icon' => 'fa-box', 'desc' => 'Quality check and packaging in progress.'],
                'shipped' => ['label' => 'Shipped', 'icon' => 'fa-shipping-fast', 'desc' => 'Handed over to logistics partner.'],
                'delivered' => ['label' => 'Delivered', 'icon' => 'fa-check-circle', 'desc' => 'Package delivered successfully.']
            ];

            $current_step_index = 0;
            switch ($status) {
                case 'pending':
                    $current_step_index = 0;
                    break;
                case 'processing':
                    $current_step_index = 1;
                    break;
                case 'shipped':
                    $current_step_index = 2;
                    break;
                case 'delivered':
                    $current_step_index = 3;
                    break;
                case 'cancelled':
                    $current_step_index = -1;
                    break;
            }

            // Estimated Delivery: +5 days from creation
            $created_date = new DateTime($order['created_at']);
            $est_delivery = clone $created_date;
            $est_delivery->modify('+5 days');
            ?>
            <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-8">

                <div class="lg:col-span-7 space-y-6">
                    <div class="bg-white rounded-[2.5rem] p-8 lg:p-10 border border-slate-100 shadow-sm">
                        <div class="flex justify-between items-start mb-10 pb-6 border-b border-slate-50">
                            <div>
                                <p class="text-[10px] font-black uppercase text-slate-400 tracking-widest">Order ID:
                                    #<?= str_pad($order['order_id'], 6, '0', STR_PAD_LEFT) ?></p>
                                <h2 class="text-xl font-bold mt-1">Status: <span
                                        class="text-orange-600 capitalize"><?= $status ?></span></h2>
                            </div>
                            <?php if ($status !== 'cancelled' && $status !== 'delivered'): ?>
                                <div class="text-right">
                                    <p class="text-[10px] font-black uppercase text-slate-400">Estimated Delivery</p>
                                    <p class="text-lg font-black text-slate-900"><?= $est_delivery->format('M d, Y') ?></p>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="space-y-12 pl-2">
                            <?php
                            $i = 0;
                            foreach ($steps as $key => $step):
                                $is_complete = $i < $current_step_index;
                                $is_current = $i === $current_step_index;
                                $is_pending = $i > $current_step_index;

                                $step_class = 'step-pending';
                                if ($is_complete || $status === 'delivered')
                                    $step_class = 'step-complete step-active';
                                if ($is_current && $status !== 'delivered')
                                    $step_class = 'step-current';

                                // If cancelled, show differently? For now simple logic
                                if ($status === 'cancelled')
                                    $step_class = 'step-pending opacity-50';
                                ?>
                                <div class="relative flex gap-6 <?= $step_class ?>">
                                    <?php if ($i < count($steps) - 1): ?>
                                        <div class="step-line"
                                            style="<?= ($is_complete || $status === 'delivered') ? 'background: var(--brand-orange);' : 'background: #e2e8f0;' ?>">
                                        </div>
                                    <?php endif; ?>

                                    <div
                                        class="step-dot flex items-center justify-center <?= ($is_complete || $status === 'delivered') ? 'text-white' : ($is_current ? 'pulse-orange' : '') ?>">
                                        <?php if ($is_complete || $status === 'delivered'): ?>
                                            <i class="fas fa-check text-[10px]"></i>
                                        <?php endif; ?>
                                    </div>

                                    <div>
                                        <h4 class="font-bold text-slate-900"><?= $step['label'] ?></h4>
                                        <?php if ($is_complete || $is_current): ?>
                                            <p class="text-xs text-orange-600 mt-1 uppercase font-bold tracking-widest">
                                                <?php
                                                // Hacky date logic for demo
                                                $step_date = clone $created_date;
                                                $step_date->modify("+$i days");
                                                echo $step_date->format('M d • h:i A');
                                                ?>
                                            </p>
                                        <?php endif; ?>
                                        <p class="text-sm text-slate-500 mt-2"><?= $step['desc'] ?></p>
                                    </div>
                                </div>
                                <?php $i++; endforeach; ?>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-5 space-y-6">

                    <div class="bg-white rounded-[2.5rem] p-8 border border-slate-100 shadow-sm">
                        <h3 class="text-sm font-black uppercase tracking-widest text-slate-400 mb-6">Delivery Address</h3>
                        <div class="flex gap-4">
                            <div
                                class="w-12 h-12 bg-slate-50 rounded-2xl flex items-center justify-center text-slate-400 shrink-0">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div>
                                <p class="font-bold text-slate-900"><?= htmlspecialchars($order['customer_name']) ?></p>
                                <p class="text-sm text-slate-500 mt-1 leading-relaxed">
                                    <?= htmlspecialchars($order['address']) ?><br>
                                    <?= htmlspecialchars($order['city']) ?> - <?= htmlspecialchars($order['pincode']) ?><br>
                                    Phone: <?= htmlspecialchars($order['phone']) ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-slate-900 rounded-[2.5rem] p-8 text-white shadow-2xl shadow-slate-300">
                        <h3 class="text-sm font-bold uppercase tracking-widest text-slate-500 mb-6 italic">Total Summary
                        </h3>
                        <div class="flex justify-between items-end">
                            <div>
                                <p class="text-xs text-slate-400 uppercase">Payment Method</p>
                                <p class="font-bold capitalize"><?= $order['payment_method'] ?></p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400 uppercase text-right">Grand Total</p>
                                <p class="text-3xl font-black text-orange-500">₹<?= number_format($order['total_amount']) ?>
                                </p>
                            </div>
                        </div>
                        <div class="mt-8 pt-6 border-t border-slate-800">
                            <a href="../orders/order-history.php"
                                class="w-full block text-center bg-orange-600 py-4 rounded-xl font-bold text-sm uppercase tracking-widest hover:bg-white hover:text-black transition-all">View
                                All Orders</a>
                        </div>
                    </div>

                    <div class="bg-orange-50 rounded-[2.5rem] p-8 border border-orange-100">
                        <p class="text-[10px] font-black uppercase text-orange-600 tracking-widest mb-4">Need Assistance?
                        </p>
                        <h4 class="text-lg font-bold text-slate-900 mb-4">Contact Logistics Desk</h4>
                        <div class="flex gap-3">
                            <a href="https://wa.me/919956510247"
                                class="flex-1 bg-white p-3 rounded-xl text-center shadow-sm hover:scale-105 transition-transform">
                                <i class="fab fa-whatsapp text-green-500 text-xl"></i>
                            </a>
                            <a href="tel:+919956510247"
                                class="flex-1 bg-white p-3 rounded-xl text-center shadow-sm hover:scale-105 transition-transform">
                                <i class="fas fa-phone-alt text-slate-900 text-xl"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php include '../../includes/footer.php'; ?>