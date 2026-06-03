<?php
namespace App\Models;
use CodeIgniter\Model;

class HotelModel extends Model
{
    protected $table         = 'hotels';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = [
        'name', 'type', 'type_score', 'price', 'rating', // ← tambah type_score
        'facilities_count', 'facilities_detail', 'discount',
        'latitude', 'longitude'
    ];
    protected $useTimestamps = true;

    public const TYPE_SCORE = [
        'resort'     => 5,
        'villa'      => 4,
        'hotel'      => 3,
        'apartment'  => 2,
        'guesthouse' => 1,
    ];

    protected $validationRules = [
        'name'              => 'required|max_length[150]',
        'price'             => 'required|decimal|greater_than[0]',
        'rating'            => 'required|decimal|greater_than_equal_to[0]|less_than_equal_to[10]',
        'facilities_count'  => 'required|integer|greater_than_equal_to[0]',
        'facilities_detail' => 'required',
        'discount'          => 'permit_empty|integer|greater_than_equal_to[0]|less_than_equal_to[100]',
        'latitude'          => 'required|decimal',
        'longitude'         => 'required|decimal',
        'type'              => 'required|in_list[resort,villa,hotel,apartment,guesthouse]',
    ];

    /** Ambil hotel beserta rata-rata jarak ke semua POI */
    public function getWithAvgDistance(): array
    {
        return $this->db->table('hotels h')
            ->select('h.*, AVG(hpd.jarak_km) as avg_distance')
            ->join('hotel_poi_distances hpd', 'hpd.hotel_id = h.id', 'left')
            ->groupBy('h.id')
            ->get()->getResultArray();
    }

    /** Ambil hotel + jarak ke POI tertentu */
    public function getWithDistanceToPoi(int $poiId): array
    {
        return $this->db->table('hotels h')
            ->select('h.*, hpd.jarak_km as poi_distance')
            ->join('hotel_poi_distances hpd', "hpd.hotel_id = h.id AND hpd.poi_id = {$poiId}", 'left')
            ->get()->getResultArray();
    }
}