<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIndexesShiftDefs extends Migration
{
    public function up()
    {
        // TODO: kalau nama tabel kamu bukan `shift_defs`,
        // ganti semua `shift_defs` di bawah ini.
        $this->db->query("
            ALTER TABLE `shift_defs`
                ADD KEY `idx_shift_start_time` (`start_time`),
                ADD KEY `idx_time_range` (`start_time`, `end_time`);
        ");
    }

    public function down()
    {
        $this->db->query("
            ALTER TABLE `shift_defs`
                DROP INDEX `idx_shift_start_time`,
                DROP INDEX `idx_time_range`;
        ");
    }
}
