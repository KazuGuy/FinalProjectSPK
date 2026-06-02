<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
$isEdit = isset($poi);
$latValue = old('latitude', $poi['latitude'] ?? '');
$lngValue = old('longitude', $poi['longitude'] ?? '');
$hasCoord = $latValue !== '' && $lngValue !== '';
$initLat = $hasCoord ? (float) $latValue : -6.2088;
$initLng = $hasCoord ? (float) $lngValue : 106.8456;
?>

<div class="page-header">
    <div>
        <h1><?= $isEdit ? 'Edit POI' : 'Tambah POI' ?></h1>
        <p>Cari point of interest melalui OpenStreetMap atau pilih langsung di peta.</p>
    </div>
    <a href="/admin/poi" class="btn btn-ghost">Kembali</a>
</div>

<div style="display:grid; grid-template-columns: 380px minmax(0, 1fr); gap:1rem; align-items:start">
    <section class="card">
        <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-error">
                <div>
                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                        <div><?= esc($error) ?></div>
                    <?php endforeach; ?>
                </div>
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
                    placeholder="Contoh: Pantai Kuta" required>
            </div>

            <div class="form-group">
                <label class="form-label">Latitude *</label>
                <input type="text" name="latitude" id="lat" class="form-control"
                    value="<?= esc($latValue) ?>" placeholder="Pilih dari peta" readonly required>
            </div>

            <div class="form-group">
                <label class="form-label">Longitude *</label>
                <input type="text" name="longitude" id="lng" class="form-control"
                    value="<?= esc($lngValue) ?>" placeholder="Pilih dari peta" readonly required>
            </div>

            <div class="form-hint" style="margin-bottom:1rem">
                Koordinat dari peta digunakan untuk menghitung jarak hotel ke POI.
            </div>

            <div style="display:flex; gap:0.65rem">
                <button type="submit" class="btn btn-primary">
                    <?= $isEdit ? 'Simpan Perubahan' : 'Tambah POI' ?>
                </button>
                <a href="/admin/poi" class="btn btn-ghost">Batal</a>
            </div>
        </form>
    </section>

    <section class="card">
        <div class="card-header">
            <span class="card-title">Input Koordinat OpenStreetMap</span>
        </div>

        <div class="map-search">
            <input type="search" id="osmQuery" class="form-control"
                placeholder="Cari tempat wisata, stasiun, bandara, atau alamat">
            <button type="button" id="osmSearchBtn" class="btn btn-blue">Cari</button>
        </div>
        <div id="osmResults" class="map-results"></div>

        <div id="map" style="height:460px; border-radius:8px; border:1px solid var(--border)"></div>
        <div id="coord-display" class="form-hint" style="min-height:1.2rem">
            <?= $hasCoord ? esc($latValue . ', ' . $lngValue) : 'Klik peta atau pilih hasil pencarian untuk mengisi koordinat.' ?>
        </div>
    </section>
</div>

<script>
(function() {
    const initLat = <?= json_encode($initLat) ?>;
    const initLng = <?= json_encode($initLng) ?>;
    const hasCoord = <?= $hasCoord ? 'true' : 'false' ?>;
    const latInput = document.getElementById('lat');
    const lngInput = document.getElementById('lng');
    const coordDisplay = document.getElementById('coord-display');
    const queryInput = document.getElementById('osmQuery');
    const searchBtn = document.getElementById('osmSearchBtn');
    const resultsBox = document.getElementById('osmResults');

    const map = L.map('map').setView([initLat, initLng], hasCoord ? 15 : 12);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    let marker;

    function placeMarker(latlng, label) {
        if (marker) {
            marker.setLatLng(latlng);
        } else {
            marker = L.marker(latlng, { draggable: true }).addTo(map);
            marker.on('dragend', (event) => updateCoords(event.target.getLatLng()));
        }
        updateCoords(latlng, label);
    }

    function updateCoords(latlng, label) {
        const lat = Number(latlng.lat).toFixed(6);
        const lng = Number(latlng.lng).toFixed(6);
        latInput.value = lat;
        lngInput.value = lng;
        coordDisplay.textContent = label ? `${lat}, ${lng} - ${label}` : `${lat}, ${lng}`;
    }

    async function searchOsm() {
        const q = queryInput.value.trim();
        if (!q) return;

        resultsBox.style.display = 'block';
        resultsBox.innerHTML = '<button type="button" class="map-result-item">Mencari lokasi...</button>';

        try {
            const params = new URLSearchParams({
                format: 'jsonv2',
                q,
                addressdetails: '1',
                limit: '6',
                countrycodes: 'id'
            });
            const response = await fetch(`https://nominatim.openstreetmap.org/search?${params.toString()}`, {
                headers: { 'Accept': 'application/json' }
            });
            const data = await response.json();

            if (!data.length) {
                resultsBox.innerHTML = '<button type="button" class="map-result-item">Lokasi tidak ditemukan.</button>';
                return;
            }

            resultsBox.innerHTML = data.map((item, index) => `
                <button type="button" class="map-result-item" data-index="${index}">
                    ${item.display_name}
                </button>
            `).join('');

            resultsBox.querySelectorAll('.map-result-item[data-index]').forEach((button) => {
                button.addEventListener('click', () => {
                    const item = data[Number(button.dataset.index)];
                    const latlng = L.latLng(Number(item.lat), Number(item.lon));
                    map.setView(latlng, 16);
                    placeMarker(latlng, item.display_name);
                    resultsBox.style.display = 'none';
                });
            });
        } catch (error) {
            resultsBox.innerHTML = '<button type="button" class="map-result-item">Gagal menghubungi OpenStreetMap.</button>';
        }
    }

    map.on('click', (event) => placeMarker(event.latlng));
    searchBtn.addEventListener('click', searchOsm);
    queryInput.addEventListener('keydown', (event) => {
        if (event.key === 'Enter') {
            event.preventDefault();
            searchOsm();
        }
    });

    if (hasCoord) {
        placeMarker(L.latLng(initLat, initLng));
    }
})();
</script>

<style>
@media (max-width: 900px) {
    div[style*="grid-template-columns: 380px"] {
        grid-template-columns: 1fr !important;
    }
}
</style>

<?= $this->endSection() ?>
