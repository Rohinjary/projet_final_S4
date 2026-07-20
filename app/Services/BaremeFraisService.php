<?php

namespace App\Services;

use App\Models\BaremeFraisModel;
use App\Models\TypeOperationModel;
use App\Models\PrefixeValableModel;


use DateTime;

class BaremeFraisService
{
    protected $baremeFraisModel;
    protected $typeOperationModel;
    protected $prefixeValableModel;

    public function __construct()
    {
        $this->baremeFraisModel = new BaremeFraisModel();
        $this->typeOperationModel = new TypeOperationModel();
        $this->prefixeValableModel = new PrefixeValableModel();
    }

    public function getAllBaremeFrais()
    {
        return $this->baremeFraisModel->findAll();
    }

    public function getBaremeFraisById($id)
    {
        return $this->baremeFraisModel->find($id);
    }

    public function getBaremeFraisByTypeOperation($typeOperationId)
    {
        return $this->baremeFraisModel->where('type_operation_id', $typeOperationId)->findAll();
    }

    public function getBaremeFraisMontant($typeOperationId, $montant, $datetime)
    {
        return $this->baremeFraisModel
            ->where('type_operation_id', $typeOperationId)
            ->where('montant_min <=', $montant)
            ->where('montant_max >=', $montant)
            ->where('date_ajout <=', $datetime)
            ->orderBy('date_ajout', 'desc')
            ->first();
    }

    public function createBaremeFrais($typeOperationId, $montantMin, $montantMax, $montantFrais)
    {
        $data = [
            'type_operation_id' => $typeOperationId,
            'montant_min' => $montantMin,
            'montant_max' => $montantMax,
            'montant_frais' => $montantFrais,
            'date_ajout' => (new DateTime())->format('Y-m-d H:i:s'),
        ];

        return $this->baremeFraisModel->insert($data);
    }

    public function updateBaremeFrais($id, $typeOperationId, $montantMin, $montantMax, $montantFrais)
    {
        $data = [
            'date_fin' => (new DateTime())->format('Y-m-d H:i:s'),
        ];

        $this->createBaremeFrais($typeOperationId, $montantMin, $montantMax, $montantFrais);

        return $this->baremeFraisModel->update($id, $data);

    }



}