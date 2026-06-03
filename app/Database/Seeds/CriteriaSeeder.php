<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CriteriaSeeder extends Seeder
{
    public function run()
    {
        $this->db->table('criterias')->truncate();
        $this->db->table('criterias')->insertBatch([
            ['code' => 'C1', 'name' => 'Harga',            'type' => 'cost',    'default_weight' => 1],
            ['code' => 'C2', 'name' => 'Rating',      'type' => 'benefit', 'default_weight' => 1],
            ['code' => 'C3', 'name' => 'Jarak ke POI',     'type' => 'cost',    'default_weight' => 1],
            ['code' => 'C4', 'name' => 'Jumlah Fasilitas', 'type' => 'benefit', 'default_weight' => 1],
            ['code' => 'C5', 'name' => 'Diskon',           'type' => 'benefit', 'default_weight' => 1],
            ['code' => 'C6', 'name' => 'Tipe Penginapan',  'type' => 'benefit', 'default_weight' => 1],
        ]);
    }
}
