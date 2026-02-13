<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CrateMeasurementSeeder extends Seeder
{
    public function run()
    {
        $measurements = [
            ['Object height (m)'],
            ['Object width (m)'],
            ['Object depth (m)'],
        ];

        $measurementValues = [
            [1.96],
            [2.49],
            [0.21],
        ];

        $crateMeasurementModelInstance = model('CrateMeasurementModel');
        
        foreach($measurements as $key => $row) {
            $data = [];
            $data['dimension_name'] = $row;
            $data['dimension_value'] = $measurementValues[$key];
            $data['created_by'] = 1;
            $crateMeasurementModelInstance->insert($data);
        }
    }
}
