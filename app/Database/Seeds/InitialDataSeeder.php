<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class InitialDataSeeder extends Seeder
{
    public function run()
    {
        $this->insererPrefixes();
        $this->insererTypesOperations();
        $this->insererUtilisateur();
    }

    private function insererPrefixes(): void
    {
        $prefixes = [
            [
                'prefixe'    => '033',
                'date_ajout' => date('Y-m-d H:i:s'),
            ],
            [
                'prefixe'    => '037',
                'date_ajout' => date('Y-m-d H:i:s'),
            ],
        ];

        foreach ($prefixes as $prefixe) {
            $existe = $this->db
                ->table('prefixe_valable')
                ->where('prefixe', $prefixe['prefixe'])
                ->countAllResults();

            if ($existe === 0) {
                $this->db
                    ->table('prefixe_valable')
                    ->insert($prefixe);
            }
        }
    }

    private function insererTypesOperations(): void
    {
        $typesOperations = [
            [
                'libelle' => 'depot',
            ],
            [
                'libelle' => 'retrait',
            ],
            [
                'libelle' => 'transfert',
            ],
        ];

        foreach ($typesOperations as $typeOperation) {
            $existe = $this->db
                ->table('type_operation')
                ->where('libelle', $typeOperation['libelle'])
                ->countAllResults();

            if ($existe === 0) {
                $this->db
                    ->table('type_operation')
                    ->insert($typeOperation);
            }
        }
    }

    private function insererUtilisateur(): void
    {
        $nombreUtilisateurs = $this->db
            ->table('user')
            ->countAllResults();

        if ($nombreUtilisateurs === 0) {
            $this->db
                ->table('user')
                ->insert([
                    'password'   => password_hash(
                        'operateur123',
                        PASSWORD_DEFAULT
                    ),
                    'date_ajout' => date('Y-m-d H:i:s'),
                ]);
        }
    }
}