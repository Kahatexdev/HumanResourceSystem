<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AspectSeeder extends Seeder
{
    public function run()
    {
        $data = [
            // Operator
            ['department' => 'Operator', 'aspect' => 'Attendance', 'percentage' => 30.00],
            ['department' => 'Operator', 'aspect' => 'Evaluation', 'percentage' => 15.00],
            ['department' => 'Operator', 'aspect' => '6s', 'percentage' => 15.00],
            ['department' => 'Operator', 'aspect' => 'Quality', 'percentage' => 40.00],

            // Technician
            ['department' => 'Technician', 'aspect' => 'Attendance', 'percentage' => 30.00],
            ['department' => 'Technician', 'aspect' => 'Evaluation', 'percentage' => 50.00],
            ['department' => 'Technician', 'aspect' => '6s', 'percentage' => 15.00],
            ['department' => 'Technician', 'aspect' => 'Needle Usage', 'percentage' => 5.00],

            // Rosso Operator
            ['department' => 'Rosso Operator', 'aspect' => 'Attendance', 'percentage' => 30.00],
            ['department' => 'Rosso Operator', 'aspect' => 'Evaluation', 'percentage' => 15.00],
            ['department' => 'Rosso Operator', 'aspect' => '6s', 'percentage' => 15.00],
            ['department' => 'Rosso Operator', 'aspect' => 'Quality', 'percentage' => 40.00],
        ];

        $this->db->table('evaluation_aspects')->insertBatch($data);
    }
}
