<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEpargne extends Migration
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

            'pourcentage' => [
                'type'       => 'NUMERIC',
                'null'       => false,
            ],

            'date_ajout' => [
                'type'    => 'DATETIME',
                'default' => 'CURRENT_TIMESTAMP',
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('client_numero', 'client', 'numero', 'CASCADE', 'CASCADE');

        $this->forge->createTable('epargne_client', true);
    }

    public function down()
    {
        $this->forge->dropTable('epargne_client', true);
    }
}