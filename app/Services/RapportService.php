<?php

namespace App\Services;

use App\Models\ClientModel;
use App\Models\OperationModel;
use App\Models\TypeOperationModel;

class RapportService
{
    private ClientModel $clientModel;
    private OperationModel $operationModel;
    private TypeOperationModel $typeOperationModel;

    public function __construct()
    {
        $this->clientModel = new ClientModel();
        $this->operationModel = new OperationModel();
        $this->typeOperationModel = new TypeOperationModel();
    }

    /**
     * Répartition mensuelle des frais et des reversements :
     * - une opération destinée à un préfixe principal reste intégralement chez MobiPay ;
     * - aucun taux de commission n'est appliqué à MobiPay : l'opérateur principal
     *   conserve séparément la totalité de ses frais de transfert ;
     * - pour un opérateur partenaire, la commission est calculée sur le montant transféré ;
     * - pour un transfert partenaire : montant à envoyer = montant transféré + commission partenaire ;
     * - les frais de transfert restent intégralement un gain brut MobiPay. La commission
     *   partenaire est une obligation distincte et n'est pas déduite de ces frais.
     */
    public function getGains(int $year, int $month): array
    {
        $operations = $this->operationsForPeriod($year, $month);
        $types = $this->typeMap();
        $configuration = $this->operatorConfiguration();

        $result = [
            'retrait'                       => 0.0,
            'transfert'                     => 0.0,
            'depot'                         => 0.0,
            'total'                         => 0.0,
            'gain_prefixes_principaux'      => 0.0,
            'gains_operations_partenaires'  => 0.0,
            'commissions_dues_partenaires'  => 0.0,
            // Alias conservé pour ne pas casser d'éventuels appels existants.
            'commissions_partenaires'       => 0.0,
            'mes_gains'                     => 0.0,
            'a_reverser'                    => 0.0,
            'operations_avec_frais'         => 0,
            'par_operateur'                 => [],
        ];

        foreach ($operations as $operation) {
            $frais = max(0.0, (float) $operation['frais']);
            $type = $types[(int) $operation['type_operation_id']] ?? 'autre';
            $estTransfert = $type === 'transfert';

            // Les dépôts sans frais n'ont aucun impact sur les gains ou reversements.
            if ($frais <= 0 && ! $estTransfert) {
                continue;
            }

            if ($frais > 0) {
                if (array_key_exists($type, $result)) {
                    $result[$type] += $frais;
                }
                $result['total'] += $frais;
                $result['operations_avec_frais']++;
            }

            /*
             * Pour un transfert, l'opérateur bénéficiaire est déterminé avec le
             * numéro destinataire. L'ancien calcul utilisait client_numero
             * (l'émetteur), ce qui classait à tort le transfert chez MobiPay.
             */
            $numeroOperateur = (string) $operation['client_numero'];
            if ($estTransfert && ! empty($operation['destinataire_numero'])) {
                $numeroOperateur = (string) $operation['destinataire_numero'];
            }

            $prefixe = substr($numeroOperateur, 0, 3);
            $operateurId = $configuration['prefix_to_operator'][$prefixe] ?? $configuration['principal_id'];
            $operateur = $configuration['operators'][$operateurId] ?? $configuration['principal'];
            $estPrincipal = (int) ($operateur['est_principal'] ?? 0) === 1;
            // Le taux de commission ne concerne que les partenaires.
            // MobiPay n'a pas de commission sur le montant : il conserve séparément
            // la totalité des frais de transfert encaissés.
            $pourcentage = $estPrincipal ? 0.0 : ($configuration['commissions'][$operateurId] ?? 0.0);

            $montantTransfere = (! $estPrincipal && $estTransfert)
                ? max(0.0, (float) $operation['montant'])
                : 0.0;
            $commissionOperateur = (! $estPrincipal && $estTransfert)
                ? round($montantTransfere * $pourcentage / 100, 2)
                : 0.0;
            // Les frais et la commission sont deux flux différents :
            // MobiPay conserve l'intégralité des frais encaissés, y compris lorsque
            // le transfert est destiné à un opérateur partenaire.
            $gainRetenu = $frais;
            $aReverser = $estPrincipal
                ? 0.0
                : $montantTransfere + $commissionOperateur;

            if ($estPrincipal) {
                $result['gain_prefixes_principaux'] += $gainRetenu;
            } else {
                $result['gains_operations_partenaires'] += $gainRetenu;
                $result['commissions_dues_partenaires'] += $commissionOperateur;
                // Alias historique : il représente désormais la commission due
                // au partenaire, et non une partie retranchée des frais MobiPay.
                $result['commissions_partenaires'] += $commissionOperateur;
            }

            $result['mes_gains'] += $gainRetenu;
            $result['a_reverser'] += $aReverser;

            if (! isset($result['par_operateur'][$operateurId])) {
                $result['par_operateur'][$operateurId] = [
                    'operateur_id'       => $operateurId,
                    'nom'                => (string) ($operateur['nom'] ?? 'Opérateur non affecté'),
                    'est_principal'      => $estPrincipal,
                    'pourcentage'        => $pourcentage,
                    'prefixes'           => $configuration['operator_prefixes'][$operateurId] ?? [],
                    'nombre_operations'  => 0,
                    'frais_bruts'        => 0.0,
                    'montant_transfere'  => 0.0,
                    'commission_operateur'=> 0.0,
                    'gain_retenu'        => 0.0,
                    // Alias de compatibilité avec les anciennes vues.
                    'commission_retenue' => 0.0,
                    'montant_a_envoyer'  => 0.0,
                ];
            }

            $result['par_operateur'][$operateurId]['nombre_operations']++;
            $result['par_operateur'][$operateurId]['frais_bruts'] += $frais;
            $result['par_operateur'][$operateurId]['montant_transfere'] += $montantTransfere;
            $result['par_operateur'][$operateurId]['commission_operateur'] += $commissionOperateur;
            $result['par_operateur'][$operateurId]['gain_retenu'] += $gainRetenu;
            $result['par_operateur'][$operateurId]['commission_retenue'] += $gainRetenu;
            $result['par_operateur'][$operateurId]['montant_a_envoyer'] += $aReverser;
        }

        $result['par_operateur'] = array_values($result['par_operateur']);
        usort($result['par_operateur'], static function (array $a, array $b): int {
            if ($a['est_principal'] !== $b['est_principal']) {
                return $a['est_principal'] ? -1 : 1;
            }
            return strcmp($a['nom'], $b['nom']);
        });

        return $result;
    }

