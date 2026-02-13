<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class TransportationEmissionFactorSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [ 
                'transportation' => 'Passenger',
                'shipment' => 'Air-passenger',
                'carbon_emission' => 149,
                'data_source' => 'GCC',
                'site_link' => 'https://galleryclimatecoalition.org/carbon-calculator/',
                'notes' => 'per passenger-km'
            ],
            [ 
                'transportation' => 'Freight',
                'shipment' => 'Air-freight',
                'carbon_emission' => 1.130,
                'data_source' => 'GCC',
                'site_link' => 'https://galleryclimatecoalition.org/carbon-calculator/',
                'notes' => null
            ],
            [ 
                'transportation' => 'Freight',
                'shipment' => 'Road-freight',
                'carbon_emission' => 0.11,
                'data_source' => 'GCC',
                'site_link' => 'https://galleryclimatecoalition.org/carbon-calculator/',
                'notes' => 'PLUS 50 g CO2e per kg crate weight'
            ],
            [ 
                'transportation' => 'Freight',
                'shipment' => 'Sea-freight',
                'carbon_emission' => 0.011,
                'data_source' => 'GCC',
                'site_link' => 'https://galleryclimatecoalition.org/carbon-calculator/',
                'notes' => 'PLUS 50 g CO2e per kg crate weight'
            ],
        ];

        $transportationEmissionFactorModelInstance = model('TransportationEmissionFactorModel');
        
        foreach($data as $row) {
            $row['created_by'] = 1;
            $transportationEmissionFactorModelInstance->insert($row);
        }
    }
}
