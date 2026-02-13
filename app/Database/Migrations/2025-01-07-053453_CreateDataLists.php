<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDataLists extends Migration
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
            'crate_type_id'         => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
            ],
            'crate_type_name'    => [
                'type'           => 'VARCHAR',
                'constraint'     => '100',
            ],
            'label_name'    => [
                'type'           => 'VARCHAR',
                'constraint'     => '100',
            ],
            'height'          => [
                'type'           => 'decimal',
                'constraint'     => '10,3',
                'unsigned'       => true,
            ],
            'width'          => [
                'type'           => 'decimal',
                'constraint'     => '10,3',
                'unsigned'       => true,
            ],
            'depth'          => [
                'type'           => 'decimal',
                'constraint'     => '10,3',
                'unsigned'       => true,
            ],
            'measurement_unit_id'          => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
            ],
            'measurement_unit_name'          => [
                'type'           => 'VARCHAR',
                'constraint'     => '100'
            ],
            'weight_in_kg'    => [
                'type'           => 'decimal',
                'constraint'     => '10,3',
                'comment'        => 'Weight (kg)'
            ],
            'carbon_footprint'    => [
                'type'           => 'decimal',
                'constraint'     => '10,3',
                'comment'        => 'Footprint (kg CO2e)'
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
        $this->forge->createTable('data_lists');
    }

    public function down()
    {
        $this->forge->dropTable('data_lists');
    }
}
