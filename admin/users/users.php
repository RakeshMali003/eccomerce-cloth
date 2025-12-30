<?php
$base_path = $_SERVER['DOCUMENT_ROOT'] . '/ecommerce-website/';
include $base_path . 'includes/sidebar.php';

require_once $_SERVER['DOCUMENT_ROOT'] . '/ecommerce-website/config/database.php';

// ----- Customer Summary -----
$statsQuery = "
    SELECT 
        COUNT(*) AS total_customers,
        SUM(status = 'active') AS active_customers,
        SUM(status = 'inactive') AS suspended_customers
    FROM users
    WHERE role = 'user'
";
$stats = $pdo->query($statsQuery)->fetch(PDO::FETCH_ASSOC);

// SAFETY: Agar DB me koi row nahi hai
$total_customers     = $stats['total_customers'] ?? 0;
$active_customers    = $stats['active_customers'] ?? 0;
$suspended_customers = $stats['suspended_customers'] ?? 0;




// ---- Customers List ----
$customersQuery = "
    SELECT 
        u.user_id,
        u.name,
        u.email,
        u.phone,
        u.status,
        u.created_at,
        COUNT(o.order_id) AS total_orders,
        COALESCE(SUM(CASE 
            WHEN o.payment_status = 'paid' THEN o.total_amount 
            ELSE 0 
        END), 0) AS total_spent
    FROM users u
    LEFT JOIN orders o ON o.user_id = u.user_id
    WHERE u.role = 'user'
    GROUP BY u.user_id
    ORDER BY u.created_at DESC
";

$stmt = $pdo->prepare($customersQuery);
$stmt->execute();
$customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 🛡️ SAFETY FALLBACK
$customers = $customers ?: [];
?>


<main class="p-6 lg:p-12">
    
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-6">
        <div>
            <h2 class="text-4xl font-black tracking-tighter">Customers<span class="text-[#FF6F1E]">.</span></h2>
            <p class="text-slate-400 font-medium text-sm mt-1">Real-time Retail & Wholesale User Directory</p>
        </div>
        <button onclick="toggleModal(true)" class="bg-[#0f172a] text-white px-8 py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-[#FF6F1E] transition-all shadow-xl shadow-slate-200">
            Initialize New User
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
    <div class="kpi-card">
        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Database</p>
        <h3 class="text-3xl font-black mt-1"><?php echo $total_customers; ?></h3>
        <i class="fas fa-users absolute -right-4 -bottom-4 text-7xl text-slate-50"></i>
    </div>
    <div class="kpi-card" style="border-left: 5px solid #10b981;">
        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Verified Active</p>
        <h3 class="text-3xl font-black text-emerald-600 mt-1"><?php echo $active_customers; ?></h3>
    </div>
    <div class="kpi-card" style="border-left: 5px solid #ef4444;">
        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Suspended</p>
        <h3 class="text-3xl font-black text-red-600 mt-1"><?php echo $suspended_customers; ?></h3>
    </div>
</div>


    <div class="table-wrapper">
        <div class="overflow-x-auto">
            <table class="w-full text-left custom-table border-collapse">
                <thead>
                    <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                        <th class="px-8 py-6">Customer Identity</th>
                        <th class="px-6 py-6">Contact Details</th>
                        <th class="px-6 py-6">Registered</th>
                        <th class="px-6 py-6 text-center">Orders</th>
                        <th class="px-6 py-6">Total Spent</th>
                        <th class="px-6 py-6">Status</th>
                        <th class="px-8 py-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php foreach($customers as $user): ?>
                    <tr>
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-4">
                                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($user['name']); ?>&background=FF6F1E&color=fff&bold=true" class="w-11 h-11 rounded-xl shadow-sm">
                                <div>
                                    <span class="text-sm font-black block"><?php echo $user['name']; ?></span>
                                    <span class="text-[9px] font-bold text-slate-400 uppercase">UID: #<?php echo $user['user_id']; ?></span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <p class="text-xs font-bold"><?php echo $user['email']; ?></p>
                            <p class="text-[10px] text-slate-400 font-bold"><?php echo $user['phone'] ?: 'N/A'; ?></p>
                        </td>
                        <td class="px-6 py-5 text-[11px] font-bold text-slate-400 uppercase">
                            <?php echo date('d M, Y', strtotime($user['created_at'])); ?>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <span class="text-sm font-black bg-slate-50 px-3 py-1 rounded-lg"><?php echo $user['total_orders']; ?></span>
                        </td>
                        <td class="px-6 py-5 font-black">
                            ₹<?php echo number_format($user['total_spent'] ?? 0); ?>
                        </td>
                        <td class="px-6 py-5">
                            <span class="badge <?php echo ($user['status'] == 'active') ? 'badge-active' : 'badge-suspended'; ?>">
                                <?php echo ($user['status'] == 'active') ? 'Active' : 'Suspended'; ?>
                            </span>
                        </td>
                       <td class="px-8 py-5">
    <div class="flex justify-end gap-2">
        <button onclick="openViewModal(<?php echo htmlspecialchars(json_encode($user)); ?>)" 
                class="action-btn bg-blue-50 text-blue-500 hover:bg-blue-600 hover:text-white" title="Quick View">
            <i class="fas fa-eye"></i>
        </button>
        
        <button onclick="openEditModal(<?php echo htmlspecialchars(json_encode($user)); ?>)" 
                class="action-btn btn-edit" title="Edit Profile">
            <i class="fas fa-pencil"></i>
        </button>

        <form method="POST" action="process_user.php" onsubmit="return confirm('Archive user?')">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
            <button type="submit" class="action-btn btn-delete">
                <i class="fas fa-trash"></i>
            </button>
        </form>
    </div>
