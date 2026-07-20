<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCommissionOperateur extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INTEGER',
                'auto_increment' => true,
            ],

            'operateur_id' => [
                'type'       => 'INTEGER',
                'null'       => false,
            ],

            'pourcentage' => [
                'type'    => 'NUMERIC',
                'null'    => false,
            ],
            
            'date_ajout' => [
                'type'    => 'DATETIME',
                'default' => 'CURRENT_TIMESTAMP',
            ],
        ]);

        $this->forge->addKey('id', true);

        $this->forge->addForeignKey('operateur_id', 'operateur', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('commission_operateur', true);
    }

    public function down()
    {
        $this->forge->dropTable('commission_operateur', true);
    }
}