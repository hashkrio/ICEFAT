<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCarbonFootprints extends Migration
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
            'carbon_footer_print'          => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
            ],
            'crate_type_id'          => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
            ],
            'crate_type_name'    => [
                'type'           => 'VARCHAR',
                'constraint'     => '100',
            ],
            'crate_weight'          => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
            ],
            'embodied_carbon_factor_in_kg'          => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
            ],
            'model_option_id'         => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
            ],
            'model_option_name'    => [
                'type'           => 'VARCHAR',
                'constraint'     => '100',
            ],
            'number_of_oneway_trips'         => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
            ],
            'per_oneway_trip'         => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
            ],
            'shipment_air_passenger'         => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
            ],
            'shipment_air_freight'         => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
            ],
            'shipment_road_freight'         => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
            ],
            'shipment_sea_freight'         => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
            ],
            'total_carbon_foot_print'         => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
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
        $this->forge->createTable('carbon_footprints');
    }

    public function down()
    {
        $this->forge->dropTable('carbon_footprints');
    }
}
