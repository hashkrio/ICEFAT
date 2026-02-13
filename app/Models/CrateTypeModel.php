<?php

namespace App\Models;

use CodeIgniter\Model;

class CrateTypeModel extends Model
{
    protected $table            = 'crate_types';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = ['name', 'is_primary', 'created_by', 'created_at', 'updated_by', 'updated_at'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'id' => 'integer'
    ];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    public function getPagination($limit, $start, $search) : array
    {
        $response = [
            'data' => [],
            'recordsTotal' => 0,
            'recordsFiltered' => 0,
        ];

        $builder = $this->builder();
        
        if (!empty($search)) {
            $builder->when($search != '',  static fn($query) =>
                $query->like('name', $search, 'both', true, true)   
            );
        }

        $builder->orderBy('id', 'ASC');

        // Save the total count of records for pagination
        $totalCount = $builder->countAllResults(false); // Don't reset the query after counting

        $builder->limit($limit, $start);

        $response ['data'] =  $builder->get()->getResultArray();
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $totalCount;    
        
        return $response;
    }

    public function crateTypes() : array
    {
        return $this->findAll();
    }
    
    public function getSelectedCrateType() : array
    {
        return $this->where('is_primary', 1)->findColumn('id');
    }
}
