<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterUser extends Migration
{
    public function up()
    {
        $this->forge->addColumn('user', [
            'operateur_id' => [
                'type' => 'INTEGER',
                'null' => true,
            ],
        ]);

        $this->forge->addForeignKey('operateur_id', 'operateur', 'id', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        $this->forge->dropColumn('user', [
            'operateur_id',
        ]);
    }
}