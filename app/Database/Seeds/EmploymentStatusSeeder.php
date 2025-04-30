<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class EmploymentStatusSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'employment_status_name' => 'HARIAN',
                'clothes_color' => 'BEBAS',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'employment_status_name' => 'TRAINING',
                'clothes_color' => 'PUTIH',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'employment_status_name' => 'KARYAWAN',
                'clothes_color' => 'BIRU',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'employment_status_name' => 'KARYAWAN',
                'clothes_color' => 'KUNING',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'employment_status_name' => 'STAFF',
                'clothes_color' => 'PINK',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'employment_status_name' => 'STAFF',
                'clothes_color' => 'NAVY',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'employment_status_name' => 'STAFF',
                'clothes_color' => 'HIJAU',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'employment_status_name' => 'STAFF',
                'clothes_color' => 'HITAM',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        // Using Query Builder
        $this->db->table('employment_statuses')->insertBatch($data);
    }
}
