<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Shiftdef extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_shift'       => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'shift_name'     => ['type' => 'VARCHAR', 'constraint' => 50],
            'start_time'     => ['type' => 'TIME'],
            'end_time'       => ['type' => 'TIME'],
            'grace_min' => ['type' => 'SMALLINT', 'unsigned' => true, 'default' => 0],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
            'updated_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id_shift', true);
        $this->forge->createTable('shift_defs', true);
    }
    public function down()
    {
        $this->forge->dropTable('shift_defs', true);
    }
}
