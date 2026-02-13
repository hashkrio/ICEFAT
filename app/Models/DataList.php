<?php

namespace App\Models;

use App\Enums\Unit;
use CodeIgniter\Model;

class DataList extends Model
{
    protected $table            = 'data_lists';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'crate_type_id',
        'crate_type_name',
        'label_name',
        'height',
        'width',
        'depth',
        'measurement_unit_id',
        'measurement_unit_name',
        'weight_in_kg',
        'carbon_footprint',
        'created_by',
        'created_at',
        'updated_at',
        'updated_by',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
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

    public function getPagination($limit, $start, $search)
    {
        $response = [
            'data' => [],
            'recordsTotal' => 0,
            'recordsFiltered' => 0,
        ];

        $builder = $this->builder()
        ->select("data_lists.*, HEX(AES_ENCRYPT(data_lists.id, '".ENCRYPTION_KEY."')) AS DT_RowId")
        ->where('data_lists.created_by', auth()->id())
        ->orderBy('id', 'DESC');
        
        // Save the total count of records for pagination
        $totalCount = $builder->countAllResults(false); // Don't reset the query after counting

        $builder->limit($limit, $start);

        $response ['data'] =  array_map(function($obj) {
            $obj['weight_in_kg'] = $obj['weight_in_kg'].' '.Unit::getUnitLabel((int)$obj['measurement_unit_id']);
            $obj['carbon_footprint_with_unit'] = $obj['carbon_footprint'].' '.Unit::getUnitLabel(Unit::METRIC->value);
            return $obj;
        }, $builder->get()->getResultArray());
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $totalCount;    
        
        return $response;
    }
}
