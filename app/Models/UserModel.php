<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'auth_users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = ['username', 'first_name', 'last_name', 'created_at', 'updated_at'];

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
    protected $beforeInsert   = ['hashPassword'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = ['hashPassword'];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        }
        return $data;
    }

    public function getPagination($limit, $start, $search)
    {
        $response = [
            'data' => [],
            'recordsTotal' => 0,
            'recordsFiltered' => 0,
        ];

        $builder = $this->builder()
        ->join('auth_groups_users', 'auth_groups_users.user_id = auth_users.id', 'left')
        ->join('auth_identities', 'auth_identities.user_id = auth_users.id')
        ->select("auth_users.*, auth_identities.secret as email, auth_identities.secret2 as password, HEX(AES_ENCRYPT(auth_users.id, '".ENCRYPTION_KEY."')) AS DT_RowId")
        ->where('auth_users.id !=', auth()->id())
        ->where('auth_users.username != "liquidfly"')
        ->orderBy('id', 'DESC');
        
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
       return $this->where("HEX(AES_ENCRYPT(auth_users.id, '".ENCRYPTION_KEY."'))", $id)
        ->join('auth_identities', 'auth_identities.user_id = auth_users.id')
        ->select("auth_users.*, auth_identities.secret as email, auth_identities.secret2 as password, HEX(AES_ENCRYPT(auth_users.id, '".ENCRYPTION_KEY."')) AS DT_RowId")
        ->first();
    }
    
    public function getRecordById($id)
    {
        $builder = $this->builder();

        $builder->where("auth_users.id", $id);
        
        return $builder->join('auth_identities', 'auth_identities.user_id = auth_users.id')
        ->select("auth_users.id, auth_users.username, auth_identities.secret as email, auth_identities.secret2 as password, HEX(AES_ENCRYPT(auth_users.id, '".ENCRYPTION_KEY."')) AS DT_RowId")
        ->get()->getResultArray();
    }
}
