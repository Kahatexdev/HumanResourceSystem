<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEvaluationAspects extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_aspect' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'department' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'aspect' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'percentage' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
            ],
            'updated_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
            ],
        ]);

        $this->forge->addKey('id_aspect', true); // Primary key
        $this->forge->createTable('evaluation_aspects');
    }

    public function down()
    {
        $this->forge->dropTable('evaluation_aspects');
    }
}
