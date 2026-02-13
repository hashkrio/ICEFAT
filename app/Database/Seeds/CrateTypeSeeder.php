<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CrateTypeSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [ 'name' => 'ECONOMY'],
            [ 'name' => 'MID-RANGE'],
            [ 'name' => 'MUSEUM'],
        ];

        $crateTypeModelInstance = model('CrateTypeModel');
        
        foreach($data as $row) {
            $row['created_by'] = 1;
            $crateTypeModelInstance->insert($row);
        }
    }
}
