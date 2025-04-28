<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEmploymentStatusTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_employment_status' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'employment_status_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'clothes_color' => [
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
        $this->forge->addPrimaryKey('id_employment_status');
        $this->forge->createTable('employment_statuses');
    }

    public function down()
    {
        $this->forge->dropTable('employment_statuses');
    }
}
