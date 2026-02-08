<?php
$base_path = $_SERVER['DOCUMENT_ROOT'] . '/ecommerce-website/';
require_once "../../config/database.php";

include $base_path . 'includes/admin-header.php';
include $base_path . 'includes/sidebar.php';
include $base_path . 'includes/notifications.php';

// Fetch users for target options
$stmt = $pdo->query("SELECT user_id, first_name, last_name, email FROM users WHERE role = 'customer' ORDER BY first_name");
$customers = $stmt->fetchAll();
?>

<main class="p-6 lg:p-12 bg-[#f8fafc] min-h-screen">
    <div class="mb-10 flex justify-between items-center">
        <div>
            <h2 class="text-3xl font-black tracking-tighter text-slate-900">Push Hub<span class="text-rose-600">.</span>
            </h2>
            <p class="text-slate-400 text-sm font-medium">Broadcast announcements, deals, and critical alerts to your
                user base.</p>
        </div>
        <a href="notification-history.php"
            class="bg-white border border-slate-200 px-6 py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest hover:bg-slate-50 transition-all shadow-sm">
            <i class="fas fa-history mr-2"></i> Sent History
        </a>
    </div>

    <form action="process_notification.php" method="POST" class="max-w-4xl space-y-10">
        <div class="bg-white p-10 rounded-[3rem] border border-slate-100 shadow-sm space-y-8">
            <div class="space-y-2">
                <label class="text-[10px] font-black uppercase text-slate-400 ml-4">Target Audience</label>
                <div class="relative">
                    <i class="fas fa-users absolute left-5 top-1/2 -translate-y-1/2 text-slate-300"></i>
                    <select name="target" id="notif_target" required onchange="toggleUserSelect()"
                        class="w-full bg-slate-50 pl-14 pr-6 py-5 rounded-[2rem] text-sm font-black outline-none border-2 border-transparent focus:border-rose-500 transition-all appearance-none">
                        <option value="all">All Registered Customers</option>
                        <option value="specific">Specific Individual</option>
                    </select>
                </div>
            </div>

            <div id="user_select_container" class="space-y-2 hidden">
                <label class="text-[10px] font-black uppercase text-slate-400 ml-4">Select Recipient</label>
                <div class="relative">
                    <i class="fas fa-search absolute left-5 top-1/2 -translate-y-1/2 text-slate-300"></i>
                    <select name="user_id"
                        class="w-full bg-slate-50 pl-14 pr-6 py-5 rounded-[2rem] text-sm font-black outline-none border-2 border-transparent focus:border-rose-500 transition-all appearance-none">
                        <?php foreach ($customers as $c): ?>
                            <option value="<?= $c['user_id'] ?>">
                                <?= htmlspecialchars($c['first_name'] . ' ' . $c['last_name'] . ' (' . $c['email'] . ')') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase text-slate-400 ml-4">Message Category</label>
                    <select name="type" required
                        class="w-full bg-slate-50 px-8 py-5 rounded-[2rem] text-sm font-black outline-none border-2 border-transparent focus:border-rose-500 transition-all appearance-none">
                        <option value="promotion">Marketing Promotion</option>
                        <option value="system">System Announcement</option>
                        <option value="order">Order Update</option>
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase text-slate-400 ml-4">Urgency / Alert Style</label>
                    <select name="priority"
                        class="w-full bg-slate-50 px-8 py-5 rounded-[2rem] text-sm font-black outline-none border-2 border-transparent focus:border-rose-500 transition-all appearance-none">
                        <option value="normal">Default</option>
                        <option value="high">High Priority (Red Badge)</option>
                    </select>
                </div>
            </div>

            <div class="space-y-2">
                <label class="text-[10px] font-black uppercase text-slate-400 ml-4">Headline / Title</label>
                <div class="relative">
                    <input type="text" name="title" required placeholder="Ex: Summer Sale is Live! ☀️"
                        class="w-full bg-slate-50 px-8 py-5 rounded-[2rem] text-sm font-black outline-none border-2 border-transparent focus:border-rose-500 transition-all">
                </div>
            </div>

            <div class="space-y-2">
                <label class="text-[10px] font-black uppercase text-slate-400 ml-4">Message Content</label>
                <textarea name="message" required placeholder="Type your broadcast message here..."
                    class="w-full bg-slate-50 p-8 rounded-[3rem] text-sm font-black outline-none border-2 border-transparent focus:border-rose-500 transition-all min-h-[150px]"></textarea>
            </div>
        </div>

        <button type="submit"
            class="w-full bg-slate-900 text-white py-8 rounded-[3rem] font-black uppercase tracking-[0.3em] shadow-2xl shadow-slate-200 hover:bg-rose-600 transition-all group">
            Dispatch Notification <i class="fas fa-paper-plane ml-4 group-hover:translate-x-2 transition-transform"></i>
        </button>
    </form>
</main>
<?php include $base_path . 'includes/admin-footer.php'; ?>
<script>
    function toggleUserSelect() {
        const target = document.getElementById('notif_target').value;
        const container = document.getElementById('user_select_container');
        if (target === 'specific') {
            container.classList.remove('hidden');
        } else {
            container.classList.add('hidden');
        }
    }
</script>
</body>

</html>