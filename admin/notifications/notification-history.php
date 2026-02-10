<?php
$base_path = __DIR__ . '/../../';
require_once "../../config/database.php";

include $base_path . 'includes/admin-header.php';
include $base_path . 'includes/sidebar.php';
include $base_path . 'includes/notifications.php';

// Fetch history
$stmt = $pdo->query("SELECT n.*, u.first_name, u.last_name, u.email 
                   FROM notifications n 
                   LEFT JOIN users u ON n.user_id = u.user_id 
                   ORDER BY n.created_at DESC LIMIT 100");
$history = $stmt->fetchAll();
?>

<main class="p-6 lg:p-12 bg-[#f8fafc] min-h-screen">
    <div class="mb-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
        <div>
            <h2 class="text-3xl font-black tracking-tighter text-slate-900">Communication Logs<span
                    class="text-rose-600">.</span></h2>
            <p class="text-slate-400 text-sm font-medium">Tracking historical broadcasts and personalized alerts sent to
                customers.</p>
        </div>
        <a href="send-notification.php"
            class="bg-slate-900 text-white px-8 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-rose-600 transition-all shadow-xl shadow-slate-200">
            <i class="fas fa-paper-plane mr-2"></i> New Broadcast
        </a>
    </div>

    <div class="bg-white rounded-[3rem] border border-slate-100 shadow-sm overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-slate-50 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                <tr>
                    <th class="px-8 py-6">Timestamp</th>
                    <th class="px-6 py-6">Recipient</th>
                    <th class="px-6 py-6">Preview</th>
                    <th class="px-6 py-6 text-center">Type</th>
                    <th class="px-8 py-6 text-right">State</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <?php foreach ($history as $h): ?>
                    <tr class="hover:bg-slate-50 transition-all group">
                        <td class="px-8 py-5">
                            <p class="text-xs font-black text-slate-900">
                                <?= date('d M, H:i', strtotime($h['created_at'])) ?>
                            </p>
                            <p class="text-[9px] text-slate-400 font-bold uppercase">
                                <?= date('Y', strtotime($h['created_at'])) ?>
                            </p>
                        </td>
                        <td class="px-6 py-5">
                            <?php if ($h['user_id']): ?>
                                <p class="text-[11px] font-black text-slate-900">
                                    <?= htmlspecialchars($h['first_name'] . ' ' . $h['last_name']) ?>
                                </p>
                                <p class="text-[9px] text-slate-400 font-bold underline">
                                    <?= htmlspecialchars($h['email']) ?>
                                </p>
                            <?php else: ?>
                                <span
                                    class="text-[9px] font-black text-indigo-500 uppercase bg-indigo-50 px-2 py-1 rounded">Global
                                    Broadcast</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-5">
                            <p class="text-[11px] font-black text-slate-900 limit-text-1">
                                <?= htmlspecialchars($h['title']) ?>
                            </p>
                            <p class="text-[10px] text-slate-400 font-medium limit-text-1">
                                <?= htmlspecialchars($h['message']) ?>
                            </p>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <span class="px-3 py-1 rounded-lg text-[9px] font-black uppercase bg-slate-100 text-slate-400">
                                <?= $h['type'] ?>
                            </span>
                        </td>
                        <td class="px-8 py-5 text-right">
                            <span
                                class="text-[10px] font-black uppercase <?= $h['status'] == 'read' ? 'text-emerald-500' : 'text-amber-500' ?>">
                                <i class="fas <?= $h['status'] == 'read' ? 'fa-check-double' : 'fa-check' ?> mr-1"></i>
                                <?= $h['status'] ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($history)): ?>
                    <tr>
                        <td colspan="5" class="px-8 py-20 text-center">
                            <i class="fas fa-envelope-open text-slate-100 text-6xl mb-4"></i>
                            <p class="text-sm font-black text-slate-300 uppercase tracking-widest">Silence is golden. No
                                logs found.</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>
<?php include $base_path . 'includes/admin-footer.php'; ?>
<style>
    .limit-text-1 {
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
</body>

</html>