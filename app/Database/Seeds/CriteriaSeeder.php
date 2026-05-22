namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CriteriaSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['kode_kriteria' => 'C1', 'nama_kriteria' => 'Harga', 'jenis' => 'cost', 'bobot' => 0.2500],
            ['kode_kriteria' => 'C2', 'nama_kriteria' => 'Rating', 'jenis' => 'benefit', 'bobot' => 0.2000],
            ['kode_kriteria' => 'C3', 'nama_kriteria' => 'Jarak POI', 'jenis' => 'cost', 'bobot' => 0.2000],
            ['kode_kriteria' => 'C4', 'nama_kriteria' => 'Jumlah Fasilitas', 'jenis' => 'benefit', 'bobot' => 0.1500],
            ['kode_kriteria' => 'C5', 'nama_kriteria' => 'Tipe Hotel', 'jenis' => 'benefit', 'bobot' => 0.1000],
            ['kode_kriteria' => 'C6', 'nama_kriteria' => 'Jumlah Diskon', 'jenis' => 'benefit', 'bobot' => 0.1000],
        ];

        $this->db->table('criterias')->insertBatch($data);
    }
}