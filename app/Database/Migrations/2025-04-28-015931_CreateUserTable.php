<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUserTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_user'          => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'username'    => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'password'    => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'role'       => [
                'type'       => 'ENUM',
                'constraint' => ['Sudo','Monitoring', 'Mandor', 'TrainingSchool'],
                'default'    => 'TrainingSchool',
            ],
            'area'        => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'created_at'  => [
                'type'      => 'DATETIME',
                'null'      => true,
            ],
            'updated_at'  => [
                'type'      => 'DATETIME',
                'null'      => true,
            ],
        ]);
        $this->forge->addPrimaryKey('id_user');
        $this->forge->createTable('users');
    }

    public function down()
    {
        $this->forge->dropTable('users');
    }
}
