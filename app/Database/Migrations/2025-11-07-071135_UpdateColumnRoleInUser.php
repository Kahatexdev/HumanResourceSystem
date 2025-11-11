<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateColumnRoleInUser extends Migration
{
    public function up()
    {
        $fields = [
            'role' => [
                'type'       => 'ENUM',
                'constraint' => [
                    'Sudo',
                    'Monitoring',
                    'Mandor',
                    'TrainingSchool',
                    'Absensi'
                ],
                'default'    => 'TrainingSchool',
                'null'       => false,
            ],
        ];

        $this->forge->modifyColumn('users', $fields);
    }


    public function down()
    {
        //
    }
}
