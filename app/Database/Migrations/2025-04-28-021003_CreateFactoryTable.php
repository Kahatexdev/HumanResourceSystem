<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFactoryTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_factory' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'factory_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'      => true,
            ],
            'main_factory'     => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
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
        $this->forge->addPrimaryKey('id_factory');
        $this->forge->createTable('factories');
    }

    public function down()
    {
        $this->forge->dropTable('factories');
    }
}
