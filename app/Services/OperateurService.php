<?php

namespace App\Services;

use App\Models\OperateurModel;

class OperateurService
{
    protected $operateurModel;

    public function __construct()
    {
        $this->operateurModel = new OperateurModel();
    }

    public function getAllOperateurs()
    {
        return $this->operateurModel->findAll();
    }

    public function getOperateurById($id)
    {
        return $this->operateurModel->find($id);
    }

    public function createOperateur($nom, $est_principal = 0)
    {
        $data = [
            'nom' => $nom,
            'est_principal' => $est_principal,
            'date_ajout' => date('Y-m-d H:i:s'),
        ];

        return $this->operateurModel->insert($data);
    }
}