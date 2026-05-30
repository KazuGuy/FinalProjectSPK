<?php
namespace App\Models;
use CodeIgniter\Model;

class PoiModel extends Model
{
    protected $table         = 'points_of_interest';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['nama_poi', 'latitude', 'longitude'];
    protected $useTimestamps = true;

    protected $validationRules = [
        'nama_poi'  => 'required|max_length[100]',
        'latitude'  => 'required|decimal',
        'longitude' => 'required|decimal',
    ];
}