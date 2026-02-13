<?php

namespace App\Models;

use CodeIgniter\Model;

class TransportationEmissionFactorModel extends Model
{
    protected $table            = 'transportation_emission_factors';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = ['transportation', 'shipment', 'carbon_emission', 'data_source', 'site_link', 'notes', 'created_by', 'created_at', 'updated_at', 'updated_by'];

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
                ->select("transportation_emission_factors.*, HEX(AES_ENCRYPT(transportation_emission_factors.id, '".ENCRYPTION_KEY."')) AS DT_RowId");

        if (!empty($search)) {
            $builder->when($search != '',  static fn($query) =>
                $query->like('transportation', $search, 'both', true, true)
                ->orLike('shipment', $search, 'both', true, true)
                ->orLike('carbon_emission', $search, 'both', true, true)
                ->orLike('data_source', $search, 'both', true, true)
                ->orLike('site_link', $search, 'both', true, true)
                ->orLike('notes', $search, 'both', true, true)
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

    public function getRecordByEncryptedId($id)
    {
        return $this->where("HEX(AES_ENCRYPT(transportation_emission_factors.id, '".ENCRYPTION_KEY."'))", $id)
        ->select("transportation_emission_factors.*, HEX(AES_ENCRYPT(transportation_emission_factors.id, '".ENCRYPTION_KEY."')) AS DT_RowId")->first();
    }
}
