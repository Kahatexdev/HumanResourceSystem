<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddColumnInShiftDefs extends Migration
{
    public function up()
    {
        $fields = [
            'break_time' => [
                'type'       => 'SMALLINT',
                'default'    => 0,
                'after'      => 'end_time',
            ],
        ];
        $this->forge->addColumn('shift_defs', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('shift_defs', 'break_time');
    }
}
