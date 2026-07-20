<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class InitializeOperatorConfiguration extends Migration
{
    public function up()
    {
        $now = date('Y-m-d H:i:s');
        $operatorTable = $this->db->table('operateur');

        $principal = $operatorTable
            ->where('est_principal', 1)
            ->orderBy('id', 'ASC')
            ->get()->getRowArray();

        if ($principal === null) {
            $first = $operatorTable->orderBy('id', 'ASC')->get()->getRowArray();
            if ($first !== null) {
                $operatorTable->where('id', (int) $first['id'])->update(['est_principal' => 1]);
                $principalId = (int) $first['id'];
            } else {
                $operatorTable->insert([
                    'nom'           => 'MobiPay',
                    'est_principal' => 1,
                    'date_ajout'    => $now,
                ]);
                $principalId = (int) $this->db->insertID();
            }
        } else {
            $principalId = (int) $principal['id'];
        }

        // Les anciennes données avaient operateur_id à NULL.
        $this->db->table('user')->where('operateur_id', null)->update(['operateur_id' => $principalId]);
        $this->db->table('prefixe_valable')->where('operateur_id', null)->update(['operateur_id' => $principalId]);

        $commission = $this->db->table('commission_operateur')
            ->where('operateur_id', $principalId)
            ->get()->getRowArray();
        if ($commission === null) {
            $this->db->table('commission_operateur')->insert([
                'operateur_id' => $principalId,
                'pourcentage'  => 100,
                'date_ajout'   => $now,
            ]);
        } else {
            $this->db->table('commission_operateur')
                ->where('id', (int) $commission['id'])
                ->update(['pourcentage' => 100]);
        }

        // Index de cohérence. Les requêtes restent compatibles SQLite/MySQL.
        try {
            $this->db->query('CREATE UNIQUE INDEX IF NOT EXISTS idx_prefixe_valable_unique ON prefixe_valable(prefixe)');
        } catch (\Throwable $e) {
            log_message('warning', 'Index unique préfixe non créé : {message}', ['message' => $e->getMessage()]);
        }
        try {
            $this->db->query('CREATE INDEX IF NOT EXISTS idx_prefixe_operateur ON prefixe_valable(operateur_id)');
        } catch (\Throwable $e) {
            log_message('warning', 'Index opérateur/préfixe non créé : {message}', ['message' => $e->getMessage()]);
        }
    }

    public function down()
    {
        try {
            $this->db->query('DROP INDEX IF EXISTS idx_prefixe_valable_unique');
            $this->db->query('DROP INDEX IF EXISTS idx_prefixe_operateur');
        } catch (\Throwable $e) {
            log_message('warning', 'Suppression des index ignorée : {message}', ['message' => $e->getMessage()]);
        }
    }
}
