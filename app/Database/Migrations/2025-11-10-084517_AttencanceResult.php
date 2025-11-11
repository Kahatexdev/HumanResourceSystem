<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AttencanceResult extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_result'       => ['type' => 'BIGINT', 'unsigned' => true, 'auto_increment' => true],
            'id_attendance'   => ['type' => 'BIGINT', 'unsigned' => true],
            'total_work_min'  => ['type' => 'INT', 'default' => 0],
            'total_break_min' => ['type' => 'INT', 'default' => 0],
            'late_min'        => ['type' => 'INT', 'default' => 0],
            'early_leave_min' => ['type' => 'INT', 'default' => 0],
            'overtime_min'    => ['type' => 'INT', 'default' => 0],
            'status_code'     => ['type' => 'ENUM', 'constraint' => ['PRESENT', 'LATE', 'ABSENT', 'LEAVE', 'SICK', 'OFF', 'REMOTE'], 'default' => 'PRESENT'],
            'processed_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id_result', true);
        $this->forge->addKey('id_attendance', false, true);
        $this->forge->addForeignKey('id_attendance', 'attendance_days', 'id_attendance', 'CASCADE', 'CASCADE');
        $this->forge->createTable('attendance_results', true);
    }
    public function down()
    {
        $this->forge->dropTable('attendance_results', true);
    }
}
