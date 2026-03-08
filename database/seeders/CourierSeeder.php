<?php

namespace Database\Seeders;

use App\Models\Courier;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CourierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $couriers = [
            [
                'name' => 'Pick Up',
                'fullname' => 'Pick Up',
                'code' => 'pickup',
                'logo' => 'pickup.png',
                'description' => 'Pick Up',
                'is_active' => true,
                'rajaongkir_code' => null,
                'apicoid_code' => null,
            ],
            [
                'name' => 'JNE',
                'fullname' => 'Jalur Nugraha Ekakurir',
                'code' => 'JNE',
                'logo' => 'jne.png',
                'description' => 'Jalur Nugraha Ekakurir',
                'is_active' => true,
                'rajaongkir_code' => 'jne',
                'apicoid_code' => 'JNE',
            ],
            [
                'name' => 'JNE Cargo',
                'fullname' => 'Jalur Nugraha Ekakurir Cargo',
                'code' => 'JNECargo',
                'logo' => 'jne.png',
                'description' => 'Jalur Nugraha Ekakurir Cargo',
                'is_active' => true,
                'rajaongkir_code' => 'jne_cargo',
                'apicoid_code' => 'JNECARGO',
            ],
            [
                'name' => 'J&T Express',
                'fullname' => 'J&T Express',
                'code' => 'JT',
                'logo' => 'jnt.png',
                'description' => 'J&T Express',
                'is_active' => true,
                'rajaongkir_code' => 'JT',
                'apicoid_code' => 'JT',
            ],
            [
                'name' => 'SiCepat Express',
                'fullname' => 'SiCepat Express',
                'code' => 'SiCepat',
                'logo' => 'sicepat.png',
                'description' => 'SiCepat Express',
                'is_active' => true,
                'rajaongkir_code' => 'sicepat',
                'apicoid_code' => 'SICEPAT',
            ],
            [
                'name' => 'SiCepat Cargo',
                'fullname' => 'SiCepat Cargo',
                'code' => 'SiCepatCargo',
                'logo' => 'sicepat-cargo.png',
                'description' => 'SiCepat Express',
                'is_active' => true,
                'rajaongkir_code' => 'sicepat_cargo',
                'apicoid_code' => 'SiCepatCargo',
            ],
            [
                'name' => 'AnterAja',
                'fullname' => 'AnterAja',
                'code' => 'anteraja',
                'logo' => 'anteraja.png',
                'description' => 'AnterAja',
                'is_active' => true,
                'rajaongkir_code' => 'anteraja',
                'apicoid_code' => 'anteraja',
            ],
            [
                'name' => 'Pos Indonesia',
                'fullname' => 'Pos Indonesia',
                'code' => 'pos',
                'logo' => 'pos.png',
                'description' => 'Pos Indonesia',
                'is_active' => true,
                'rajaongkir_code' => 'pos',
                'apicoid_code' => 'Pos',
            ],
            [
                'name' => 'Ninja Express',
                'fullname' => 'Ninja Express',
                'code' => 'Ninja',
                'logo' => 'ninja.png',
                'description' => 'Ninja Express',
                'is_active' => true,
                'rajaongkir_code' => 'Ninja',
                'apicoid_code' => 'Ninja'
            ],
            [
                'name' => 'Lion Parcel',
                'fullname' => 'Lion Parcel',
                'code' => 'lion',
                'logo' => 'lion.png',
                'description' => 'Lion Parcel',
                'is_active' => true,
                'rajaongkir_code' => 'lion',
                'apicoid_code' => 'lion'
            ],
            [
                'name' => 'Wahana',
                'fullname' => 'Wahana',
                'code' => 'wahana',
                'logo' => 'wahana.png',
                'description' => 'Wahana',
                'is_active' => true,
                'rajaongkir_code' => 'wahana',
                'apicoid_code' => 'Wahana'
            ],
            [
                'name' => 'TIKI',
                'fullname' => 'TIKI',
                'code' => 'tiki',
                'logo' => 'tiki.png',
                'description' => 'TIKI',
                'is_active' => true,
                'rajaongkir_code' => 'tiki',
                'apicoid_code' => 'Tiki'
            ],
            [
                'name' => 'SAP Express',
                'fullname' => 'SAP Express',
                'code' => 'SAP',
                'logo' => 'sap.png',
                'description' => 'SAP Express',
                'is_active' => true,
                'rajaongkir_code' => 'sap',
                'apicoid_code' => 'SAP'
            ],
            [
                'name' => 'SAP Lite',
                'fullname' => 'SAP Lite',
                'code' => 'SAPLite',
                'logo' => 'sap.png',
                'description' => 'SAP Lite',
                'is_active' => true,
                'rajaongkir_code' => null,
                'apicoid_code' => 'SAPLite'
            ],
            [
                'name' => 'SAP Cargo',
                'fullname' => 'SAP Cargo',
                'code' => 'SapCargo',
                'logo' => 'sap.png',
                'description' => 'SAP Cargo',
                'is_active' => true,
                'rajaongkir_code' => 'SapCargo',
                'apicoid_code' => 'SapCargo'
            ],
            [
                'name' => 'iDexpress',
                'fullname' => 'iDexpress',
                'code' => 'iDexpress',
                'logo' => 'idexpress.png',
                'description' => 'iDexpress',
                'is_active' => true,
                'rajaongkir_code' => 'iDexpress',
                'apicoid_code' => 'iDexpress'
            ],
            [
                'name' => 'iDlite',
                'fullname' => 'iDlite',
                'code' => 'iDlite',
                'logo' => 'idexpress.png',
                'description' => 'iDlite',
                'is_active' => true,
                'rajaongkir_code' => 'iDlite',
                'apicoid_code' => 'iDlite'
            ],
            [
                'name' => 'iDexpress Cargo',
                'fullname' => 'iDexpress Cargo',
                'code' => 'iDexpressCargo',
                'logo' => 'idexpress.png',
                'description' => 'iDexpress Cargo',
                'is_active' => true,
                'rajaongkir_code' => 'iDexpressCargo',
                'apicoid_code' => 'iDexpressCargo'
            ],
            [
                'name' => 'Paxel',
                'fullname' => 'Paxel',
                'code' => 'paxel',
                'logo' => 'paxel.png',
                'description' => 'Paxel',
                'is_active' => true,
                'rajaongkir_code' => 'paxel',
                'apicoid_code' => 'paxel'
            ],

        ];

        foreach ($couriers as $courier) {
            Courier::updateOrCreate(['code' => $courier['code']], $courier);
        }
    }
}
