<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class JobSectionSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'job_section_name' => 'OPERATOR',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'job_section_name' => 'MONTIR',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'job_section_name' => 'ROSSO',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        // Using Query Builder
        $this->db->table('job_sections')->insertBatch($data);
    }
}
