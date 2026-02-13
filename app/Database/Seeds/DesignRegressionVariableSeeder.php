<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DesignRegressionVariableSeeder extends Seeder
{
    public function run()
    {
        $xVar = [
            "object height",
            "object width",
            "object depth",
            "object height",
            "object width",
            "object depth",
            "object surface area",
            "package front panel area",
            "constant",
            "package perimeter area",
            "crate surface area",
            "crate surface area",
            "crate front panel area",
            "crate front panel area",
            "crate surface area",
            "crate surface area",
            "crate max dimension",
            "crate volume",
            "crate volume",
            "crate footprint area",
            "crate footprint area",
            "crate front panel area",
            "crate surface area",
            "crate surface area",
            "crate surface area",
            "crate surface area"
        ];
        
        $xVarText = [
            "Inner package height (m)",
            "Inner package width (m)",
            "Inner package depth (m)",
            "Crate outer dimension height (m)",
            "Crate outer dimension width (m)",
            "Crate outer dimension depth (m)",
            "Object Wrap",
            "2D Tray Panel",
            "2D Tray Panel Edge Tape",
            "2D Tray Panel Foam Perimeter",
            "Foam Cushion Pads",
            "Foam Insulation",
            "Lid Gasket",
            "Lid Gasket Liner Tape",
            "Plywood Crate Walls",
            "Crate Wall Battens",
            "Oversized Crate Wall Seams",
            "Corner Battens",
            "Handles",
            "Skids",
            "Skid Spacers",
            "Lid Closure Hardware",
            "Exterior Seal Paint",
            "Wood Assembly Adhesive",
            "Wood Assembly Staples",
            "Wood Assembly Screws"
        ];
        
        $xVarNote = [
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            "triggered by max crate dimension > 1.8m",
            null,
            null,
            null,
            null,
            "included in Wood Assembly Screws for economy and mid-range crates",
            null,
            null,
            null,
            null
        ];

        $modelDesignRegressionVariableInstance = model('DesignRegressionVariableModel');
        
        foreach($xVar as $key => $row) {
            $data = [];
            $data['x_var'] = $row;
            $data['x_var_text'] = $xVarText[$key];
            $data['notes'] = $xVarNote[$key];
            $data['created_by'] = 1;
            $modelDesignRegressionVariableInstance->insert($data);
        }
    }
}
