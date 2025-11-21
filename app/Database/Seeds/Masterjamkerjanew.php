<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class Masterjamkerjanew extends Seeder
{
    public function run()
    {
        $data = [
            [
                'shift_name' => 'A',
                'start_time'     => '14:45:00',
                'end_time'    => '22:45:00',
                'break_time' => 20,
                'grace_min' => 15,
                'created_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'shift_name' => 'A',
                'start_time'     => '22:45:00',
                'end_time'    => '06:45:00',
                'break_time' => 20,
                'grace_min' => 15,
                'created_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'shift_name' => 'C',
                'start_time'     => '06:45:00',
                'end_time'    => '14:45:00',
                'break_time' => 20,
                'grace_min' => 15,
                'created_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'shift_name' => 'C',
                'start_time'     => '14:45:00',
                'end_time'    => '22:45:00',
                'break_time' => 20,
                'grace_min' => 15,
                'created_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'shift_name' => 'C',
                'start_time'     => '22:45:00',
                'end_time'    => '06:45:00',
                'break_time' => 20,
                'grace_min' => 15,
                'created_at'    => date('Y-m-d H:i:s'),
            ]
        ];

        // Using Query Builder
        $this->db->table('shift_defs')->insertBatch($data);
    }
}
