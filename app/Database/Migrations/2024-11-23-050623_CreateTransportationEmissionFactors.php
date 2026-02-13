<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTransportationEmissionFactors extends Migration
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
            'transportation'    => [
                'type'           => 'VARCHAR',
                'constraint'     => '100',
            ],
            'shipment'    => [
                'type'           => 'VARCHAR',
                'constraint'     => '100',
                'unique'         => true,
            ],
            'carbon_emission'    => [
                'type'           => 'decimal',
                'constraint'     => '10,3',
                'comment'        => 'Carbon emissions g CO2e/kg-km'
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
        $this->forge->createTable('transportation_emission_factors');
    }

    public function down()
    {
        $this->forge->dropTable('transportation_emission_factors');
    }
}
