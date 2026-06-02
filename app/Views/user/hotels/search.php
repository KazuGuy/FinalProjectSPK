<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
$currentUser = session()->get('user') ?? ['role' => 'guest'];
$canEvaluate = ($currentUser['role'] ?? 'guest') === 'user';
$hotelMarkers = array_values(array_filter(array_map(static fn($h) => [
    'name'      => $h['name'],
    'lat'       => isset($h['latitude']) ? (float) $h['latitude'] : null,
    'lng'       => isset($h['longitude']) ? (float) $h['longitude'] : null,
    'price'     => (float) $h['price'],
    'rating'    => (float) $h['rating'],
    'discount'  => (int) $h['discount'],
], $hotels), static fn($h) => $h['lat'] !== null && $h['lng'] !== null));
$poiMarkers = array_values(array_filter(array_map(static fn($p) => [
    'name' => $p['nama_poi'],
    'lat'  => isset($p['latitude']) ? (float) $p['latitude'] : null,
    'lng'  => isset($p['longitude']) ? (float) $p['longitude'] : null,
], $pois), static fn($p) => $p['lat'] !== null && $p['lng'] !== null));
?>

<section class="hero-band">
    <h1>Cari hotel dengan data lokasi, harga, dan review.</h1>
    <p>Telusuri hotel serta POI terdekat. Skor review adalah penilaian pengguna, bukan kelas bintang hotel.</p>
</section>

<section class="search-panel overlap">
    <form method="GET" action="/hotels" class="search-grid">
        <div>
            <label class="form-label">Nama hotel</label>
            <input type="text" name="q" class="form-control"
                placeholder="Cari hotel atau area"
                value="<?= esc($filters['q'] ?? '') ?>">
        </div>
        <div>
            <label class="form-label">Harga min</label>
            <input type="number" name="min_price" class="form-control"
                placeholder="Rp min"
                value="<?= esc($filters['min_price'] ?? '') ?>" step="50000">
        </div>
        <div>
            <label class="form-label">Harga max</label>
            <input type="number" name="max_price" class="form-control"
                placeholder="Rp max"
                value="<?= esc($filters['max_price'] ?? '') ?>" step="50000">
        </div>
        <div>
            <input type="hidden" name="sort" value="<?= esc($filters['sort'] ?? 'rating_desc') ?>">
            <input type="hidden" name="min_rating" value="<?= esc($filters['min_rating'] ?? '') ?>">
            <input type="hidden" name="min_discount" value="<?= esc($filters['min_discount'] ?? '') ?>">
            <button type="submit" class="btn btn-primary" style="width:100%">Cari Hotel</button>
        </div>
    </form>
</section>

