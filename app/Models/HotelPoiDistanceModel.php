<?php
namespace App\Models;
use CodeIgniter\Model;

class HotelPoiDistanceModel extends Model
{
    protected $table         = 'hotel_poi_distances';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['hotel_id', 'poi_id', 'jarak_km'];
    protected $useTimestamps = false;

    /**
     * Hitung jarak Haversine antara dua koordinat (km)
     */
    public static function haversine(
        float $lat1, float $lon1,
        float $lat2, float $lon2
    ): float {
        $R    = 6371; // radius bumi km
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a    = sin($dLat / 2) ** 2
              + cos(deg2rad($lat1)) * cos(deg2rad($lat2))
              * sin($dLon / 2) ** 2;
        return round($R * 2 * atan2(sqrt($a), sqrt(1 - $a)), 4);
    }

    /**
     * Hitung & simpan ulang semua jarak untuk satu hotel ke semua POI
     */
    public function recalculateForHotel(int $hotelId): void
    {
        $hotelModel = new HotelModel();
        $poiModel   = new PoiModel();

        $hotel = $hotelModel->find($hotelId);
        $pois  = $poiModel->findAll();

        // Hapus data lama
        $this->where('hotel_id', $hotelId)->delete();

        $rows = [];
        foreach ($pois as $poi) {
            $rows[] = [
                'hotel_id' => $hotelId,
                'poi_id'   => $poi['id'],
                'jarak_km' => self::haversine(
                    $hotel['latitude'], $hotel['longitude'],
                    $poi['latitude'],   $poi['longitude']
                ),
            ];
        }

        if (!empty($rows)) {
            $this->insertBatch($rows);
        }
    }

    /**
     * Hitung & simpan ulang semua jarak untuk satu POI ke semua hotel
     * (dipanggil saat POI baru ditambah/diedit)
     */
    public function recalculateForPoi(int $poiId): void
    {
        $hotelModel = new HotelModel();
        $poiModel   = new PoiModel();

        $poi    = $poiModel->find($poiId);
        $hotels = $hotelModel->findAll();

        $this->where('poi_id', $poiId)->delete();

        $rows = [];
        foreach ($hotels as $hotel) {
            $rows[] = [
                'hotel_id' => $hotel['id'],
                'poi_id'   => $poiId,
                'jarak_km' => self::haversine(
                    $hotel['latitude'], $hotel['longitude'],
                    $poi['latitude'],   $poi['longitude']
                ),
            ];
        }

        if (!empty($rows)) {
            $this->insertBatch($rows);
        }
    }
}