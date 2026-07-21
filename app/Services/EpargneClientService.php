<?php

namespace App\Services;

use App\Models\ClientModel;
use App\Models\EpargneClientModel;

class EpargneClientService
{
    protected $clientModel;
    protected $epargneClientModel;

    public function __construct()
    {
        $this->clientModel = new ClientModel();
        $this->epargneClientModel = new EpargneClientModel();
    }

    public function createEpargne(string $numero, float $pourcentage){
        return $this->epargneClientModel->insert([
            'client_numero' => $numero,
            'pourcentage' => $pourcentage,
        ]);
    }

    public function getEpargneByNumero(string $numero){
        return $this->epargneClientModel
            ->where('client_numero', $numero)
            ->first();
    }
}