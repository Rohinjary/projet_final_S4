<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class InitialDataSeeder extends Seeder
{
    public function run()
    {
        $principalId = $this->insererOperateurPrincipal();
        $this->insererPrefixes($principalId);
        $this->insererTypesOperations();
        $this->insererUtilisateur($principalId);
        $this->insererCommissionPrincipale($principalId);
    }

    private function insererOperateurPrincipal(): int
    {
        $principal = $this->db->table('operateur')
            ->where('est_principal', 1)
            ->orderBy('id', 'ASC')
            ->get()->getRowArray();

        if ($principal !== null) {
            return (int) $principal['id'];
        }

        $this->db->table('operateur')->insert([
            'nom'           => 'MobiPay',
            'est_principal' => 1,
            'date_ajout'    => date('Y-m-d H:i:s'),
        ]);

        return (int) $this->db->insertID();
    }

    private function insererPrefixes(int $principalId): void
    {
        foreach (['033', '037'] as $valeur) {
            $row = $this->db->table('prefixe_valable')->where('prefixe', $valeur)->get()->getRowArray();
            if ($row === null) {
                $this->db->table('prefixe_valable')->insert([
                    'operateur_id' => $principalId,
                    'prefixe'      => $valeur,
                    'date_ajout'   => date('Y-m-d H:i:s'),
                ]);
            } elseif (empty($row['operateur_id'])) {
                $this->db->table('prefixe_valable')->where('id', (int) $row['id'])->update(['operateur_id' => $principalId]);
            }
        }
    }

    private function insererTypesOperations(): void
    {
        foreach (['depot', 'retrait', 'transfert'] as $libelle) {
            if ($this->db->table('type_operation')->where('libelle', $libelle)->countAllResults() === 0) {
                $this->db->table('type_operation')->insert(['libelle' => $libelle]);
            }
        }
    }

    private function insererUtilisateur(int $principalId): void
    {
        $user = $this->db->table('user')->orderBy('id', 'ASC')->get()->getRowArray();
        if ($user === null) {
            $this->db->table('user')->insert([
                'operateur_id' => $principalId,
                'password'     => password_hash('operateur123', PASSWORD_DEFAULT),
                'date_ajout'   => date('Y-m-d H:i:s'),
            ]);
        } elseif (empty($user['operateur_id'])) {
            $this->db->table('user')->where('id', (int) $user['id'])->update(['operateur_id' => $principalId]);
        }
    }

    private function insererCommissionPrincipale(int $principalId): void
    {
        $row = $this->db->table('commission_operateur')->where('operateur_id', $principalId)->get()->getRowArray();
        if ($row === null) {
            $this->db->table('commission_operateur')->insert([
                'operateur_id' => $principalId,
                'pourcentage'  => 100,
                'date_ajout'   => date('Y-m-d H:i:s'),
            ]);
        }
    }
}
