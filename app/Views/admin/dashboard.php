<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <div>
        <h1>Dashboard Admin</h1>
        <p>Ringkasan data hotel, POI, kriteria, dan akun pengguna.</p>
    </div>
    <a href="/admin/hotels/create" class="btn btn-primary">Tambah Hotel</a>
</div>

<div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(190px, 1fr)); gap:0.85rem; margin-bottom:1rem">
    <?php foreach ([
        ['label' => 'Hotel', 'value' => $hotelCount],
        ['label' => 'POI', 'value' => $poiCount],
        ['label' => 'Kriteria', 'value' => $criteriaCount],
        ['label' => 'User Terdaftar', 'value' => $userCount],
    ] as $stat): ?>
        <div class="card">
            <div class="muted" style="font-size:0.82rem; font-weight:750"><?= esc($stat['label']) ?></div>
            <div style="font-size:2rem; font-weight:850; color:var(--primary-dark); margin-top:0.2rem"><?= esc((string) $stat['value']) ?></div>
        </div>
    <?php endforeach; ?>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">Hotel Terbaru</span>
        <a href="/admin/hotels" class="btn btn-ghost btn-sm">Kelola Hotel</a>
    </div>
    <table>
        <thead>
            <tr>
                <th>Hotel</th>
                <th>Harga</th>
                <th>Skor Review</th>
                <th>Avg. Jarak POI</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($hotels)): ?>
                <tr>
                    <td colspan="4" style="text-align:center; color:var(--muted); padding:2rem">Belum ada hotel.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($hotels as $hotel): ?>
                    <tr>
                        <td><strong><?= esc($hotel['name']) ?></strong></td>
                        <td>Rp <?= number_format($hotel['price'], 0, ',', '.') ?></td>
                        <td><span class="review-score"><?= number_format((float) $hotel['rating'], 1) ?></span></td>
                        <td><?= isset($hotel['avg_distance']) ? number_format((float) $hotel['avg_distance'], 2) . ' km' : '-' ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?= $this->endSection() ?>
