<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePrefixeValable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INTEGER',
                'auto_increment' => true,
            ],

            'prefixe' => [
                'type' => 'VARCHAR',
                'constraint' => 3,
                'null' => false,
            ],

            'date_ajout' => [
                'type' => 'DATETIME',
                'default' => 'CURRENT_TIMESTAMP',
            ],
        ]);

        $this->forge->addKey('id', true);

        $this->forge->createTable('prefixe_valable');
    }

    public function down()
    {
        $this->forge->dropTable('prefixe_valable');
    }
}