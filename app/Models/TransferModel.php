<?php

namespace App\Models;

use CodeIgniter\Model;

class TransferModel extends Model
{
    protected $table      = 'transfers';
    protected $primaryKey = 'id';
    protected $allowedFields = ['transaction_id', 'from_client_id', 'to_client_id', 'from_phone', 'to_phone', 'created_at'];
}
