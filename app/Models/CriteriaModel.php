<?php
namespace App\Models;
use CodeIgniter\Model;

class CriteriaModel extends Model
{
    protected $table            = 'criterias';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['code', 'name', 'type', 'default_weight'];
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    protected $validationRules = [
        'code'           => 'required|max_length[5]|is_unique[criterias.code,id,{id}]',
        'name'           => 'required|max_length[100]',
        'type'           => 'required|in_list[cost,benefit]',
        'default_weight' => 'required|decimal|greater_than[0]',
    ];
}