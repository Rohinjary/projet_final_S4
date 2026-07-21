<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCommissionOperateurToOperation extends Migration
{
    public function up()
    {
        $fields = $this->db->getFieldNames('operation');
        if (in_array('commission_operateur', $fields, true)) {
            return;
        }

        $this->forge->addColumn('operation', [
            'commission_operateur' => [
                'type'       => 'NUMERIC',
                'null'       => true,
                'default'    => null,
                'constraint' => '12,2',
                'after'      => 'frais_retrait',
            ],
        ]);
    }

    public function down()
    {
        $fields = $this->db->getFieldNames('operation');
        if (in_array('commission_operateur', $fields, true)) {
            $this->forge->dropColumn('operation', 'commission_operateur');
        }
    }
}
