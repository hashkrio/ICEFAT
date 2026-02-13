<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDimensionValues extends Migration
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
            'name'    => [
                'type'           => 'VARCHAR',
                'constraint'     => '100',
            ],
            'value'    => [
                'type'           => 'decimal',
                'constraint'     => '10,2',
            ],
            'crate_type_id' => [
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
        $this->forge->createTable('dimension_values');
    }

    public function down()
    {
        $this->forge->dropTable('dimension_values');
    }
}
