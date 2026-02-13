<?php

namespace App\Models;

use CodeIgniter\Model;

class CarbonFootPrint extends Model
{
    protected $table            = 'carbon_footprints';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'carbon_footer_print',
        'crate_type_id',
        'crate_type_name',
        'crate_weight',
        'embodied_carbon_factor_in_kg',
        'model_option_id',
        'model_option_name',
        'number_of_oneway_trips',
        'per_oneway_trip',
        'shipment_air_passenger',
        'shipment_air_freight',
        'shipment_road_freight',
        'shipment_sea_freight',
        'total_carbon_foot_print',
        'created_by', 
        'created_at', 
        'updated_by', 
        'updated_at'
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

        $builder = $this->builder();

        if (!empty($search)) {
            $builder->when($search != '',  static fn($query) =>
                $query->like('crate_type_name', $search, 'both', true, true)
                    ->orLike('total_carbon_foot_print', $search)
            );
        }
                    
        $builder->orderBy('id', 'DESC');
        
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
        $model = $this->find($id);

        $response = [
            'crate_type_name' => '',
            'model_option_name' => '',
            'crate_weight' => '',
            'embodied_carbon_factor_in_kg' => '',
            'number_of_oneway_trips' => '',
            'per_oneway_trip' => '',
            'shipment_air_passenger' => '',
            'shipment_air_freight' => '',
            'shipment_road_freight' => '',
            'shipment_sea_freight' => '',
            'carbon_footer_print' => '',
            'total_carbon_foot_print' => '',
        ];

        if($model) {
            $response['crate_type_name'] = $model['crate_type_name'];
            $response['model_option_name'] = $model['model_option_name'];
            $response['crate_weight'] = $model['crate_weight'];
            $response['embodied_carbon_factor_in_kg'] = $model['embodied_carbon_factor_in_kg'];
            $response['number_of_oneway_trips'] = $model['number_of_oneway_trips'];
            $response['per_oneway_trip'] = $model['per_oneway_trip'];
            $response['shipment_air_passenger'] = $model['shipment_air_passenger'];
            $response['shipment_air_freight'] = $model['shipment_air_freight'];
            $response['shipment_road_freight'] = $model['shipment_road_freight'];
            $response['shipment_sea_freight'] = $model['shipment_sea_freight'];
            $response['carbon_footer_print'] = $model['carbon_footer_print'];
            $response['total_carbon_foot_print'] = $model['total_carbon_foot_print'];
        }

        return $response;
    }
}
