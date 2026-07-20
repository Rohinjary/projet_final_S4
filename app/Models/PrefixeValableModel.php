<?php

namespace App\Models;

use CodeIgniter\Model;

class PrefixeValableModel extends Model
{
    protected $table = 'prefixe_valable';
    protected $primaryKey = 'id';
    protected $allowedFields = ['operateur_id', 'prefixe', 'date_ajout'];
}