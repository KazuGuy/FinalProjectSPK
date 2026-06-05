<?php
// app/Database/Migrations/xxxx_AddTypeToHotels.php

namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;

class AddTypeToHotels extends Migration
{
    public function up()
    {
        $this->forge->addColumn('hotels', [
            'type' => [
                'type'       => 'ENUM',
                'constraint' => ['hotel', 'resort', 'apartment', 'villa', 'guesthouse'],
                'not null'   => true,
                'default'    => 'hotel',
                'after'      => 'name',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('hotels', 'type');
    }
}