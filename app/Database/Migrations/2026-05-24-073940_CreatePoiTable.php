<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePoiTable extends Migration
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
            'nama_poi' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
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
        $this->forge->createTable('points_of_interest');
    }

    public function down()
    {
        $this->forge->dropTable('points_of_interest');
    }
}