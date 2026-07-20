<?php

namespace App\Models;

use CodeIgniter\Model;

class CommissionOperateurModel extends Model
{
    protected $table = 'commission_operateur';
    protected $primaryKey = 'id';
    protected $allowedFields = ['operateur_id','pourcentage', 'date_ajout'];
}