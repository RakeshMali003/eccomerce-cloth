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
            <span class="px-3 py-1 bg-purple-100 text-purple-700 text-[9px] font-black uppercase rounded-lg">Logistic
                Fulfillment</span>
            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Order
                #ORD-<?= $o['order_id'] ?></span>
        </div>
        <h3 class="text-3xl font-black text-slate-900 tracking-tighter italic">Courier Assignment<span
                class="text-purple-600">.</span></h3>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-10 mb-10 pb-10 border-b border-slate-50">
        <div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Destination Details</p>
            <h4 class="text-lg font-black text-slate-900"><?= htmlspecialchars($o['name']) ?></h4>
            <div class="text-sm text-slate-500 mt-2 font-medium leading-relaxed">
                <?= htmlspecialchars($o['address']) ?><br>
                <?= htmlspecialchars($o['city']) ?> | <?= $o['phone'] ?>
            </div>
        </div>
        <div class="bg-slate-50 p-6 rounded-[2rem] border border-slate-100">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Order Progress</p>
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div>
                    <p class="text-xs font-black text-slate-900 uppercase">Ready for Dispatch</p>
                    <p class="text-[9px] text-slate-400 font-bold">Items packed and verified</p>
                </div>
            </div>
        </div>
    </div>

    <form id="shippingForm" class="space-y-8">
        <input type="hidden" name="order_id" value="<?= $o['order_id'] ?>">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="space-y-2">
                <label class="text-[10px] font-black uppercase text-slate-400 ml-4">Logistic Partner</label>
                <div class="relative">
                    <i class="fas fa-truck-fast absolute left-5 top-1/2 -translate-y-1/2 text-slate-300"></i>
                    <select name="courier_name" required
                        class="w-full bg-slate-50 pl-14 pr-6 py-5 rounded-[2rem] text-sm font-black outline-none border-2 border-transparent focus:border-purple-500 transition-all appearance-none">
                        <option value="">Choose Courier</option>
                        <option value="Delhivery">Delhivery Direct</option>
                        <option value="BlueDart">BlueDart Express</option>
                        <option value="Amazon Shipping">Amazon Logistics</option>
                        <option value="Ecom Express">Ecom Express</option>
                        <option value="Trackon">Trackon Prime</option>
                        <option value="XpressBees">XpressBees</option>
                        <option value="Shadowfax">Shadowfax</option>
                        <option value="Self / Local">Local Hand-Delivery</option>
                    </select>
                </div>
            </div>

            <div class="space-y-2">
                <label class="text-[10px] font-black uppercase text-slate-400 ml-4">Tracking ID / AWB No.</label>
                <div class="relative">
                    <i class="fas fa-hashtag absolute left-5 top-1/2 -translate-y-1/2 text-slate-300"></i>
                    <input type="text" name="tracking_id" required placeholder="Ex: 556781290"
                        class="w-full bg-slate-50 pl-14 pr-6 py-5 rounded-[2rem] text-sm font-black outline-none border-2 border-transparent focus:border-purple-500 transition-all">
                </div>
            </div>
        </div>

        <div class="space-y-2">
            <label class="text-[10px] font-black uppercase text-slate-400 ml-4">Shipment Memo</label>
            <textarea name="notes" placeholder="Note: Fragile items, handle with care..."
                class="w-full bg-slate-50 p-6 rounded-[2rem] text-sm font-bold outline-none border-2 border-transparent focus:border-purple-500 transition-all h-32"></textarea>
        </div>

        <div class="flex gap-4 pt-4">
            <button type="button" onclick="closeModal()"
                class="flex-1 py-6 text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-900 transition-all">Cancel</button>
            <button type="submit"
                class="flex-[2] bg-slate-900 text-white py-6 rounded-[2.5rem] font-black uppercase tracking-[0.2em] hover:bg-purple-600 transition-all shadow-xl shadow-purple-50">
                Finalize & Ship Order
            </button>
        </div>
    </form>
</div>

<script>
    document.getElementById('shippingForm').addEventListener('submit', function (e) {
        e.preventDefault();
        const btn = this.querySelector('button[type="submit"]');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-circle-notch fa-spin mr-2"></i> Syncing Data...';

        const formData = new FormData(this);
        fetch('process-shipping.php', {
            method: 'POST',
            body: formData
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    notify("Order #ORD-<?= $o['order_id'] ?> is now Shipped!", "success");
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