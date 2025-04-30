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
            ],
            [
                'factory_name' => 'KK2B',
                'main_factory' => 'KK2',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'factory_name' => 'KK2C',
                'main_factory' => 'KK2',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'factory_name' => 'KK5',
                'main_factory' => 'KK5',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'factory_name' => 'KK7K',
                'main_factory' => 'KK7',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'factory_name' => 'KK7L',
                'main_factory' => 'KK7',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'factory_name' => 'KK8D',
                'main_factory' => 'KK8',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'factory_name' => 'KK8F',
                'main_factory' => 'KK8',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'factory_name' => 'KK8J',
                'main_factory' => 'KK8',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'factory_name' => 'KK9',
                'main_factory' => 'KK9',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'factory_name' => 'KK10',
                'main_factory' => 'KK10',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'factory_name' => 'KK11M',
                'main_factory' => 'KK11',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        // Using Query Builder
        $this->db->table('factories')->insertBatch($data);
    }
}
