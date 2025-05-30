<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AspectSeeder extends Seeder
{
    public function run()
    {
        $data = [
            // Operator
            ['department' => 'OPERATOR', 'aspect' => 'Attendance', 'percentage' => 30.00],
            ['department' => 'OPERATOR', 'aspect' => 'Evaluation', 'percentage' => 15.00],
            ['department' => 'OPERATOR', 'aspect' => '6s', 'percentage' => 15.00],
            ['department' => 'OPERATOR', 'aspect' => 'Quality', 'percentage' => 40.00],

            // Technician
            ['department' => 'MONTIR', 'aspect' => 'Attendance', 'percentage' => 30.00],
            ['department' => 'MONTIR', 'aspect' => 'Evaluation', 'percentage' => 50.00],
            ['department' => 'MONTIR', 'aspect' => '6s', 'percentage' => 15.00],
            ['department' => 'MONTIR', 'aspect' => 'Needle Usage', 'percentage' => 5.00],

            // Rosso Operator
            ['department' => 'ROSSO', 'aspect' => 'Attendance', 'percentage' => 30.00],
            ['department' => 'ROSSO', 'aspect' => 'Evaluation', 'percentage' => 15.00],
            ['department' => 'ROSSO', 'aspect' => '6s', 'percentage' => 15.00],
            ['department' => 'ROSSO', 'aspect' => 'Quality', 'percentage' => 40.00],
        ];

        $this->db->table('evaluation_aspects')->insertBatch($data);
    }
}
