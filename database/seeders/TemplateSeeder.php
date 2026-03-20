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
        $default = Template::updateOrCreate(
            ['code' => 'default'],
            [
                'name'         => 'Default',
                'description'  => 'Template default toko online dengan tampilan modern, warm orange theme, sesuai design system.',
                'color_scheme' => [
                    'primary'     => '#F97316',
                    'secondary'   => '#F5F3FC',
                    'accent'      => '#FB923C',
                    'destructive' => '#F43F5E',
                    'background'  => '#FCFCFE',
                    'foreground'  => '#2D1B0E',
                ],
                'thumbnail' => 'https://placehold.co/800x500/F97316/FFFFFF?text=Default+Template',
                'is_active'    => true,
            ]
        );

        $this->seedDefaultSections($default);
    }

    protected function seedDefaultSections(Template $template): void
    {
        // Section 1 — Hero
        $hero = $this->createSection($template, [
            'name'           => 'Hero Banner',
            'type'           => 'hero',
            'description'    => 'Banner utama halaman beranda dengan headline, subheadline, dan CTA.',
            'icon'           => 'heroicon-o-photo',
            'is_active'      => true,
            'order_priority' => 1,
        ]);

        $this->createFields($hero, [
            ['key' => 'title',          'label' => 'Headline',           'type' => 'text',     'placeholder' => 'Belanja Hemat, Belanja Mudah',    'is_required' => true,  'order_priority' => 1],
            ['key' => 'subtitle',       'label' => 'Subheadline',        'type' => 'textarea', 'placeholder' => 'Temukan ribuan produk berkualitas...', 'is_required' => false, 'order_priority' => 2],
            ['key' => 'image_url',      'label' => 'Gambar Background',  'type' => 'image',    'placeholder' => 'https://.../hero.jpg',              'is_required' => true,  'order_priority' => 3],
            ['key' => 'overlay_color',  'label' => 'Warna Overlay',      'type' => 'color',    'default_value' => '#2D1B0E80',                  'is_required' => false, 'order_priority' => 4],
            ['key' => 'button_text',    'label' => 'Teks Tombol',        'type' => 'text',     'placeholder' => 'Belanja Sekarang',                  'is_required' => false, 'order_priority' => 5],
            ['key' => 'button_link',    'label' => 'Link Tombol',        'type' => 'url',      'placeholder' => '/products',                         'is_required' => false, 'order_priority' => 6],
        ]);

        $this->createContents($hero, [
            'title'         => 'Belanja Hemat, Belanja Mudah',
            'subtitle'      => 'Temukan ribuan produk berkualitas dengan harga terbaik. Pengiriman cepat ke seluruh Indonesia.',
            'image_url'     => 'https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?w=1400',
            'overlay_color' => '#2D1B0E80',
            'button_text'   => 'Belanja Sekarang',
            'button_link'   => '/products',
        ]);

        // Section 2 — Category Menu
        $categoryMenu = $this->createSection($template, [
            'name'           => 'Menu Kategori',
            'type'           => 'category_menu',
            'description'    => 'Menu horizontal scrollable untuk filter kategori produk.',
            'icon'           => 'heroicon-o-squares-2x2',
            'is_active'      => true,
            'order_priority' => 2,
        ]);

        $this->createFields($categoryMenu, [
            ['key' => 'title',        'label' => 'Judul Section',    'type' => 'text',   'placeholder' => 'Kategori',                     'is_required' => false, 'order_priority' => 1],
            ['key' => 'show_all',    'label' => 'Tampilkan "Semua"', 'type' => 'toggle', 'default_value' => '1',                       'is_required' => false, 'order_priority' => 2],
        ]);

        $this->createContents($categoryMenu, [
            'title'      => 'Kategori',
            'show_all'  => '1',
        ]);

        // Section 3 — Pilihan Terbaik (Featured Products)
        $featured = $this->createSection($template, [
            'name'           => 'Pilihan Terbaik',
            'type'           => 'featured_products',
            'description'    => 'Tampilkan produk-produk pilihan/terlaris dalam carousel horizontal.',
            'icon'           => 'heroicon-o-sparkles',
            'is_active'      => true,
            'order_priority' => 3,
        ]);

        $this->createFields($featured, [
            ['key' => 'title',         'label' => 'Judul Section',      'type' => 'text',   'placeholder' => 'Pilihan Terbaik',              'is_required' => false, 'order_priority' => 1],
            ['key' => 'subtitle',      'label' => 'Sub Judul',          'type' => 'text',   'placeholder' => 'Produk paling diminati...',      'is_required' => false, 'order_priority' => 2],
            ['key' => 'limit',         'label' => 'Jumlah Produk',       'type' => 'number', 'default_value' => '4',                         'is_required' => false, 'order_priority' => 3],
            ['key' => 'show_discount', 'label' => 'Tampilkan Diskon',    'type' => 'toggle', 'default_value' => '1',                         'is_required' => false, 'order_priority' => 4],
        ]);

        $this->createContents($featured, [
            'title'         => 'Pilihan Terbaik',
            'subtitle'      => 'Produk paling diminati pelanggan kami',
            'limit'         => '4',
            'show_discount' => '1',
        ]);

        // Section 4 — Flash Sale
        $flashSale = $this->createSection($template, [
            'name'           => 'Flash Sale',
            'type'           => 'flash_sale',
            'description'    => 'Section promo dengan countdown timer dan produk-produk diskon.',
            'icon'           => 'heroicon-o-bolt',
            'is_active'      => true,
            'order_priority' => 4,
        ]);

        $this->createFields($flashSale, [
            ['key' => 'title',         'label' => 'Judul Section',      'type' => 'text',   'placeholder' => 'Flash Sale',                   'is_required' => false, 'order_priority' => 1],
            ['key' => 'subtitle',      'label' => 'Sub Judul',          'type' => 'text',   'placeholder' => 'Diskon s/d 70%',              'is_required' => false, 'order_priority' => 2],
            ['key' => 'show_timer',   'label' => 'Tampilkan Timer',    'type' => 'toggle', 'default_value' => '1',                         'is_required' => false, 'order_priority' => 3],
            ['key' => 'limit',         'label' => 'Jumlah Produk',       'type' => 'number', 'default_value' => '8',                         'is_required' => false, 'order_priority' => 4],
        ]);

        $this->createContents($flashSale, [
            'title'         => 'Flash Sale',
            'subtitle'      => 'Diskon s/d 70% untuk produk pilihan',
            'show_timer'    => '1',
            'limit'         => '8',
        ]);

        // Section 5 — Semua Produk
        $allProducts = $this->createSection($template, [
            'name'           => 'Semua Produk',
            'type'           => 'products_grid',
            'description'    => 'Grid produk lengkap dengan header dan pagination/load more.',
            'icon'           => 'heroicon-o-shopping-bag',
            'is_active'      => true,
            'order_priority' => 5,
        ]);

        $this->createFields($allProducts, [
            ['key' => 'title',         'label' => 'Judul Section',      'type' => 'text',   'placeholder' => 'Semua Produk',                 'is_required' => false, 'order_priority' => 1],
            ['key' => 'subtitle',      'label' => 'Sub Judul',          'type' => 'text',   'placeholder' => 'Jelajahi koleksi lengkap...',   'is_required' => false, 'order_priority' => 2],
            ['key' => 'category_id',   'label' => 'Filter Kategori',    'type' => 'number', 'placeholder' => '0 = semua',                     'is_required' => false, 'order_priority' => 3],
            ['key' => 'limit',         'label' => 'Jumlah Awal',        'type' => 'number', 'default_value' => '10',                        'is_required' => false, 'order_priority' => 4],
            ['key' => 'columns',       'label' => 'Jumlah Kolom',       'type' => 'select', 'default_value' => '4',
                'options' => [
                    ['label' => '2 Kolom', 'value' => '2'],
                    ['label' => '3 Kolom', 'value' => '3'],
                    ['label' => '4 Kolom', 'value' => '4'],
                    ['label' => '5 Kolom', 'value' => '5'],
                ],
                'is_required' => false, 'order_priority' => 5,
            ],
            ['key' => 'show_load_more', 'label' => 'Tampilkan Load More', 'type' => 'toggle', 'default_value' => '1', 'is_required' => false, 'order_priority' => 6],
        ]);

        $this->createContents($allProducts, [
            'title'          => 'Semua Produk',
            'subtitle'       => 'Jelajahi koleksi lengkap produk kami',
            'category_id'    => '0',
            'limit'          => '10',
            'columns'        => '4',
            'show_load_more' => '1',
        ]);

        // Section 6 — Newsletter
        $newsletter = $this->createSection($template, [
            'name'           => 'Newsletter',
            'type'           => 'newsletter',
            'description'    => 'Section email subscription dengan form dan deskripsi promo.',
            'icon'           => 'heroicon-o-envelope',
            'is_active'      => true,
            'order_priority' => 6,
        ]);

        $this->createFields($newsletter, [
            ['key' => 'title',         'label' => 'Judul Section',      'type' => 'text',     'placeholder' => 'Dapatkan Penawaran Spesial',      'is_required' => false, 'order_priority' => 1],
            ['key' => 'subtitle',      'label' => 'Deskripsi',          'type' => 'textarea', 'placeholder' => 'Daftar untuk info produk baru...', 'is_required' => false, 'order_priority' => 2],
            ['key' => 'button_text',  'label' => 'Teks Tombol',        'type' => 'text',     'placeholder' => 'Berlangganan',                   'is_required' => false, 'order_priority' => 3],
            ['key' => 'placeholder',   'label' => 'Placeholder Input',   'type' => 'text',     'placeholder' => 'Masukkan email Anda',           'is_required' => false, 'order_priority' => 4],
            ['key' => 'bg_style',      'label' => 'Style Background',    'type' => 'select',  'default_value' => 'gradient',
                'options' => [
                    ['label' => 'Gradient', 'value' => 'gradient'],
                    ['label' => 'Solid', 'value' => 'solid'],
                    ['label' => 'Minimal', 'value' => 'minimal'],
                ],
                'is_required' => false, 'order_priority' => 5,
            ],
        ]);

        $this->createContents($newsletter, [
            'title'         => 'Dapatkan Penawaran Spesial',
            'subtitle'      => 'Daftar newsletter untuk mendapatkan informasi tentang produk baru dan promo menarik.',
            'button_text'   => 'Berlangganan',
            'placeholder'   => 'Masukkan email Anda',
            'bg_style'      => 'gradient',
        ]);
    }

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