</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<div id="editUserModal" class="modal-overlay fixed inset-0 z-[100] items-center justify-center p-4" style="display:none;">
    <div class="bg-white w-full max-w-lg rounded-[3rem] shadow-2xl p-10 md:p-12">
        <h3 class="text-2xl font-black tracking-tight mb-2">Edit User Profile</h3>
        <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-8 text-blue-600">Update Account Information</p>
        
        <form action="process_user.php" method="POST" class="space-y-6">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="user_id" id="edit_user_id">
            
            <div class="space-y-1">
                <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Full Name</label>
                <input type="text" name="name" id="edit_name" required class="input-premium">
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Email</label>
                    <input type="email" name="email" id="edit_email" required class="input-premium">
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Phone</label>
                    <input type="text" name="phone" id="edit_phone" required class="input-premium">
                </div>
            </div>

            <div class="space-y-1">
                <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Account Status</label>
                <select name="status" id="edit_status" class="input-premium">
                    <option value="active">Active</option>
                    <option value="inactive">Suspended</option>
                </select>
            </div>

            <div class="flex gap-4 pt-6">
                <button type="button" onclick="closeModal('editUserModal')" class="flex-1 py-4 text-[10px] font-black uppercase text-slate-400">Cancel</button>
                <button type="submit" class="flex-1 bg-blue-600 text-white py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-xl">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<div id="viewUserModal" class="modal-overlay fixed inset-0 z-[100] items-center justify-center p-4" style="display:none;">
    <div class="bg-white w-full max-w-xl rounded-[3rem] shadow-2xl p-8 md:p-12 overflow-y-auto max-h-[90vh]">
        <div class="text-center mb-8">
            <div id="view_avatar" class="mb-4 flex justify-center"></div>
            <h3 id="view_name" class="text-2xl font-black text-slate-900 leading-tight"></h3>
            <div class="flex justify-center gap-2 mt-2">
                <span id="view_role_badge" class="px-3 py-1 bg-slate-100 text-[9px] font-black uppercase rounded-lg tracking-widest text-slate-500"></span>
                <span id="view_status_badge" class="px-3 py-1 text-[9px] font-black uppercase rounded-lg tracking-widest"></span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 border-t border-slate-100 pt-8">
            <div class="space-y-4">
                <div>
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Email Address</p>
                    <p id="view_email" class="text-sm font-bold text-slate-700"></p>
                </div>
                <div>
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Phone Number</p>
                    <p id="view_phone" class="text-sm font-bold text-slate-700"></p>
                </div>
                <div>
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Registration Date</p>
                    <p id="view_created" class="text-sm font-bold text-slate-700"></p>
                </div>
            </div>

            <div class="space-y-4 p-5 bg-slate-50 rounded-[2rem]">
                <div>
                    <p class="text-[9px] font-black text-orange-500 uppercase tracking-widest">Business Name</p>
                    <p id="view_business" class="text-sm font-bold text-slate-700 italic">Not Applicable</p>
                </div>
                <div>
                    <p class="text-[9px] font-black text-orange-500 uppercase tracking-widest">GST Number</p>
                    <p id="view_gst" class="text-sm font-bold text-slate-700">--</p>
                </div>
                <div>
                    <p class="text-[9px] font-black text-orange-500 uppercase tracking-widest">Wholesale Access</p>
                    <p id="view_wholesale_status" class="text-xs font-bold"></p>
                </div>
            </div>
        </div>

        <div class="mt-8 pt-8 border-t border-slate-100">
            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2">Primary Address</p>
            <p id="view_full_address" class="text-sm font-medium text-slate-600 leading-relaxed"></p>
            <div class="flex gap-6 mt-4">
                <div>
                    <p class="text-[9px] font-black text-slate-400 uppercase">City</p>
                    <p id="view_city" class="text-xs font-bold text-slate-700"></p>
                </div>
                <div>
                    <p class="text-[9px] font-black text-slate-400 uppercase">Pincode</p>
                    <p id="view_pincode" class="text-xs font-bold text-slate-700"></p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 mt-8 pt-8 border-t border-slate-100">
            <div class="bg-blue-50 p-4 rounded-2xl text-center">
                <p class="text-[9px] font-black text-blue-400 uppercase">Total Orders</p>
                <p id="view_orders" class="text-xl font-black text-blue-600"></p>
            </div>
            <div class="bg-emerald-50 p-4 rounded-2xl text-center">
                <p class="text-[9px] font-black text-emerald-400 uppercase">Lifetime Spent</p>
                <p id="view_spent" class="text-xl font-black text-emerald-600"></p>
            </div>
        </div>

        <button onclick="closeModal('viewUserModal')" class="mt-10 w-full py-5 bg-slate-900 text-white rounded-[1.5rem] text-[10px] font-black uppercase tracking-widest hover:bg-orange-600 transition-all shadow-xl">Close Detailed View</button>
    </div>
