<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateJobSectionTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_job_section' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'job_section_name' => [
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
        $this->forge->addPrimaryKey('id_job_section');
        $this->forge->createTable('job_sections');
    }

    public function down()
    {
        $this->forge->dropTable('job_sections');
    }
}
