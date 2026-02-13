<?php

namespace App\Models;

use CodeIgniter\Model;

class DesignRegressionVariableModel extends Model
{
    protected $table            = 'design_regression_variables';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = ['x_var', 'x_var_text', 'notes', 'created_by', 'created_at', 'updated_at', 'updated_by'];

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

    // Method to pluck ids
    public function designRregressionVariableIds()
    {
        // Directly use the findColumn() method to get the 'id' column as an array
        return $this->whereIn('x_var_text', [
            'Inner package height (m)',
            'Inner package width (m)',
            'Inner package depth (m)',
            'Crate outer dimension height (m)',
            'Crate outer dimension width (m)',
            'Crate outer dimension depth (m)'
        ])->findColumn('id');
    }
}
