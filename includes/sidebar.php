<?php
$current_page = basename($_SERVER['PHP_SELF']);
$base_url = ADMIN_URL;
?>
<aside
    class="w-72 bg-white h-screen fixed left-0 top-0 border-r border-slate-100 flex-col z-[100] sidebar-scroll overflow-y-auto font-sans hidden lg:flex">
    <div class="p-8 mb-4">
        <h1 class="text-2xl font-black tracking-tighter text-slate-900 flex items-center gap-2">
            <div class="w-8 h-8 bg-orange-600 rounded-lg flex items-center justify-center text-white text-sm italic">J
            </div>
            Joshi Electricals<span class="text-orange-600">.</span>
        </h1>
    </div>

    <nav class="flex-1 px-4 space-y-10">
        <!-- Core Dashboard -->
        <?php if (has_permission('dashboard')): ?>
            <div>
                <a href="<?= $base_url; ?>dashboard.php"
                    class="flex items-center gap-4 px-6 py-4 rounded-2xl text-sm font-bold <?= ($current_page == 'dashboard.php') ? 'bg-slate-900 text-white shadow-xl shadow-slate-200' : 'text-slate-500 hover:bg-slate-50' ?> transition-all">
                    <i class="fas fa-th-large text-lg"></i> Overview Console
                </a>
            </div>
        <?php endif; ?>

        <!-- Operations Hub -->
        <?php if (has_permission('orders')): ?>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] px-6 mb-3">Operations Hub</p>
                <div class="space-y-1">
                    <a href="<?= $base_url; ?>orders/orders-list.php"
                        class="flex items-center gap-4 px-6 py-4 rounded-2xl text-sm font-bold <?= ($current_page == 'orders-list.php') ? 'bg-orange-600 text-white shadow-xl shadow-orange-100' : 'text-slate-500 hover:bg-slate-50' ?> transition-all">
                        <i class="fas fa-shopping-basket"></i> Orders & Shipping
                    </a>
                    <a href="<?= $base_url; ?>billing/invoices.php"
                        class="flex items-center gap-4 px-6 py-4 rounded-2xl text-sm font-bold <?= ($current_page == 'invoices.php') ? 'bg-orange-500 text-white shadow-xl shadow-orange-100' : 'text-slate-500 hover:bg-slate-50' ?> transition-all">
                        <i class="fas fa-file-invoice"></i> Sales Invoices
                    </a>
                </div>
            </div>
        <?php endif; ?>

        <!-- Catalog Hub -->
        <?php if (has_permission('products')): ?>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] px-6 mb-3">Catalog Assets</p>
                <div class="space-y-1">
                    <a href="<?= $base_url; ?>products/products-list.php"
                        class="flex items-center gap-4 px-6 py-4 rounded-2xl text-sm font-bold <?= ($current_page == 'products-list.php') ? 'bg-slate-900 text-white shadow-xl shadow-slate-200' : 'text-slate-500 hover:bg-slate-50' ?> transition-all">
                        <i class="fas fa-tshirt"></i> Master Catalog
                    </a>
                    <a href="<?= $base_url; ?>promotions/promo-codes.php"
                        class="flex items-center gap-4 px-6 py-4 rounded-2xl text-sm font-bold <?= ($current_page == 'promo-codes.php') ? 'bg-slate-900 text-white shadow-xl shadow-slate-200' : 'text-slate-500 hover:bg-slate-50' ?> transition-all">
                        <i class="fas fa-ticket-alt"></i> Discount Engines
                    </a>
                </div>
            </div>
        <?php endif; ?>

        <!-- Procurement & Finance -->
        <?php if (has_permission('orders') || has_permission('products')): ?>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] px-6 mb-3">Backend & Finance</p>
                <div class="space-y-1">
                    <?php if (has_permission('orders')): ?>
                        <a href="<?= $base_url; ?>billing/order-list.php"
                            class="flex items-center gap-4 px-6 py-4 rounded-2xl text-sm font-bold <?= ($current_page == 'order-list.php') ? 'bg-orange-600 text-white shadow-xl shadow-orange-100' : 'text-slate-500 hover:bg-slate-50' ?> transition-all">
                            <i class="fas fa-receipt"></i> Order Ledger
                        </a>
                        <a href="<?= $base_url; ?>billing/all-bills.php"
                            class="flex items-center gap-4 px-6 py-4 rounded-2xl text-sm font-bold <?= ($current_page == 'all-bills.php') ? 'bg-emerald-600 text-white shadow-xl shadow-emerald-100' : 'text-slate-500 hover:bg-slate-50' ?> transition-all">
                            <i class="fas fa-file-alt"></i> Bills Ledger
                        </a>
                    <?php endif; ?>

                    <?php if (has_permission('products')): ?>
                        <a href="<?= $base_url; ?>billing/supplier-bills.php"
                            class="flex items-center gap-4 px-6 py-4 rounded-2xl text-sm font-bold <?= ($current_page == 'supplier-bills.php') ? 'bg-slate-900 text-white shadow-xl shadow-slate-200' : 'text-slate-500 hover:bg-slate-50' ?> transition-all">
                            <i class="fas fa-file-invoice-dollar"></i> Supplier Bills
                        </a>
                        <a href="<?= $base_url; ?>billing/supplier-dues.php"
                            class="flex items-center gap-4 px-6 py-4 rounded-2xl text-sm font-bold <?= ($current_page == 'supplier-dues.php') ? 'bg-emerald-600 text-white shadow-xl shadow-emerald-100' : 'text-slate-500 hover:bg-slate-50' ?> transition-all">
                            <i class="fas fa-calendar-exclamation"></i> Supplier Dues
                        </a>
                        <a href="<?= $base_url; ?>suppliers/suppliers.php"
                            class="flex items-center gap-4 px-6 py-4 rounded-2xl text-sm font-bold <?= ($current_page == 'suppliers.php') ? 'bg-slate-900 text-white shadow-xl shadow-slate-200' : 'text-slate-500 hover:bg-slate-50' ?> transition-all">
                            <i class="fas fa-truck-loading"></i> Vendor Network
                        </a>
                    <?php endif; ?>

                    <?php if (has_permission('orders')): ?>
                        <a href="<?= $base_url; ?>Payments/Payment History.php"
                            class="flex items-center gap-4 px-6 py-4 rounded-2xl text-sm font-bold <?= ($current_page == 'Payment History.php') ? 'bg-blue-600 text-white shadow-xl shadow-blue-100' : 'text-slate-500 hover:bg-slate-50' ?> transition-all">
                            <i class="fas fa-history"></i> Payment History
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Analytics Hub -->
        <?php if (has_permission('reports')): ?>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] px-6 mb-3">Intelligence</p>
                <div class="space-y-1">
                    <a href="<?= $base_url; ?>reports/gst-report.php"
                        class="flex items-center gap-4 px-6 py-4 rounded-2xl text-sm font-bold <?= ($current_page == 'gst-report.php') ? 'bg-orange-600 text-white shadow-xl shadow-orange-100' : 'text-slate-500 hover:bg-slate-50' ?> transition-all">
                        <i class="fas fa-landmark"></i> Tax Registry
                    </a>
                    <a href="<?= $base_url; ?>reports/inventory-report.php"
                        class="flex items-center gap-4 px-6 py-4 rounded-2xl text-sm font-bold <?= ($current_page == 'inventory-report.php') ? 'bg-slate-900 text-white shadow-xl shadow-slate-200' : 'text-slate-500 hover:bg-slate-50' ?> transition-all">
                        <i class="fas fa-chart-pie"></i> Stock Valuation
                    </a>
                    <a href="<?= $base_url; ?>reports/delivery-report.php"
                        class="flex items-center gap-4 px-6 py-4 rounded-2xl text-sm font-bold <?= ($current_page == 'delivery-report.php') ? 'bg-slate-900 text-white shadow-xl shadow-slate-200' : 'text-slate-500 hover:bg-slate-50' ?> transition-all">
                        <i class="fas fa-route"></i> Logistics Performance
                    </a>
                </div>
            </div>
        <?php endif; ?>

        <!-- Administration -->
        <?php if (has_permission('users') || has_permission('settings') || has_permission('workers')): ?>
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] px-6 mb-3">Control Plane</p>
                <div class="space-y-1">
                    <?php if (has_permission('users')): ?>
                        <a href="<?= $base_url; ?>users/users.php"
                            class="flex items-center gap-4 px-6 py-4 rounded-2xl text-sm font-bold <?= ($current_page == 'users.php') ? 'bg-slate-900 text-white shadow-xl shadow-slate-200' : 'text-slate-500 hover:bg-slate-50' ?> transition-all">
                            <i class="fas fa-users-cog"></i> Access Control
                        </a>
                    <?php endif; ?>

                    <?php if (has_permission('workers')): ?>
                        <a href="<?= $base_url; ?>workers/list.php"
                            class="flex items-center gap-4 px-6 py-4 rounded-2xl text-sm font-bold <?= ($current_page == 'list.php') ? 'bg-slate-900 text-white shadow-xl shadow-slate-200' : 'text-slate-500 hover:bg-slate-50' ?> transition-all">
                            <i class="fas fa-id-badge"></i> Staff Management
                        </a>
                    <?php endif; ?>

                    <?php if (has_permission('settings')): ?>
                        <a href="<?= $base_url; ?>settings/site-settings.php"
                            class="flex items-center gap-4 px-6 py-4 rounded-2xl text-sm font-bold <?= ($current_page == 'site-settings.php') ? 'bg-slate-900 text-white shadow-xl shadow-slate-200' : 'text-slate-500 hover:bg-slate-50' ?> transition-all">
                            <i class="fas fa-cogs"></i> System Config
                        </a>
                    <?php endif; ?>

                    <!-- New Inquiry & Dealer Links -->
                    <a href="<?= $base_url; ?>inquiries/inquiry-list.php"
                        class="flex items-center gap-4 px-6 py-4 rounded-2xl text-sm font-bold <?= ($current_page == 'inquiry-list.php') ? 'bg-slate-900 text-white shadow-xl shadow-slate-200' : 'text-slate-500 hover:bg-slate-50' ?> transition-all">
                        <i class="fas fa-envelope-open-text"></i> Inquiries
                    </a>
                    <a href="<?= $base_url; ?>dealers/applications.php"
                        class="flex items-center gap-4 px-6 py-4 rounded-2xl text-sm font-bold <?= ($current_page == 'applications.php') ? 'bg-slate-900 text-white shadow-xl shadow-slate-200' : 'text-slate-500 hover:bg-slate-50' ?> transition-all">
                        <i class="fas fa-handshake"></i> Dealer Apps
                    </a>
                    <a href="<?= $base_url; ?>settings/pages.php"
                        class="flex items-center gap-4 px-6 py-4 rounded-2xl text-sm font-bold <?= ($current_page == 'pages.php') ? 'bg-slate-900 text-white shadow-xl shadow-slate-200' : 'text-slate-500 hover:bg-slate-50' ?> transition-all">
                        <i class="fas fa-pen-nib"></i> Page Content
                    </a>

                </div>
            </div>
        <?php endif; ?>
    </nav>

    <div class="p-8 border-t border-slate-50 mt-10">
        <a href="<?= $base_url; ?>logout.php"
            class="flex items-center gap-4 px-6 py-4 rounded-2xl text-sm font-black text-red-500 hover:bg-red-50 transition-all uppercase tracking-widest">
            <i class="fas fa-power-off"></i> Terminate Session
        </a>
    </div>
</aside>

<style>
    .sidebar-scroll::-webkit-scrollbar {
        width: 4px;
    }

    .sidebar-scroll::-webkit-scrollbar-thumb {
        background: #f1f5f9;
        border-radius: 10px;
    }

    .sidebar-scroll:hover::-webkit-scrollbar-thumb {
        background: #e2e8f0;
    }
</style>