<?php

namespace App\Models;

use CodeIgniter\Model;

class MaterialByCrateDesignModel extends Model
{
    protected $table            = 'material_by_crate_designs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = ['crate_type_id', 'design_regression_variable_id', 'material_type', 'weight_in_kg', 'embodied_carbon_factor_unit_per_kg', 'embodied_carbon_factor_in_kg', 'material_by_quantity_id', 'created_by', 'created_at', 'updated_at', 'updated_by'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'material_by_quantity_id' => 'integer'
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

    public function getPagination(int $limit, int $start, string $search) : array
    {
        $response = [
            'data' => [],
            'recordsTotal' => 0,
            'recordsFiltered' => 0,
        ];

        $builder = $this->builder()
        ->join('crate_types', 'crate_types.id = material_by_crate_designs.crate_type_id')
        ->join('design_regression_variables', 'design_regression_variables.id = material_by_crate_designs.design_regression_variable_id')
        ->select('material_by_crate_designs.*,crate_types.name, design_regression_variables.x_var_text');

        if (!empty($search)) {
            $builder->when($search != '',  static fn($query) =>
                $query->like('crate_types.name', $search, 'both', true, true)
                    ->orLike('design_regression_variables.x_var_text', $search, 'both', true, true)
                    ->orLike('material_type', $search, 'both', true, true)
                    ->orLike('weight_in_kg', $search, 'both', true, true)
                    ->orLike('embodied_carbon_factor_unit_per_kg', $search, 'both', true, true)
                    ->orLike('embodied_carbon_factor_in_kg', $search, 'both', true, true)
            );
        }

        $builder->orderBy('crate_types.id', 'ASC');
        
        // Save the total count of records for pagination
        $totalCount = $builder->countAllResults(false); // Don't reset the query after counting

        $builder->limit($limit, $start);

        $response ['data'] =  $builder->get()->getResultArray();
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $totalCount;    
        return $response;
    }
}
