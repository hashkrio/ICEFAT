<?php

namespace App\Models;

use CodeIgniter\Model;

class DesignRegressionModel extends Model
{
    protected $table            = 'design_regressions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = ['crate_type_id', 'design_regression_variable_id', 'calculate_m', 'calculate_b', 'created_by', 'created_at', 'updated_by', 'updated_at'];

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
        ->join('crate_types', 'crate_types.id = design_regressions.crate_type_id')
        ->join('design_regression_variables', 'design_regression_variables.id = design_regressions.design_regression_variable_id')
        ->select('design_regressions.*,crate_types.name, design_regression_variables.x_var, design_regression_variables.x_var_text, design_regression_variables.notes, HEX(AES_ENCRYPT(design_regressions.id, "'.ENCRYPTION_KEY.'")) AS DT_RowId, CAST(design_regressions.calculate_m * '.PER_INCH_VALUE.' as DECIMAL(10,3)) as calculate_m_inches, CAST(design_regressions.calculate_b * '.PER_INCH_VALUE.' as DECIMAL(10,3)) as calculate_b_inches');

        if (!empty($search)) {
            $builder->when($search != '',  static fn($query) =>
                $query->like('crate_types.name', $search, 'both', true, true)
                ->orLike('design_regression_variables.x_var_text', $search, 'both', true, true)
                ->orLike('design_regressions.calculate_m', $search, 'both', true, true)
                ->orLike('design_regressions.calculate_b', $search, 'both', true, true)
                ->orLike('design_regression_variables.x_var', $search, 'both', true, true)
                ->orLike('design_regression_variables.notes', $search, 'both', true, true)
            );
        }

        $builder->orderBy('crate_types.id', 'ASC')
        ->orderBy('design_regression_variable_id', 'ASC');
        
        // Save the total count of records for pagination
        $totalCount = $builder->countAllResults(false); // Don't reset the query after counting

        $builder->limit($limit, $start);

        $response ['data'] =  $builder->get()->getResultArray();
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $totalCount;    
        return $response;
    }

    public function getRecordByEncryptedId($id)
    {
        return $this->where("HEX(AES_ENCRYPT(design_regressions.id, '".ENCRYPTION_KEY."'))", $id)
        ->join('crate_types', 'crate_types.id = design_regressions.crate_type_id')
        ->join('design_regression_variables', 'design_regression_variables.id = design_regressions.design_regression_variable_id')
        ->select('design_regressions.*,crate_types.name, design_regression_variables.x_var, design_regression_variables.x_var_text, design_regression_variables.notes, HEX(AES_ENCRYPT(design_regressions.id, "'.ENCRYPTION_KEY.'")) AS DT_RowId, CAST(design_regressions.calculate_m * '.PER_INCH_VALUE.' as DECIMAL(10,3)) as calculate_m_inches, CAST(design_regressions.calculate_b * '.PER_INCH_VALUE.' as DECIMAL(10,3)) as calculate_b_inches')
        ->first();
    }
}
