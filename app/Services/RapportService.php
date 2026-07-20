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

    public function getGains(int $year, int $month): array
    {
        $operations = $this->operationModel
            ->where('date_operation >=', sprintf('%04d-%02d-01 00:00:00', $year, $month))
            ->where('date_operation <', $this->nextMonth($year, $month))
            ->findAll();

        $types = $this->typeMap();
        $retrait = 0.0;
        $transfert = 0.0;

        foreach ($operations as $operation) {
            $type = $types[(int) $operation['type_operation_id']] ?? '';
            $frais = (float) $operation['frais'];

            if ($type === 'retrait') {
                $retrait += $frais;
            } elseif ($type === 'transfert') {
                $transfert += $frais;
            }
        }

        return [
            'retrait' => $retrait,
            'transfert' => $transfert,
            'total' => $retrait + $transfert,
        ];
    }

    public function getAnnualGains(int $year): array
    {
        $rows = [];
        for ($month = 1; $month <= 12; $month++) {
            $rows[$month] = $this->getGains($year, $month);
        }

        return $rows;
    }

    public function getClientAccounts(string $search = '', string $status = 'tous'): array
    {
        $clients = $this->clientModel->orderBy('date_ajout', 'DESC')->findAll();
        $operations = $this->operationModel->orderBy('date_operation', 'ASC')->findAll();
        $types = $this->typeMap();

        $accounts = [];
        foreach ($clients as $client) {
            $numero = (string) $client['numero'];
            $accounts[$numero] = [
                'numero' => $numero,
                'nom' => trim(((string) ($client['prenom'] ?? '')) . ' ' . ((string) ($client['nom'] ?? ''))),
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
                    $accounts[$source]['solde'] += $montant;
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
        $search = trim($search);
        if ($search !== '') {
            $result = array_values(array_filter($result, static function (array $account) use ($search): bool {
                return str_contains($account['numero'], $search)
                    || stripos($account['nom'], $search) !== false;
            }));
        }

        if (in_array($status, ['actif', 'inactif'], true)) {
            $result = array_values(array_filter(
                $result,
                static fn (array $account): bool => $account['statut'] === $status
            ));
        }

        usort($result, static fn (array $a, array $b): int => strcmp($b['numero'], $a['numero']));

        return $result;
    }

    public function getClientSummary(): array
    {
        $accounts = $this->getClientAccounts();
        $active = count(array_filter($accounts, static fn (array $item): bool => $item['statut'] === 'actif'));
        $totalBalance = array_sum(array_column($accounts, 'solde'));

        return [
            'total' => count($accounts),
            'actifs' => $active,
            'inactifs' => count($accounts) - $active,
            'solde_total' => $totalBalance,
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
        $date = new \DateTimeImmutable(sprintf('%04d-%02d-01 00:00:00', $year, $month));
        return $date->modify('+1 month')->format('Y-m-d H:i:s');
    }
}
