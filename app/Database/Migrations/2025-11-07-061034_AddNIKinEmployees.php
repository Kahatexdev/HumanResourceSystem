<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddNIKinEmployees extends Migration
{
    public function up()
    {
        $this->forge->addColumn('employees', [
            'nik' => [
                'type'       => 'VARCHAR',
                'constraint' => 55,
                'null'       => true,
                'after'      => 'id_employee',
            ],
        ]);

        $this->forge->addColumn('former_employee', [
            'nik' => [
                'type'       => 'VARCHAR',
                'constraint' => 55,
                'null'       => true,
                'after'      => 'id_former_employee',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('employees', 'nik');
        $this->forge->dropColumn('former_employee', 'nik');
    }
}
