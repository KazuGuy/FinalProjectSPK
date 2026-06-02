<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
$isEdit = isset($hotel);
$latValue = old('latitude', $hotel['latitude'] ?? '');
$lngValue = old('longitude', $hotel['longitude'] ?? '');
$hasCoord = $latValue !== '' && $lngValue !== '';
$initLat = $hasCoord ? (float) $latValue : -6.2088;
$initLng = $hasCoord ? (float) $lngValue : 106.8456;
?>

<div class="page-header">
    <div>
        <h1><?= $isEdit ? 'Edit Hotel' : 'Tambah Hotel' ?></h1>
        <p>Cari lokasi lewat OpenStreetMap, lalu sesuaikan pin bila diperlukan.</p>
    </div>
    <a href="/admin/hotels" class="btn btn-ghost">Kembali</a>
</div>

<div style="display:grid; grid-template-columns: minmax(360px, 440px) minmax(0, 1fr); gap:1rem; align-items:start">
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

        <form method="POST" action="<?= $isEdit ? "/admin/hotels/{$hotel['id']}" : '/admin/hotels' ?>">
            <?= csrf_field() ?>
            <?php if ($isEdit): ?>
                <input type="hidden" name="_method" value="PUT">
            <?php endif; ?>

            <div class="form-group">
                <label class="form-label">Nama Hotel *</label>
                <input type="text" name="name" class="form-control"
                    value="<?= old('name', $hotel['name'] ?? '') ?>"
                    placeholder="Contoh: Hotel Grand Melia" required>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:0.75rem">
                <div class="form-group">
                    <label class="form-label">Harga/Malam (Rp) *</label>
                    <input type="number" name="price" class="form-control"
                        value="<?= old('price', $hotel['price'] ?? '') ?>"
                        placeholder="350000" min="0" step="1000" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Skor Review Pengguna *</label>
                    <input type="number" name="rating" class="form-control"
                        value="<?= old('rating', $hotel['rating'] ?? '') ?>"
                        placeholder="4.5" min="0" max="5" step="0.1" required>
                    <div class="form-hint">Ini bukan bintang hotel, melainkan rating ulasan pengguna.</div>
                </div>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:0.75rem">
                <div class="form-group">
                    <label class="form-label">Jumlah Fasilitas *</label>
                    <input type="number" name="facilities_count" class="form-control"
                        value="<?= old('facilities_count', $hotel['facilities_count'] ?? '') ?>"
                        placeholder="10" min="0" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Diskon (%)</label>
                    <input type="number" name="discount" class="form-control"
                        value="<?= old('discount', $hotel['discount'] ?? 0) ?>"
                        placeholder="0" min="0" max="100">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Detail Fasilitas *</label>
                <textarea name="facilities_detail" class="form-control" rows="3"
                    placeholder="WiFi, kolam renang, restoran, akses transportasi"><?= old('facilities_detail', $hotel['facilities_detail'] ?? '') ?></textarea>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:0.75rem">
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
            </div>

            <div style="display:flex; gap:0.65rem; margin-top:0.8rem">
                <button type="submit" class="btn btn-primary">
                    <?= $isEdit ? 'Simpan Perubahan' : 'Tambah Hotel' ?>
                </button>
                <a href="/admin/hotels" class="btn btn-ghost">Batal</a>
            </div>
        </form>
    </section>

    <section class="card">
        <div class="card-header">
            <span class="card-title">Input Koordinat OpenStreetMap</span>
        </div>

        <div class="map-search">
            <input type="search" id="osmQuery" class="form-control"
                placeholder="Cari nama hotel, alamat, atau area">
            <button type="button" id="osmSearchBtn" class="btn btn-blue">Cari</button>
        </div>
        <div id="osmResults" class="map-results"></div>

        <div id="map" style="height:500px; border-radius:8px; border:1px solid var(--border)"></div>
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
@media (max-width: 960px) {
    div[style*="grid-template-columns: minmax(360px, 440px)"] {
        grid-template-columns: 1fr !important;
    }
}
</style>

<?= $this->endSection() ?>
