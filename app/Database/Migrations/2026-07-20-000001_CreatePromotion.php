<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePromotion extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INTEGER',
                'auto_increment' => true,
            ],
            'pourcentage' => [
                'type'    => 'NUMERIC',
                'null'    => false,
            ],
        ]);

        $this->forge->addKey('id', true);

        // $this->forge->addForeignKey('operateur_id', 'operateur', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('promotion', true);
    }

    public function down()
    {
        $this->forge->dropTable('promotion', true);
    }
}