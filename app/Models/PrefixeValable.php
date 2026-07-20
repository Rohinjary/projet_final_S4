<?php

namespace App\Models;

use CodeIgniter\Model;

class PrefixeValable extends Model
{
    protected $table = 'prefixe_valable';
    protected $primaryKey = 'id';
    protected $allowedFields = ['prefixe', 'date_ajout'];
}