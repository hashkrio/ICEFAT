<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Forge;
use CodeIgniter\Database\Migration;

class AddFullNameToUsers extends Migration
{
    /**
     * @var string[]
     */
    private array $tables;

    public function __construct(?Forge $forge = null)
    {
        parent::__construct($forge);

        /** @var \Config\Auth $authConfig */
        $authConfig   = config('Auth');
        $this->tables = $authConfig->tables;
    }

    public function up()
    {
        $fields = [
            'first_name' => ['type' => 'VARCHAR', 'constraint' => '100', 'null' => true],
            'last_name'  => ['type' => 'VARCHAR', 'constraint' => '100', 'null' => true],
        ];
        $this->forge->addColumn($this->tables['users'], $fields);
    }

    public function down()
    {
        $fields = [
            'first_name',
            'last_name',
        ];
        $this->forge->dropColumn($this->tables['users'], $fields);
    }
}
