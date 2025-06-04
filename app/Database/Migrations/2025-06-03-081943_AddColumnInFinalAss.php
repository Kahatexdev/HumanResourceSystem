<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddColumnInFinalAss extends Migration
{
    public function up()
    {
        // Tambah kolom id_factory ke tabel final_assessment
        $this->forge->addColumn('final_assessment', [
            'id_factory' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true, // Tambahkan null agar tidak error jika kolom kosong saat migrasi
                'after'      => 'id_periode',
            ],
        ]);

        // Tambah foreign key dengan nama eksplisit
        $this->db->query(
            'ALTER TABLE final_assessment 
             ADD CONSTRAINT fk_final_assessment_factory 
             FOREIGN KEY (id_factory) 
             REFERENCES factories(id_factory) 
             ON UPDATE CASCADE 
             ON DELETE CASCADE'
        );
    }

    public function down()
    {
        // Hapus foreign key dulu
        $this->db->query(
            'ALTER TABLE final_assessment 
             DROP FOREIGN KEY fk_final_assessment_factory'
        );

        // Baru hapus kolom
        $this->forge->dropColumn('final_assessment', 'id_factory');
    }
}
