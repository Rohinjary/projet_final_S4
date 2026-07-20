<?php

namespace App\Services;

use App\Models\CommissionOperateurModel;

class CommissionOperateurService
{
    protected $commissionOperateurModel;

    public function __construct()
    {
        $this->commissionOperateurModel = new CommissionOperateurModel();
    }

    public function getCommissionByOperateurId($operateurId)
    {
        return $this->commissionOperateurModel->where('operateur_id', $operateurId)->first();
    }

    public function createCommission($operateurId, $pourcentage)
    {
        $data = [
            'operateur_id' => $operateurId,
            'pourcentage' => $pourcentage,
            'date_ajout' => (new \DateTime())->format('Y-m-d H:i:s'),
        ];

        return $this->commissionOperateurModel->insert($data);
    }
}