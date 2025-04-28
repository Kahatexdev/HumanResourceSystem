<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateJobRoleTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_job_role' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_main_job_role' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
            ],
            'jobdescription' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'description' => [
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
        $this->forge->addPrimaryKey('id_job_role');
        $this->forge->addForeignKey('id_main_job_role', 'main_job_roles', 'id_main_job_role', 'CASCADE', 'CASCADE');
        $this->forge->createTable('job_roles');
    }

    public function down()
    {
        $this->forge->dropForeignKey('job_roles', 'job_roles_id_main_job_role_foreign');
        $this->forge->dropTable('job_roles');
    }
}
