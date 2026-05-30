<?php
namespace App\Controllers;

use App\Models\HotelModel;
use App\Models\CriteriaModel;
use App\Models\HotelPoiDistanceModel;
use App\Models\MabacModel;

class DssController extends BaseController
{
    public function index()
    {
        $criterias = (new CriteriaModel())->findAll();
        return view('dss/index', ['criterias' => $criterias]);
    }

    public function calculate()
    {
        $criteriaModel  = new CriteriaModel();
        $hotelModel     = new HotelModel();
        $distanceModel  = new HotelPoiDistanceModel();
        $mabac          = new MabacModel();

        // Ambil bobot dari input user (atau pakai default_weight)
        $criterias = $criteriaModel->findAll();
        $totalInput = array_sum(array_column($criterias, 'default_weight')) ?: 1;

        foreach ($criterias as &$c) {
            $inputWeight   = $this->request->getPost("weight_{$c['code']}") ?? $c['default_weight'];
            $c['weight']   = (float) $inputWeight / $totalInput; // normalisasi bobot
        }

        // Bangun dataset hotel dengan nilai per kriteria
        $hotelsRaw = $hotelModel->getWithAvgDistance();
        $hotels    = array_map(fn($h) => [
            'id'          => $h['id'],
            'name'        => $h['name'],
            'C1'          => $h['price'],           // Harga (cost)
            'C2'          => $h['rating'],           // Rating (benefit)
            'C3'          => $h['avg_distance'],     // Jarak ke POI (cost)
            'C4'          => $h['facilities_count'], // Fasilitas (benefit)
            'C5'          => $h['discount'],         // Diskon (benefit)
        ], $hotelsRaw);

        $results = $mabac->calculate($hotels, $criterias);

        // Kembalikan JSON untuk AJAX / Flutter
        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'ok', 'data' => $results]);
        }

        return view('dss/result', ['results' => $results, 'criterias' => $criterias]);
    }
}