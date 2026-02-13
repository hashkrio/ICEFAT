<?php

namespace App\Controllers;
class Home extends BaseController
{
    public function index(): string
    {
        $primaryCrateType = model('CrateTypeModel')->crateTypes();

        return view('dashboard-list-3', [
            'primaryCrateType' => $primaryCrateType
        ])
        .view('dashboard_3_script', [
            // 'primaryCrateType' => $primaryCrateType
            'unitArr' => \App\Enums\Unit::customValues(),
            'primaryCrateType' => array_map(static fn($obj) => [
                'label' => $obj['name'],
                'value' => $obj['id'],
                'selected' => $obj['is_primary'] == "1",
            ], $primaryCrateType)
        ]);
    }
    
    public function getMaterialByQuantity(): string
    {
        return view('material-quantity-list');
    }

    public function getMaterialByCrateDesign(): string
    {
        return view('crate-design-list');
    }
    
    public function getDesignRegression(): string
    {
        return view('design-regression-list');
    }
    
    public function getTransportEmissionFactor(): string
    {
        return view('transportation-emission-factor-list');
    }
    
    public function getCrateType(): string
    {
        return view('crate-type-list');
    }

    public function getModelOption(): string
    {
        return view('model-option-list');
    }
    
    public function add(): string
    {
        $transportationEmissionFactors = model('TransportationEmissionFactorModel')->select('id,shipment')->findAll();
        $primaryCrateType = model('CrateTypeModel')->crateTypes();
        
        return view('dashboard', [
            'transport_emissions' => $transportationEmissionFactors, 
            'primaryCrateType' => $primaryCrateType
        ]).view('dashboard_script', [
            'transport_emissions' => $transportationEmissionFactors, 
            'primaryCrateType' => array_filter($primaryCrateType, static fn($obj) => $obj['is_primary'])
        ]);
    }

    public function users() : string
    {        
        return view('user-list');
    }
}