    public function getAnnualGains(int $year): array
    {
        $rows = [];
        for ($month = 1; $month <= 12; $month++) {
            $rows[$month] = $this->getGains($year, $month);
        }
        return $rows;
    }

    public function getReversements(int $year, int $month): array
    {
        $gains = $this->getGains($year, $month);
        return array_values(array_filter(
            $gains['par_operateur'],
            static fn (array $row): bool => ! $row['est_principal']
        ));
    }

    public function getClientAccounts(string $search = '', string $status = 'tous'): array
    {
        $clients = $this->clientModel->orderBy('date_ajout', 'DESC')->findAll();
        $operations = $this->operationModel->orderBy('date_operation', 'ASC')->findAll();
        $types = $this->typeMap();
        $prefixes = $this->operatorConfiguration();

        $accounts = [];
        foreach ($clients as $client) {
            $numero = (string) $client['numero'];
            $operateurId = $prefixes['prefix_to_operator'][substr($numero, 0, 3)] ?? $prefixes['principal_id'];
            $operateur = $prefixes['operators'][$operateurId] ?? $prefixes['principal'];
            $accounts[$numero] = [
                'numero' => $numero,
                'nom' => trim(((string) ($client['prenom'] ?? '')) . ' ' . ((string) ($client['nom'] ?? ''))),
                'operateur' => (string) ($operateur['nom'] ?? 'Non affecté'),
                'solde' => 0.0,
                'nb_operations' => 0,
                'derniere_activite' => null,
                'statut' => 'inactif',
                'date_ajout' => $client['date_ajout'] ?? null,
            ];
        }

        foreach ($operations as $operation) {
            $source = (string) $operation['client_numero'];
            $destinataire = (string) ($operation['destinataire_numero'] ?? '');
            $type = $types[(int) $operation['type_operation_id']] ?? '';
            $montant = (float) $operation['montant'];
            $frais = (float) $operation['frais'];
            $date = (string) $operation['date_operation'];

            if (isset($accounts[$source])) {
                if ($type === 'depot') {
                    $accounts[$source]['solde'] += $montant - $frais;
                } elseif ($type === 'retrait' || $type === 'transfert') {
                    $accounts[$source]['solde'] -= ($montant + $frais);
                }
                $accounts[$source]['nb_operations']++;
                $accounts[$source]['derniere_activite'] = $date;
                $accounts[$source]['statut'] = 'actif';
            }

            if ($type === 'transfert' && $destinataire !== '' && isset($accounts[$destinataire])) {
                $accounts[$destinataire]['solde'] += $montant;
                $accounts[$destinataire]['nb_operations']++;
                if ($accounts[$destinataire]['derniere_activite'] === null || $date > $accounts[$destinataire]['derniere_activite']) {
                    $accounts[$destinataire]['derniere_activite'] = $date;
                }
                $accounts[$destinataire]['statut'] = 'actif';
            }
        }

        $result = array_values($accounts);
        if (trim($search) !== '') {
            $needle = trim($search);
            $result = array_values(array_filter($result, static function (array $account) use ($needle): bool {
                return str_contains($account['numero'], $needle)
                    || stripos($account['nom'], $needle) !== false
                    || stripos($account['operateur'], $needle) !== false;
            }));
        }
        if (in_array($status, ['actif', 'inactif'], true)) {
            $result = array_values(array_filter($result, static fn (array $account): bool => $account['statut'] === $status));
        }
        usort($result, static fn (array $a, array $b): int => strcmp($b['numero'], $a['numero']));
        return $result;
    }

