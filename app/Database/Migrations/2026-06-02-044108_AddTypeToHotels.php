<?php
// app/Database/Migrations/xxxx_AddTypeToHotels.php

namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;

class AddTypeToHotels extends Migration
{
    public function up()
{
    $this->forge->addColumn('hotels', [
        'type_score' => [
            'type'       => 'TINYINT',
            'constraint' => 3,
            'not null'   => true,
            'default'    => 1,
            'after'      => 'type',
        ],
    ]);
}

public function down()
{
    $this->forge->dropColumn('hotels', 'type_score');
}
}