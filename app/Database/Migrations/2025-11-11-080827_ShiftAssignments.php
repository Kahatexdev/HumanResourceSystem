<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ShiftAssignments extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_assignment' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_employee' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'id_shift' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'date_of_change' => [
                'type'       => 'DATE',
                'null'       => true,
            ],
            'note' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'created_by' => [
                'type'       => 'INT',
                'unsigned'   => true,
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id_assignment', true);
        $this->forge->addForeignKey('id_employee', 'employees', 'id_employee', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_shift', 'shift_defs', 'id_shift', 'CASCADE', 'CASCADE');
        $this->forge->createTable('shift_assignments');
    }

    public function down()
    {
        $this->forge->dropTable('shift_assignments');
    }
}
