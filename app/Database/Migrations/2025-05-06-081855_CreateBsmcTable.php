<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBsmcTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_bsmc'        => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true
            ],
            'id_employee'  => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'tgl_input'    => [
                'type' => 'DATE'
            ],
            'produksi'  => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'bs_mc'  => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'id_factory'  => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
            'updated_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addPrimaryKey('id_bsmc');
        $this->forge->addForeignKey('id_employee', 'employees', 'id_employee', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_factory', 'factories', 'id_factory', 'CASCADE', 'CASCADE');
        $this->forge->createTable('sum_bsmc');
    }

    public function down()
    {
        $this->forge->dropTable('sum_bsmc');
    }
}
