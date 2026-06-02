<?php
namespace App\Controllers\User;

use App\Controllers\BaseController;
use App\Models\HotelModel;
use App\Models\CriteriaModel;
use App\Models\HotelPoiDistanceModel;
use App\Models\MabacModel;

class EvaluationController extends BaseController
{
    /**
     * Step 1 — Pilih Alternatif (hotel)
     * User centang hotel mana saja yang ingin dibandingkan
     */
    public function selectAlternatives()
    {
        return view('user/evaluation/step1_alternatives', [
            'hotels' => (new HotelModel())->getWithAvgDistance(),
        ]);
    }

    /**
     * Step 2 — Atur Bobot Kriteria
     * Bobot hanya berlaku untuk sesi ini (tidak disimpan ke DB)
     */
    public function setWeights()
    {
        $selectedIds = $this->request->getPost('hotel_ids');

        // Validasi minimal 2 alternatif
        if (empty($selectedIds) || count($selectedIds) < 2) {
            return redirect()->back()
                ->with('error', 'Pilih minimal 2 hotel untuk dibandingkan.');
        }

        // Simpan pilihan di session sementara
        session()->set('eval_hotel_ids', $selectedIds);

        return view('user/evaluation/step2_weights', [
            'criterias'    => (new CriteriaModel())->findAll(),
            'selectedCount'=> count($selectedIds),
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

        // Normalisasi bobot agar total = 1
        $totalWeight = array_sum($rawWeights) ?: 1;
        foreach ($criterias as &$c) {
            $raw        = (float)($rawWeights[$c['code']] ?? $c['default_weight']);
            $c['weight'] = $raw / $totalWeight;
        }
        unset($c);

        // ── Dataset hotel yang dipilih ─────────────────────────
        $distanceModel = new HotelPoiDistanceModel();
        $hotelsRaw     = $hotelModel->getWithAvgDistance();

        // Filter hanya hotel yang dipilih user
        $hotels = array_values(array_filter(
            $hotelsRaw,
            fn($h) => in_array($h['id'], $hotelIds)
        ));

        // Map ke format kriteria
        $hotels = array_map(fn($h) => [
            'id'   => $h['id'],
            'name' => $h['name'],
            'C1'   => $h['price'],            // cost
            'C2'   => $h['rating'],            // review score, benefit
            'C3'   => $h['avg_distance'] ?? 0, // cost
            'C4'   => $h['facilities_count'],  // benefit
            'C5'   => $h['discount'],          // benefit
        ], $hotels);

        $results = $mabac->calculate($hotels, $criterias);

        // Bersihkan session setelah kalkulasi
        session()->remove('eval_hotel_ids');

        return view('user/evaluation/step3_result', [
            'results'   => $results,
            'criterias' => $criterias,
        ]);
    }
}
