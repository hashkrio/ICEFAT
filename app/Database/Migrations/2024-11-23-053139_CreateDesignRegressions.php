<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDesignRegressions extends Migration
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
            'crate_type_id' => [
                'type'                      => 'INT',
                'constraint'                => 11,
                'unsigned'                  => true,
            ],
            'design_regression_variable_id' => [
                'type'                      => 'INT',
                'constraint'                => 11,
                'unsigned'                  => true,
            ],
            'calculate_m'    => [
                'type'           => 'decimal',
                'constraint'     => '10,3',
                'comment'        => 'Carbon emissions g CO2e/kg-km'
            ],
            'calculate_b'    => [
                'type'           => 'decimal',
                'constraint'     => '10,3',
                'comment'        => 'Carbon emissions g CO2e/kg-km'
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
        $this->forge->createTable('design_regressions');
    }

    public function down()
    {
        $this->forge->dropTable('design_regressions');
    }
}
