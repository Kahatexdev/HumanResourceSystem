<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateHistoryEmployeeTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_history_employee' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
            ],
            'id_employee' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'id_job_section_old' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'id_factory_old' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'id_job_section_new' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'id_factory_new' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'date_of_change' => [
                'type' => 'DATETIME',
            ],
            'reason' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'id_user' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
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
        $this->forge->addKey('id_history_employee', true);
        $this->forge->addForeignKey('id_employee', 'employees', 'id_employee', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_job_section_old', 'job_sections', 'id_job_section', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_factory_old', 'factories', 'id_factory', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_job_section_new', 'job_sections', 'id_job_section', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_factory_new', 'factories', 'id_factory', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_user', 'users', 'id_user', 'CASCADE', 'CASCADE');
        $this->forge->createTable('history_employees');
    }

    public function down()
    {
        $this->forge->dropForeignKey('history_employees', 'history_employees_id_employee_foreign');
        $this->forge->dropForeignKey('history_employees', 'history_employees_id_job_section_old_foreign');
        $this->forge->dropForeignKey('history_employees', 'history_employees_id_factory_old_foreign');
        $this->forge->dropForeignKey('history_employees', 'history_employees_id_job_section_new_foreign');
        $this->forge->dropForeignKey('history_employees', 'history_employees_id_factory_new_foreign');
        $this->forge->dropForeignKey('history_employees', 'history_employees_id_user_foreign');
        $this->forge->dropTable('history_employees');
    }
}