    public function getClientSummary(): array
    {
        $accounts = $this->getClientAccounts();
        $active = count(array_filter($accounts, static fn (array $item): bool => $item['statut'] === 'actif'));
        return [
            'total'       => count($accounts),
            'actifs'      => $active,
            'inactifs'    => count($accounts) - $active,
            'solde_total' => array_sum(array_column($accounts, 'solde')),
        ];
    }

    private function operationsForPeriod(int $year, int $month): array
    {
        return $this->operationModel
            ->where('date_operation >=', sprintf('%04d-%02d-01 00:00:00', $year, $month))
            ->where('date_operation <', $this->nextMonth($year, $month))
            ->findAll();
    }

    private function operatorConfiguration(): array
    {
        $operatorRows = db_connect()->table('operateur')->orderBy('est_principal', 'DESC')->orderBy('id', 'ASC')->get()->getResultArray();
        $operators = [];
        $principal = null;
        foreach ($operatorRows as $row) {
            $id = (int) $row['id'];
            $operators[$id] = $row;
            if ($principal === null && (int) $row['est_principal'] === 1) {
                $principal = $row;
            }
        }
        if ($principal === null && $operatorRows !== []) {
            $principal = $operatorRows[0];
        }
        if ($principal === null) {
            $principal = ['id' => 0, 'nom' => 'MobiPay', 'est_principal' => 1];
            $operators[0] = $principal;
        }

        $prefixToOperator = [];
        $operatorPrefixes = [];
        foreach (db_connect()->table('prefixe_valable')->orderBy('prefixe', 'ASC')->get()->getResultArray() as $prefix) {
            $operatorId = (int) ($prefix['operateur_id'] ?? 0);
            if ($operatorId <= 0 || ! isset($operators[$operatorId])) {
                $operatorId = (int) $principal['id'];
            }
            $value = (string) $prefix['prefixe'];
            $prefixToOperator[$value] = $operatorId;
            $operatorPrefixes[$operatorId][] = $value;
        }

        return [
            'operators'         => $operators,
            'principal'         => $principal,
            'principal_id'      => (int) $principal['id'],
            'prefix_to_operator'=> $prefixToOperator,
            'operator_prefixes' => $operatorPrefixes,
            'commissions'       => (new CommissionOperateurService())->getLatestMap(),
        ];
    }

    private function typeMap(): array
    {
        $map = [];
        foreach ($this->typeOperationModel->findAll() as $type) {
            $map[(int) $type['id']] = strtolower(trim((string) $type['libelle']));
        }
        return $map;
    }

    private function nextMonth(int $year, int $month): string
    {
        return (new \DateTimeImmutable(sprintf('%04d-%02d-01 00:00:00', $year, $month)))
            ->modify('+1 month')->format('Y-m-d H:i:s');
    }
}
