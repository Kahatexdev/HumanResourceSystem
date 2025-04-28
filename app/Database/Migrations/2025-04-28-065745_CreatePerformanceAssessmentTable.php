<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePerformanceAssessmentTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_performance_assessment' => [
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
            'id_main_job_role' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'nilai'   => [
                'type'      => 'INT',
                'constraint' => 11,
                'null'      => true,
            ],
            'id_factory' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
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

        $this->forge->addPrimaryKey('id_performance_assessment');
        $this->forge->addForeignKey('id_employee', 'employees', 'id_employee', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_periode', 'periodes', 'id_periode', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_main_job_role', 'main_job_roles', 'id_main_job_role', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_factory', 'factories', 'id_factory', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_user', 'users', 'id_user', 'CASCADE', 'CASCADE');
        $this->forge->createTable('performance_assessments');
    }

    public function down()
    {
        $this->forge->dropForeignKey('performance_assessments', 'performance_assessments_id_employee_foreign');
        $this->forge->dropForeignKey('performance_assessments', 'performance_assessments_id_periode_foreign');
        $this->forge->dropForeignKey('performance_assessments', 'performance_assessments_id_main_job_role_foreign');
        $this->forge->dropForeignKey('performance_assessments', 'performance_assessments_id_factory_foreign');
        $this->forge->dropForeignKey('performance_assessments', 'performance_assessments_id_user_foreign');
        $this->forge->dropTable('performance_assessments');
    }
}
