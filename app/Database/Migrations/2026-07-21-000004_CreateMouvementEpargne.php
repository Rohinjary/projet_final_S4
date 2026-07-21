<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMouvementEpargne extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INTEGER',
                'auto_increment' => true,
            ],

            'client_numero' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => false,
            ],

            'montant_epargne' => [
                'type'       => 'NUMERIC',
                'null'       => false,
            ],

            'date_epargne' => [
                'type'    => 'DATETIME',
                'default' => 'CURRENT_TIMESTAMP',
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('client_numero', 'client', 'numero', 'CASCADE', 'CASCADE');

        $this->forge->createTable('mouvement_epargne', true);
    }

    public function down()
    {
        $this->forge->dropTable('mouvement_epargne', true);
    }
}