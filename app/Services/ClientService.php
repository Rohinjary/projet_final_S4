<?php

namespace App\Services;

use App\Models\ClientModel;

class ClientService
{
    protected $clientModel;

    public function __construct()
    {
        $this->clientModel = new ClientModel();
    }

    public function existeParNumero(string $numero)
    {
        return $this->clientModel->existeParNumero($numero);
    }

    public function creerCompte(string $numero, ?string $nom = null, ?string $prenom = null)
    {
        return $this->clientModel->creerCompte($numero, $nom, $prenom);
    }

    public function updateInfos(string $numero, ?string $nom, ?string $prenom)
    {
        return $this->clientModel->updateInfos($numero, $nom, $prenom);
    }
}