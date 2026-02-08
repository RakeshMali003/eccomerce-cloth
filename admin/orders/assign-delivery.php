<?php
require_once "../../config/database.php";
$id = $_GET['id'] ?? null;

if (!$id)
    die("Order ID required.");

$order = $pdo->prepare("SELECT o.*, u.name, u.address, u.city, u.phone FROM orders o JOIN users u ON o.user_id = u.user_id WHERE o.order_id = ?");
$order->execute([$id]);
$o = $order->fetch();
?>

<div class="p-8 md:p-12 bg-white">
    <div class="mb-10">
        <div class="flex items-center gap-3 mb-2">
            <span class="px-3 py-1 bg-emerald-100 text-emerald-700 text-[9px] font-black uppercase rounded-lg">Last-Mile
                Delivery</span>
            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Order #ORD-
                <?= $o['order_id'] ?>
            </span>
        </div>
        <h3 class="text-3xl font-black text-slate-900 tracking-tighter italic">Local Dispatch<span
                class="text-emerald-600">.</span></h3>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-10 mb-10 pb-10 border-b border-slate-50">
        <div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Customer Location</p>
            <h4 class="text-lg font-black text-slate-900">
                <?= htmlspecialchars($o['name']) ?>
            </h4>
            <div class="text-sm text-slate-500 mt-2 font-medium leading-relaxed">
                <?= htmlspecialchars($o['address']) ?><br>
                <?= htmlspecialchars($o['city']) ?> |
                <?= $o['phone'] ?>
            </div>
        </div>
        <div class="bg-slate-50 p-6 rounded-[2rem] border border-slate-100">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Delivery Contact</p>
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-slate-900 text-white flex items-center justify-center">
                    <i class="fas fa-phone-alt animate-pulse"></i>
                </div>
                <div>
                    <p class="text-xs font-black text-slate-900 uppercase">Call Customer</p>
                    <p class="text-[9px] text-slate-400 font-bold">
                        <?= $o['phone'] ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <form id="deliveryForm" class="space-y-8">
        <input type="hidden" name="order_id" value="<?= $o['order_id'] ?>">
        <input type="hidden" name="courier_name" value="Self / Local">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="space-y-2">
                <label class="text-[10px] font-black uppercase text-slate-400 ml-4">Assign Delivery Executive</label>
                <div class="relative">
                    <i class="fas fa-user-tag absolute left-5 top-1/2 -translate-y-1/2 text-slate-300"></i>
                    <select name="tracking_id" required
                        class="w-full bg-slate-50 pl-14 pr-6 py-5 rounded-[2rem] text-sm font-black outline-none border-2 border-transparent focus:border-emerald-500 transition-all appearance-none">
                        <option value="">Select Staff</option>
                        <option value="Staff-01">Rajesh Kumar</option>
                        <option value="Staff-02">Suresh Singh</option>
                        <option value="Staff-03">Amit Patel</option>
                        <option value="External">External Porter/Dunzo</option>
                    </select>
                </div>
            </div>

            <div class="space-y-2">
                <label class="text-[10px] font-black uppercase text-slate-400 ml-4">Estimated Time</label>
                <div class="relative">
                    <i class="fas fa-clock absolute left-5 top-1/2 -translate-y-1/2 text-slate-300"></i>
                    <select name="delivery_eta"
                        class="w-full bg-slate-50 pl-14 pr-6 py-5 rounded-[2rem] text-sm font-black outline-none border-2 border-transparent focus:border-emerald-500 transition-all appearance-none">
                        <option value="30 mins">Within 30 Mins</option>
                        <option value="1 hour">Within 1 Hour</option>
                        <option value="2 hours">Within 2 Hours</option>
                        <option value="Today">By End of Day</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="space-y-2">
            <label class="text-[10px] font-black uppercase text-slate-400 ml-4">Dispatch Notes</label>
            <textarea name="notes" placeholder="Note: Gate code is 1234, or call on arrival..."
                class="w-full bg-slate-50 p-6 rounded-[2rem] text-sm font-bold outline-none border-2 border-transparent focus:border-emerald-500 transition-all h-32"></textarea>
        </div>

        <div class="flex gap-4 pt-4">
            <button type="button" onclick="closeModal()"
                class="flex-1 py-6 text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-900 transition-all">Cancel</button>
            <button type="submit"
                class="flex-[2] bg-slate-900 text-white py-6 rounded-[2.5rem] font-black uppercase tracking-[0.2em] hover:bg-emerald-600 transition-all shadow-xl shadow-emerald-50">
                Dispatch Now
            </button>
        </div>
    </form>
</div>

<script>
    document.getElementById('deliveryForm').addEventListener('submit', function (e) {
        e.preventDefault();
        const btn = this.querySelector('button[type="submit"]');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-motorcycle fa-spin mr-2"></i> Dispatching...';

        const formData = new FormData(this);
        fetch('process-shipping.php', {
            method: 'POST',
            body: formData
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    notify("Order #ORD-<?= $o['order_id'] ?> is out for Delivery!", "success");
                    setTimeout(() => location.reload(), 1500);
                } else {
                    alert("Error: " + data.error);
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }
            })
            .catch(err => {
                alert("Connection lost. Please try again.");
                btn.disabled = false;
                btn.innerHTML = originalText;
            });
    });
</script>