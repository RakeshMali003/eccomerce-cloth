<?php
session_start();
$base_path = $_SERVER['DOCUMENT_ROOT'] . '/ecommerce-website/';
require_once "../../config/database.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch delivered orders for this user
$stmt = $pdo->prepare("SELECT order_id, created_at, total_amount FROM orders WHERE user_id = ? AND order_status = 'delivered' ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Initiate Return | Thread & Trend</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;900&display=swap');

        body {
            font-family: 'Outfit', sans-serif;
        }
    </style>
</head>

<body class="bg-slate-50 min-h-screen">
    <div class="max-w-4xl mx-auto px-6 py-12">
        <div class="mb-10 text-center">
            <h1 class="text-4xl font-black tracking-tighter text-slate-900 mb-2">Return Portal<span
                    class="text-orange-600">.</span></h1>
            <p class="text-slate-400 font-medium italic">Easy returns, instant trust.</p>
        </div>

        <?php if (empty($orders)): ?>
            <div class="bg-white p-20 rounded-[3rem] text-center border border-slate-100 shadow-sm">
                <i class="fas fa-box-open text-slate-100 text-7xl mb-6"></i>
                <h3 class="text-xl font-black text-slate-900 mb-2 uppercase">No returnable orders</h3>
                <p class="text-slate-400 text-sm max-w-sm mx-auto">Only orders with 'Delivered' status are eligible for
                    returns within our 30-day window.</p>
                <a href="../products/product-list.php"
                    class="inline-block mt-8 bg-slate-900 text-white px-10 py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-orange-600 transition-all">Back
                    to Shop</a>
            </div>
        <?php else: ?>
            <form action="process-return.php" method="POST" class="space-y-10">
                <div class="bg-white p-10 rounded-[3rem] border border-slate-100 shadow-sm space-y-8">
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase text-slate-400 ml-4">Select Order</label>
                        <select name="order_id" required
                            class="w-full bg-slate-50 px-8 py-5 rounded-[2rem] text-sm font-black outline-none border-2 border-transparent focus:border-orange-500 transition-all appearance-none">
                            <option value="">Choose an order to return</option>
                            <?php foreach ($orders as $o): ?>
                                <option value="<?= $o['order_id'] ?>">Order #ORD-
                                    <?= $o['order_id'] ?> (₹
                                    <?= number_format($o['total_amount'], 2) ?>) -
                                    <?= date('d M Y', strtotime($o['created_at'])) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase text-slate-400 ml-4">Reason for Return</label>
                        <select name="reason" required
                            class="w-full bg-slate-50 px-8 py-5 rounded-[2rem] text-sm font-black outline-none border-2 border-transparent focus:border-orange-500 transition-all appearance-none">
                            <option value="size_issue">Size Doesn't Fit</option>
                            <option value="defective">Defective / Damaged</option>
                            <option value="wrong_item">Received Wrong Item</option>
                            <option value="changed_mind">No Longer Needed</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase text-slate-400 ml-4">Additional Comments</label>
                        <textarea name="comments" placeholder="Tell us more about the issue..."
                            class="w-full bg-slate-50 p-8 rounded-[3rem] text-sm font-black outline-none border-2 border-transparent focus:border-orange-500 transition-all min-h-[150px]"></textarea>
                    </div>
                </div>

                <div class="bg-orange-50 p-8 rounded-[2.5rem] border border-orange-100 flex items-start gap-5">
                    <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center text-orange-600 shadow-sm">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-black text-orange-900 uppercase tracking-widest mb-1">Return Policy</h4>
                        <p class="text-[11px] text-orange-800 font-medium leading-relaxed">
                            Once submitted, our logistics partner will pick up the item within 48 hours. Refunds are
                            processed to the original payment method within 5-7 business days after quality check.
                        </p>
                    </div>
                </div>

                <button type="submit"
                    class="w-full bg-slate-900 text-white py-8 rounded-[3rem] font-black uppercase tracking-[0.3em] shadow-2xl shadow-slate-200 hover:bg-orange-600 transition-all group">
                    Confirm Return Request <i
                        class="fas fa-arrow-right ml-4 group-hover:translate-x-2 transition-transform"></i>
                </button>
            </form>
        <?php endif; ?>
    </div>
</body>

</html>