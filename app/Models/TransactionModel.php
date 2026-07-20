<?php

namespace App\Models;

use CodeIgniter\Model;

class TransactionModel extends Model
{
    protected $table      = 'transactions';
    protected $primaryKey = 'id';
    protected $allowedFields = ['account_id', 'operation_type_id', 'amount', 'fee', 'total_debited', 'description', 'created_at'];
}
