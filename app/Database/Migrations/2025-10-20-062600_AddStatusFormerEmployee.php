<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStatusFormerEmployee extends Migration
{
    public function up()
    {
        $this->forge->addColumn('former_employee', [
            'status' => [
                'type'       => "ENUM('0','1')",
                'default'    => '0',
                'null'       => false,
                'after'      => 'id_user',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('former_employee', 'status');
    }
}
