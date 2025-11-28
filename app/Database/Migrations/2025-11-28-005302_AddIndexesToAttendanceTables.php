<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIndexesToAttendanceTables extends Migration
{
    public function up()
    {
        // Tabel attendance_days
        $this->db->query("
            ALTER TABLE `attendance_days`
                ADD INDEX `idx_attendance_days_work_date` (`work_date`),
                ADD INDEX `idx_attendance_days_id_shift` (`id_shift`),
                ADD INDEX `idx_attendance_days_work_date_shift` (`work_date`, `id_shift`)
        ");

        // Tabel attendance_results
        $this->db->query("
            ALTER TABLE `attendance_results`
                ADD INDEX `idx_attendance_results_work_break` (`total_work_min`, `total_break_min`)
        ");
    }

    public function down()
    {
        // Rollback index di attendance_days
        $this->db->query("
            ALTER TABLE `attendance_days`
                DROP INDEX `idx_attendance_days_work_date`,
                DROP INDEX `idx_attendance_days_id_shift`,
                DROP INDEX `idx_attendance_days_work_date_shift`
        ");

        // Rollback index di attendance_results
        $this->db->query("
            ALTER TABLE `attendance_results`
                DROP INDEX `idx_attendance_results_work_break`
        ");
    }
}
