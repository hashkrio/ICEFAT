<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MaterialByCrateDesignSeeder extends Seeder
{
    public function run()
    {

        $crateTypeValues = [
            'ECONOMY' => [
                30,
                16,
                43,
                22,
                22,
                24,
                40,
                43,
                10,
                3,
                10,
                3,
                3,
                7,
                10,
                52,
                58,
                42,
                53,
                51,
            ],
            'MID-RANGE' => [
                30,
                16,
                43,
                22,
                22,
                24,
                40,
                43,
                10,
                3,
                10,
                3,
                3,
                7,
                10,
                52,
                58,
                42,
                53,
                51                
            ],
            'MUSEUM' => [
                30,
                19,
                43,
                22,
                24,
                24,
                40,
                43,
                12,
                3,
                10,
                3,
                3,
                7,
                10,
                52,
                58,
                42,
                53,
                51
            ]
        ];

        $mainTypes = [
            'Object Wrap',
            '2D Tray Panel',
            '2D Tray Panel Edge Tape',
            '2D Tray Panel Foam Perimeter',
            'Foam Cushion Pads',
            'Foam Insulation',
            'Lid Gasket',
            'Lid Gasket Liner Tape',
            'Plywood Crate Walls',
            'Crate Wall Battens',
            'Oversized Crate Wall Seams',
            'Corner Battens',
            'Handles',
            'Skids',
            'Skid Spacers',
            'Lid Closure Hardware',
            'Exterior Seal Paint',
            'Wood Assembly Adhesive',
            'Wood Assembly Staples',
            'Wood Assembly Screws'
        ];

        $data = [];

        $crateTypeInstance = model('CrateTypeModel')->findAll();

        if($crateTypeInstance) {

            foreach($crateTypeInstance as $crateType) {

                $materialTypeGroupByMainType = [];

                $materialByQuantySyncId = $crateTypeValues[$crateType["name"]];
                
                $materialTypes = [
                    'PE film',
                    ($crateType["name"]  == "MUSEUM" ? "Foamcore" : "Cardboard"),
                    'EVA Tape',
                    'Polyethylene foam',
                    ($crateType["name"]  == "MUSEUM" ? "Polyurethane foam" : "Polyethylene foam"),
                    'Polyurethane foam',
                    'Neoprene',
                    'EVA Tape',
                    ($crateType["name"]  == "MUSEUM" ? "MDO Plywood" : "AC Plywood"),
                    'Pine lumber',
                    'Plywood',
                    'Pine lumber',
                    'Pine lumber',
                    'Fir lumber',
                    'Plywood',
                    'Stainless steel',
                    'Water based polyurethane',
                    'Wood glue',
                    'Steel',
                    'Steel'
                ];
                
                foreach($mainTypes as $key => $mainType) {
                    $materialTypeGroupByMainType[$mainType] = [
                        'material_type' => $materialTypes[$key],
                        'material_qty'  => $materialByQuantySyncId[$key]
                    ];
                }

                $modelDesignRegressionVariableInstance = model('DesignRegressionVariableModel')->whereIn('x_var_text', $mainTypes)->findAll();

                if($modelDesignRegressionVariableInstance) {
                    foreach($modelDesignRegressionVariableInstance as $modelDesignRegressionVariable) {
    
                        $objectSurfaceArea = 0.00;
                        $packageFrontArea = 0.00;
                        $packagePerimeterArea = 0.00;
                        $crateSurfaceArea = 0.00;
                        $crateFrontArea = 0.00;
                        $innerPackageHeight = 0.00;
                        $innerPackageWidth = 0.00;
                        $innerPackageDepth = 0.00;
                        $crateMaxDimension = 0.00;
                        $crateVolume = 0.00;
                        $crateFooterPrintArea = 0.00;

                        $modelDimensionValueInstance = model('DimensionValueModel')->where('crate_type_id', (int)$crateType['id'])->findAll();

                        if($modelDimensionValueInstance) {
                            foreach($modelDimensionValueInstance as $modelDimensionValue) {
                                if($modelDimensionValue['name'] == 'Object surface area (m2)') {
                                    $objectSurfaceArea = (float)$modelDimensionValue['value'];
                                } else if($modelDimensionValue['name'] == 'Package front area (m2)') {
                                    $packageFrontArea = (float)$modelDimensionValue['value'];
                                } else if($modelDimensionValue['name'] == 'Package perimeter area (m2)') {
                                    $packagePerimeterArea = (float)$modelDimensionValue['value'];
                                } else if($modelDimensionValue['name'] == 'Crate surface area (m2)') {
                                    $crateSurfaceArea = (float)$modelDimensionValue['value'];
                                } else if($modelDimensionValue['name'] == 'Crate front area (m2)') {
                                    $crateFrontArea = (float)$modelDimensionValue['value'];
                                } else if($modelDimensionValue['name'] == 'Inner package height (m)') {
                                    $innerPackageHeight = (float)$modelDimensionValue['value'];
                                } else if($modelDimensionValue['name'] == 'Inner package width (m)') {
                                    $innerPackageWidth = (float)$modelDimensionValue['value'];
                                } else if($modelDimensionValue['name'] == 'Inner package depth (m)') {
                                    $innerPackageDepth = (float)$modelDimensionValue['value'];
                                } else if($modelDimensionValue['name'] == 'Crate max dimension (m)') {
                                    $crateMaxDimension = (float)$modelDimensionValue['value'];
                                } else if($modelDimensionValue['name'] == 'Crate volume (m3)') {
                                    $crateVolume = (float)$modelDimensionValue['value'];
                                } else if($modelDimensionValue['name'] == 'Crate footprint area (m2)') {
                                    $crateFooterPrintArea = (float)$modelDimensionValue['value'];
                                }
                            }
                        }

                        $modelDesignRegressionInstance = model('DesignRegressionModel')->where('crate_type_id', (int)$crateType['id'])
                        ->where('design_regression_variable_id', $modelDesignRegressionVariable['id'])->findAll();
                        
                        if($modelDesignRegressionInstance) {
                        
                            foreach($modelDesignRegressionInstance as $designRegression) {

                                $calculateM = (float)$designRegression['calculate_m'];
                                $calculateB = (float)$designRegression['calculate_b'];
                                $setValue = false;
                                $materialTypeGroup = [];
                                $emobodiedCarbonFactorUnitPerKg = 0.00;
                                $weightInKg = 0.00;

                                if($modelDesignRegressionVariable['x_var_text'] == 'Object Wrap') {
                                    $weightInKg = ($calculateM*$objectSurfaceArea)+$calculateB;
                                    $setValue = true;
                                } else if($modelDesignRegressionVariable['x_var_text'] == '2D Tray Panel') {
                                    $weightInKg = ($calculateM*$packageFrontArea)+$calculateB;
                                    $setValue = true;
                                } else if($modelDesignRegressionVariable['x_var_text'] == '2D Tray Panel Edge Tape') {
                                    $weightInKg = ($calculateM*$packageFrontArea)+$calculateB;
                                    $setValue = true;
                                } else if($modelDesignRegressionVariable['x_var_text'] == '2D Tray Panel Foam Perimeter') {
                                    $weightInKg = ($calculateM*$packagePerimeterArea)+$calculateB;
                                    $setValue = true;
                                } else if($modelDesignRegressionVariable['x_var_text'] == 'Foam Cushion Pads') {
                                    $weightInKg = ($calculateM*$crateSurfaceArea)+$calculateB;
                                    $setValue = true;
                                } else if($modelDesignRegressionVariable['x_var_text'] == 'Foam Insulation') {
                                    $weightInKg = ($calculateM*$crateSurfaceArea)+$calculateB;
                                    $setValue = true;
                                } else if($modelDesignRegressionVariable['x_var_text'] == 'Lid Gasket') {
                                    $weightInKg = ($calculateM*$crateFrontArea)+$calculateB;
                                    $setValue = true;
                                } else if($modelDesignRegressionVariable['x_var_text'] == 'Lid Gasket Liner Tape') {
                                    $weightInKg = ($calculateM*$crateFrontArea)+$calculateB;
                                    $setValue = true;
                                } else if($modelDesignRegressionVariable['x_var_text'] == 'Plywood Crate Walls') {
                                    $weightInKg = ($calculateM*$crateSurfaceArea)+$calculateB;
                                    $setValue = true;
                                } else if($modelDesignRegressionVariable['x_var_text'] == 'Crate Wall Battens') {
                                    $weightInKg = ($calculateM*$crateSurfaceArea)+$calculateB;
                                    $setValue = true;
                                } else if($modelDesignRegressionVariable['x_var_text'] == 'Oversized Crate Wall Seams') {
                                    $countableOutput = 0;

                                    if($innerPackageHeight > 1.22) {
                                        $countableOutput++;
                                    }

                                    if($innerPackageWidth > 1.22) {
                                        $countableOutput++;
                                    }

                                    if($innerPackageDepth > 1.22) {
                                        $countableOutput++;
                                    }

                                    if($crateMaxDimension > 2.44 || $countableOutput > 1) {
                                        $weightInKg = ($calculateM*$crateMaxDimension)+$calculateB;
                                    }

                                    $setValue = true;
                                } else if($modelDesignRegressionVariable['x_var_text'] == 'Corner Battens') {
                                    $weightInKg = ($calculateM*$crateVolume)+$calculateB;
                                    $setValue = true;
                                } else if($modelDesignRegressionVariable['x_var_text'] == 'Handles') {
                                    $weightInKg = ($calculateM*$crateVolume)+$calculateB;
                                    $setValue = true;
                                } else if($modelDesignRegressionVariable['x_var_text'] == 'Skids') {
                                    $weightInKg = ($calculateM*$crateFooterPrintArea)+$calculateB;
                                    $setValue = true;
                                } else if($modelDesignRegressionVariable['x_var_text'] == 'Skid Spacers') {
                                    $weightInKg = ($calculateM*$crateFooterPrintArea)+$calculateB;
                                    $setValue = true;
                                } else if($modelDesignRegressionVariable['x_var_text'] == 'Lid Closure Hardware') {
                                    $weightInKg = ($calculateM*$crateFrontArea)+$calculateB;
                                    $setValue = true;
                                } else if($modelDesignRegressionVariable['x_var_text'] == 'Exterior Seal Paint') {
                                    $weightInKg = ($calculateM*$crateSurfaceArea)+$calculateB;
                                    $setValue = true;
                                } else if($modelDesignRegressionVariable['x_var_text'] == 'Wood Assembly Adhesive') {
                                    $weightInKg = ($calculateM*$crateSurfaceArea)+$calculateB;
                                    $setValue = true;
                                } else if($modelDesignRegressionVariable['x_var_text'] == 'Wood Assembly Staples') {
                                    $weightInKg = ($calculateM*$crateSurfaceArea)+$calculateB;
                                    $setValue = true;
                                } else if($modelDesignRegressionVariable['x_var_text'] == 'Wood Assembly Screws') {
                                    $weightInKg = ($calculateM*$crateSurfaceArea)+$calculateB;
                                    $setValue = true;
                                }

                                if($setValue === true) {
                                    $materialTypeGroup = $materialTypeGroupByMainType[$modelDesignRegressionVariable['x_var_text']];
                                    $emobodiedCarbonFactorUnitPerKg = $this->getEmobodiedCarbonFactorUnitPerKg($materialTypeGroup['material_qty']);
                                    $data[] = [
                                        'crate_type_id' => (int)$crateType['id'],
                                        'design_regression_variable_id' => (int)$modelDesignRegressionVariable['id'],
                                        'material_type' => $materialTypeGroup['material_type'],
                                        'weight_in_kg' => max(0, $weightInKg),
                                        'embodied_carbon_factor_unit_per_kg' => $emobodiedCarbonFactorUnitPerKg,
                                        'embodied_carbon_factor_in_kg' => $weightInKg*$emobodiedCarbonFactorUnitPerKg,
                                        'material_by_quantity_id' => $materialTypeGroup['material_qty']
                                    ];
                                }
                            }
                        }
                    }
                }
            }
        }

        if(count($data) > 0) {
            $materialByCrateDesignModelInstance = model('MaterialByCrateDesignModel');
            foreach($data as $row) {
                $row['created_by'] = 1;
                $materialByCrateDesignModelInstance->insert($row);
            }
        }
    }

    public function getEmobodiedCarbonFactorUnitPerKg($id) : float {
        $modelMaterialByQuantityInstance = model('MaterialByQuantityModel')->where('crate_design_value', $id)->first();
        
        if($modelMaterialByQuantityInstance) {
            return (float)$modelMaterialByQuantityInstance['embodied_carbon_factor_unit_per_kg'];
        }

        return 0.00;
    }
}