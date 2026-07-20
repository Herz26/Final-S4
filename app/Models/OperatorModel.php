<?php

namespace App\Models;

use CodeIgniter\Model;

class OperatorModel extends Model
{
    protected $table      = 'operators';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'prefix', 'created_at'];
}
