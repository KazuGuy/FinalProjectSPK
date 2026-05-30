<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php $isEdit = isset($hotel); ?>

<div class="page-header">
    <div>
        <h1><?= $isEdit ? 'Edit Hotel' : 'Tambah Hotel' ?></h1>
        <p>Klik peta untuk menentukan koordinat lokasi hotel</p>
    </div>
    <a href="/admin/hotels" class="btn btn-ghost">← Kembali</a>
</div>

<div style="display:grid; grid-template-columns: 1fr 1fr; gap:1.25rem; align-items:start">

    <!-- Form -->
    <div class="card">
        <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-error" style="margin-bottom:1rem">
                <?php foreach (session()->getFlashdata('errors') as $e): ?>
                    <div>✕ <?= esc($e) ?></div>
                <?php endforeach; ?>
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
                    placeholder="contoh: Hotel Grand Melia" required>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:0.75rem">
                <div class="form-group">
                    <label class="form-label">Harga/Malam (Rp) *</label>
                    <input type="number" name="price" class="form-control"
                        value="<?= old('price', $hotel['price'] ?? '') ?>"
                        placeholder="350000" min="0" step="1000" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Rating *</label>
                    <input type="number" name="rating" class="form-control"
                        value="<?= old('rating', $hotel['rating'] ?? '') ?>"
                        placeholder="4.5" min="0" max="5" step="0.1" required>
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
                    placeholder="WiFi, Kolam Renang, Restoran, ..."><?= old('facilities_detail', $hotel['facilities_detail'] ?? '') ?></textarea>
            </div>

            <!-- Koordinat (diisi otomatis dari klik peta) -->
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:0.75rem">
                <div class="form-group">
                    <label class="form-label">Latitude *</label>
                    <input type="text" name="latitude" id="lat" class="form-control"
                        value="<?= old('latitude', $hotel['latitude'] ?? '') ?>"
                        placeholder="Klik peta..." readonly required
                        style="background:#f9fafb; cursor:default">
                </div>
                <div class="form-group">
                    <label class="form-label">Longitude *</label>
                    <input type="text" name="longitude" id="lng" class="form-control"
                        value="<?= old('longitude', $hotel['longitude'] ?? '') ?>"
                        placeholder="Klik peta..." readonly required
                        style="background:#f9fafb; cursor:default">
                </div>
            </div>
            <div class="form-hint" style="margin-top:-0.75rem; margin-bottom:1rem">
                📍 Klik pada peta di sebelah kanan untuk mengisi koordinat otomatis
            </div>

            <div style="display:flex; gap:0.75rem; margin-top:1rem">
                <button type="submit" class="btn btn-primary">
                    <?= $isEdit ? 'Simpan Perubahan' : 'Tambah Hotel' ?>
                </button>
                <a href="/admin/hotels" class="btn btn-ghost">Batal</a>
            </div>
        </form>
    </div>

    <!-- Peta -->
    <div class="card" style="padding:0.75rem">
        <div style="font-size:0.8rem; color:var(--muted); margin-bottom:0.5rem; padding: 0 0.25rem">
            Klik lokasi hotel di peta
        </div>
        <div id="map" style="height:500px; border-radius:6px; border:1px solid var(--border)"></div>
        <div id="coord-display" style="font-size:0.78rem; color:var(--muted); margin-top:0.5rem; padding:0 0.25rem; min-height:1.2rem"></div>
    </div>

</div>

<script>
(function() {
    const initLat = <?= $hotel['latitude'] ?? -6.2088 ?>;
    const initLng = <?= $hotel['longitude'] ?? 106.8456 ?>;
    const hasCoord = <?= isset($hotel['latitude']) ? 'true' : 'false' ?>;

    const map = L.map('map').setView([initLat, initLng], hasCoord ? 15 : 12);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© <a href="https://openstreetmap.org">OpenStreetMap</a>'
    }).addTo(map);

    let marker;

    function placeMarker(latlng) {
        if (marker) {
            marker.setLatLng(latlng);
        } else {
            marker = L.marker(latlng, { draggable: true }).addTo(map);
            marker.on('dragend', function(e) {
                updateCoords(e.target.getLatLng());
            });
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

    map.on('click', function(e) {
        placeMarker(e.latlng);
    });

    // Jika edit, tampilkan marker awal
    if (hasCoord) {
        placeMarker(L.latLng(initLat, initLng));
    }
})();
</script>

<?= $this->endSection() ?>
