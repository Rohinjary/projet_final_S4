<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOperation extends Migration
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

            'type_operation_id' => [
                'type' => 'INTEGER',
                'null' => false,
            ],

            'destinataire_numero' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => true,
            ],

            'montant' => [
                'type' => 'NUMERIC',
                'null' => false,
            ],

            'frais' => [
                'type'    => 'NUMERIC',
                'null'    => false,
                'default' => 0,
            ],

            'date_operation' => [
                'type'    => 'DATETIME',
                'default' => 'CURRENT_TIMESTAMP',
            ],
        ]);

        // Clé primaire
        $this->forge->addKey('id', true);

        // Index utiles
        $this->forge->addKey('client_numero');
        $this->forge->addKey('type_operation_id');
        $this->forge->addKey('destinataire_numero');
        $this->forge->addKey('date_operation');

        // Client qui effectue l'opération
        $this->forge->addForeignKey(
            'client_numero',
            'client',
            'numero',
            'CASCADE',
            'RESTRICT'
        );

        // Type d'opération : dépôt, retrait ou transfert
        $this->forge->addForeignKey(
            'type_operation_id',
            'type_operation',
            'id',
            'CASCADE',
            'RESTRICT'
        );

        // Destinataire pour les transferts
        $this->forge->addForeignKey(
            'destinataire_numero',
            'client',
            'numero',
            'CASCADE',
            'SET NULL'
        );

        $this->forge->createTable('operation', true);
    }

    public function down()
    {
        $this->forge->dropTable('operation', true);
    }
}