<div class="content-grid">
    <aside class="filter-panel">
        <div class="card-header">
            <span class="card-title">Filter</span>
            <a href="/hotels" class="muted" style="font-size:0.82rem; text-decoration:none; font-weight:750">Reset</a>
        </div>

        <form method="GET" action="/hotels" id="filterForm">
            <input type="hidden" name="q" value="<?= esc($filters['q'] ?? '') ?>">

            <div class="form-group">
                <label class="form-label">Urutkan</label>
                <select name="sort" class="form-control" onchange="this.form.submit()">
                    <option value="rating_desc" <?= ($filters['sort'] ?? '') === 'rating_desc' ? 'selected' : '' ?>>Skor review tertinggi</option>
                    <option value="price_asc" <?= ($filters['sort'] ?? '') === 'price_asc' ? 'selected' : '' ?>>Harga terendah</option>
                    <option value="price_desc" <?= ($filters['sort'] ?? '') === 'price_desc' ? 'selected' : '' ?>>Harga tertinggi</option>
                    <option value="discount_desc" <?= ($filters['sort'] ?? '') === 'discount_desc' ? 'selected' : '' ?>>Diskon terbesar</option>
                    <option value="facilities_desc" <?= ($filters['sort'] ?? '') === 'facilities_desc' ? 'selected' : '' ?>>Fasilitas terbanyak</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Harga per malam</label>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:0.5rem">
                    <input type="number" name="min_price" class="form-control"
                        placeholder="Min" value="<?= esc($filters['min_price'] ?? '') ?>" step="50000">
                    <input type="number" name="max_price" class="form-control"
                        placeholder="Max" value="<?= esc($filters['max_price'] ?? '') ?>" step="50000">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Skor review minimum</label>
                <div style="display:grid; gap:0.45rem">
                    <?php foreach ([4.5, 4, 3, 2] as $score): ?>
                        <label style="display:flex; align-items:center; gap:0.55rem; font-size:0.88rem; cursor:pointer">
                            <input type="radio" name="min_rating" value="<?= $score ?>"
                                <?= ($filters['min_rating'] ?? '') == $score ? 'checked' : '' ?>>
                            <span class="review-score" style="height:26px; min-width:38px; font-size:0.78rem"><?= number_format($score, 1) ?></span>
                            ke atas
                        </label>
                    <?php endforeach; ?>
                    <label style="display:flex; align-items:center; gap:0.55rem; font-size:0.88rem; cursor:pointer">
                        <input type="radio" name="min_rating" value=""
                            <?= empty($filters['min_rating']) ? 'checked' : '' ?>>
                        Semua review
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Promo</label>
                <label style="display:flex; align-items:center; gap:0.55rem; font-size:0.88rem; cursor:pointer">
                    <input type="checkbox" name="min_discount" value="1"
                        <?= !empty($filters['min_discount']) ? 'checked' : '' ?>>
                    Hotel dengan diskon
                </label>
            </div>

            <button type="submit" class="btn btn-blue" style="width:100%">Terapkan Filter</button>
        </form>

        <div class="map-card" style="margin-top:1rem">
            <div id="hotelMap" style="height:260px"></div>
        </div>
    </aside>

    <section>
        <div class="split-row" style="margin-bottom:0.85rem">
            <div class="muted" style="font-size:0.9rem">
                Menampilkan <strong><?= count($hotels) ?></strong> hotel
                <?php if (!empty($filters['q'])): ?>
                    untuk "<strong><?= esc($filters['q']) ?></strong>"
                <?php endif; ?>
            </div>
            <a href="/poi" class="btn btn-ghost btn-sm">Lihat POI</a>
        </div>

        <?php if (empty($hotels)): ?>
            <div class="card" style="text-align:center; padding:3rem; color:var(--muted)">
                Tidak ada hotel yang sesuai filter. <a href="/hotels">Tampilkan semua hotel</a>
            </div>
        <?php else: ?>
            <div class="results-stack">
                <?php foreach ($hotels as $hotel): ?>
                    <article class="result-card">
                        <div style="display:grid; grid-template-columns:minmax(0, 1fr) 170px; gap:1rem; align-items:start">
                            <div>
                                <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:0.8rem; margin-bottom:0.55rem">
                                    <div>
                                        <h2 style="font-size:1.08rem; font-weight:850; line-height:1.25"><?= esc($hotel['name']) ?></h2>
                                        <div class="muted" style="font-size:0.84rem; margin-top:0.18rem">
                                            <?= esc(mb_strimwidth($hotel['facilities_detail'], 0, 110, '...')) ?>
                                        </div>
                                    </div>
                                    <?php if ((int) $hotel['discount'] > 0): ?>
                                        <span class="badge badge-deal"><?= (int) $hotel['discount'] ?>% OFF</span>
                                    <?php endif; ?>
                                </div>

                                <div style="display:flex; flex-wrap:wrap; align-items:center; gap:0.55rem 0.85rem; font-size:0.86rem">
                                    <span class="review-score"><?= number_format((float) $hotel['rating'], 1) ?></span>
                                    <span><strong>Skor review</strong> pengguna</span>
                                    <span class="muted"><?= (int) $hotel['facilities_count'] ?> fasilitas</span>
                                    <?php if (isset($hotel['avg_distance'])): ?>
                                        <span class="muted"><?= number_format((float) $hotel['avg_distance'], 1) ?> km avg. ke POI</span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div style="text-align:right; border-left:1px solid var(--border); padding-left:1rem">
                                <div class="muted" style="font-size:0.78rem">Mulai dari</div>
                                <div class="price" style="font-size:1.15rem">Rp <?= number_format($hotel['price'], 0, ',', '.') ?></div>
                                <div class="muted" style="font-size:0.78rem; margin-bottom:0.85rem">per malam</div>
                                <?php if ($canEvaluate): ?>
                                    <a href="/evaluation" class="btn btn-primary btn-sm" style="width:100%">Bandingkan</a>
                                <?php else: ?>
                                    <a href="/register" class="btn btn-ghost btn-sm" style="width:100%">Daftar untuk DSS</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

            <div class="pager">
                <?= $pager->links() ?>
            </div>
        <?php endif; ?>

        <div class="<?= $canEvaluate ? 'card' : 'locked-note' ?>" style="margin-top:1rem">
            <?php if ($canEvaluate): ?>
                <div class="split-row">
                    <div>
                        <div style="font-weight:850; margin-bottom:0.15rem">Butuh ranking berbasis kriteria?</div>
                        <div class="muted" style="font-size:0.88rem">Pilih beberapa hotel, atur bobot, lalu jalankan metode MABAC.</div>
                    </div>
                    <a href="/evaluation" class="btn btn-primary">Mulai Evaluasi</a>
                </div>
            <?php else: ?>
                <div class="split-row">
                    <div>
                        <div style="font-weight:850; margin-bottom:0.15rem">Mode guest aktif</div>
                        <div style="font-size:0.88rem">Guest dapat mencari hotel dan POI. Evaluasi DSS hanya untuk pengguna terdaftar.</div>
                    </div>
                    <div style="display:flex; gap:0.5rem">
                        <a href="/login" class="btn btn-ghost btn-sm">Masuk</a>
                        <a href="/register" class="btn btn-primary btn-sm">Daftar</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>
</div>

<script>
(function() {
    const hotels = <?= json_encode($hotelMarkers, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;
    const pois = <?= json_encode($poiMarkers, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;
    const mapEl = document.getElementById('hotelMap');

    if (!mapEl || typeof L === 'undefined') return;

    const map = L.map(mapEl, { scrollWheelZoom: false }).setView([-6.2088, 106.8456], 11);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    const bounds = [];

    hotels.forEach((hotel) => {
        const marker = L.marker([hotel.lat, hotel.lng]).addTo(map);
        marker.bindPopup(`<strong>${hotel.name}</strong><br>Review ${hotel.rating.toFixed(1)}<br>Rp ${new Intl.NumberFormat('id-ID').format(hotel.price)}`);
        bounds.push([hotel.lat, hotel.lng]);
    });

    pois.forEach((poi) => {
        const marker = L.circleMarker([poi.lat, poi.lng], {
            radius: 6,
            color: '#ff6d00',
            fillColor: '#ff6d00',
            fillOpacity: 0.8
        }).addTo(map);
        marker.bindPopup(`<strong>POI</strong><br>${poi.name}`);
        bounds.push([poi.lat, poi.lng]);
    });

    if (bounds.length) {
        map.fitBounds(bounds, { padding: [24, 24], maxZoom: 14 });
    }
})();
</script>

<?= $this->endSection() ?>
