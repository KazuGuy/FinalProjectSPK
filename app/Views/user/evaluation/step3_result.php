<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <div>
        <h1>Evaluasi MABAC</h1>
        <p>Langkah 3 dari 3 — Hasil rekomendasi</p>
    </div>
</div>

<!-- Stepper -->
<div style="display:flex; align-items:center; gap:0; margin-bottom:1.75rem">
    <?php foreach ([['1','Pilih Hotel'],['2','Atur Bobot'],['3','Hasil']] as $i => [$num, $label]): ?>
    <div style="display:flex; align-items:center; gap:0; flex:<?= $i < 2 ? '1' : '0' ?>">
        <div style="display:flex; align-items:center; gap:0.5rem">
            <div style="width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:0.8rem;font-weight:600;background:<?= $num==='3' ? 'var(--primary)' : '#dcfce7' ?>;color:<?= $num==='3' ? 'white' : '#166534' ?>"><?= $num==='3' ? '3' : '✓' ?></div>
            <span style="font-size:0.875rem;font-weight:<?= $num==='3' ? '600' : '400' ?>;color:<?= $num==='3' ? 'var(--text)' : 'var(--muted)' ?>"><?= $label ?></span>
        </div>
        <?php if ($i < 2): ?><div style="flex:1;height:1px;background:#e5e7eb;margin:0 0.75rem"></div><?php endif; ?>
    </div>
    <?php endforeach; ?>
</div>

<!-- Pemenang -->
<?php $winner = $results[0]; ?>
<div class="card" style="background:linear-gradient(135deg,#1a56db,#1e429f); color:white; margin-bottom:1.25rem; padding:1.5rem">
    <div style="font-size:0.8rem; opacity:0.8; margin-bottom:0.25rem">🏆 Rekomendasi Terbaik</div>
    <div style="font-size:1.375rem; font-weight:700"><?= esc($winner['name']) ?></div>
    <div style="font-size:0.875rem; opacity:0.85; margin-top:0.25rem">
        Skor MABAC: <?= number_format($winner['score'], 4) ?>
    </div>
</div>

<!-- Tabel ranking -->
<div class="card" style="margin-bottom:1.25rem">
    <div class="card-header">
        <span class="card-title">Ranking Lengkap</span>
    </div>
    <table>
        <thead>
            <tr>
                <th>Rank</th>
                <th>Hotel</th>
                <th>Skor MABAC</th>
                <th>Status BAA</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($results as $r): ?>
            <tr>
                <td>
                    <?php if ($r['rank'] === 1): ?>
                        <span style="font-size:1.25rem">🥇</span>
                    <?php elseif ($r['rank'] === 2): ?>
                        <span style="font-size:1.25rem">🥈</span>
                    <?php elseif ($r['rank'] === 3): ?>
                        <span style="font-size:1.25rem">🥉</span>
                    <?php else: ?>
                        <span style="color:var(--muted); font-weight:600">#<?= $r['rank'] ?></span>
                    <?php endif; ?>
                </td>
                <td style="font-weight:<?= $r['rank'] === 1 ? '600' : '400' ?>"><?= esc($r['name']) ?></td>
                <td style="font-family:monospace; font-size:0.9rem">
                    <span style="color:<?= $r['score'] >= 0 ? '#166534' : '#991b1b' ?>; font-weight:600">
                        <?= ($r['score'] >= 0 ? '+' : '') . number_format($r['score'], 4) ?>
                    </span>
                </td>
                <td>
                    <?php if ($r['score'] > 0): ?>
                        <span class="badge" style="background:#dcfce7;color:#166534">Di atas BAA ✓</span>
                    <?php elseif ($r['score'] == 0): ?>
                        <span class="badge" style="background:#f3f4f6;color:var(--muted)">Tepat di BAA</span>
                    <?php else: ?>
                        <span class="badge" style="background:#fee2e2;color:#991b1b">Di bawah BAA</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Bobot yang digunakan -->
<div class="card" style="margin-bottom:1.25rem">
    <div class="card-header" style="margin-bottom:1rem">
        <span class="card-title">Bobot yang Digunakan</span>
    </div>
    <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap:0.75rem">
        <?php foreach ($criterias as $c): ?>
        <div style="background:var(--bg); border-radius:6px; padding:0.75rem">
            <div style="font-size:0.75rem; color:var(--muted)"><?= esc($c['name']) ?></div>
            <div style="font-weight:700; font-size:1.125rem; color:var(--primary); margin-top:0.125rem">
                <?= number_format($c['weight'] * 100, 1) ?>%
            </div>
            <span class="badge badge-<?= $c['type'] ?>" style="font-size:0.68rem; margin-top:0.25rem">
                <?= $c['type'] === 'benefit' ? '↑ Benefit' : '↓ Cost' ?>
            </span>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Toggle tampilkan/sembunyikan proses -->
<div style="margin-bottom:1rem">
    <button onclick="toggleProses()" class="btn btn-ghost" id="btnProses">
        📊 Tampilkan Proses Perhitungan
    </button>
</div>

