<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AttendanceDay extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_attendance' => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'id_employee'   => ['type' => 'INT', 'unsigned' => true],
            'work_date'     => ['type' => 'DATE'],
            'id_shift'      => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'in_time'        => ['type' => 'DATETIME', 'null' => true],
            'break_out_time' => ['type' => 'DATETIME', 'null' => true],
            'break_in_time'  => ['type' => 'DATETIME', 'null' => true],
            'out_time'       => ['type' => 'DATETIME', 'null' => true],
            'status_final'  => ['type' => 'ENUM', 'constraint' => ['DRAFT', 'PENDING', 'APPROVED', 'LOCKED'], 'default' => 'DRAFT'],
            'verified_by'   => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'verified_at'   => ['type' => 'DATETIME', 'null' => true],
            'note'          => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
            'updated_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id_attendance', true);
        $this->forge->addUniqueKey(['id_employee', 'work_date']);
        $this->forge->addKey('work_date');
        $this->forge->addForeignKey('id_employee', 'employees', 'id_employee', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('id_shift', 'shift_defs', 'id_shift', 'SET NULL', 'RESTRICT');
        $this->forge->addForeignKey('verified_by', 'users', 'id_user', 'SET NULL', 'RESTRICT');
        $this->forge->createTable('attendance_days', true);
    }
    public function down()
    {
        $this->forge->dropTable('attendance_days', true);
    }
}
