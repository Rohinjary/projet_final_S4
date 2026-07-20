<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterOperationV2 extends Migration
{
    public function up()
    {
        $db = db_connect();
        $existingColumns = array_map(
            static fn (array $field): string => $field['name'],
            $db->query('PRAGMA table_info(operation)')->getResultArray()
        );

        $columnsToAdd = [];

        if (! in_array('frais_retrait', $existingColumns, true)) {
            $columnsToAdd['frais_retrait'] = [
                'type'    => 'NUMERIC',
                'null'    => false,
                'default' => 0,
                'after'   => 'frais',
            ];
        }

        if (! in_array('reference_transfert', $existingColumns, true)) {
            $columnsToAdd['reference_transfert'] = [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'frais_retrait',
            ];
        }

        if (! in_array('nb_destinataires', $existingColumns, true)) {
            $columnsToAdd['nb_destinataires'] = [
                'type'    => 'INTEGER',
                'null'    => false,
                'default' => 1,
                'after'   => 'reference_transfert',
            ];
        }

        if ($columnsToAdd !== []) {
            $this->forge->addColumn('operation', $columnsToAdd);
        }
    }

    public function down()
    {
        $this->forge->dropColumn('operation', [
            'frais_retrait',
            'reference_transfert',
            'nb_destinataires',
        ]);
    }
}