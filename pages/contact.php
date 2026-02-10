<?php include '../includes/header.php';
require_once '../includes/cms_helper.php';
?>

<section class="bg-gray-50 py-20 px-4 md:px-10">
    <div class="container mx-auto">

        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-5xl font-sans font-bold text-zinc-900 mb-4">Get In Touch</h2>
            <p class="text-zinc-500 max-w-xl mx-auto">Have questions about our electrical products or huge project
                requirements? Our team is here to assist you.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-start">

            <div class="space-y-8">
                <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-zinc-100">
                    <h3 class="text-xl font-bold mb-6 text-zinc-800 uppercase tracking-widest">Shop Information</h3>

                    <div class="space-y-6">
                        <div class="flex items-start gap-4">
                            <div
                                class="w-10 h-10 bg-yellow-100 text-yellow-600 rounded-full flex items-center justify-center shrink-0">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-sm">Our Location</h4>
                                <p class="text-gray-500 text-sm">
                                    <?= nl2br(get_cms_content('contact', 'address', '[Shop Address Here]<br>[City, Uttar Pradesh, Zip Code]')) ?>
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div
                                class="w-10 h-10 bg-yellow-100 text-yellow-600 rounded-full flex items-center justify-center shrink-0">
                                <i class="fas fa-phone-alt"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-sm">Call Us</h4>
                                <p class="text-gray-500 text-sm">
                                    <?= get_cms_content('contact', 'phone', '6386517300, 9956510247, 7007465665') ?>
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div
                                class="w-10 h-10 bg-yellow-100 text-yellow-600 rounded-full flex items-center justify-center shrink-0">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-sm">Owner</h4>
                                <p class="text-gray-500 text-sm">
                                    <?= get_cms_content('about', 'owner_name', 'Dr. Praveen Joshi') ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <a href="https://wa.me/917007465665"
                        class="mt-8 flex items-center justify-center gap-2 bg-[#25D366] text-white py-4 rounded-xl font-bold hover:opacity-90 transition-all">
                        <i class="fab fa-whatsapp text-xl"></i> Chat on WhatsApp
                    </a>
                </div>

                <div
                    class="rounded-[2rem] overflow-hidden h-[300px] shadow-sm border border-zinc-100 grayscale hover:grayscale-0 transition-all duration-500">
                    <iframe
                        src="<?= get_cms_content('contact', 'map_url', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3559.877284414614!2d80.946165!3d26.846694!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMjbCsDUwJzQ4LjEiTiA4MMKwNTYnNDYuMiJF!5e0!3m2!1sen!2sin!4v1630000000000!5m2!1sen!2sin') ?>"
                        width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                </div>
            </div>

            <div class="bg-white p-8 md:p-12 rounded-[2.5rem] shadow-xl border border-zinc-100">
                <h3 class="text-2xl font-bold mb-2 text-zinc-900">Send an Inquiry</h3>
                <p class="text-gray-500 mb-8 text-sm">Fill out the form below and we will get back to you shortly.</p>

                <?php
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_inquiry'])) {
                    $name = $_POST['name'];
                    $phone = $_POST['phone'];
                    $email = $_POST['email'];
                    $type = $_POST['type'];
                    $message = $_POST['message'];

                    try {
                        require_once '../config/database.php';
                        $pdo->prepare("INSERT INTO inquiries (name, phone, email, type, message) VALUES (?, ?, ?, ?, ?)")->execute([$name, $phone, $email, $type, $message]);
                        echo "<div class='bg-emerald-100 text-emerald-700 p-4 rounded-xl mb-6 font-bold text-center'>Inquiry Sent Successfully! We will contact you soon.</div>";
                    } catch (Exception $e) {
                        echo "<div class='bg-red-100 text-red-700 p-4 rounded-xl mb-6 font-bold text-center'>Error sending inquiry. Please try again.</div>";
                    }
                }
                ?>
                <form action="" method="POST" class="space-y-5">
                    <input type="hidden" name="submit_inquiry" value="1">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="space-y-2">
                            <label class="text-[10px] font-bold uppercase text-gray-400 tracking-widest">Full
                                Name</label>
                            <input type="text" name="name" required placeholder="Enter your name"
                                class="w-full bg-gray-50 border-none rounded-xl px-5 py-4 focus:ring-2 focus:ring-yellow-500 transition-all font-bold">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-bold uppercase text-gray-400 tracking-widest">Phone
                                Number</label>
                            <input type="tel" name="phone" required placeholder="+91 00000 00000"
                                class="w-full bg-gray-50 border-none rounded-xl px-5 py-4 focus:ring-2 focus:ring-yellow-500 transition-all font-bold">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-bold uppercase text-gray-400 tracking-widest">Email
                            Address (Optional)</label>
                        <input type="email" name="email" placeholder="example@gmail.com"
                            class="w-full bg-gray-50 border-none rounded-xl px-5 py-4 focus:ring-2 focus:ring-yellow-500 transition-all font-bold">
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-bold uppercase text-gray-400 tracking-widest">Inquiry
                            Type</label>
                        <select name="type"
                            class="w-full bg-gray-50 border-none rounded-xl px-5 py-4 focus:ring-2 focus:ring-yellow-500 transition-all text-gray-500 font-bold outline-none">
                            <option value="Retail Purchase">Retail Purchase</option>
                            <option value="Wholesale Order">Wholesale Order</option>
                            <option value="Project Supply">Project Supply</option>
                            <option value="Product Availability Check">Product Availability Check</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-bold uppercase text-gray-400 tracking-widest">Your
                            Message</label>
                        <textarea name="message" rows="4" placeholder="How can we help you today?"
                            class="w-full bg-gray-50 border-none rounded-xl px-5 py-4 focus:ring-2 focus:ring-yellow-500 transition-all font-bold"></textarea>
                    </div>

                    <button type="submit"
                        class="w-full bg-yellow-500 text-black py-5 rounded-xl font-bold hover:bg-zinc-900 hover:text-white transition-all uppercase tracking-widest shadow-lg">
                        Submit Message
                    </button>
                </form>
            </div>

        </div>
    </div>
</section>


<?php include '../includes/footer.php'; ?>