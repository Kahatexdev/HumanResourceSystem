<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEmployeeTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_employee' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'employee_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'employee_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'shift' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'gender' => [
                'type'       => 'ENUM',
                'constraint' => ['P','L'],
            ],
            'id_job_section' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'id_factory' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'id_employment_status' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'holiday' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'additional_holiday' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'date_of_birth' => [
                'type'       => 'DATE',
                'null'      => true,
            ],
            'date_of_joining' => [
                'type'       => 'DATE',
                'null'      => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['active', 'inactive'],
                'default'    => 'active',
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
        $this->forge->addPrimaryKey('id_employee');
        $this->forge->addForeignKey('id_job_section', 'job_sections', 'id_job_section', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_factory', 'factories', 'id_factory', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_employment_status', 'employment_statuses', 'id_employment_status', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('holiday', 'days', 'id_day', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('additional_holiday', 'days', 'id_day', 'CASCADE', 'CASCADE');
        $this->forge->createTable('employees');
    }

    public function down()
    {
        $this->forge->dropForeignKey('employees', 'employees_id_job_section_foreign');
        $this->forge->dropForeignKey('employees', 'employees_id_factory_foreign');
        $this->forge->dropForeignKey('employees', 'employees_id_employment_status_foreign');
        $this->forge->dropForeignKey('employees', 'employees_holiday_foreign');
        $this->forge->dropForeignKey('employees', 'employees_additional_holiday_foreign');
        $this->forge->dropTable('employees');
    }
}
