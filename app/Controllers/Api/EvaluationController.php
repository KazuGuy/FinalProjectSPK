<?php
namespace App\Controllers\Api;
use CodeIgniter\RESTful\ResourceController;
use App\Models\HotelModel;
use App\Models\CriteriaModel;
use App\Models\MabacModel;

class EvaluationController extends ResourceController
{
    protected $format = 'json';

    private const CRITERIA_FIELD_MAP = [
        'C1' => 'price',
        'C2' => 'rating',
        'C3' => 'poi_distance',
        'C4' => 'facilities_count',
        'C5' => 'discount',
        'C6' => 'type_score',
    ];

    public function calculate()
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            return $this->response->setStatusCode(200);
        }

        $json     = $this->request->getJSON(true);
        $hotelIds = $json['hotel_ids'] ?? [];
        $poiId    = $json['poi_id']    ?? null;
        $weights  = $json['weights']   ?? [];

        if (count($hotelIds) < 2) {
            return $this->respond([
                'status'  => 400,
                'message' => 'Pilih minimal 2 hotel'
            ], 400);
        }

        $criteriaModel = new CriteriaModel();
        $hotelModel    = new HotelModel();
        $mabac         = new MabacModel();

        $criterias = $criteriaModel->findAll();

        foreach ($criterias as &$c) {
            $raw         = (float)($weights[$c['code']] ?? 0);
            $c['weight'] = $raw / 100;
        }
        unset($c);

        $hotelsRaw = $poiId
            ? $hotelModel->getWithDistanceToPoi((int) $poiId)
            : $hotelModel->getWithAvgDistance();

        $hotels = array_values(array_filter(
            $hotelsRaw,
            fn($h) => in_array((int) $h['id'], array_map('intval', $hotelIds))
        ));

        $hotels = array_map(function($h) use ($criterias) {
            $row = ['id' => $h['id'], 'name' => $h['name']];
            foreach ($criterias as $c) {
                $field           = self::CRITERIA_FIELD_MAP[$c['code']] ?? null;
                $row[$c['code']] = $field ? ($h[$field] ?? 0) : 0;
            }
            return $row;
        }, $hotels);

        $results = $mabac->calculate($hotels, $criterias);

        return $this->respond([
            'status'  => 200,
            'message' => 'Kalkulasi berhasil',
            'data'    => $results,
        ], 200);
    }
}