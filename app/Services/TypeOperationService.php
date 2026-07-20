<?php

namespace App\Services;

use App\Models\TypeOperationModel;

use DateTime;

class TypeOperationService
{
    protected $typeOperationModel;

    public function __construct()
    {
        $this->typeOperationModel = new TypeOperationModel();
    }

    public function getAllTypeOperation()
    {
        return $this->typeOperationModel->findAll();
    }

    public function getTypeOperationById($id)
    {
        return $this->typeOperationModel->find($id);
    }

    public function createTypeOperation($libelle)
    {
        $data = [
            'libelle' => $libelle,
        ];

        return $this->typeOperationModel->insert($data);
    }

}