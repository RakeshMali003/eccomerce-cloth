<?php
$base_path = __DIR__ . '/../../';
require_once "../../config/database.php";
require_once "../../includes/functions.php";

include $base_path . 'includes/admin-header.php';
include $base_path . 'includes/sidebar.php';
include $base_path . 'includes/notifications.php';

if (!has_permission('dashboard')) { // Restricted but open to most admins
    die("Access Denied");
}

// Fetch Inquiries
$stmt = $pdo->query("SELECT * FROM inquiries ORDER BY created_at DESC");
$inquiries = $stmt->fetchAll();
?>

<main class="p-6 lg:p-12 bg-[#f8fafc] min-h-screen">
    <div class="mb-10">
        <h2 class="text-3xl font-black tracking-tighter text-slate-900">Inquiry Inbox<span
                class="text-orange-600">.</span></h2>
        <p class="text-slate-400 text-sm font-medium">Messages from customers and visitors</p>
    </div>

    <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-slate-50 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                <tr>
                    <th class="px-8 py-6">Date</th>
                    <th class="px-6 py-6">Sender Details</th>
                    <th class="px-6 py-6">Type</th>
                    <th class="px-6 py-6 w-1/2">Message</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php foreach ($inquiries as $i): ?>
                    <tr class="hover:bg-slate-50/50 transition-all">
                        <td class="px-8 py-5 text-xs font-bold text-slate-500">
                            <?= date('d M Y, h:i A', strtotime($i['created_at'])) ?>
                        </td>
                        <td class="px-6 py-5">
                            <p class="text-sm font-black text-slate-900">
                                <?= htmlspecialchars($i['name']) ?>
                            </p>
                            <p class="text-xs text-slate-500">
                                <?= htmlspecialchars($i['email']) ?>
                            </p>
                            <p class="text-xs text-slate-500">
                                <?= htmlspecialchars($i['phone']) ?>
                            </p>
                        </td>
                        <td class="px-6 py-5">
                            <span
                                class="px-3 py-1 rounded-lg bg-orange-100 text-orange-600 text-[9px] font-black uppercase">
                                <?= htmlspecialchars($i['type']) ?>
                            </span>
                        </td>
                        <td class="px-6 py-5 text-sm text-slate-600 leading-relaxed">
                            <?= nl2br(htmlspecialchars($i['message'])) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($inquiries)): ?>
                    <tr>
                        <td colspan="4" class="px-8 py-10 text-center text-slate-400 text-sm font-bold">No inquiries found.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>
<?php include $base_path . "includes/admin-footer.php"; ?>