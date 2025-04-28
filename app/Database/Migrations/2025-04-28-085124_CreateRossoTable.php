<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRossoTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_rosso' => [
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
            'input_date' => [
                'type'       => 'DATETIME',
            ],
            'production' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'rework' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'id_factory' => [
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
            ]
        ]);
        $this->forge->addPrimaryKey('id_rosso');
        $this->forge->addForeignKey('id_employee', 'employees', 'id_employee', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_factory', 'factories', 'id_factory', 'CASCADE', 'CASCADE');
        $this->forge->createTable('rosso'); 
    }

    public function down()
    {
        $this->forge->dropForeignKey('rosso', 'rosso_id_employee_foreign');
        $this->forge->dropForeignKey('rosso', 'rosso_id_factory_foreign');
        $this->forge->dropTable('rosso');
    }
}
