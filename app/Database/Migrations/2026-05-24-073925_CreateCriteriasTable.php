<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCriteriasTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'code' => [
                'type'       => 'VARCHAR',
                'constraint' => '5',
                'unique'     => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'type' => [
                'type'       => 'ENUM',
                'constraint' => ['cost', 'benefit'],
            ],
            'default_weight' => [
                'type'       => 'DOUBLE',
            ],
            'created_at DATETIME DEFAULT CURRENT_TIMESTAMP',
            'updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('criterias');
    }

    public function down()
    {
        $this->forge->dropTable('criterias');
    }
}