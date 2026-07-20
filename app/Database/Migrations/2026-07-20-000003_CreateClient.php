<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateClient extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'numero' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'null' => false,
            ],

            'nom' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],

            'prenom' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],

            'date_ajout' => [
                'type' => 'DATETIME',
                'default' => 'CURRENT_TIMESTAMP',
            ],
        ]);

        $this->forge->addKey('numero', true);

        $this->forge->createTable('client');
    }

    public function down()
    {
        $this->forge->dropTable('client');
    }
}