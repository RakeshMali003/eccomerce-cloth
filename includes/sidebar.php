
  
    <?php
// Set this to your local project folder name
$base_url = "http://localhost/ecommerce-website/"; 
require_once $_SERVER['DOCUMENT_ROOT'] . '/ecommerce-website/config/database.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../admin/index.php");
    exit();
}

$admin_name = $_SESSION['admin_name'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gurukrupa Admin | Hub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  

   
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f8fafc;
            color: #1e293b;
            overflow-x: hidden;
        }

        #sidebar {
            width: 280px;
            z-index: 60;
            transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            background: white; /* Required for mobile visibility */
        }

        @media (min-width: 1025px) {
            .main-content { margin-left: 280px; }
        }

        @media (max-width: 1024px) {
            #sidebar { transform: translateX(-100%); }
            #sidebar.open { transform: translateX(0); }
            .main-content { margin-left: 0 !important; }
        }

        .logo-img { mix-blend-mode: multiply; height: auto; object-fit: contain; }

      /* Remove the max-height and opacity lines you had before */
.submenu-container {
    display: none; /* Hide by default */
}

.submenu-container.show {
    display: block; /* Show when this class is added */
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Ensure the chevron rotates correctly */
.chevron {
    transition: transform 0.3s ease;
}

        .menu-item.active .chevron { transform: rotate(180deg); color: #FF6F1E; }

        .search-wrapper:focus-within {
            border-color: #f97316;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(249, 115, 22, 0.1);
        }

            @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

        :root { --gk-orange: #FF6F1E; }

        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background-color: #f8fafc; 
            color: #0f172a; 
        }

        /* Bento KPI Cards */
        .kpi-card {
            background: white;
            padding: 2rem;
            border-radius: 2.5rem;
            border: 1px solid #f1f5f9;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .kpi-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05);
        }

        /* Premium Table Design */
        .table-wrapper {
            background: white;
            border-radius: 2.5rem;
            border: 1px solid #f1f5f9;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }
        .custom-table thead {
            background-color: #f8fafc;
        }
        .custom-table tbody tr {
            transition: background-color 0.2s ease;
        }
        .custom-table tbody tr:hover {
            background-color: rgba(255, 111, 30, 0.03);
        }

        /* Modern Inputs */
        .input-premium {
            width: 100%;
            background-color: #f1f5f9;
            border: 2px solid transparent;
            border-radius: 1.25rem;
            padding: 1rem 1.5rem;
            font-size: 0.875rem;
            font-weight: 700;
            transition: all 0.3s ease;
            outline: none;
        }
        .input-premium:focus {
            background-color: white;
            border-color: var(--gk-orange);
            box-shadow: 0 0 0 4px rgba(255, 111, 30, 0.1);
        }

        /* Status Badges */
        .badge {
            padding: 0.375rem 1rem;
            border-radius: 1rem;
            font-size: 0.7rem;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            display: inline-block;
        }
        .badge-active { background: #ecfdf5; color: #059669; border: 1px solid #d1fae5; }
        .badge-suspended { background: #fef2f2; color: #dc2626; border: 1px solid #fee2e2; }

        /* Actions */
        .action-btn {
            width: 2.25rem;
            height: 2.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.75rem;
            background: #f1f5f9;
            color: #64748b;
            transition: all 0.2s ease;
        }
        .action-btn:hover { color: white; }
        .btn-edit:hover { background: #0f172a; transform: rotate(8deg); }
        .btn-delete:hover { background: #ef4444; transform: rotate(-8deg); }

        /* Glass Modal */
        .modal-overlay {
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(8px);
            display: none; /* Controlled by JS */
        }
    </style>
</head>
<body class="min-h-screen">

    <div id="overlay" onclick="toggleSidebar()" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 hidden lg:hidden"></div>

   
   <?php
// Ensure base_url is set relative to your structure
$base_url = "/ecommerce-website/admin/";
?>
<aside id="sidebar" class="fixed top-0 left-0 h-full bg-white border-r border-slate-100 flex flex-col shadow-2xl lg:shadow-none lg:translate-x-0 w-72 z-50">
    
    <div class="logo-container p-8 border-b border-slate-50">
        <a href="<?= $base_url; ?>dashboard.php" class="block w-full">
            <h1 class='text-2xl font-black italic uppercase tracking-tighter'>Gurukrupa<span class='text-orange-600'>.</span></h1>
            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-[0.2em]">ERP System v2.0</p>
        </a>
    </div>

    <nav class="flex-1 overflow-y-auto sidebar-scroll px-4 py-6">
        
        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] px-6 mb-3">Core</p>
        <a href="<?= $base_url; ?>dashboard.php" class="flex items-center gap-4 px-6 py-3.5 rounded-2xl text-sm font-bold text-white bg-slate-900 shadow-xl shadow-slate-200 transition-all mb-6">
            <i class="fas fa-grid-2 text-orange-500"></i> Dashboard
        </a>

        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] px-6 mb-3">Inventory & Catalog</p>
        
        <div class="menu-item group">
            <button onclick="toggleMenu(this)" class="w-full flex items-center justify-between px-6 py-3.5 rounded-2xl text-sm font-bold text-slate-500 hover:bg-slate-50 transition-all">
                <div class="flex items-center gap-4"><i class="fas fa-box-open"></i> Products</div>
               <i class="fas fa-chevron-down text-[10px] chevron transition-transform duration-300"></i>

            </button>
            <div class="submenu-container hidden pl-4">
                <a href="<?= $base_url; ?>products/products-list.php" class="block px-8 py-3 text-xs font-bold text-slate-400 hover:text-orange-600">All Products</a>
                <a href="<?= $base_url; ?>products/add-product.php" class="block px-8 py-3 text-xs font-bold text-slate-400 hover:text-orange-600">Add New</a>
                <a href="<?= $base_url; ?>products/product-variants.php" class="block px-8 py-3 text-xs font-bold text-slate-400 hover:text-orange-600">Variants</a>
                <a href="<?= $base_url; ?>products/bulk-upload.php" class="block px-8 py-3 text-xs font-bold text-slate-400 hover:text-orange-600">Bulk Upload</a>
            </div>
        </div>

        <div class="menu-item group mt-1">
            <button onclick="toggleMenu(this)" class="w-full flex items-center justify-between px-6 py-3.5 rounded-2xl text-sm font-bold text-slate-500 hover:bg-slate-50 transition-all">
                <div class="flex items-center gap-4"><i class="fas fa-warehouse"></i> Stock Control</div>
               <i class="fas fa-chevron-down text-[10px] chevron transition-transform duration-300"></i>

            </button>
            <div class="submenu-container hidden pl-4">
                <a href="<?= $base_url; ?>inventory/stock-list.php" class="block px-8 py-3 text-xs font-bold text-slate-400 hover:text-orange-600">Stock Levels</a>
                <a href="<?= $base_url; ?>inventory/stock-adjustment.php" class="block px-8 py-3 text-xs font-bold text-slate-400 hover:text-orange-600">Manual Adjust</a>
                <a href="<?= $base_url; ?>inventory/low-stock-alerts.php" class="block px-8 py-3 text-xs font-bold text-slate-400 hover:text-orange-600 text-red-400">Low Stock Alerts</a>
            </div>
        </div>

        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] px-6 mb-3 mt-8">Procurement</p>
        
        <div class="menu-item group">
            <button onclick="toggleMenu(this)" class="w-full flex items-center justify-between px-6 py-3.5 rounded-2xl text-sm font-bold text-slate-500 hover:bg-slate-50 transition-all">
                <div class="flex items-center gap-4"><i class="fas fa-truck-loading"></i> Purchase Cycle</div>
               <i class="fas fa-chevron-down text-[10px] chevron transition-transform duration-300"></i>

            </button>
            <div class="submenu-container hidden pl-4">
                <a href="<?= $base_url; ?>Purchases/Purchase Order.php" class="block px-8 py-3 text-xs font-bold text-slate-400 hover:text-orange-600">New Order (PO)</a>
                <a href="<?= $base_url; ?>Purchases/pending_pos.php" class="block px-8 py-3 text-xs font-bold text-slate-400 hover:text-orange-600">Pending Deliveries</a>
                <a href="<?= $base_url; ?>Purchases/Purchase Bill.php" class="block px-8 py-3 text-xs font-bold text-slate-400 hover:text-orange-600">Purchase Entry (Bill)</a>
            </div>
        </div>

        <div class="menu-item group mt-1">
            <button onclick="toggleMenu(this)" class="w-full flex items-center justify-between px-6 py-3.5 rounded-2xl text-sm font-bold text-slate-500 hover:bg-slate-50 transition-all">
                <div class="flex items-center gap-4"><i class="fas fa-handshake"></i> Suppliers</div>
               <i class="fas fa-chevron-down text-[10px] chevron transition-transform duration-300"></i>

            </button>
            <div class="submenu-container hidden pl-4">
                <a href="<?= $base_url; ?>suppliers/Supplier_List.php" class="block px-8 py-3 text-xs font-bold text-slate-400 hover:text-orange-600">Supplier Directory</a>
                <a href="<?= $base_url; ?>suppliers/Supplier_Ledger.php" class="block px-8 py-3 text-xs font-bold text-slate-400 hover:text-orange-600">Party Ledger</a>
            </div>
        </div>

        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] px-6 mb-3 mt-8">Finance</p>

        <div class="menu-item group">
            <button onclick="toggleMenu(this)" class="w-full flex items-center justify-between px-6 py-3.5 rounded-2xl text-sm font-bold text-slate-500 hover:bg-slate-50 transition-all">
                <div class="flex items-center gap-4"><i class="fas fa-wallet"></i> Account Payables</div>
               <i class="fas fa-chevron-down text-[10px] chevron transition-transform duration-300"></i>

            </button>
            <div class="submenu-container hidden pl-4">
                <a href="<?= $base_url; ?>Payments/Pending Bills.php" class="block px-8 py-3 text-xs font-bold text-slate-400 hover:text-orange-600">Unpaid Bills</a>
                <a href="<?= $base_url; ?>Payments/Make Payment.php" class="block px-8 py-3 text-xs font-bold text-slate-400 hover:text-orange-600">Pay Supplier</a>
                <a href="<?= $base_url; ?>Payments/Payment History.php" class="block px-8 py-3 text-xs font-bold text-slate-400 hover:text-orange-600">Disbursement History</a>
            </div>
        </div>

        <a href="<?= $base_url; ?>billing/invoices.php" class="flex items-center gap-4 px-6 py-3.5 rounded-2xl text-sm font-bold text-slate-500 hover:bg-slate-50 transition-all mt-1">
            <i class="fas fa-file-invoice-dollar"></i> Sales Invoices
        </a>

        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] px-6 mb-3 mt-8">Analytics</p>
        
        <div class="menu-item group">
            <button onclick="toggleMenu(this)" class="w-full flex items-center justify-between px-6 py-3.5 rounded-2xl text-sm font-bold text-slate-500 hover:bg-slate-50 transition-all">
                <div class="flex items-center gap-4"><i class="fas fa-chart-pie"></i> Reports Center</div>
               <i class="fas fa-chevron-down text-[10px] chevron transition-transform duration-300"></i>

            </button>
            <div class="submenu-container hidden pl-4">
                <a href="<?= $base_url; ?>reports/sales-report.php" class="block px-8 py-3 text-xs font-bold text-slate-400 hover:text-orange-600">Sales Report</a>
                <a href="<?= $base_url; ?>reports/inventory-report.php" class="block px-8 py-3 text-xs font-bold text-slate-400 hover:text-orange-600">Stock Value</a>
                <a href="<?= $base_url; ?>reports/supplier-report.php" class="block px-8 py-3 text-xs font-bold text-slate-400 hover:text-orange-600">Vendor Analysis</a>
                <a href="<?= $base_url; ?>reports/gst-report.php" class="block px-8 py-3 text-xs font-bold text-slate-400 hover:text-orange-600">Tax/GST Report</a>
            </div>
        </div>

        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] px-6 mb-3 mt-8">Admin</p>
        
        <a href="<?= $base_url; ?>users/users.php" class="flex items-center gap-4 px-6 py-3.5 rounded-2xl text-sm font-bold text-slate-500 hover:bg-slate-50 transition-all">
            <i class="fas fa-user-shield"></i> User Management
        </a>

        <a href="<?= $base_url; ?>settings/site-settings.php" class="flex items-center gap-4 px-6 py-3.5 rounded-2xl text-sm font-bold text-slate-500 hover:bg-slate-50 transition-all mt-1">
            <i class="fas fa-cog"></i> Settings
        </a>

    </nav>

    <div class="p-6 border-t border-slate-50">
        <a href="<?= $base_url; ?>logout.php" class="flex items-center gap-4 px-6 py-4 bg-red-50 text-red-500 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-red-500 hover:text-white transition-all">
            <i class="fas fa-power-off"></i> Logout
        </a>
    </div>
</aside>


    <div class="main-content flex flex-col min-h-screen">
        
        <header class="sticky top-0 z-40 bg-white/80 backdrop-blur-md border-b border-slate-100 px-4 md:px-6 py-3 flex items-center justify-between">
            
            <div class="flex items-center gap-4 flex-1">
                <button onclick="toggleSidebar()" class="lg:hidden w-10 h-10 bg-slate-900 text-white rounded-xl flex items-center justify-center active:scale-95 transition-transform">
                    <i class="fas fa-bars-staggered"></i>
                </button>

                <div class="search-wrapper hidden md:flex items-center gap-3 bg-slate-50 border border-slate-200 px-4 py-2.5 rounded-2xl w-full max-w-md transition-all">
                    <i class="fas fa-search text-slate-400 text-sm"></i>
                    <input type="text" placeholder="Find orders, clients, SKUs..." class="bg-transparent border-none outline-none text-sm font-semibold w-full">
                    <div class="flex items-center gap-1">
                        <span class="text-[9px] font-black text-slate-300 border border-slate-200 px-1.5 py-0.5 rounded bg-white uppercase">Ctrl + K</span>
                    </div>
                </div>
                
                <div class="lg:hidden">
                    <h1 class="text-xl font-black italic uppercase">GK<span class="text-orange-500">.</span></h1>
                </div>
            </div>

            <div class="flex items-center gap-3 md:gap-6">
                <button class="relative w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-slate-500">
                    <i class="far fa-bell text-lg"></i>
                    <span class="absolute top-2 right-2 w-2 h-2 bg-orange-600 rounded-full border-2 border-white shadow-sm"></span>
                </button>

                <div class="flex items-center gap-3 pl-3 md:pl-6 border-l border-slate-200 group cursor-pointer">
                    <div class="text-right hidden sm:block">
                        <p class="text-xs font-black text-slate-900"><?php echo $admin_name; ?></p>
                    </div>
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($admin_name); ?>&background=f1f5f9&color=1e293b&bold=true" class="w-10 h-10 rounded-xl border border-slate-200">
                </div>
            </div>
        </header>

      
<script>
    // 1. MOBILE SIDEBAR TOGGLE
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        
        sidebar.classList.toggle('translate-x-0'); // For Tailwind
        sidebar.classList.toggle('-translate-x-full'); 
        
        if (overlay) overlay.classList.toggle('hidden');
        
        // Prevent body scroll when mobile menu is open
        document.body.style.overflow = sidebar.classList.contains('translate-x-0') ? 'hidden' : 'auto';
    }

function toggleMenu(button) {
    const submenu = button.nextElementSibling;
    const chevron = button.querySelector('.chevron');
    
    // Check if it's currently open
    const isOpen = submenu.classList.contains('show');

    // Close all other open submenus (Accordion effect)
    document.querySelectorAll('.submenu-container').forEach(container => {
        container.classList.remove('show');
        const otherBtn = container.previousElementSibling;
        if(otherBtn && otherBtn.querySelector('.chevron')) {
            otherBtn.querySelector('.chevron').style.transform = 'rotate(0deg)';
        }
    });

    // Toggle the clicked one
    if (!isOpen) {
        submenu.classList.add('show');
        if(chevron) chevron.style.transform = 'rotate(180deg)';
    } else {
        submenu.classList.remove('show');
        if(chevron) chevron.style.transform = 'rotate(0deg)';
    }
}
    // 3. GLOBAL SEARCH SHORTCUT (Ctrl + K)
    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey && e.key === 'k') {
            e.preventDefault();
            const searchInput = document.querySelector('.search-wrapper input') || document.querySelector('input[type="search"]');
            if(searchInput) searchInput.focus();
        }
    });
</script>
</body>
</html>