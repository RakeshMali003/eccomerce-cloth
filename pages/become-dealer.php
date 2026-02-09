<?php include '../includes/header.php';

// Handle Dealer Application Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_application'])) {
    $shop_name = $_POST['shop_name'];
    $owner_name = $_POST['owner_name'];
    $mobile = $_POST['mobile'];
    $city_state = $_POST['city_state'];
    $gst_number = $_POST['gst_number'];

    try {
        require_once '../config/database.php';
        $pdo->prepare("INSERT INTO dealer_applications (shop_name, owner_name, mobile, city_state, gst_number) VALUES (?, ?, ?, ?, ?)")->execute([$shop_name, $owner_name, $mobile, $city_state, $gst_number]);
        $success_msg = "Application Submitted! We will review your shop details and contact you.";
    } catch (Exception $e) {
        $error_msg = "Error submitting application: " . $e->getMessage();
    }
}
?>

<section class="relative py-24 bg-zinc-900 overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <img src="https://images.unsplash.com/photo-1558449028-b53a39d100fc?q=80&w=2600&auto=format&fit=crop"
            class="w-full h-full object-cover grayscale">
    </div>
    <div class="container mx-auto px-6 relative z-10 text-center">
        <h1 class="text-4xl md:text-6xl font-sans font-bold text-white mb-6">Join Our <span
                class="text-orange-500">Network</span></h1>
        <p class="max-w-2xl mx-auto text-gray-400 text-lg">Partner with Joshi Electricals and get access to exclusive
            wholesale rates, priority support, and marketing materials.</p>
    </div>
</section>

<section class="py-20 bg-gray-50">
    <div class="container mx-auto px-6">
        <div class="flex flex-col lg:flex-row gap-16">

            <!-- Benefits -->
            <div class="w-full lg:w-1/2">
                <h3 class="text-3xl font-black text-zinc-900 mb-8">Why Partner With Us?</h3>
                <div class="grid grid-cols-1 gap-6">
                    <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-zinc-100 flex gap-6 items-center">
                        <div
                            class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center text-orange-600 text-2xl shrink-0">
                            <i class="fas fa-percentage"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-lg mb-1">Best Margins</h4>
                            <p class="text-sm text-gray-500">Competitive wholesale pricing ensuring high profitability
                                for your retail business.</p>
                        </div>
                    </div>
                    <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-zinc-100 flex gap-6 items-center">
                        <div
                            class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center text-orange-600 text-2xl shrink-0">
                            <i class="fas fa-certificate"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-lg mb-1">HPL Authorized</h4>
                            <p class="text-sm text-gray-500">Sell genuine, warranty-backed products from top brands like
                                HPL, Anchor, and Polycab.</p>
                        </div>
                    </div>
                    <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-zinc-100 flex gap-6 items-center">
                        <div
                            class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center text-orange-600 text-2xl shrink-0">
                            <i class="fas fa-bullhorn"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-lg mb-1">Marketing Support</h4>
                            <p class="text-sm text-gray-500">Get branding materials, banners, and digital assets to
                                boost your shop's visibility.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Application Form -->
            <div class="w-full lg:w-1/2">
                <div class="bg-white p-10 rounded-[2.5rem] shadow-xl border border-zinc-100">
                    <h3 class="text-2xl font-black text-zinc-900 mb-2">Register as Dealer</h3>
                    <p class="text-slate-400 text-sm font-medium mb-8">Join the network of successful electrical dealers
                        with Joshi Electrical.</p>

                    <?php if (isset($success_msg)): ?>
                        <div
                            class="bg-emerald-100 text-emerald-700 p-4 rounded-xl mb-6 font-bold text-center border-l-4 border-emerald-500">
                            <i class="fas fa-check-circle mr-2"></i>
                            <?= $success_msg ?>
                        </div>
                    <?php endif; ?>

                    <form action="" method="POST" class="space-y-6">
                        <input type="hidden" name="submit_application" value="1">

                        <div class="space-y-1">
                            <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Shop/Business
                                Name</label>
                            <input type="text" name="shop_name" required
                                class="w-full px-6 py-4 bg-slate-50 rounded-2xl border-none focus:ring-2 focus:ring-orange-500/20 font-bold outline-none text-slate-900">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Owner Name</label>
                                <input type="text" name="owner_name" required
                                    class="w-full px-6 py-4 bg-slate-50 rounded-2xl border-none focus:ring-2 focus:ring-orange-500/20 font-bold outline-none text-slate-900">
                            </div>
                            <div class="space-y-1">
                                <label class="text-[10px] font-black uppercase text-slate-400 ml-2">Mobile
                                    Number</label>
                                <input type="text" name="mobile" required
                                    class="w-full px-6 py-4 bg-slate-50 rounded-2xl border-none focus:ring-2 focus:ring-orange-500/20 font-bold outline-none text-slate-900">
                            </div>
                        </div>

                        <div class="space-y-1">
                            <label class="text-[10px] font-black uppercase text-slate-400 ml-2">City & State</label>
                            <input type="text" name="city_state" required
                                class="w-full px-6 py-4 bg-slate-50 rounded-2xl border-none focus:ring-2 focus:ring-orange-500/20 font-bold outline-none text-slate-900">
                        </div>

                        <div class="space-y-1">
                            <label class="text-[10px] font-black uppercase text-slate-400 ml-2">GST Number
                                (Optional)</label>
                            <input type="text" name="gst_number"
                                class="w-full px-6 py-4 bg-slate-50 rounded-2xl border-none focus:ring-2 focus:ring-orange-500/20 font-bold outline-none text-slate-900">
                        </div>

                        <button type="submit"
                            class="w-full bg-slate-900 text-white py-5 rounded-2xl font-black uppercase tracking-widest hover:bg-orange-600 transition-all shadow-xl">
                            Apply Now
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>