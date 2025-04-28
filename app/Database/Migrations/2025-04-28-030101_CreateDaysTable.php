<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDaysTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_day' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'day_name' => [
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
        $this->forge->addPrimaryKey('id_day');
        $this->forge->createTable('days');
    }

    public function down()
    {
        $this->forge->dropTable('days');
    }
}
