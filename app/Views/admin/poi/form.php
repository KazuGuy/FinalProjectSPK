<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php $isEdit = isset($poi); ?>

<div class="page-header">
    <div>
        <h1><?= $isEdit ? 'Edit POI' : 'Tambah POI' ?></h1>
        <p>Klik peta untuk menentukan koordinat point of interest</p>
    </div>
    <a href="/admin/poi" class="btn btn-ghost">← Kembali</a>
</div>

<div style="display:grid; grid-template-columns: 400px 1fr; gap:1.25rem; align-items:start">

    <div class="card">
        <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-error" style="margin-bottom:1rem">
                <?php foreach (session()->getFlashdata('errors') as $e): ?>
                    <div>✕ <?= esc($e) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= $isEdit ? "/admin/poi/{$poi['id']}" : '/admin/poi' ?>">
            <?= csrf_field() ?>
            <?php if ($isEdit): ?>
                <input type="hidden" name="_method" value="PUT">
            <?php endif; ?>

            <div class="form-group">
                <label class="form-label">Nama POI *</label>
                <input type="text" name="nama_poi" class="form-control"
                    value="<?= old('nama_poi', $poi['nama_poi'] ?? '') ?>"
                    placeholder="contoh: Pantai Kuta" required>
            </div>

            <div class="form-group">
                <label class="form-label">Latitude *</label>
                <input type="text" name="latitude" id="lat" class="form-control"
                    value="<?= old('latitude', $poi['latitude'] ?? '') ?>"
                    placeholder="Klik peta..." readonly required
                    style="background:#f9fafb; cursor:default">
            </div>

            <div class="form-group">
                <label class="form-label">Longitude *</label>
                <input type="text" name="longitude" id="lng" class="form-control"
                    value="<?= old('longitude', $poi['longitude'] ?? '') ?>"
                    placeholder="Klik peta..." readonly required
                    style="background:#f9fafb; cursor:default">
            </div>

            <div class="form-hint" style="margin-bottom:1rem">
                📍 Klik pada peta untuk mengisi koordinat
            </div>

            <div style="display:flex; gap:0.75rem">
                <button type="submit" class="btn btn-primary">
                    <?= $isEdit ? 'Simpan Perubahan' : 'Tambah POI' ?>
                </button>
                <a href="/admin/poi" class="btn btn-ghost">Batal</a>
            </div>
        </form>
    </div>

    <div class="card" style="padding:0.75rem">
        <div style="font-size:0.8rem; color:var(--muted); margin-bottom:0.5rem; padding:0 0.25rem">
            Klik lokasi POI di peta
        </div>
        <div id="map" style="height:420px; border-radius:6px; border:1px solid var(--border)"></div>
        <div id="coord-display" style="font-size:0.78rem; color:var(--muted); margin-top:0.5rem; padding:0 0.25rem; min-height:1.2rem"></div>
    </div>

</div>

<script>
(function() {
    const initLat = <?= $poi['latitude'] ?? -6.2088 ?>;
    const initLng = <?= $poi['longitude'] ?? 106.8456 ?>;
    const hasCoord = <?= isset($poi['latitude']) ? 'true' : 'false' ?>;

    const map = L.map('map').setView([initLat, initLng], hasCoord ? 15 : 12);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© <a href="https://openstreetmap.org">OpenStreetMap</a>'
    }).addTo(map);

    let marker;

    function placeMarker(latlng) {
        if (marker) {
            marker.setLatLng(latlng);
        } else {
            marker = L.marker(latlng, {
                draggable: true,
                icon: L.icon({
                    iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
                    iconRetinaUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon-2x.png',
                    shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
                    iconSize: [25, 41], iconAnchor: [12, 41]
                })
            }).addTo(map);
            marker.on('dragend', e => updateCoords(e.target.getLatLng()));
        }
        updateCoords(latlng);
    }

    function updateCoords(latlng) {
        const lat = latlng.lat.toFixed(6);
        const lng = latlng.lng.toFixed(6);
        document.getElementById('lat').value = lat;
        document.getElementById('lng').value = lng;
        document.getElementById('coord-display').textContent = `📍 ${lat}, ${lng}`;
    }

    map.on('click', e => placeMarker(e.latlng));

    if (hasCoord) placeMarker(L.latLng(initLat, initLng));
})();
</script>

<?= $this->endSection() ?>
