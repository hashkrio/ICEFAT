<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ModelOptionSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'name' => 'By Material Quantities',
            ],
            [
                'name' => 'By Crate Designs',
            ],
        ];


        $modelOptionInstance = model('ModelOptionModel');
        
        foreach($data as $row) {
            $row['created_by'] = 1;
            $modelOptionInstance->insert($row);
        }
    }
}
