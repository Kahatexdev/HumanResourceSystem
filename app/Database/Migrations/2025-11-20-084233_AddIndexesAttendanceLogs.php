<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIndexesAttendanceLogs extends Migration
{
    public function up()
    {
        $this->db->query("
            ALTER TABLE `attendance_logs`
                -- UNIQUE terminal + tanggal + jam + nik
                ADD UNIQUE KEY `uq_logs_terminal_date_time_nik`
                    (`terminal_id`,`log_date`,`log_time`,`nik`),

                -- idx_logs_date_nik_time (nik, log_time, log_date)
                ADD KEY `idx_logs_date_nik_time`
                    (`nik`,`log_time`,`log_date`),

                -- idx_logs_date_nik_time_terminal (log_date, nik, log_time, terminal_id)
                ADD KEY `idx_logs_date_nik_time_terminal`
                    (`log_date`,`nik`,`log_time`,`terminal_id`),

                -- idx_nik_logdate (nik, log_date)
                ADD KEY `idx_nik_logdate`
                    (`nik`,`log_date`),

                -- idx_datetime (log_date, log_time)
                ADD KEY `idx_datetime`
                    (`log_date`,`log_time`),

                -- idx_logs_date_nik (log_date, nik)
                ADD KEY `idx_logs_date_nik`
                    (`log_date`,`nik`);
        ");
    }

    public function down()
    {
        $this->db->query("
            ALTER TABLE `attendance_logs`
                DROP INDEX `uq_logs_terminal_date_time_nik`,
                DROP INDEX `idx_logs_date_nik_time`,
                DROP INDEX `idx_logs_date_nik_time_terminal`,
                DROP INDEX `idx_nik_logdate`,
                DROP INDEX `idx_datetime`,
                DROP INDEX `idx_logs_date_nik`;
        ");
    }
}
