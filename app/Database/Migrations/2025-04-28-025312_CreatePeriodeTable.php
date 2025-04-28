<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePeriodeTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_periode' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_batch' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'periode_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'start_date' => [
                'type'       => 'DATE',
            ],
            'end_date' => [
                'type'       => 'DATE',
            ],
            'holiday' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['active', 'inactive'],
                'default'    => 'active',
            ],
            'created_at'   => [
                'type'      => 'DATETIME',
                'null'      => true,
            ],
            'updated_at'   => [
                'type'      => 'DATETIME',
                'null'      => true,
            ],
        ]);
        $this->forge->addPrimaryKey('id_periode');
        $this->forge->addForeignKey('id_batch', 'batches', 'id_batch', 'CASCADE', 'CASCADE');
        $this->forge->createTable('periodes');
    }

    public function down()
    {
        $this->forge->dropForeignKey('periodes', 'periodes_id_batch_foreign');
        $this->forge->dropTable('periodes');
    }
}
