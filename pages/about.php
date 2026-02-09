<?php
include '../includes/header.php';
require_once '../includes/cms_helper.php';
?>

<section class="relative py-24 bg-zinc-900 text-white overflow-hidden">
    <div class="absolute inset-0 opacity-20">
        <img src="<?= get_cms_content('about', 'hero_image', 'https://images.unsplash.com/photo-1621905252507-b35492cc74b4?q=80&w=2069&auto=format&fit=crop') ?>"
            class="w-full h-full object-cover" alt="Electrical Work">
    </div>
    <div class="container mx-auto px-6 relative z-10 text-center">
        <h1 class="text-5xl md:text-7xl font-sans font-bold mb-6">
            <?= get_cms_content('about', 'hero_title', 'About Joshi <br><span class="text-yellow-500 text-4xl md:text-6xl">Electrical</span>') ?>
        </h1>
        <p class="max-w-3xl mx-auto text-gray-400 text-lg md:text-xl leading-relaxed">
            <?= get_cms_content('about', 'hero_desc', 'Your trusted electrical shop established in 2024. Serving customers with quality electrical products and reliable service.') ?>
        </p>
    </div>
</section>

<section class="py-20 container mx-auto px-6">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
        <div class="bg-white p-8 rounded-3xl shadow-sm border border-zinc-100 hover:shadow-xl transition-all">
            <div class="text-yellow-600 text-3xl mb-4"><i class="fas fa-certificate"></i></div>
            <h3 class="font-bold mb-2"><?= get_cms_content('about', 'feat_1_title', 'Authorized Distributor') ?></h3>
            <p class="text-zinc-500 text-sm">
                <?= get_cms_content('about', 'feat_1_desc', 'We are the authorized distributor for HPL products, ensuring 100% genuineness.') ?>
            </p>
        </div>
        <div class="bg-white p-8 rounded-3xl shadow-sm border border-zinc-100 hover:shadow-xl transition-all">
            <div class="text-yellow-600 text-3xl mb-4"><i class="fas fa-tags"></i></div>
            <h3 class="font-bold mb-2"><?= get_cms_content('about', 'feat_2_title', 'Competitive Pricing') ?></h3>
            <p class="text-zinc-500 text-sm">
                <?= get_cms_content('about', 'feat_2_desc', 'Best market rates for both single items and bulk wholesale orders.') ?>
            </p>
        </div>
        <div class="bg-white p-8 rounded-3xl shadow-sm border border-zinc-100 hover:shadow-xl transition-all">
            <div class="text-yellow-600 text-3xl mb-4"><i class="fas fa-shield-alt"></i></div>
            <h3 class="font-bold mb-2"><?= get_cms_content('about', 'feat_3_title', 'Safety Assured') ?></h3>
            <p class="text-zinc-500 text-sm">
                <?= get_cms_content('about', 'feat_3_desc', 'We only deal in certified and safe electrical equipment for your home.') ?>
            </p>
        </div>
        <div class="bg-white p-8 rounded-3xl shadow-sm border border-zinc-100 hover:shadow-xl transition-all">
            <div class="text-yellow-600 text-3xl mb-4"><i class="fas fa-headset"></i></div>
            <h3 class="font-bold mb-2"><?= get_cms_content('about', 'feat_4_title', 'Expert Support') ?></h3>
            <p class="text-zinc-500 text-sm">
                <?= get_cms_content('about', 'feat_4_desc', 'Get guidance from Dr. Praveen Joshi for all your electrical needs.') ?>
            </p>
        </div>
    </div>
</section>


<section class="py-24 bg-zinc-50">
    <div class="container mx-auto px-6">
        <div class="flex flex-col lg:flex-row gap-16 items-center">
            <div class="w-full lg:w-1/2">
                <div class="relative">
                    <img src="<?= htmlspecialchars(get_cms_content('about', 'owner_image', $base_url . 'assets/images/WhatsApp Image 2026-02-09 at 12.21.30 PM.jpeg')) ?>"
                        class="rounded-[3rem] shadow-2xl relative z-10" alt="Owner">
                    <div
                        class="absolute -bottom-6 -right-6 w-full h-full border-2 border-yellow-500 rounded-[3rem] z-0">
                    </div>
                </div>
            </div>
            <div class="w-full lg:w-1/2">
                <h2 class="text-xs font-bold text-yellow-600 tracking-[0.4em] uppercase mb-4">Leadership</h2>
                <h3 class="text-4xl md:text-5xl font-sans font-bold mb-6">Meet the Owner</h3>
                <h4 class="text-2xl font-bold text-zinc-900 mb-4">
                    <?= get_cms_content('about', 'owner_name', 'Dr. Praveen Joshi') ?></h4>
                <p class="text-zinc-600 text-lg mb-8 leading-relaxed italic">
                    "<?= get_cms_content('about', 'owner_quote', 'At Joshi Electrical, our focus is on providing safe, durable, and long-lasting electrical solutions for homes and businesses. We believe in building trust through quality and affordable service.') ?>"
                </p>

                <div class="mb-10">
                    <h4 class="font-bold text-zinc-900 text-xl mb-2">Our Specialization</h4>
                    <p class="text-zinc-500">
                        <?= get_cms_content('about', 'specialization', 'We specialize in supplying Electrical appliances, Switch fittings, Wiring materials, Lighting solutions, and Safety electrical equipment.') ?>
                    </p>
                </div>

                <div class="flex gap-8 border-t pt-8 border-zinc-200">
                    <div class="flex flex-col">
                        <span class="font-bold text-zinc-900">Distributors of:</span>
                        <div class="flex gap-4 mt-2 text-zinc-500">
                            <span>HPL</span> • <span>Anchor</span> • <span>Polycab</span> • <span>Havells</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-20 bg-yellow-500 text-black text-center">
    <div class="container mx-auto px-6">
        <h2 class="text-4xl font-sans font-bold mb-6">
            <?= get_cms_content('about', 'cta_title', 'Visit Our Shop Today') ?></h2>
        <p class="mb-10 opacity-80 max-w-xl mx-auto font-medium">
            <?= get_cms_content('about', 'cta_desc', 'Experience the best quality electrical products. Affordable prices guaranteed.') ?>
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="tel:<?= get_cms_content('contact', 'phone', '9956510247') ?>"
                class="bg-black text-white px-10 py-4 rounded-full font-bold hover:bg-white hover:text-black transition-all">Call
                Now</a>
            <a href="<?= get_cms_content('contact', 'map_url', 'https://maps.app.goo.gl/b5akKLL9gf7UJDPJ8') ?>"
                target="_blank"
                class="bg-white text-black px-10 py-4 rounded-full font-bold hover:bg-black hover:text-white transition-all">Get
                Directions</a>
        </div>
    </div>
</section>

<?php
include '../includes/footer.php';
?>