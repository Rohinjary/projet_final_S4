<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterPrefixeValable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('prefixe_valable', [
            'operateur_id' => [
                'type' => 'INTEGER',
                'null' => true,
            ],
        ]);

        $this->forge->addForeignKey('operateur_id', 'operateur', 'id', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        $this->forge->dropColumn('prefixe_valable', [
            'operateur_id',
        ]);
    }
}