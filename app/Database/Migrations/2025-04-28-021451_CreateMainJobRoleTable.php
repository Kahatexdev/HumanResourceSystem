<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMainJobRoleTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_main_job_role' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'main_job_role_name' => [
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
        $this->forge->addPrimaryKey('id_main_job_role');
        $this->forge->createTable('main_job_roles');
    }

    public function down()
    {
        $this->forge->dropTable('main_job_roles');
    }
}
