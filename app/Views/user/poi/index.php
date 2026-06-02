<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
$poiMarkers = array_values(array_filter(array_map(static fn($p) => [
    'name' => $p['nama_poi'],
    'lat'  => isset($p['latitude']) ? (float) $p['latitude'] : null,
    'lng'  => isset($p['longitude']) ? (float) $p['longitude'] : null,
], $pois), static fn($p) => $p['lat'] !== null && $p['lng'] !== null));
?>

<div class="page-header">
    <div>
        <h1>Point of Interest</h1>
        <p>Lihat lokasi POI yang digunakan sebagai referensi jarak hotel.</p>
    </div>
    <a href="/hotels" class="btn btn-ghost">Kembali ke Hotel</a>
</div>

<div style="display:grid; grid-template-columns:minmax(0, 1fr) 360px; gap:1rem; align-items:start">
    <div class="map-card">
        <div id="poiMap" style="height:560px"></div>
    </div>

    <aside class="card">
        <div class="card-header">
            <span class="card-title">Daftar POI</span>
            <span class="badge badge-deal"><?= count($pois) ?> lokasi</span>
        </div>

        <input type="search" id="poiSearch" class="form-control" placeholder="Cari POI" style="margin-bottom:0.85rem">

        <?php if (empty($pois)): ?>
            <div class="muted" style="text-align:center; padding:2rem 0">Belum ada data POI.</div>
        <?php else: ?>
            <div id="poiList" style="display:grid; gap:0.65rem; max-height:450px; overflow:auto">
                <?php foreach ($pois as $poi): ?>
                    <button
                        type="button"
                        class="poi-item"
                        data-name="<?= strtolower(esc($poi['nama_poi'])) ?>"
                        data-lat="<?= esc($poi['latitude']) ?>"
                        data-lng="<?= esc($poi['longitude']) ?>"
                        style="text-align:left; background:#fff; border:1px solid var(--border); border-radius:8px; padding:0.75rem; cursor:pointer">
                        <strong style="display:block; font-size:0.94rem"><?= esc($poi['nama_poi']) ?></strong>
                        <span class="muted" style="font-size:0.78rem"><?= esc($poi['latitude']) ?>, <?= esc($poi['longitude']) ?></span>
                    </button>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </aside>
</div>

<script>
(function() {
    const pois = <?= json_encode($poiMarkers, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;
    const mapEl = document.getElementById('poiMap');
    const search = document.getElementById('poiSearch');
    const list = document.getElementById('poiList');

    if (!mapEl || typeof L === 'undefined') return;

    const map = L.map(mapEl).setView([-6.2088, 106.8456], 11);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    const markers = new Map();
    const bounds = [];

    pois.forEach((poi) => {
        const marker = L.marker([poi.lat, poi.lng]).addTo(map);
        marker.bindPopup(`<strong>${poi.name}</strong>`);
        markers.set(`${poi.lat},${poi.lng}`, marker);
        bounds.push([poi.lat, poi.lng]);
    });

    if (bounds.length) {
        map.fitBounds(bounds, { padding: [28, 28], maxZoom: 14 });
    }

    list?.addEventListener('click', (event) => {
        const item = event.target.closest('.poi-item');
        if (!item) return;
        const lat = Number(item.dataset.lat);
        const lng = Number(item.dataset.lng);
        map.setView([lat, lng], 15);
        markers.get(`${lat},${lng}`)?.openPopup();
    });

    search?.addEventListener('input', () => {
        const q = search.value.trim().toLowerCase();
        list?.querySelectorAll('.poi-item').forEach((item) => {
            item.style.display = item.dataset.name.includes(q) ? '' : 'none';
        });
    });
})();
</script>

<style>
@media (max-width: 900px) {
    div[style*="grid-template-columns:minmax(0, 1fr) 360px"] {
        grid-template-columns: 1fr !important;
    }
}
</style>

<?= $this->endSection() ?>