</div>


<div id="addUserModal" class="modal-overlay fixed inset-0 z-[100] items-center justify-center p-4">
    <div class="bg-white w-full max-w-lg rounded-[3rem] shadow-2xl p-10 md:p-12 animate-in fade-in zoom-in duration-300">
        <h3 class="text-2xl font-black tracking-tight mb-2">Add New User</h3>
        <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-8 text-orange-600">Customer Registration</p>
        
        <form action="process_user.php" method="POST" class="space-y-6">
            <div class="space-y-1">
                <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Full Name</label>
                <input type="text" name="name" required placeholder="Ex: Rahul Sharma" class="input-premium">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Email Address</label>
                    <input type="email" name="email" required placeholder="rahul@gk.com" class="input-premium">
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Phone</label>
                    <input type="text" name="phone" required placeholder="+91" class="input-premium">
                </div>
            </div>
            <div class="space-y-1">
                <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Account Status</label>
                <select name="status" class="input-premium">
                    <option value="active">Active</option>
                    <option value="inactive">Suspended</option>
                </select>
            </div>
            <div class="flex gap-4 pt-6">
                <button type="button" onclick="toggleModal(false)" class="flex-1 py-4 text-[10px] font-black uppercase tracking-widest text-slate-400">Cancel</button>
                <button type="submit" class="flex-1 bg-[#0f172a] text-white py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-xl shadow-slate-100 hover:bg-[#FF6F1E] transition-all">Add Customer</button>
            </div>
        </form>
    </div>
</div>

<script>
  function openEditModal(user) {
    document.getElementById('edit_user_id').value = user.user_id;
    document.getElementById('edit_name').value = user.name;
    document.getElementById('edit_email').value = user.email;
    document.getElementById('edit_phone').value = user.phone;
    document.getElementById('edit_status').value = user.status;
    
    document.getElementById('editUserModal').style.display = 'flex';
}

function closeModal(id) {
    document.getElementById(id).style.display = 'none';
    document.body.style.overflow = 'auto';
}

function openViewModal(user) {
    // Basic Details
    document.getElementById('view_name').innerText = user.name || 'N/A';
    document.getElementById('view_email').innerText = user.email || 'N/A';
    document.getElementById('view_phone').innerText = user.phone || 'N/A';
    document.getElementById('view_created').innerText = user.created_at;
    
    // Role and Status Badges
    document.getElementById('view_role_badge').innerText = user.role || 'USER';
    const statusBox = document.getElementById('view_status_badge');
    statusBox.innerText = user.status;
    statusBox.className = user.status === 'active' 
        ? 'px-3 py-1 text-[9px] font-black uppercase rounded-lg tracking-widest bg-emerald-100 text-emerald-600' 
        : 'px-3 py-1 text-[9px] font-black uppercase rounded-lg tracking-widest bg-red-100 text-red-600';

    // Business Details
    document.getElementById('view_business').innerText = user.business_name || 'Individual Retailer';
    document.getElementById('view_gst').innerText = user.gst_number || 'No GST Registered';
    
    const wholesaleLabel = document.getElementById('view_wholesale_status');
    wholesaleLabel.innerText = user.is_verified_wholesale == 1 ? 'VERIFIED DEALER' : 'RETAIL ONLY';
    wholesaleLabel.className = user.is_verified_wholesale == 1 ? 'text-xs font-bold text-emerald-600' : 'text-xs font-bold text-slate-400';

    // Address Details
    document.getElementById('view_full_address').innerText = user.address || 'No address provided';
    document.getElementById('view_city').innerText = user.city || '--';
    document.getElementById('view_pincode').innerText = user.pincode || '--';

    // Stats
    document.getElementById('view_orders').innerText = user.total_orders;
    document.getElementById('view_spent').innerText = '₹' + new Intl.NumberFormat('en-IN').format(user.total_spent);
    
    // Avatar
    document.getElementById('view_avatar').innerHTML = `
        <img src="https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&background=0f172a&color=fff&size=128&bold=true" 
        class="w-24 h-24 rounded-[2rem] shadow-2xl border-4 border-white">
    `;

    document.getElementById('viewUserModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
</script>

</body>
</html>