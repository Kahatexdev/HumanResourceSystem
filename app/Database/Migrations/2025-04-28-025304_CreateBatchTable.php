<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBatchTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_batch' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'batch_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
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
        $this->forge->addPrimaryKey('id_batch');
        $this->forge->createTable('batches');
    }

    public function down()
    {
        $this->forge->dropTable('batches');
    }
}
