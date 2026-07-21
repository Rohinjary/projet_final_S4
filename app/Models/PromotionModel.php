<?php

namespace App\Models;

use CodeIgniter\Model;

class PromotionModel extends Model
{
    protected $table = 'promotion';
    protected $primaryKey = 'id';
    protected $allowedFields = ['pourcentage'];
}