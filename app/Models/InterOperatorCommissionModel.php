<?php

namespace App\Models;

use CodeIgniter\Model;

class InterOperatorCommissionModel extends Model
{
    protected $table      = 'inter_operator_commissions';
    protected $primaryKey = 'id';
    protected $allowedFields = ['from_operator_id', 'to_operator_id', 'commission_percentage', 'created_at'];
}
