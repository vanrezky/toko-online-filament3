<?php

namespace Database\Seeders;

use App\Models\Template;
use App\Models\TemplateSection;
use App\Models\TemplateSectionContent;
use App\Models\TemplateSectionField;
use Illuminate\Database\Seeder;

class TemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ---------------------------------------------------------------
        // TEMPLATE 1 — Default (Active)
        // ---------------------------------------------------------------
        $default = Template::create([
            'name'         => 'Default',
            'code'         => 'default',
            'description'  => 'Template bawaan toko online dengan tampilan modern dan bersih.',
            'color_scheme' => [
                'primary'    => '#2563EB',
                'secondary'  => '#7C3AED',
                'accent'     => '#F59E0B',
                'background' => '#FFFFFF',
                'text'       => '#111827',
            ],
            'thumbnail' => 'https://placehold.co/800x500/2563EB/FFFFFF?text=Default+Template',
            'is_active'    => true,
        ]);

        $this->seedDefaultSections($default);

        // ---------------------------------------------------------------
        // TEMPLATE 2 — Dark Mode
        // ---------------------------------------------------------------
        $dark = Template::create([
            'name'         => 'Dark Mode',
            'code'         => 'dark_mode',
            'description'  => 'Template elegan dengan tema gelap, cocok untuk produk premium.',
            'color_scheme' => [
                'primary'    => '#6D28D9',
                'secondary'  => '#EC4899',
                'accent'     => '#10B981',
                'background' => '#0F172A',
                'text'       => '#F1F5F9',
            ],
            'thumbnail' => 'https://placehold.co/800x500/0F172A/6D28D9?text=Dark+Mode+Template',
            'is_active'    => false,
        ]);

        $this->seedDarkSections($dark);

        // ---------------------------------------------------------------
        // TEMPLATE 3 — Minimal
        // ---------------------------------------------------------------
        $minimal = Template::create([
            'name'         => 'Minimal',
            'code'         => 'minimal',
            'description'  => 'Template minimalis bersih untuk toko fashion dan lifestyle.',
            'color_scheme' => [
                'primary'    => '#18181B',
                'secondary'  => '#52525B',
                'accent'     => '#E11D48',
                'background' => '#FAFAFA',
                'text'       => '#18181B',
            ],
            'thumbnail' => 'https://placehold.co/800x500/18181B/FAFAFA?text=Minimal+Template',
            'is_active'    => false,
        ]);

        $this->seedMinimalSections($minimal);
    }

    // ================================================================
    // DEFAULT TEMPLATE SECTIONS
    // ================================================================
    protected function seedDefaultSections(Template $template): void
    {
        // Section 1 — Hero
        $hero = $this->createSection($template, [
            'name'           => 'Hero Banner',
            'type'           => 'hero',
            'description'    => 'Banner utama halaman beranda dengan CTA.',
            'icon'           => 'heroicon-o-photo',
            'is_active'      => true,
            'order_priority' => 1,
        ]);

        $this->createFields($hero, [
            ['key' => 'title',          'label' => 'Judul Utama',      'type' => 'text',     'placeholder' => 'Belanja Lebih Hemat, Lebih Mudah',  'is_required' => true,  'order_priority' => 1],
            ['key' => 'subtitle',       'label' => 'Sub Judul',        'type' => 'textarea', 'placeholder' => 'Temukan ribuan produk berkualitas...','is_required' => false, 'order_priority' => 2],
            ['key' => 'image_url',      'label' => 'Gambar Background','type' => 'image',    'placeholder' => 'https://.../hero.jpg',              'is_required' => true,  'order_priority' => 3],
            ['key' => 'button_text',    'label' => 'Teks Tombol',      'type' => 'text',     'placeholder' => 'Belanja Sekarang',                  'is_required' => false, 'order_priority' => 4],
            ['key' => 'button_link',    'label' => 'Link Tombol',      'type' => 'url',      'placeholder' => '/products',                         'is_required' => false, 'order_priority' => 5],
            ['key' => 'overlay_color',  'label' => 'Warna Overlay',    'type' => 'color',    'default_value' => '#00000055',                        'is_required' => false, 'order_priority' => 6],
        ]);

        $this->createContents($hero, [
            'title'         => 'Belanja Lebih Hemat, Lebih Mudah',
            'subtitle'      => 'Temukan ribuan produk berkualitas dengan harga terbaik. Pengiriman cepat ke seluruh Indonesia.',
            'image_url'     => 'https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?w=1400',
            'button_text'   => 'Belanja Sekarang',
            'button_link'   => '/products',
            'overlay_color' => '#00000040',
        ]);

        // Section 2 — Stories / Highlights
        $stories = $this->createSection($template, [
            'name'           => 'Stories & Highlights',
            'type'           => 'stories',
            'description'    => 'Highlight kategori produk atau promo dalam format story.',
            'icon'           => 'heroicon-o-squares-plus',
            'is_active'      => true,
            'order_priority' => 2,
        ]);

        $this->createFields($stories, [
            ['key' => 'title',        'label' => 'Judul Section',    'type' => 'text',   'placeholder' => 'Kategori Pilihan',       'is_required' => false, 'order_priority' => 1],
            ['key' => 'items',        'label' => 'Items (JSON)',      'type' => 'textarea','placeholder' => '[{"label":"Fashion","image":"url"}]', 'is_required' => false, 'order_priority' => 2],
            ['key' => 'show_label',   'label' => 'Tampilkan Label',  'type' => 'toggle', 'default_value' => '1',                    'is_required' => false, 'order_priority' => 3],
        ]);

        $this->createContents($stories, [
            'title'      => 'Kategori Pilihan',
            'items'      => json_encode([
                ['label' => 'Fashion',     'image' => 'https://placehold.co/120x120/2563EB/FFF?text=Fashion'],
                ['label' => 'Elektronik', 'image' => 'https://placehold.co/120x120/7C3AED/FFF?text=Elektronik'],
                ['label' => 'Kecantikan', 'image' => 'https://placehold.co/120x120/EC4899/FFF?text=Kecantikan'],
                ['label' => 'Olahraga',   'image' => 'https://placehold.co/120x120/10B981/FFF?text=Olahraga'],
                ['label' => 'Rumah',      'image' => 'https://placehold.co/120x120/F59E0B/FFF?text=Rumah'],
            ]),
            'show_label' => '1',
        ]);

        // Section 3 — Banner Promo
        $banner = $this->createSection($template, [
            'name'           => 'Banner Promo',
            'type'           => 'banner',
            'description'    => 'Banner promosi dengan gambar dan teks overlay.',
            'icon'           => 'heroicon-o-megaphone',
            'is_active'      => true,
            'order_priority' => 3,
        ]);

        $this->createFields($banner, [
            ['key' => 'title',       'label' => 'Judul Banner',    'type' => 'text',  'placeholder' => 'Flash Sale Hari Ini!',       'is_required' => true,  'order_priority' => 1],
            ['key' => 'description', 'label' => 'Deskripsi',       'type' => 'textarea','placeholder' => 'Diskon hingga 70%...',       'is_required' => false, 'order_priority' => 2],
            ['key' => 'image_url',   'label' => 'Gambar Banner',   'type' => 'image', 'placeholder' => 'https://.../banner.jpg',     'is_required' => true,  'order_priority' => 3],
            ['key' => 'badge_text',  'label' => 'Teks Badge',      'type' => 'text',  'placeholder' => 'Limited Time',               'is_required' => false, 'order_priority' => 4],
            ['key' => 'button_text', 'label' => 'Teks Tombol',     'type' => 'text',  'placeholder' => 'Lihat Penawaran',            'is_required' => false, 'order_priority' => 5],
            ['key' => 'button_link', 'label' => 'Link Tombol',     'type' => 'url',   'placeholder' => '/sale',                      'is_required' => false, 'order_priority' => 6],
        ]);

        $this->createContents($banner, [
            'title'       => 'Flash Sale Hari Ini! 🔥',
            'description' => 'Diskon hingga 70% untuk produk-produk pilihan. Jangan sampai kehabisan!',
            'image_url'   => 'https://images.unsplash.com/photo-1607083206968-13611e3d76db?w=1200',
            'badge_text'  => 'Terbatas!',
            'button_text' => 'Lihat Penawaran',
            'button_link' => '/sale',
        ]);

        // Section 4 — Products (Featured)
        $products = $this->createSection($template, [
            'name'           => 'Produk Unggulan',
            'type'           => 'products',
            'description'    => 'Tampilkan produk-produk unggulan atau terlaris.',
            'icon'           => 'heroicon-o-shopping-bag',
            'is_active'      => true,
            'order_priority' => 4,
        ]);

        $this->createFields($products, [
            ['key' => 'title',         'label' => 'Judul Section',      'type' => 'text',   'placeholder' => 'Produk Terlaris',    'is_required' => false, 'order_priority' => 1],
            ['key' => 'subtitle',      'label' => 'Sub Judul',          'type' => 'text',   'placeholder' => 'Pilihan terbaik...',  'is_required' => false, 'order_priority' => 2],
            ['key' => 'category_id',   'label' => 'Filter Kategori',    'type' => 'number', 'placeholder' => '0 = semua',           'is_required' => false, 'order_priority' => 3],
            ['key' => 'limit',         'label' => 'Jumlah Produk',      'type' => 'number', 'default_value' => '8',                 'is_required' => false, 'order_priority' => 4],
            ['key' => 'show_discount', 'label' => 'Tampilkan Diskon',   'type' => 'toggle', 'default_value' => '1',                 'is_required' => false, 'order_priority' => 5],
            ['key' => 'layout',        'label' => 'Layout Grid',        'type' => 'select', 'default_value' => 'grid',
                'options' => [['label' => 'Grid', 'value' => 'grid'], ['label' => 'List', 'value' => 'list'], ['label' => 'Carousel', 'value' => 'carousel']],
                'is_required' => false, 'order_priority' => 6,
            ],
        ]);

        $this->createContents($products, [
            'title'         => 'Produk Terlaris',
            'subtitle'      => 'Pilihan terbaik dari pelanggan kami',
            'category_id'   => '0',
            'limit'         => '8',
            'show_discount' => '1',
            'layout'        => 'grid',
        ]);

        // Section 5 — Testimonials
        $testimonials = $this->createSection($template, [
            'name'           => 'Testimoni Pelanggan',
            'type'           => 'testimonials',
            'description'    => 'Ulasan dan testimoni dari pelanggan setia.',
            'icon'           => 'heroicon-o-chat-bubble-left-right',
            'is_active'      => true,
            'order_priority' => 5,
        ]);

        $this->createFields($testimonials, [
            ['key' => 'title',         'label' => 'Judul Section',  'type' => 'text',    'placeholder' => 'Kata Mereka',              'is_required' => false, 'order_priority' => 1],
            ['key' => 'subtitle',      'label' => 'Sub Judul',      'type' => 'text',    'placeholder' => 'Lebih dari 10.000 ulasan..','is_required' => false, 'order_priority' => 2],
            ['key' => 'reviews',       'label' => 'Ulasan (JSON)',  'type' => 'textarea','placeholder' => '[{"name":"...","text":"...","rating":5}]', 'is_required' => false, 'order_priority' => 3],
            ['key' => 'bg_color',      'label' => 'Warna Background','type' => 'color',  'default_value' => '#F9FAFB',               'is_required' => false, 'order_priority' => 4],
        ]);

        $this->createContents($testimonials, [
            'title'    => 'Kata Mereka',
            'subtitle' => 'Lebih dari 10.000 pelanggan puas berbelanja bersama kami.',
            'reviews'  => json_encode([
                ['name' => 'Andi Prasetyo',     'avatar' => 'https://i.pravatar.cc/60?img=1',  'text' => 'Produknya berkualitas banget, pengiriman juga super cepat. Recommended!', 'rating' => 5],
                ['name' => 'Siti Rahayu',       'avatar' => 'https://i.pravatar.cc/60?img=5',  'text' => 'Harga terjangkau dengan kualitas premium. Saya sudah repeat order 3 kali.',  'rating' => 5],
                ['name' => 'Budi Santoso',      'avatar' => 'https://i.pravatar.cc/60?img=12', 'text' => 'Pelayanannya ramah, barang sesuai foto. Packing juga rapi dan aman.',        'rating' => 4],
                ['name' => 'Dewi Lestari',      'avatar' => 'https://i.pravatar.cc/60?img=20', 'text' => 'Belanja di sini jadi kebiasaan. Selalu ada promo menarik setiap hari!',      'rating' => 5],
            ]),
            'bg_color' => '#EFF6FF',
        ]);

        // Section 6 — CTA
        $cta = $this->createSection($template, [
            'name'           => 'Call to Action',
            'type'           => 'cta',
            'description'    => 'Section ajakan bertindak untuk mendorong konversi.',
            'icon'           => 'heroicon-o-cursor-arrow-rays',
            'is_active'      => true,
            'order_priority' => 6,
        ]);

        $this->createFields($cta, [
            ['key' => 'title',        'label' => 'Judul CTA',       'type' => 'text',    'placeholder' => 'Bergabung Sekarang!',      'is_required' => true,  'order_priority' => 1],
            ['key' => 'description',  'label' => 'Deskripsi',       'type' => 'textarea','placeholder' => 'Dapatkan penawaran eksklusif...','is_required' => false, 'order_priority' => 2],
            ['key' => 'button_text',  'label' => 'Teks Tombol',     'type' => 'text',    'placeholder' => 'Daftar Gratis',            'is_required' => false, 'order_priority' => 3],
            ['key' => 'button_link',  'label' => 'Link Tombol',     'type' => 'url',     'placeholder' => '/register',                'is_required' => false, 'order_priority' => 4],
            ['key' => 'bg_gradient',  'label' => 'Warna Gradient',  'type' => 'text',    'default_value' => 'from-blue-600 to-violet-600', 'is_required' => false, 'order_priority' => 5],
        ]);

        $this->createContents($cta, [
            'title'       => 'Bergabung & Nikmati Keuntungan Eksklusif! 🎉',
            'description' => 'Daftar sekarang dan dapatkan voucher selamat datang senilai Rp50.000 untuk pembelian pertamamu.',
            'button_text' => 'Daftar Gratis',
            'button_link' => '/register',
            'bg_gradient' => 'from-blue-600 to-violet-600',
        ]);

        // Section 7 — FAQ
        $faq = $this->createSection($template, [
            'name'           => 'FAQ',
            'type'           => 'faq',
            'description'    => 'Pertanyaan yang sering ditanyakan pelanggan.',
            'icon'           => 'heroicon-o-question-mark-circle',
            'is_active'      => true,
            'order_priority' => 7,
        ]);

        $this->createFields($faq, [
            ['key' => 'title',    'label' => 'Judul Section', 'type' => 'text',    'placeholder' => 'Pertanyaan Umum',    'is_required' => false, 'order_priority' => 1],
            ['key' => 'items',    'label' => 'Daftar FAQ',    'type' => 'textarea','placeholder' => '[{"q":"...","a":"..."}]', 'is_required' => false, 'order_priority' => 2],
        ]);

        $this->createContents($faq, [
            'title' => 'Pertanyaan yang Sering Ditanyakan',
            'items' => json_encode([
                ['q' => 'Berapa lama pengiriman?',              'a' => 'Pengiriman reguler membutuhkan 2-5 hari kerja tergantung lokasi tujuan. Tersedia juga pengiriman same day dan next day untuk area tertentu.'],
                ['q' => 'Apakah bisa retur barang?',           'a' => 'Ya, kami menyediakan garansi retur 7 hari sejak barang diterima selama kondisi barang masih original dan belum digunakan.'],
                ['q' => 'Metode pembayaran apa saja?',         'a' => 'Kami menerima transfer bank, kartu kredit/debit, e-wallet (GoPay, OVO, Dana, ShopeePay), dan QRIS.'],
                ['q' => 'Bagaimana cara lacak pesanan saya?',  'a' => 'Setelah pesanan dikonfirmasi, Anda akan mendapatkan nomor resi via email dan SMS. Gunakan nomor tersebut di halaman Lacak Pesanan.'],
            ]),
        ]);
    }

    // ================================================================
    // DARK TEMPLATE SECTIONS
    // ================================================================
    protected function seedDarkSections(Template $template): void
    {
        // Section 1 — Hero
        $hero = $this->createSection($template, [
            'name'           => 'Hero Cinematic',
            'type'           => 'hero',
            'description'    => 'Hero fullscreen dengan efek parallax dan video background.',
            'icon'           => 'heroicon-o-film',
            'is_active'      => true,
            'order_priority' => 1,
        ]);

        $this->createFields($hero, [
            ['key' => 'title',       'label' => 'Headline',         'type' => 'text',    'placeholder' => 'Temukan Gaya Hidupmu',    'is_required' => true,  'order_priority' => 1],
            ['key' => 'subtitle',    'label' => 'Tagline',          'type' => 'textarea','placeholder' => 'Koleksi Premium...',       'is_required' => false, 'order_priority' => 2],
            ['key' => 'video_url',   'label' => 'URL Video Background','type' => 'url',  'placeholder' => 'https://.../hero.mp4',    'is_required' => false, 'order_priority' => 3],
            ['key' => 'image_url',   'label' => 'Gambar Fallback',  'type' => 'image',   'placeholder' => 'https://.../hero.jpg',    'is_required' => true,  'order_priority' => 4],
            ['key' => 'button_text', 'label' => 'Teks CTA',         'type' => 'text',    'placeholder' => 'Jelajahi Koleksi',        'is_required' => false, 'order_priority' => 5],
            ['key' => 'button_link', 'label' => 'Link CTA',         'type' => 'url',     'placeholder' => '/collections',            'is_required' => false, 'order_priority' => 6],
        ]);

        $this->createContents($hero, [
            'title'       => 'Temukan Gaya Hidupmu',
            'subtitle'    => 'Koleksi premium yang dirancang untuk mereka yang mengerti kualitas.',
            'video_url'   => '',
            'image_url'   => 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=1400',
            'button_text' => 'Jelajahi Koleksi',
            'button_link' => '/collections',
        ]);

        // Section 2 — Gallery
        $gallery = $this->createSection($template, [
            'name'           => 'Gallery Showcase',
            'type'           => 'gallery',
            'description'    => 'Tampilan galeri masonry untuk produk premium.',
            'icon'           => 'heroicon-o-photo',
            'is_active'      => true,
            'order_priority' => 2,
        ]);

        $this->createFields($gallery, [
            ['key' => 'title',    'label' => 'Judul Gallery',   'type' => 'text',    'placeholder' => 'Inspirasi Gaya',                 'is_required' => false, 'order_priority' => 1],
            ['key' => 'images',   'label' => 'Gambar (JSON)',   'type' => 'textarea','placeholder' => '[{"url":"...","alt":"..."}]',     'is_required' => false, 'order_priority' => 2],
            ['key' => 'columns',  'label' => 'Jumlah Kolom',   'type' => 'number',  'default_value' => '3',                             'is_required' => false, 'order_priority' => 3],
        ]);

        $this->createContents($gallery, [
            'title'   => 'Inspirasi Gaya',
            'images'  => json_encode([
                ['url' => 'https://images.unsplash.com/photo-1523381210434-271e8be1f52b?w=600', 'alt' => 'Fashion Collection 1'],
                ['url' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=600', 'alt' => 'Fashion Collection 2'],
                ['url' => 'https://images.unsplash.com/photo-1576201836106-db1758fd1c97?w=600', 'alt' => 'Premium Accessories'],
                ['url' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=600', 'alt' => 'Lifestyle Product'],
                ['url' => 'https://images.unsplash.com/photo-1525966222134-fcfa99b8ae77?w=600', 'alt' => 'Footwear Collection'],
                ['url' => 'https://images.unsplash.com/photo-1491553895911-0055eca6402d?w=600', 'alt' => 'Sport Collection'],
            ]),
            'columns' => '3',
        ]);

        // Section 3 — Products
        $products = $this->createSection($template, [
            'name'           => 'Koleksi Terbaru',
            'type'           => 'products',
            'description'    => 'Showcase produk terbaru dengan layout kartu gelap.',
            'icon'           => 'heroicon-o-sparkles',
            'is_active'      => true,
            'order_priority' => 3,
        ]);

        $this->createFields($products, [
            ['key' => 'title',  'label' => 'Judul Section',    'type' => 'text',   'placeholder' => 'New Arrivals',  'is_required' => false, 'order_priority' => 1],
            ['key' => 'limit',  'label' => 'Jumlah Produk',   'type' => 'number', 'default_value' => '6',            'is_required' => false, 'order_priority' => 2],
            ['key' => 'layout', 'label' => 'Layout',          'type' => 'select', 'default_value' => 'carousel',
                'options' => [['label' => 'Grid', 'value' => 'grid'], ['label' => 'Carousel', 'value' => 'carousel']],
                'is_required' => false, 'order_priority' => 3,
            ],
        ]);

        $this->createContents($products, [
            'title'  => 'New Arrivals',
            'limit'  => '6',
            'layout' => 'carousel',
        ]);

        // Section 4 — CTA
        $cta = $this->createSection($template, [
            'name'           => 'Newsletter CTA',
            'type'           => 'cta',
            'description'    => 'Form langganan newsletter dengan styling gelap.',
            'icon'           => 'heroicon-o-envelope',
            'is_active'      => true,
            'order_priority' => 4,
        ]);

        $this->createFields($cta, [
            ['key' => 'title',       'label' => 'Judul',          'type' => 'text',    'placeholder' => 'Subscribe & Dapatkan Promo',  'is_required' => false, 'order_priority' => 1],
            ['key' => 'description', 'label' => 'Deskripsi',      'type' => 'textarea','placeholder' => 'Jangan lewatkan penawaran...',  'is_required' => false, 'order_priority' => 2],
            ['key' => 'button_text', 'label' => 'Teks Tombol',    'type' => 'text',    'placeholder' => 'Subscribe',                    'is_required' => false, 'order_priority' => 3],
        ]);

        $this->createContents($cta, [
            'title'       => 'Subscribe & Dapatkan Promo Eksklusif',
            'description' => 'Masukkan email kamu dan jadilah yang pertama mengetahui koleksi terbaru dan penawaran spesial.',
            'button_text' => 'Subscribe Sekarang',
        ]);
    }

    // ================================================================
    // MINIMAL TEMPLATE SECTIONS
    // ================================================================
    protected function seedMinimalSections(Template $template): void
    {
        // Section 1 — Hero
        $hero = $this->createSection($template, [
            'name'           => 'Hero Clean',
            'type'           => 'hero',
            'description'    => 'Hero minimalis dengan teks besar dan whitespace.',
            'icon'           => 'heroicon-o-bars-3-center-left',
            'is_active'      => true,
            'order_priority' => 1,
        ]);

        $this->createFields($hero, [
            ['key' => 'title',       'label' => 'Judul',         'type' => 'text',    'placeholder' => 'Less is More.',  'is_required' => true,  'order_priority' => 1],
            ['key' => 'subtitle',    'label' => 'Sub Judul',     'type' => 'textarea','placeholder' => 'Pilih, beli, nikmati.', 'is_required' => false, 'order_priority' => 2],
            ['key' => 'image_url',   'label' => 'Gambar',        'type' => 'image',   'placeholder' => 'https://.../hero.jpg', 'is_required' => false, 'order_priority' => 3],
            ['key' => 'button_text', 'label' => 'Teks Tombol',  'type' => 'text',    'placeholder' => 'Shop Now', 'is_required' => false, 'order_priority' => 4],
            ['key' => 'button_link', 'label' => 'Link Tombol',  'type' => 'url',     'placeholder' => '/products', 'is_required' => false, 'order_priority' => 5],
            ['key' => 'align',       'label' => 'Alignment',    'type' => 'select',  'default_value' => 'center',
                'options' => [['label' => 'Kiri', 'value' => 'left'], ['label' => 'Tengah', 'value' => 'center'], ['label' => 'Kanan', 'value' => 'right']],
                'is_required' => false, 'order_priority' => 6,
            ],
        ]);

        $this->createContents($hero, [
            'title'       => 'Less is More.',
            'subtitle'    => 'Pilih dengan cermat, beli yang perlu, nikmati kualitasnya.',
            'image_url'   => 'https://images.unsplash.com/photo-1555529669-e69e7aa0ba9a?w=1400',
            'button_text' => 'Shop Now',
            'button_link' => '/products',
            'align'       => 'center',
        ]);

        // Section 2 — Products
        $products = $this->createSection($template, [
            'name'           => 'Produk Pilihan',
            'type'           => 'products',
            'description'    => 'Grid produk bersih tanpa dekorasi berlebihan.',
            'icon'           => 'heroicon-o-tag',
            'is_active'      => true,
            'order_priority' => 2,
        ]);

        $this->createFields($products, [
            ['key' => 'title',   'label' => 'Judul',           'type' => 'text',  'placeholder' => 'Produk Pilihan', 'is_required' => false, 'order_priority' => 1],
            ['key' => 'limit',   'label' => 'Jumlah Produk',  'type' => 'number', 'default_value' => '4',            'is_required' => false, 'order_priority' => 2],
            ['key' => 'columns', 'label' => 'Kolom Grid',     'type' => 'number', 'default_value' => '4',            'is_required' => false, 'order_priority' => 3],
        ]);

        $this->createContents($products, [
            'title'   => 'Produk Pilihan',
            'limit'   => '4',
            'columns' => '4',
        ]);

        // Section 3 — Contact
        $contact = $this->createSection($template, [
            'name'           => 'Hubungi Kami',
            'type'           => 'contact',
            'description'    => 'Form kontak sederhana dengan informasi toko.',
            'icon'           => 'heroicon-o-phone',
            'is_active'      => true,
            'order_priority' => 3,
        ]);

        $this->createFields($contact, [
            ['key' => 'title',      'label' => 'Judul',          'type' => 'text',    'placeholder' => 'Hubungi Kami',           'is_required' => false, 'order_priority' => 1],
            ['key' => 'email',      'label' => 'Email',          'type' => 'text',    'placeholder' => 'halo@toko.com',          'is_required' => false, 'order_priority' => 2],
            ['key' => 'phone',      'label' => 'Telepon',        'type' => 'text',    'placeholder' => '+62 812-XXXX-XXXX',      'is_required' => false, 'order_priority' => 3],
            ['key' => 'address',    'label' => 'Alamat',         'type' => 'textarea','placeholder' => 'Jl. ..., Jakarta',       'is_required' => false, 'order_priority' => 4],
            ['key' => 'maps_embed', 'label' => 'Google Maps URL','type' => 'url',     'placeholder' => 'https://maps.google...', 'is_required' => false, 'order_priority' => 5],
            ['key' => 'show_form',  'label' => 'Tampilkan Form', 'type' => 'toggle',  'default_value' => '1',                    'is_required' => false, 'order_priority' => 6],
        ]);

        $this->createContents($contact, [
            'title'      => 'Hubungi Kami',
            'email'      => 'halo@tokoonline.com',
            'phone'      => '+62 811-0000-1234',
            'address'    => 'Jl. Sudirman No. 99, Jakarta Pusat, DKI Jakarta 10220',
            'maps_embed' => 'https://maps.google.com/?q=-6.2088,106.8456',
            'show_form'  => '1',
        ]);
    }

    // ================================================================
    // HELPERS
    // ================================================================

    protected function createSection(Template $template, array $data): TemplateSection
    {
        return TemplateSection::create(array_merge(['template_id' => $template->id], $data));
    }

    protected function createFields(TemplateSection $section, array $fields): void
    {
        foreach ($fields as $field) {
            TemplateSectionField::create(array_merge(
                ['section_id' => $section->id],
                $field,
                isset($field['options']) ? ['options' => $field['options']] : []
            ));
        }
    }

    protected function createContents(TemplateSection $section, array $contents): void
    {
        // Re-fetch fields to map key → field_id
        $fieldMap = TemplateSectionField::where('section_id', $section->id)
            ->pluck('id', 'key');

        foreach ($contents as $key => $value) {
            if (isset($fieldMap[$key])) {
                TemplateSectionContent::create([
                    'section_id' => $section->id,
                    'field_id'   => $fieldMap[$key],
                    'value'      => $value,
                ]);
            }
        }
    }
}
