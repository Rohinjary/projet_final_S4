<?php

namespace App\Services;

use App\Models\CommissionOperateurModel;

class CommissionOperateurService
{
    private CommissionOperateurModel $commissionOperateurModel;

    public function __construct()
    {
        $this->commissionOperateurModel = new CommissionOperateurModel();
    }

    public function getCommissionByOperateurId(int $operateurId): ?array
    {
        return $this->commissionOperateurModel
            ->where('operateur_id', $operateurId)
            ->orderBy('date_ajout', 'DESC')
            ->orderBy('id', 'DESC')
            ->first();
    }

    public function getLatestMap(): array
    {
        $map = [];
        $rows = $this->commissionOperateurModel
            ->orderBy('date_ajout', 'DESC')
            ->orderBy('id', 'DESC')
            ->findAll();

        foreach ($rows as $row) {
            $id = (int) $row['operateur_id'];
            if (! isset($map[$id])) {
                $map[$id] = (float) $row['pourcentage'];
            }
        }

        return $map;
    }

    public function saveCommission(int $operateurId, float $pourcentage): bool
    {
        $existing = $this->getCommissionByOperateurId($operateurId);
        if ($existing !== null) {
            return $this->commissionOperateurModel->update((int) $existing['id'], [
                'pourcentage' => $pourcentage,
                'date_ajout'  => date('Y-m-d H:i:s'),
            ]);
        }

        return $this->commissionOperateurModel->insert([
            'operateur_id' => $operateurId,
            'pourcentage'  => $pourcentage,
            'date_ajout'   => date('Y-m-d H:i:s'),
        ]) !== false;
    }

    public function createCommission(int $operateurId, float $pourcentage)
    {
        return $this->saveCommission($operateurId, $pourcentage);
    }
}
