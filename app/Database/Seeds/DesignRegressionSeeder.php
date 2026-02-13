<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DesignRegressionSeeder extends Seeder
{
    public function run()
    {
        $insertData = [
            'ECONOMY' => [],
            'MID-RANGE' => [],
            'MUSEUM' => []
        ];

        $insertData['ECONOMY']['M'] = [
            1.000,
            1.000,
            1.000,
            1.008,
            1.000,
            1.111,
            0.076,
            1.831,
            0.000,
            1.327,
            0.107,
            0.000,
            0.000,
            0.000,
            6.579,
            0.000,
            4.350,
            0.289,
            0.145,
            2.795,
            1.281,
            0.000,
            0.000,
            0.028,
            0.010,
            0.007
        ];

        $insertData['ECONOMY']['B'] = [
            0.118,
            0.118,
            0.027,
            0.381,
            0.337,
            0.183,
            0.000,
            0.000,
            0.000,
            0.000,
            0.434,
            0.000,
            0.000,
            0.000,
            0.000,
            0.000,
            0.000,
            0.856,
            0.428,
            0.164,
            0.075,
            0.000,
            0.000,
            0.237,
            0.083,
            0.056
        ];

        $insertData['MID-RANGE']['M'] = [
            1.000,
            1.000,
            1.000,
            0.999,
            1.000,
            0.999,
            0.076,
            1.831,
            0.000,
            1.327,
            0.108,
            0.000,
            0.000,
            0.013,
            6.043,
            1.095,
            4.521,
            0.528,
            0.072,
            2.756,
            1.263,
            0.000,
            0.000,
            0.028,
            0.010,
            0.007
        ];

        $insertData['MID-RANGE']['B'] = [
            0.118,
            0.118,
            0.027,
            0.420,
            0.330,
            0.200,
            0.000,
            0.000,
            0.000,
            0.000,
            0.401,
            0.000,
            0.000,
            0.040,
            0.000,
            6.704,
            0.000,
            1.647,
            0.260,
            0.230,
            0.106,
            0.000,
            0.000,
            0.245,
            0.086,
            0.057
        ];

        $insertData['MUSEUM']['M'] = [
            1.000,
            1.000,
            1.000,
            1.000,
            1.000,
            0.994,
            0.076,
            1.770,
            0.005,
            1.241,
            0.614,
            1.243,
            0.047,
            0.012,
            6.828,
            1.001,
            4.681,
            0.363,
            0.054,
            2.655,
            1.208,
            0.382,
            0.183,
            0.026,
            0.009,
            0.006
        ];

        $insertData['MUSEUM']['B'] = [
            0.118,
            0.118,
            0.042,
            0.521,
            0.433,
            0.330,
            0.000,
            0.000,
            0.000,
            0.000,
            0.000,
            0.000,
            0.163,
            0.043,
            0.000,
            7.687,
            0.000,
            2.771,
            0.408,
            0.476,
            0.261,
            1.495,
            0.000,
            0.285,
            0.101,
            0.067
        ];

        $crateTypeInstance = model('CrateTypeModel')->findAll();
        if($crateTypeInstance) {

            foreach($crateTypeInstance as $crateType) {
                    
                $modelDesignRegressionVariableInstance = model('DesignRegressionVariableModel')->findAll();
                
                if($modelDesignRegressionVariableInstance) {

                    $modelDesignRegressionInstance = model('DesignRegressionModel');
                    
                    foreach($modelDesignRegressionVariableInstance as $key => $designRegressionVariable) {
                        $data = [];
                        $data['crate_type_id'] = $crateType['id'];
                        $data['design_regression_variable_id'] = $designRegressionVariable['id']; 
                        $data['calculate_m'] = $insertData[$crateType['name']]['M'][$key]; 
                        $data['calculate_b'] = $insertData[$crateType['name']]['B'][$key];
                        $data['created_by'] = 1;
                        $modelDesignRegressionInstance->insert($data);
                    }
                }
            }
        }
           
    }
}
