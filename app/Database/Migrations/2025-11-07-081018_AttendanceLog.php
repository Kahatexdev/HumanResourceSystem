<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AttendanceLog extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_log' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],

            // ID mesin absensi / terminal
            'terminal_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],

            // NIK yang terbaca (kalau ada)
            'nik' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],

            // Kode kartu / card number di mesin
            'card_no' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],

            // Nama yang terbaca
            'employee_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],

            // department yang terbaca (kalau ada)
            'department' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],

            // Disimpan terpisah biar gampang di-query per hari
            'log_date' => [
                'type' => 'DATE',
                'null' => true,
            ],

            'log_time' => [
                'type' => 'TIME',
                'null' => true,
            ],

            // Sumber log: dari mesin, input manual, atau koreksi
            'source' => [
                'type'       => 'ENUM',
                'constraint' => ['DEVICE', 'MANUAL', 'IMPORT'],
                'default'    => 'MANUAL',
            ],

            // Keterangan/verifikasi dari mesin (Face, Card, Password, dsb)
            'verification_source' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],

            // admin
            'admin' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
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

        $this->forge->addKey('id_log', true);

        $this->forge->createTable('attendance_logs', true);
    }

    public function down()
    {
        $this->forge->dropTable('attendance_logs', true);
    }
}
