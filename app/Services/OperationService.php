<?php

namespace App\Services;

use App\Models\OperationModel;

class OperationService
{
    protected $operationModel;

    public function __construct()
    {
        $this->operationModel = new OperationModel();
    }

    public function enregistrer(
        string $clientNumero,
        int $typeOperationId,
        float $montant,
        float $frais,
        ?string $destinataireNumero = null,
        float $fraisRetrait = 0.0,
        ?string $referenceTransfert = null,
        int $nbDestinataires = 1
    ) {
        return $this->operationModel->enregistrer(
            $clientNumero,
            $typeOperationId,
            $montant,
            $frais,
            $destinataireNumero,
            $fraisRetrait,
            $referenceTransfert,
            $nbDestinataires
        );
    }

    public function calculerSolde(string $numero): float
    {
        return $this->operationModel->calculerSolde($numero);
    }

    public function getHistorique(string $numero, ?int $typeOperationId = null): array
    {
        return $this->operationModel->getHistorique($numero, $typeOperationId);
    }
}