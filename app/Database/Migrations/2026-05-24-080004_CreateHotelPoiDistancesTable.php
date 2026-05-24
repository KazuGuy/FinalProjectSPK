<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateHotelPoiDistancesTable extends Migration
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
            'hotel_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'poi_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'jarak_km' => [
                'type'       => 'DOUBLE',
            ],
            'created_at DATETIME DEFAULT CURRENT_TIMESTAMP',
        ]);
        $this->forge->addKey('id', true);
        
        // Pengaturan Foreign Key Cascade
        $this->forge->addForeignKey('hotel_id', 'hotels', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('poi_id', 'points_of_interest', 'id', 'CASCADE', 'CASCADE');
        
        $this->forge->createTable('hotel_poi_distances');
    }

    public function down()
    {
        $this->forge->dropTable('hotel_poi_distances');
    }
}