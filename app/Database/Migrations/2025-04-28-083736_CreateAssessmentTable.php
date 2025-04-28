<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAssessmentTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_assessment' => [
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
            'id_job_role' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'id_periode' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'score'   => [
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
            ]
        ]);
        $this->forge->addPrimaryKey('id_assessment');
        $this->forge->addForeignKey('id_employee', 'employees', 'id_employee', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_job_role', 'job_roles', 'id_job_role', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_periode', 'periodes', 'id_periode', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_user', 'users', 'id_user', 'CASCADE', 'CASCADE');
        $this->forge->createTable('assessments');
    }

    public function down()
    {
        $this->forge->dropForeignKey('assessments', 'assessments_id_employee_foreign');
        $this->forge->dropForeignKey('assessments', 'assessments_id_job_role_foreign');
        $this->forge->dropForeignKey('assessments', 'assessments_id_periode_foreign');
        $this->forge->dropForeignKey('assessments', 'assessments_id_user_foreign');
        $this->forge->dropTable('assessments');
    }
}
