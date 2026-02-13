<?php

namespace App\Models;

use CodeIgniter\Model;

class MaterialByQuantityModel extends Model
{
    protected $table            = 'material_by_quantities';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = ['crate_type_id', 'main_type', 'material_type', 'weight_in_kg', 'embodied_carbon_factor_unit_per_kg', 'embodied_carbon_factor_in_kg', 'created_by', 'data_source', 'site_link', 'notes', 'crate_design_value', 'created_at', 'updated_by', 'updated_at'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'weight_in_kg' => 'float'
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
    protected $afterFind      = ['decodeOutput'];
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
                ->select("material_by_quantities.*, HEX(AES_ENCRYPT(material_by_quantities.id, '".ENCRYPTION_KEY."')) AS DT_RowId");

        if (!empty($search)) {
            $builder->when($search != '',  static fn($query) =>
                $query->like('main_type', $search, 'both', true, true)
                    ->orLike('material_type', $search, 'both', true, true)
                    ->orLike('weight_in_kg', $search, 'both', true, true)
                    ->orLike('embodied_carbon_factor_unit_per_kg', $search, 'both', true, true)
                    ->orLike('embodied_carbon_factor_in_kg', $search, 'both', true, true)
            );
        }
                    
        $builder->orderBy('crate_design_value', 'ASC');
        
        // Save the total count of records for pagination
        $totalCount = $builder->countAllResults(false); // Don't reset the query after counting

        $builder->limit($limit, $start);
        
        $response ['data'] =  $builder->get()->getResultArray();
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $totalCount;
        
        return $response;
    }
    
    public function getDetails($id)
    {
        $model = $this->where("HEX(AES_ENCRYPT(`material_by_quantities`.`id`, '".ENCRYPTION_KEY."'))", $id)->first();

        $response = [
            'data_source' => '',
            'site_link' => '',
            'notes' => '',
        ];

        if($model) {
            $response['data_source'] = $model['data_source'];
            $response['site_link'] = $model['site_link'];
            $response['notes'] = $model['notes'];
        }

        return $response;
    }
    
    public function getRecordByEncryptedId($id)
    {
        return $this->where("HEX(AES_ENCRYPT(`material_by_quantities`.`id`, '".ENCRYPTION_KEY."'))", $id)
        ->select("material_by_quantities.*, HEX(AES_ENCRYPT(`material_by_quantities`.`id`, '".ENCRYPTION_KEY."')) AS DT_RowId")->first();
    }

    protected function decodeOutput(array $data)
    {
        // Check if it's a single record or multiple records
        if (isset($data['data']))  {
            if(!is_array($data['data'])) {
                // This is a single result from find()
                if(isset($data['data']['material_type'])) {
                    $data['data']['material_type'] = stripslashes($data['data']['material_type']);
                }
            } else {
                // This is a result from findAll()
                foreach ($data['data'] as $key => $user) {
                    if(isset($data['data'][$key]['material_type'])) {
                        $data['data'][$key]['material_type'] = stripslashes($user['material_type']);
                    }
                }
            }
        }
        return $data;
    }
}
