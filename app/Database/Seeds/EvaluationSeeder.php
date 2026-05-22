namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class EvaluationSeeder extends Seeder
{
    public function run()
    {
        $data = [
            // --- Nilai untuk A1 (Ubud) ---
            ['alternative_id' => 1, 'criteria_id' => 1, 'nilai' => 3.00],   // Harga (Skala 1-5)
            ['alternative_id' => 1, 'criteria_id' => 2, 'nilai' => 4.70],   // Rating (Riil)
            ['alternative_id' => 1, 'criteria_id' => 3, 'nilai' => 1.50],   // Jarak POI (1.5 Km)
            ['alternative_id' => 1, 'criteria_id' => 4, 'nilai' => 4.00],   // Jumlah fasilitas
            ['alternative_id' => 1, 'criteria_id' => 5, 'nilai' => 4.00],   // Tipe Hotel (4 = Konsep Villa)
            ['alternative_id' => 1, 'criteria_id' => 6, 'nilai' => 15.00],  // Diskon 15%

            // --- Nilai untuk A2 (Kuta) ---
            ['alternative_id' => 2, 'criteria_id' => 1, 'nilai' => 2.00],   // Harga lebih ekonomis
            ['alternative_id' => 2, 'criteria_id' => 2, 'nilai' => 4.20],   // Rating
            ['alternative_id' => 2, 'criteria_id' => 3, 'nilai' => 0.50],   // Sangat dekat POI (0.5 Km)
            ['alternative_id' => 2, 'criteria_id' => 4, 'nilai' => 3.00],   // Fasilitas standar
            ['alternative_id' => 2, 'criteria_id' => 5, 'nilai' => 2.00],   // Tipe Hotel (2 = Dominan Hotel biasa)
            ['alternative_id' => 2, 'criteria_id' => 6, 'nilai' => 30.00],  // Diskon besar 30%

            // --- Nilai untuk A3 (Canggu) ---
            ['alternative_id' => 3, 'criteria_id' => 1, 'nilai' => 5.00],   // Harga premium/mahal
            ['alternative_id' => 3, 'criteria_id' => 2, 'nilai' => 4.50],   // Rating
            ['alternative_id' => 3, 'criteria_id' => 3, 'nilai' => 2.00],   // Jarak POI 2 Km
            ['alternative_id' => 3, 'criteria_id' => 4, 'nilai' => 5.00],   // Fasilitas sangat lengkap
            ['alternative_id' => 3, 'criteria_id' => 5, 'nilai' => 3.00],   // Tipe Hotel (3 = Resort)
            ['alternative_id' => 3, 'criteria_id' => 6, 'nilai' => 10.00],  // Diskon kecil 10%
        ];

        $this->db->table('evaluations')->insertBatch($data);
    }
}