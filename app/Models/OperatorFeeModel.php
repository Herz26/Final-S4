<?php

namespace App\Models;

use CodeIgniter\Model;

class OperatorFeeModel extends Model
{
    protected $table      = 'operator_fees';
    protected $primaryKey = 'id';
    protected $allowedFields = ['operator_id', 'operation_type_id', 'min_amount', 'max_amount', 'fee', 'created_at'];
}
