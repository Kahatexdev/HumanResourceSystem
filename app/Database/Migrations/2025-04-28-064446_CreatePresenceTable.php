<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePresenceTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_presence' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_employee' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'id_periode' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'permit'   => [
                'type'      => 'INT',
                'constraint' => 11,
                'null'      => true,
            ],
            'sick'   => [
                'type'      => 'INT',
                'constraint' => 11,
                'null'      => true,
            ],
            'absent'   => [
                'type'      => 'INT',
                'constraint' => 11,
                'null'      => true,
            ],
            'leave'   => [
                'type'      => 'INT',
                'constraint' => 11,
                'null'      => true,
            ],
            'id_user' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'created_at'   => [
                'type'      => 'DATETIME',
                'null'      => true,
            ],
            'updated_at'   => [
                'type'      => 'DATETIME',
                'null'      => true,
            ],
        ]);
        $this->forge->addPrimaryKey('id_presence');
        $this->forge->addForeignKey('id_employee', 'employees', 'id_employee', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_periode', 'periodes', 'id_periode', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_user', 'users', 'id_user', 'CASCADE', 'CASCADE');
        $this->forge->createTable('presences');
    }

    public function down()
    {
        $this->forge->dropForeignKey('presences', 'presences_id_employee_foreign');
        $this->forge->dropForeignKey('presences', 'presences_id_periode_foreign');
        $this->forge->dropForeignKey('presences', 'presences_id_user_foreign');
        $this->forge->dropTable('presences');
    }
}
