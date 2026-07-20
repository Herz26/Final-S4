<?php

namespace App\Models;

use CodeIgniter\Model;

class OperationTypeModel extends Model
{
    protected $table      = 'operation_types';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'description', 'created_at'];
}
