<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIndexesShiftAssignments extends Migration
{
    public function up()
    {
        $this->db->query("
            ALTER TABLE `shift_assignments`
                ADD KEY `idx_emp_shift` (`id_employee`, `id_shift`);
        ");
    }

    public function down()
    {
        $this->db->query("
            ALTER TABLE `shift_assignments`
                DROP INDEX `idx_emp_shift`;
        ");
    }
}
