<?php

namespace App\Models;

use CodeIgniter\Model;

class EpargneClientModel extends Model
{
    protected $table            = 'epargne_client';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['client_numero', 'pourcentage', 'date_ajout'];
}