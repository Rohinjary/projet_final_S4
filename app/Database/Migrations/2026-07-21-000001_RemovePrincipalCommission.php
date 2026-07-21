<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemovePrincipalCommission extends Migration
{
    public function up()
    {
        $principalIds = array_column(
            $this->db->table('operateur')
                ->select('id')
                ->where('est_principal', 1)
                ->get()
                ->getResultArray(),
            'id'
        );

        if ($principalIds !== []) {
            $this->db->table('commission_operateur')
                ->whereIn('operateur_id', array_map('intval', $principalIds))
                ->delete();
        }
    }

    public function down()
    {
        $principals = $this->db->table('operateur')
            ->select('id')
            ->where('est_principal', 1)
            ->get()
            ->getResultArray();

        foreach ($principals as $principal) {
            $operateurId = (int) $principal['id'];
            $exists = $this->db->table('commission_operateur')
                ->where('operateur_id', $operateurId)
                ->countAllResults() > 0;

            if (! $exists) {
                $this->db->table('commission_operateur')->insert([
                    'operateur_id' => $operateurId,
                    'pourcentage'  => 100,
                    'date_ajout'   => date('Y-m-d H:i:s'),
                ]);
            }
        }
    }
}
