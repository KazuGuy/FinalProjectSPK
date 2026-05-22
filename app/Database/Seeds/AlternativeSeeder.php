namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AlternativeSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['kode_alternatif' => 'A1', 'nama_lokasi' => 'Ubud', 'deskripsi' => 'Kawasan budaya dan ketenangan'],
            ['kode_alternatif' => 'A2', 'nama_lokasi' => 'Kuta', 'deskripsi' => 'Kawasan pantai populer dan pusat belanja'],
            ['kode_alternatif' => 'A3', 'nama_lokasi' => 'Canggu', 'deskripsi' => 'Kawasan hits, pantai, dan cafe'],
        ];

        $this->db->table('alternatives')->insertBatch($data);
    }
}