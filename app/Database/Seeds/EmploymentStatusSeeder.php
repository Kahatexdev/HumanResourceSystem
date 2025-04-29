<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class EmploymentStatusSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'employment_status_name' => 'KARYAWAN',
                'clothes_color' => 'BIRU',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
        ];

        // Using Query Builder
        $this->db->table('employment_statuses')->insertBatch($data);
    }
}
