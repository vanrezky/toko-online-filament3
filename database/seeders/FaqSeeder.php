<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faqs = [
            [
                'question' => 'Bagaimana cara membeli?',
                'answer' => fake()->paragraph()
            ],
            [
                'question' => 'Apa yang dimaksud Multipromo?',
                'answer' => 'Toppers bisa menggunakan lebih dari satu promo dalam satu kali checkout transaksi.',
            ],
            [
                'question' => 'Apakah saya bisa menggabungkan promo Bebas Ongkir dengan promo lainnya?',
                'answer' => 'Mulai 8 Agustus 2023, kamu bisa memilih promo yang ingin digunakan, Cashback atau Bebas Ongkir. Hal ini sebagai upaya Tokopedia untuk menyediakan preferensi berbagai promo Tokopedia serta memudahkan pengguna dalam memilih dan mengkombinasikan promo sesuai dengan kebutuhan.≈'
            ]
        ];

        foreach ($faqs as $key => $faq) {
            Faq::create($faq);
        }
    }
}
