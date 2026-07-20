<?php

namespace App\Models;

use CodeIgniter\Model;

class ClientModel extends Model
{
    protected $table            = 'client';
    protected $primaryKey       = 'numero';
    protected $useAutoIncrement = false; // PK = numero (varchar), pas d'auto-increment
    protected $returnType       = 'array';
    protected $allowedFields    = ['numero', 'nom', 'prenom'];
    protected $useTimestamps    = false; // date_ajout gere par SQLite (default current_timestamp)

    public function existeParNumero(string $numero)
    {
        return $this->where('numero', $numero)->first();
    }

    public function creerCompte(string $numero, ?string $nom = null, ?string $prenom = null)
    {
        return $this->insert([
            'numero' => $numero,
            'nom'    => $nom,
            'prenom' => $prenom,
        ]);
    }

    public function updateInfos(string $numero, ?string $nom, ?string $prenom)
    {
        return $this->update($numero, [
            'nom'    => $nom,
            'prenom' => $prenom,
        ]);
    }
}