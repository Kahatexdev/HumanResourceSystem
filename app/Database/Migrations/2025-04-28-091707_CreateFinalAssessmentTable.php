<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFinalAssessmentTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
            ],
            'id_employee' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'id_main_job_role' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'id_periode' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'score_presence' => [
                'type' => 'FLOAT',
                'null' => true,
            ],
            'score_performance_job' => [
                'type' => 'FLOAT',
                'null' => true,
            ],
            'score_performance_6s' => [
                'type' => 'FLOAT',
                'null' => true,
            ],
            'score_productivity' => [
                'type' => 'FLOAT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ]
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('id_employee', 'employees', 'id_employee', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_main_job_role', 'main_job_roles', 'id_main_job_role', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_periode', 'periodes', 'id_periode', 'CASCADE', 'CASCADE');
        $this->forge->createTable('final_assessment');
    }

    public function down()
    {
        $this->forge->dropForeignKey('final_assessment', 'final_assessment_id_employee_foreign');
        $this->forge->dropForeignKey('final_assessment', 'final_assessment_id_main_job_role_foreign');
        $this->forge->dropForeignKey('final_assessment', 'final_assessment_id_periode_foreign');
        $this->forge->dropTable('final_assessment');
    }
}
