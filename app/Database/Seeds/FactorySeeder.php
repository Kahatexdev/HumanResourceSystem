<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class FactorySeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'factory_name' => 'KK1A',
                'main_factory' => 'KK1',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'factory_name' => 'KK1B',
                'main_factory' => 'KK1',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'factory_name' => 'KK2A',
                'main_factory' => 'KK2',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        // Using Query Builder
        $this->db->table('factories')->insertBatch($data);
    }
}
