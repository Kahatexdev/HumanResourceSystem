<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTableFormerEmployee extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_former_employee' => [
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
                'constraint' => 20,
            ],
            'gender' => [
                'type'       => 'ENUM',
                'constraint' => ['P', 'L', 'Other'],
                'default'    => 'Other',
            ],
            'job_section_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'factory_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'main_factory' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'employment_status_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'clothes_color' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'holiday' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'additional_holiday' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'date_of_birth' => [
                'type'       => 'DATE',
            ],
            'date_of_joining' => [
                'type'       => 'DATE',
            ],
            'date_of_leaving' => [
                'type'       => 'DATE',
            ],
            'reason_for_leaving' => [
                'type'       => 'TEXT',
            ],
            'id_user' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'created_at' => [
                'type'       => 'DATETIME',
                'null'       => true,
            ],
            'updated_at' => [
                'type'       => 'DATETIME',
                'null'       => true,
            ]
        ]);

        $this->forge->addKey('id_former_employee', true);
        $this->forge->createTable('former_employee');
    }

    public function down()
    {
        $this->forge->dropTable('former_employee');
    }
}
