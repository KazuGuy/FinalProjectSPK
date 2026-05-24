<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateHotelsTable extends Migration
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
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => '150',
            ],
            'price' => [
                'type'       => 'DOUBLE',
            ],
            'rating' => [
                'type'       => 'DOUBLE',
            ],
            'facilities_count' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'facilities_detail' => [
                'type'       => 'TEXT',
            ],
            'discount' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'latitude' => [
                'type'       => 'DOUBLE',
            ],
            'longitude' => [
                'type'       => 'DOUBLE',
            ],
            'created_at DATETIME DEFAULT CURRENT_TIMESTAMP',
            'updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('hotels');
    }

    public function down()
    {
        $this->forge->dropTable('hotels');
    }
}