<div id="prosesSection" style="display:none">

    <!-- Matriks Keputusan Awal -->
    <div class="card" style="margin-bottom:1.25rem; overflow-x:auto">
        <div class="card-header">
            <span class="card-title">Step 1 — Matriks Keputusan Awal (X)</span>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Alternatif</th>
                    <?php foreach ($criterias as $c): ?>
                        <th><?= esc($c['name']) ?><br><small style="font-weight:400;text-transform:none"><?= esc($c['code']) ?></small></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($hotels_raw as $hi => $h): ?>
                <tr>
                    <td><strong><?= esc($h['name']) ?></strong></td>
                    <?php foreach ($criterias as $ci => $c): ?>
                        <td style="font-family:monospace"><?= number_format((float)$h[$c['code']], 4) ?></td>
                    <?php endforeach; ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Matriks Normalisasi -->
    <div class="card" style="margin-bottom:1.25rem; overflow-x:auto">
        <div class="card-header">
            <span class="card-title">Step 2 — Matriks Normalisasi (N)</span>
        </div>
        <p style="font-size:0.8rem; color:var(--muted); margin-bottom:1rem">
            Benefit: (x − min) / (max − min) &nbsp;|&nbsp; Cost: (max − x) / (max − min)
        </p>
        <table>
            <thead>
                <tr>
                    <th>Alternatif</th>
                    <?php foreach ($criterias as $c): ?>
                        <th>
                            <?= esc($c['code']) ?>
                            <span class="badge badge-<?= $c['type'] ?>" style="font-size:0.65rem">
                                <?= $c['type'] === 'benefit' ? '↑' : '↓' ?>
                            </span>
                        </th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($hotels_raw as $hi => $h): ?>
                <tr>
                    <td><strong><?= esc($h['name']) ?></strong></td>
                    <?php foreach ($criterias as $ci => $c): ?>
                        <td style="font-family:monospace"><?= number_format($normalized[$hi][$ci], 4) ?></td>
                    <?php endforeach; ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Weighted Matrix -->
    <div class="card" style="margin-bottom:1.25rem; overflow-x:auto">
        <div class="card-header">
            <span class="card-title">Step 3 — Weighted Matrix (V = w × (N + 1))</span>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Alternatif</th>
                    <?php foreach ($criterias as $c): ?>
                        <th><?= esc($c['code']) ?><br><small style="font-weight:400">w=<?= number_format($c['weight'], 3) ?></small></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($hotels_raw as $hi => $h): ?>
                <tr>
                    <td><strong><?= esc($h['name']) ?></strong></td>
                    <?php foreach ($criterias as $ci => $c): ?>
                        <td style="font-family:monospace"><?= number_format($V[$hi][$ci], 4) ?></td>
                    <?php endforeach; ?>
                </tr>
                <?php endforeach; ?>
                <!-- BAA Row -->
                <tr style="background:#fffbeb; font-weight:600">
                    <td>G (BAA)</td>
                    <?php foreach ($criterias as $ci => $c): ?>
                        <td style="font-family:monospace; color:#b45309"><?= number_format($G[$ci], 4) ?></td>
                    <?php endforeach; ?>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Q Matrix -->
    <div class="card" style="margin-bottom:1.25rem; overflow-x:auto">
        <div class="card-header">
            <span class="card-title">Step 4 — Jarak dari BAA (Q = V − G)</span>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Alternatif</th>
                    <?php foreach ($criterias as $c): ?>
                        <th><?= esc($c['code']) ?></th>
                    <?php endforeach; ?>
                    <th>Skor (S)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Reorder Q sesuai ranking results
                $rankedHotels = array_column($results, null, 'id');
                foreach ($results as $r):
                    // Cari index hotel di hotels_raw
                    $hi = null;
                    foreach ($hotels_raw as $idx => $h) {
                        if ($h['id'] == $r['id']) { $hi = $idx; break; }
                    }
                ?>
                <tr>
                    <td>
                        <strong><?= esc($r['name']) ?></strong>
                        <?php if ($r['rank'] === 1): ?>
                            <span style="font-size:1rem">🥇</span>
                        <?php elseif ($r['rank'] === 2): ?>
                            <span style="font-size:1rem">🥈</span>
                        <?php elseif ($r['rank'] === 3): ?>
                            <span style="font-size:1rem">🥉</span>
                        <?php endif; ?>
                    </td>
                    <?php foreach ($criterias as $ci => $c): ?>
                        <td style="font-family:monospace; color:<?= $Q[$hi][$ci] >= 0 ? '#166534' : '#991b1b' ?>">
                            <?= ($Q[$hi][$ci] >= 0 ? '+' : '') . number_format($Q[$hi][$ci], 4) ?>
                        </td>
                    <?php endforeach; ?>
                    <td style="font-family:monospace; font-weight:700; color:<?= $r['score'] >= 0 ? '#166534' : '#991b1b' ?>">
                        <?= ($r['score'] >= 0 ? '+' : '') . number_format($r['score'], 4) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</div>

<script>
function toggleProses() {
    const section = document.getElementById('prosesSection');
    const btn     = document.getElementById('btnProses');
    if (section.style.display === 'none') {
        section.style.display = 'block';
        btn.textContent = '📊 Sembunyikan Proses Perhitungan';
    } else {
        section.style.display = 'none';
        btn.textContent = '📊 Tampilkan Proses Perhitungan';
    }
}
</script>

<!-- Penjelasan -->
<div class="card" style="background:#fffbeb; border-color:#fde68a; font-size:0.875rem; color:#78350f">
    <strong>Tentang skor MABAC:</strong>
    Skor positif (+) berarti hotel berada <em>di atas</em> Border Approximation Area (BAA) — lebih baik dari rata-rata tertimbang.
    Skor negatif (−) berarti di bawah BAA. Semakin tinggi skornya, semakin direkomendasikan.
</div>

<div style="display:flex; gap:0.75rem; margin-top:1.5rem">
    <a href="/evaluation" class="btn btn-primary">← Evaluasi Baru</a>
    <a href="/hotels" class="btn btn-ghost">Kembali ke Daftar Hotel</a>
</div>

<?= $this->endSection() ?>
