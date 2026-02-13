<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDesignRegressionVariables extends Migration
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
            'x_var'    => [
                'type'           => 'VARCHAR',
                'constraint'     => '100',
            ],
            'x_var_text'    => [
                'type'           => 'VARCHAR',
                'constraint'     => '100',
                'unique'         => true,
            ],
            'notes'    => [
                'type'           => 'VARCHAR',
                'constraint'     => '100',
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
        $this->forge->createTable('design_regression_variables');
    }

    public function down()
    {
        $this->forge->dropTable('design_regression_variables');
    }
}
