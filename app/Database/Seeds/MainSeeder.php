<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MainSeeder extends Seeder
{
    public function run()
    {
        $this->call('UserSeeder');
        $this->call('CrateTypeSeeder');
        $this->call('ModelOptionSeeder');
        $this->call('CrateMeasurementSeeder');
        $this->call('DesignRegressionVariableSeeder');
        $this->call('DesignRegressionSeeder');
        $this->call('TransportationEmissionFactorSeeder');
        $this->call('CreateDimensionValueSeeder');
        $this->call('MaterialByQuantitySeeder');
        $this->call('MaterialByCrateDesignSeeder');
    }
}
