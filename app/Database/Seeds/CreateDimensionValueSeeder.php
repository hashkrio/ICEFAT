<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CreateDimensionValueSeeder extends Seeder
{
    public function run()
    {
        $objectHeight = 0.00;
        $objectWidth = 0.00;
        $objectDepth = 0.00;
        $objectVolume = 0.00;
        $objectSurfaceArea = 0.00;     

        $crateMeasurementModelInstance = model('CrateMeasurementModel')->findAll();

        if($crateMeasurementModelInstance) {

            foreach($crateMeasurementModelInstance as $crateMeasurement) {
                if($crateMeasurement['dimension_name'] == 'Object height (m)') {
                    $objectHeight = (float)($crateMeasurement['dimension_value']);
                }

                if($crateMeasurement['dimension_name'] == 'Object width (m)') {
                    $objectWidth = (float)($crateMeasurement['dimension_value']);
                }

                if($crateMeasurement['dimension_name'] == 'Object depth (m)') {
                    $objectDepth = (float)($crateMeasurement['dimension_value']);
                }
            }
        }

        $objectVolume = $objectHeight * $objectWidth * $objectDepth;

        $objectSurfaceArea = 2 * ($objectHeight * $objectWidth + $objectHeight * $objectDepth + $objectWidth * $objectDepth);

        $crateTypeInstance = model('CrateTypeModel')->findAll();

        if($crateTypeInstance) {

            foreach($crateTypeInstance as $crateType) {

                $innerPackageHeight = 0.00;
                $innerPackageWidth = 0.00;
                $innerPackageDepth = 0.00;
                $packageFrontArea = 0.00;
                $packagePerimeterArea = 0.00;
                $crateOuterDimensionHeight = 0.00;
                $crateOuterDimensionWidth = 0.00;
                $crateOuterDimensionDepth = 0.00;
                $CrateOuterMaxDimension = 0.00;
                $CrateSurfaceArea = 0.00;
                $CrateFootprintArea = 0.00;
                $CrateFrontArea = 0.00;
                $CrateVolume = 0.00;   

                $modelDesignRegressionVariableInstance = model('DesignRegressionVariableModel')->whereIn('x_var_text', [
                    'Inner package height (m)',
                    'Inner package width (m)',
                    'Inner package depth (m)',
                    'Crate outer dimension height (m)',
                    'Crate outer dimension width (m)',
                    'Crate outer dimension depth (m)'
                ])->findAll();

                if($modelDesignRegressionVariableInstance) {
                    foreach($modelDesignRegressionVariableInstance as $modelDesignRegressionVariable) {
    
                        $modelDesignRegressionInstance = model('DesignRegressionModel')->where('crate_type_id', (int)$crateType['id'])
                        ->where('design_regression_variable_id', $modelDesignRegressionVariable['id'])->findAll();
                        
                        if($modelDesignRegressionInstance) {

                            foreach($modelDesignRegressionInstance as $key => $designRegression) {
                                
                                $calculateM = (float)$designRegression['calculate_m'];
                                $calculateB = (float)$designRegression['calculate_b'];
        
                                if($modelDesignRegressionVariable['x_var_text'] == 'Inner package height (m)') {
                                    $innerPackageHeight = ($objectHeight*$calculateM)+$calculateB;
                                }
                                
                                if($modelDesignRegressionVariable['x_var_text'] == 'Inner package width (m)') {
                                    $innerPackageWidth = ($objectWidth*$calculateM)+$calculateB;
                                }
        
                                if($modelDesignRegressionVariable['x_var_text'] == 'Inner package depth (m)') {
                                    $innerPackageDepth = ($objectDepth*$calculateM)+$calculateB;
                                }
        
                                if($modelDesignRegressionVariable['x_var_text'] == 'Crate outer dimension height (m)') {
                                    $crateOuterDimensionHeight = ($objectHeight*$calculateM)+$calculateB;
                                }
                                
                                if($modelDesignRegressionVariable['x_var_text'] == 'Crate outer dimension width (m)') {
                                    $crateOuterDimensionWidth = ($objectWidth*$calculateM)+$calculateB;
                                }
        
                                if($modelDesignRegressionVariable['x_var_text'] == 'Crate outer dimension depth (m)') {
                                    $crateOuterDimensionDepth = ($objectDepth*$calculateM)+$calculateB;
                                }
                            }
                        }
                    }
                }

                $packageFrontArea = $innerPackageHeight * $innerPackageWidth;
                $packagePerimeterArea = 2 * ($innerPackageHeight * $innerPackageDepth + $innerPackageWidth * $innerPackageDepth);
                $CrateOuterMaxDimension =  max([
                    $crateOuterDimensionHeight,
                    $crateOuterDimensionWidth,
                    $crateOuterDimensionDepth,
                ]);
                $CrateSurfaceArea = 2 * ($crateOuterDimensionHeight * $crateOuterDimensionWidth + $crateOuterDimensionWidth * $crateOuterDimensionDepth + $crateOuterDimensionHeight * $crateOuterDimensionDepth);

                $CrateFootprintArea = ($crateOuterDimensionWidth * $crateOuterDimensionDepth);
                $CrateFrontArea = ($crateOuterDimensionHeight * $crateOuterDimensionWidth);
                $CrateVolume = $crateOuterDimensionHeight * $crateOuterDimensionWidth * $crateOuterDimensionDepth;

                $dimensionNames = [
                    'Object volume (m3)' => $objectVolume,
                    'Object surface area (m2)' => $objectSurfaceArea,
                    'Inner package height (m)' => $innerPackageHeight,
                    'Inner package width (m)' => $innerPackageWidth,
                    'Inner package depth (m)' => $innerPackageDepth,
                    'Package front area (m2)' => $packageFrontArea,
                    'Package perimeter area (m2)' => $packagePerimeterArea,
                    'Crate outer dimension height (m)' => $crateOuterDimensionHeight,
                    'Crate outer dimension width (m)' => $crateOuterDimensionWidth,
                    'Crate outer dimension depth (m)' => $crateOuterDimensionDepth,
                    'Crate max dimension (m)' => $CrateOuterMaxDimension,
                    'Crate surface area (m2)' => $CrateSurfaceArea,
                    'Crate footprint area (m2)' => $CrateFootprintArea,
                    'Crate front area (m2)' => $CrateFrontArea,
                    'Crate volume (m3)' => $CrateVolume,
                ];
        
                $modelDimensionValueInstance = model('DimensionValueModel');
                foreach($dimensionNames as $key => $dimensionName) {
                    $data = [];
                    $data['name'] = $key;
                    $data['value'] = $dimensionName;
                    $data['crate_type_id'] = (int)$crateType['id'];
                    $modelDimensionValueInstance->insert($data);
                }
            }
        }

        
    }
}
