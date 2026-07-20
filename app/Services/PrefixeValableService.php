<?php

namespace App\Services;

use App\Models\PrefixeValableModel;

use DateTime;

class PrefixeValableService
{
    protected $prefixeValableModel;

    public function __construct()
    {
        $this->prefixeValableModel = new PrefixeValableModel();
    }

    public function getAllPrefixeValable()
    {
        return $this->prefixeValableModel->findAll();
    }

    public function getPrefixeValableById($id)
    {
        return $this->prefixeValableModel->find($id);
    }

    public function createPrefixeValable($prefixe)
    {
        $data = [
            'prefixe' => $prefixe,
            'date_ajout' => (new DateTime())->format('Y-m-d H:i:s'),
        ];

        return $this->prefixeValableModel->insert($data);
    }

    public function getPrefixeValableByPrefixe($prefixe)
    {
        return $this->prefixeValableModel->where('prefixe', $prefixe)->first();
    }

    public function getPrefixeValableByOperateurId($operateurId)
    {
        return $this->prefixeValableModel->where('operateur_id', $operateurId)->findAll();
    }

}