<?php

namespace App\Services;

use App\Models\TypeOperationModel;

class TypeOperationService
{
    protected TypeOperationModel $typeOperationModel;

    public function __construct()
    {
        $this->typeOperationModel = new TypeOperationModel();
    }

    public function getAllTypeOperation(): array
    {
        return $this->typeOperationModel->orderBy('id', 'ASC')->findAll();
    }

    public function getTypeOperationById(int $id): ?array
    {
        return $this->typeOperationModel->find($id);
    }

    public function createTypeOperation(string $libelle)
    {
        return $this->typeOperationModel->insert(['libelle' => trim($libelle)]);
    }

    public function libelleExists(string $libelle): bool
    {
        return $this->typeOperationModel
            ->where('LOWER(libelle)', mb_strtolower(trim($libelle)))
            ->countAllResults() > 0;
    }
}
