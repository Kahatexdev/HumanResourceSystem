<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateJarumTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_sj'        => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true
            ],
            'id_employee'  => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'tgl_input'    => [
                'type' => 'DATE'
            ],
            'used_needle'  => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'id_factory'  => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
            'updated_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addPrimaryKey('id_sj');
        $this->forge->addForeignKey('id_employee', 'employees', 'id_employee', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_factory', 'factories', 'id_factory', 'CASCADE', 'CASCADE');
        $this->forge->createTable('sum_jarum');
    }

    public function down()
    {
        $this->forge->dropTable('sum_jarum');
    }
}
