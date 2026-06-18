<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'name'        => 'Cuci Setrika Reguler',
                'price'       => 6000,
                'unit'        => 'Kg',
                'description' => 'Pakaian dicuci bersih, wangi, dan disetrika rapi (2-3 hari).',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'name'        => 'Cuci Kering Express',
                'price'       => 10000,
                'unit'        => 'Kg',
                'description' => 'Cucian selesai dalam 1 hari. Tanpa setrika.',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'name'        => 'Cuci Karpet',
                'price'       => 15000,
                'unit'        => 'Meter',
                'description' => 'Pencucian karpet dengan alat khusus agar bebas debu dan tungau.',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
        ];

        // Using Query Builder
        $this->db->table('services')->insertBatch($data);
    }
}
