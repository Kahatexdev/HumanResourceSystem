<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAttendanceLettersTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_letter' => [
                'type'           => 'BIGINT',
                'constraint'     => 20,
                'unsigned'       => true,
                'auto_increment' => true,
            ],

            // relasi ke tabel employees (atau karyawan) kamu
            'employee_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],

            // tipe surat: IZIN / SAKIT / CUTI / DL / dll
            'letter_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'comment'    => 'IZIN, SAKIT, CUTI, DINAS_LUAR, DLL',
            ],

            // range tanggal ketidakhadiran
            'date_from' => [
                'type' => 'DATE',
            ],
            'date_to' => [
                'type' => 'DATE',
            ],

            // cache jumlah hari (optional)
            'total_days' => [
                'type'       => 'SMALLINT',
                'constraint' => 5,
                'unsigned'   => true,
                'null'       => true,
            ],

            // keterangan / alasan
            'reason' => [
                'type' => 'TEXT',
                'null' => true,
            ],

            // status approval
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['PENDING', 'APPROVED', 'REJECTED'],
                'default'    => 'PENDING',
            ],

            // approval info
            'approved_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'approved_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],

            // audit (siapa yang input)
            'created_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],

            // timestamps & soft delete
            'created_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
            ],
            'updated_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
            ],
            'deleted_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
            ],
        ]);

        $this->forge->addKey('id_letter', true);
        $this->forge->addKey('employee_id');
        $this->forge->addKey('letter_type');
        $this->forge->addKey('status');
        $this->forge->addKey('date_from');
        $this->forge->addKey(['employee_id', 'date_from']);

        $this->forge->createTable('attendance_letters', true);
    }

    public function down()
    {
        $this->forge->dropTable('attendance_letters', true);
    }
}
