<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBaremeFrais extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INTEGER',
                'auto_increment' => true,
            ],

            'type_operation_id' => [
                'type' => 'INTEGER',
                'null' => false,
            ],

            'montant_min' => [
                'type' => 'NUMERIC',
                'null' => false,
            ],

            'montant_max' => [
                'type' => 'NUMERIC',
                'null' => false,
            ],

            'montant_frais' => [
                'type' => 'NUMERIC',
                'null' => false,
            ],

            'date_ajout' => [
                'type'    => 'DATETIME',
                'default' => 'CURRENT_TIMESTAMP',
            ],

            'date_fin' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);

        $this->forge->addForeignKey(
            'type_operation_id',
            'type_operation',
            'id',
            'CASCADE',
            'RESTRICT'
        );

        $this->forge->createTable('bareme_frais', true);
    }

    public function down()
    {
        $this->forge->dropTable('bareme_frais', true);
    }
}