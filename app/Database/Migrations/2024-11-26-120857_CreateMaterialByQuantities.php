<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMaterialByQuantities extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'          => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'main_type'    => [
                'type'           => 'VARCHAR',
                'constraint'     => '100',
            ],
            'material_type'    => [
                'type'           => 'VARCHAR',
                'constraint'     => '100',
            ],
            'weight_in_kg'    => [
                'type'           => 'decimal',
                'constraint'     => '10,3',
                'comment'        => 'Weight (kg)'
            ],
            'embodied_carbon_factor_unit_per_kg'    => [
                'type'           => 'decimal',
                'constraint'     => '10,3',
                'comment'        => 'Unit embodied carbon factor (kg CO2e/kg)'
            ],
            'embodied_carbon_factor_in_kg'    => [
                'type'           => 'decimal',
                'constraint'     => '10,3',
                'comment'        => 'Embodied carbon (kg CO2e)'
            ],
            'data_source'    => [
                'type'           => 'VARCHAR',
                'constraint'     => '100',
                'null'           => true,
            ],
            'site_link'    => [
                'type'           => 'VARCHAR',
                'constraint'     => '255',
                'null'           => true,
            ],
            'notes'    => [
                'type'           => 'VARCHAR',
                'constraint'     => '255',
                'null'           => true,
            ],
            'crate_design_value' => [
                'type'                      => 'INT',
                'constraint'                => 11,
                'unsigned'                  => true,
            ],
            'created_by'         => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
            ],
            'created_at'  => [
                'type'           => 'DATETIME',
            ],
            'updated_at'  => [
                'type'           => 'DATETIME',
                'null'           => true,
            ],
            'updated_by'         => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'null'           => true,
            ],
            'deleted_at'  => [
                'type'           => 'DATETIME',
                'null'           => true,
            ],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('material_by_quantities');
    }

    public function down()
    {
        $this->forge->dropTable('material_by_quantities');
    }
}
