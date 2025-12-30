 <?php
include '..\includes\sidebar.php';


?>

    <main class="p-4 md:p-10 flex-1">
    <div class="md:hidden mb-6">
        <div class="flex items-center gap-3 bg-white border border-slate-200 px-4 py-3 rounded-2xl shadow-sm">
            <i class="fas fa-search text-slate-400"></i>
            <input type="text" placeholder="Search system..." class="bg-transparent outline-none text-sm font-semibold w-full">
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-md transition-all group">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 bg-orange-50 text-orange-600 rounded-2xl flex items-center justify-center text-xl group-hover:bg-orange-600 group-hover:text-white transition-all">
                    <i class="fas fa-indian-rupee-sign"></i>
                </div>
                <span class="text-[10px] font-black text-emerald-500 bg-emerald-50 px-2 py-1 rounded-lg">+12.5%</span>
            </div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Today's Sales</p>
            <h3 class="text-2xl font-black text-slate-900 mt-1">₹42,850</h3>
        </div>

        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-md transition-all group">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center text-xl group-hover:bg-blue-600 group-hover:text-white transition-all">
                    <i class="fas fa-shopping-bag"></i>
                </div>
                <span class="text-[10px] font-black text-slate-400 bg-slate-50 px-2 py-1 rounded-lg">Realtime</span>
            </div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Today's Orders</p>
            <h3 class="text-2xl font-black text-slate-900 mt-1">18</h3>
        </div>

        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm hover:shadow-md transition-all group">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 bg-purple-50 text-purple-600 rounded-2xl flex items-center justify-center text-xl group-hover:bg-purple-600 group-hover:text-white transition-all">
                    <i class="fas fa-user-plus"></i>
                </div>
            </div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">New Customers</p>
            <h3 class="text-2xl font-black text-slate-900 mt-1">12</h3>
        </div>

        <div class="bg-red-50 p-6 rounded-[2rem] border border-red-100 shadow-sm hover:shadow-md transition-all group cursor-pointer">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 bg-white text-red-600 rounded-2xl flex items-center justify-center text-xl shadow-sm">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
            </div>
            <p class="text-[10px] font-black text-red-400 uppercase tracking-widest">Low Stock Alert</p>
            <h3 class="text-2xl font-black text-red-600 mt-1">04 Items</h3>
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <a href="#" class="bg-amber-50 border border-amber-100 p-4 rounded-2xl text-center hover:scale-105 transition-transform">
            <p class="text-[9px] font-black text-amber-600 uppercase">Pending</p>
            <h4 class="text-xl font-black text-amber-700">12</h4>
        </a>
        <a href="#" class="bg-sky-50 border border-sky-100 p-4 rounded-2xl text-center hover:scale-105 transition-transform">
            <p class="text-[9px] font-black text-sky-600 uppercase">Processing</p>
            <h4 class="text-xl font-black text-sky-700">08</h4>
        </a>
        <a href="#" class="bg-indigo-50 border border-indigo-100 p-4 rounded-2xl text-center hover:scale-105 transition-transform">
            <p class="text-[9px] font-black text-indigo-600 uppercase">Shipped</p>
            <h4 class="text-xl font-black text-indigo-700">24</h4>
        </a>
        <a href="#" class="bg-slate-50 border border-slate-100 p-4 rounded-2xl text-center hover:scale-105 transition-transform">
            <p class="text-[9px] font-black text-slate-600 uppercase">Returns</p>
            <h4 class="text-xl font-black text-slate-700">03</h4>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <div class="lg:col-span-8 bg-white rounded-[2.5rem] p-8 border border-slate-100 shadow-sm">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-black text-slate-900">Recent Logistics</h3>
                <div class="flex gap-2">
                    <button class="bg-slate-50 text-slate-400 p-2 rounded-lg hover:text-orange-600"><i class="fas fa-filter text-xs"></i></button>
                    <a href="orders.php" class="text-[10px] font-black text-orange-600 uppercase tracking-widest bg-orange-50 px-4 py-2 rounded-xl">View All</a>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-[10px] font-black text-slate-300 uppercase tracking-widest border-b border-slate-50">
                            <th class="pb-4 px-2">Order ID</th>
                            <th class="pb-4">Customer</th>
                            <th class="pb-4">Amount</th>
                            <th class="pb-4">Mode</th>
                            <th class="pb-4">Status</th>
                            <th class="pb-4 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <tr class="group hover:bg-slate-50/50 transition-colors">
                            <td class="py-4 px-2 font-bold text-sm text-slate-400">#GK-9921</td>
                            <td class="py-4">
                                <p class="text-sm font-bold text-slate-900">Rajesh Kumar</p>
                                <p class="text-[10px] text-slate-400">Mumbai, MH</p>
                            </td>
                            <td class="py-4 font-black text-slate-900">₹4,200</td>
                            <td class="py-4"><span class="text-[9px] font-bold text-blue-500 border border-blue-100 px-2 py-0.5 rounded-md uppercase">Online</span></td>
                            <td class="py-4"><span class="text-[9px] font-black text-amber-600 bg-amber-50 px-2 py-1 rounded-full uppercase">Processing</span></td>
                            <td class="py-4 text-right">
                                <button class="w-8 h-8 rounded-lg bg-slate-100 text-slate-400 hover:bg-orange-600 hover:text-white transition-all"><i class="fas fa-pencil text-xs"></i></button>
                            </td>
                        </tr>
                        </tbody>
                </table>
            </div>
        </div>

        <div class="lg:col-span-4 space-y-8">
            
            <div class="bg-slate-900 rounded-[2.5rem] p-8 shadow-xl shadow-slate-200">
                <h3 class="text-white text-sm font-black uppercase tracking-widest mb-6">Quick Launch</h3>
                <div class="grid grid-cols-2 gap-4">
                    <button class="flex flex-col items-center gap-2 p-4 bg-white/5 rounded-2xl hover:bg-orange-600 transition-all group">
                        <i class="fas fa-plus-circle text-orange-500 group-hover:text-white"></i>
                        <span class="text-[9px] font-bold text-slate-400 group-hover:text-white uppercase">Product</span>
                    </button>
                    <button class="flex flex-col items-center gap-2 p-4 bg-white/5 rounded-2xl hover:bg-orange-600 transition-all group">
                        <i class="fas fa-file-invoice text-blue-400 group-hover:text-white"></i>
                        <span class="text-[9px] font-bold text-slate-400 group-hover:text-white uppercase">Invoice</span>
                    </button>
                    <button class="flex flex-col items-center gap-2 p-4 bg-white/5 rounded-2xl hover:bg-orange-600 transition-all group">
                        <i class="fas fa-boxes-stacked text-purple-400 group-hover:text-white"></i>
                        <span class="text-[9px] font-bold text-slate-400 group-hover:text-white uppercase">Add Stock</span>
                    </button>
                    <button class="flex flex-col items-center gap-2 p-4 bg-white/5 rounded-2xl hover:bg-orange-600 transition-all group">
                        <i class="fas fa-ticket text-emerald-400 group-hover:text-white"></i>
                        <span class="text-[9px] font-bold text-slate-400 group-hover:text-white uppercase">Coupon</span>
                    </button>
                </div>
            </div>

            <div class="bg-white rounded-[2.5rem] p-8 border border-slate-100 shadow-sm">
                <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-6 flex items-center gap-2">
                    <i class="fas fa-triangle-exclamation text-red-500"></i> Stock Alerts
                </h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-3 bg-red-50 rounded-2xl border border-red-100">
                        <div>
                            <p class="text-xs font-bold text-slate-900">Cotton Printed Saree</p>
                            <p class="text-[10px] text-red-500 font-bold italic">Only 2 left</p>
                        </div>
                        <button class="bg-white text-red-600 text-[10px] font-black px-3 py-1.5 rounded-xl shadow-sm border border-red-100">Stock +</button>
                    </div>
                    </div>
            </div>
        </div>

        <div class="lg:col-span-8 bg-white rounded-[2.5rem] p-8 border border-slate-100 shadow-sm">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-black text-slate-900">Sales Analytics</h3>
                <select class="bg-slate-50 border-none rounded-xl text-[10px] font-black uppercase px-3 py-2 outline-none">
                    <option>Weekly View</option>
                    <option>Monthly View</option>
                </select>
            </div>
            <div class="h-64">
                <canvas id="salesChart"></canvas>
            </div>
        </div>

        <div class="lg:col-span-4 bg-white rounded-[2.5rem] p-8 border border-slate-100 shadow-sm">
            <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-6">Payment Pulse</h3>
            <div class="space-y-6">
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                        <span class="text-xs font-bold text-slate-500">Online Received</span>
                    </div>
                    <span class="text-sm font-black text-slate-900">₹3.2L</span>
                </div>
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 rounded-full bg-amber-500"></div>
                        <span class="text-xs font-bold text-slate-500">COD Pending</span>
                    </div>
                    <span class="text-sm font-black text-slate-900">₹1.8L</span>
                </div>
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 rounded-full bg-red-500"></div>
                        <span class="text-xs font-bold text-slate-500">Refunded</span>
                    </div>
                    <span class="text-sm font-black text-slate-900">₹12,400</span>
                </div>
                <div class="pt-4 mt-4 border-t border-slate-50">
                    <div class="flex justify-between mb-2">
                        <span class="text-[10px] font-black text-slate-400 uppercase">Target (₹10L)</span>
                        <span class="text-[10px] font-black text-orange-600">50%</span>
                    </div>
                    <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                        <div class="bg-orange-600 h-full" style="width: 50%"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('salesChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            datasets: [{
                label: 'Sales (₹)',
                data: [12000, 19000, 15000, 25000, 22000, 30000, 42850],
                borderColor: '#FF6F1E',
                backgroundColor: 'rgba(255, 111, 30, 0.1)',
                fill: true,
                tension: 0.4,
                borderWidth: 4,
                pointRadius: 0,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { display: false },
                x: { grid: { display: false }, ticks: { font: { weight: 'bold', size: 10 } } }
            }
        }
    });
</script>