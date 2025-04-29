<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'username' => 'Sudo',
                'password' => 'sudo123',
                'role'     => 'Sudo',
                'area'     => '',
            ],
            [
                'username' => 'monitoring',
                'password' => 'monitoring123',
                'role'     => 'Monitoring',
                'area'     => '',
            ],
            [
                'username' => 'mandor',
                'password' => 'mandor123',
                'role'     => 'Mandor',
                'area'     => 'KK1A',
            ],
            [
                'username' => 'trainingschool',
                'password' => 'trainingschool123',
                'role'     => 'TrainingSchool',
                'area'     => '',
            ],
        ];

        // Using Query Builder
        $this->db->table('users')->insertBatch($data);
    }
}
