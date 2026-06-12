<?php
namespace App\Models;

class MabacModel
{
    /**
     * Jalankan seluruh kalkulasi MABAC
     *
     * @param  array $hotels    [['id'=>, 'name'=>, ...nilai kriteria...]]
     * @param  array $criterias [['code'=>, 'type'=>'cost'|'benefit', 'weight'=>]]
     * @return array hasil ranking lengkap
     */
    public function calculate(array $hotels, array $criterias): array
    {
        $n = count($hotels);
        $m = count($criterias);

        // --- STEP 1: Normalisasi ---
        $normalized = [];
        foreach ($criterias as $ci => $c) {
            $vals = array_column($hotels, $c['code']);
            $min  = min($vals);
            $max  = max($vals);
            $diff = ($max - $min) ?: 1;

            foreach ($hotels as $hi => $hotel) {
                $x = $hotel[$c['code']];
                $normalized[$hi][$ci] = ($c['type'] === 'benefit')
                    ? ($x - $min) / $diff
                    : ($max - $x) / $diff;
            }
        }

        // --- STEP 2: Weighted matrix (V) ---
        $weights = array_column($criterias, 'weight');
        $V = [];
        foreach ($normalized as $hi => $row) {
            foreach ($row as $ci => $val) {
                $V[$hi][$ci] = $weights[$ci] * ($val + 1);
            }
        }

        // --- STEP 3: BAA (G) ---
        $G = [];
        for ($ci = 0; $ci < $m; $ci++) {
            $product = 1;
            foreach ($hotels as $hi => $_) {
                $product *= $V[$hi][$ci];
            }
            $G[$ci] = pow($product, 1 / $n);
        }

        // --- STEP 4: Q = V - G ---
        $Q = [];
        foreach ($hotels as $hi => $_) {
            $Q[$hi] = array_map(
                fn($ci) => $V[$hi][$ci] - $G[$ci],
                range(0, $m - 1)
            );
        }

        // --- STEP 5 & 6: Skor & Ranking ---
        $results = [];
        foreach ($hotels as $hi => $hotel) {
            $results[] = [
                'id'    => $hotel['id'],
                'name'  => $hotel['name'],
                'score' => array_sum($Q[$hi]),
                'q'     => $Q[$hi],
            ];
        }

        usort($results, fn($a, $b) => $b['score'] <=> $a['score']);
        foreach ($results as $rank => &$r) {
            $r['rank'] = $rank + 1;
        }

        // Kembalikan detail perhitungan juga
        return [
            'results'    => $results,
            'normalized' => $normalized,
            'V'          => $V,
            'G'          => $G,
            'Q'          => $Q,
            'hotels'     => $hotels,   // data mentah
            'criterias'  => $criterias,
        ];
    }
}