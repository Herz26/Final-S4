<?php

namespace App\Models;

use CodeIgniter\Model;

class AccountModel extends Model
{
    protected $table      = 'accounts';
    protected $primaryKey = 'id';
    protected $allowedFields = ['client_id', 'operator_id', 'solde', 'created_at'];
}
