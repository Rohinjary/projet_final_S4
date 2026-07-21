<?php

namespace App\Models;

use CodeIgniter\Model;

class MouvementEpargneModel extends Model
{
    protected $table            = 'mouvement_epargne';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['client_numero', 'montant_epargne', 'date_epargne'];
}