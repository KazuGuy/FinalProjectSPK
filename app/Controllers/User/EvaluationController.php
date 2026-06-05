<?php
namespace App\Controllers\User;
use App\Controllers\BaseController;
use App\Models\HotelModel;
use App\Models\CriteriaModel;
use App\Models\HotelPoiDistanceModel;
use App\Models\MabacModel;
class EvaluationController extends BaseController
{
        private const CRITERIA_FIELD_MAP = [
        'C1' => 'price',
        'C2' => 'rating',
        'C3' => 'poi_distance',  // ← ganti dari avg_distance
        'C4' => 'facilities_count',
        'C5' => 'discount',
        'C6' => 'type_score',
    ];
    /**
     * Step 1 — Pilih Alternatif (hotel)
     * User centang hotel mana saja yang ingin dibandingkan
     */
    public function selectAlternatives()
    {
        return view('user/evaluation/step1_alternatives', [
            'hotels' => (new HotelModel())->getWithAvgDistance(),
            'pois'   => (new \App\Models\PoiModel())->findAll(), // ← tambah
        ]);
    }
    /**
     * Step 2 — Atur Bobot Kriteria
     * Bobot hanya berlaku untuk sesi ini (tidak disimpan ke DB)
     */
    public function setWeights()
    {
        $selectedIds = $this->request->getPost('hotel_ids');
        $selectedPoi = $this->request->getPost('poi_id');
        if (empty($selectedIds) || count($selectedIds) < 2) {
            return redirect()->back()
                ->with('error', 'Pilih minimal 2 hotel untuk dibandingkan.');
        }
        if (empty($selectedPoi)) {
            return redirect()->back()
                ->with('error', 'Pilih POI acuan jarak terlebih dahulu.');
        }
        session()->set('eval_hotel_ids', $selectedIds);
        session()->set('eval_poi_id', $selectedPoi);
        return view('user/evaluation/step2_weights', [
            'criterias'     => (new CriteriaModel())->findAll(),
            'selectedCount' => count($selectedIds),
        ]);
    }
    /**
     * Step 3 — Hitung & Tampilkan Hasil Ranking
     */
    public function calculate()
    {
        $hotelIds  = session()->get('eval_hotel_ids');
        if (empty($hotelIds)) {
            return redirect()->to('/evaluation')
                ->with('error', 'Sesi evaluasi tidak ditemukan. Mulai ulang.');
        }
        $criteriaModel = new CriteriaModel();
        $hotelModel    = new HotelModel();
        $mabac         = new MabacModel();
        // ── Bobot dari input user ─────────────────────────────
        $criterias  = $criteriaModel->findAll();
        $rawWeights = $this->request->getPost('weights') ?? [];
        foreach ($criterias as &$c) {
            $raw         = (float)($rawWeights[$c['code']] ?? 0);
            $c['weight'] = $raw / 100; // ← bagi 100 karena input dalam %
        }
        unset($c);
    // ── Dataset hotel yang dipilih ─────────────────────────
    $poiId     = session()->get('eval_poi_id');
    $hotelsRaw = $poiId
        ? $hotelModel->getWithDistanceToPoi((int) $poiId)
        : $hotelModel->getWithAvgDistance();
        // Filter hanya hotel yang dipilih user
        $hotels = array_values(array_filter(
            $hotelsRaw,
            fn($h) => in_array($h['id'], $hotelIds)
        ));
        
        // Map ke format kriteria
        $hotels = array_map(function($h) use ($criterias) {
            $row = [
                'id'   => $h['id'],
                'name' => $h['name'],
            ];
            foreach ($criterias as $c) {
                $field       = self::CRITERIA_FIELD_MAP[$c['code']] ?? null;
                $row[$c['code']] = $field ? ($h[$field] ?? 0) : 0;
            }
            return $row;
        }, $hotels);
        // echo '<pre>';
        // print_r($hotels);
        // echo '</pre>';
        // die();
        $results = $mabac->calculate($hotels, $criterias);
        // Bersihkan session setelah kalkulasi
        session()->remove('eval_hotel_ids');
        return view('user/evaluation/step3_result', [
            'results'   => $results,
            'criterias' => $criterias,
        ]);
    }
}