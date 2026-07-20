<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTypeOperation extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INTEGER',
                'auto_increment' => true,
            ],

            'libelle' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false,
            ],
        ]);

        $this->forge->addKey('id', true);

        $this->forge->createTable('type_operation');
    }

    public function down()
    {
        $this->forge->dropTable('type_operation');
    }
}