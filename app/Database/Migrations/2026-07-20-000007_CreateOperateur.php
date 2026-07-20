<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOperateur extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INTEGER',
                'auto_increment' => true,
            ],

            'nom' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],

            'est_principal' => [
                'type'    => 'INTEGER',
                'default' => 0,
            ],

            'date_ajout' => [
                'type'    => 'DATETIME',
                'default' => 'CURRENT_TIMESTAMP',
            ],
        ]);

        $this->forge->addKey('id', true);

        $this->forge->createTable('operateur', true);
    }

    public function down()
    {
        $this->forge->dropTable('operateur', true);
    }
}