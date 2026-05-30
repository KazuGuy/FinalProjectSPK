<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <div>
        <h1>Cari Hotel</h1>
        <p>Temukan hotel yang sesuai kebutuhan Anda</p>
    </div>
</div>

<div style="display:grid; grid-template-columns: 260px 1fr; gap:1.25rem; align-items:start">

    <!-- Filter sidebar -->
    <div class="card" style="position:sticky; top:70px">
        <div class="card-header" style="margin-bottom:1rem">
            <span class="card-title">Filter</span>
            <a href="/hotels" style="font-size:0.8rem; color:var(--muted); text-decoration:none">Reset</a>
        </div>

        <form method="GET" action="/hotels" id="filterForm">
            <!-- Sort -->
            <div class="form-group">
                <label class="form-label">Urutkan</label>
                <select name="sort" class="form-control" onchange="this.form.submit()">
                    <option value="rating_desc"    <?= ($filters['sort'] ?? '') === 'rating_desc'     ? 'selected' : '' ?>>Rating Tertinggi</option>
                    <option value="price_asc"      <?= ($filters['sort'] ?? '') === 'price_asc'       ? 'selected' : '' ?>>Harga Terendah</option>
                    <option value="price_desc"     <?= ($filters['sort'] ?? '') === 'price_desc'      ? 'selected' : '' ?>>Harga Tertinggi</option>
                    <option value="discount_desc"  <?= ($filters['sort'] ?? '') === 'discount_desc'   ? 'selected' : '' ?>>Diskon Terbesar</option>
                    <option value="facilities_desc"<?= ($filters['sort'] ?? '') === 'facilities_desc' ? 'selected' : '' ?>>Fasilitas Terlengkap</option>
                </select>
            </div>

            <!-- Range harga -->
            <div class="form-group">
                <label class="form-label">Harga Per Malam (Rp)</label>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:0.5rem">
                    <input type="number" name="min_price" class="form-control"
                        placeholder="Min" value="<?= $filters['min_price'] ?? '' ?>" step="50000">
                    <input type="number" name="max_price" class="form-control"
                        placeholder="Max" value="<?= $filters['max_price'] ?? '' ?>" step="50000">
                </div>
            </div>

            <!-- Rating -->
            <div class="form-group">
                <label class="form-label">Rating Minimum</label>
                <div style="display:flex; flex-direction:column; gap:0.375rem">
                    <?php foreach ([5, 4, 3, 2] as $r): ?>
                    <label style="display:flex; align-items:center; gap:0.5rem; font-size:0.875rem; cursor:pointer">
                        <input type="radio" name="min_rating" value="<?= $r ?>"
                            <?= ($filters['min_rating'] ?? '') == $r ? 'checked' : '' ?>>
                        <?= str_repeat('⭐', $r) ?> <?= $r ?>+
                    </label>
                    <?php endforeach; ?>
                    <label style="display:flex; align-items:center; gap:0.5rem; font-size:0.875rem; cursor:pointer">
                        <input type="radio" name="min_rating" value=""
                            <?= empty($filters['min_rating']) ? 'checked' : '' ?>>
                        Semua Rating
                    </label>
                </div>
            </div>

            <!-- Diskon -->
            <div class="form-group">
                <label class="form-label">Diskon</label>
                <label style="display:flex; align-items:center; gap:0.5rem; font-size:0.875rem; cursor:pointer">
                    <input type="checkbox" name="min_discount" value="1"
                        <?= !empty($filters['min_discount']) ? 'checked' : '' ?>>
                    Ada diskon
                </label>
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%">Terapkan Filter</button>
        </form>
    </div>

    <!-- Hasil -->
    <div>
        <!-- Search bar -->
        <form method="GET" action="/hotels" style="margin-bottom:1rem">
            <input type="hidden" name="sort"         value="<?= $filters['sort'] ?? 'rating_desc' ?>">
            <input type="hidden" name="min_price"    value="<?= $filters['min_price'] ?? '' ?>">
            <input type="hidden" name="max_price"    value="<?= $filters['max_price'] ?? '' ?>">
            <input type="hidden" name="min_rating"   value="<?= $filters['min_rating'] ?? '' ?>">
            <input type="hidden" name="min_discount" value="<?= $filters['min_discount'] ?? '' ?>">
            <div style="display:flex; gap:0.5rem">
                <input type="text" name="q" class="form-control"
                    placeholder="Cari nama hotel..."
                    value="<?= esc($filters['q'] ?? '') ?>"
                    style="flex:1">
                <button type="submit" class="btn btn-primary">Cari</button>
            </div>
        </form>

        <!-- Info jumlah -->
        <div style="font-size:0.875rem; color:var(--muted); margin-bottom:1rem">
            Menampilkan <?= count($hotels) ?> hotel
            <?php if (!empty($filters['q'])): ?>
                untuk "<strong><?= esc($filters['q']) ?></strong>"
            <?php endif; ?>
        </div>

        <!-- Hotel cards -->
        <?php if (empty($hotels)): ?>
            <div class="card" style="text-align:center; padding:3rem; color:var(--muted)">
                Tidak ada hotel yang sesuai filter. <a href="/hotels">Tampilkan semua</a>
            </div>
        <?php else: ?>
        <div style="display:flex; flex-direction:column; gap:0.875rem">
            <?php foreach ($hotels as $h): ?>
            <div class="card" style="padding:1.25rem">
                <div style="display:flex; justify-content:space-between; align-items:start; gap:1rem">
                    <div style="flex:1">
                        <div style="display:flex; align-items:center; gap:0.5rem; margin-bottom:0.25rem">
                            <h3 style="font-size:1rem; font-weight:600"><?= esc($h['name']) ?></h3>
                            <?php if ($h['discount'] > 0): ?>
                                <span class="badge" style="background:#fef9c3;color:#854d0e"><?= $h['discount'] ?>% OFF</span>
                            <?php endif; ?>
                        </div>
                        <div style="font-size:0.875rem; color:var(--muted); margin-bottom:0.75rem">
                            ⭐ <?= $h['rating'] ?> &nbsp;·&nbsp;
                            <?= $h['facilities_count'] ?> fasilitas &nbsp;·&nbsp;
                            <?php if (isset($h['avg_distance'])): ?>
                                📍 <?= number_format($h['avg_distance'], 1) ?> km dari POI
                            <?php endif; ?>
                        </div>
                        <div style="font-size:0.8rem; color:var(--muted)">
                            <?= esc(mb_strimwidth($h['facilities_detail'], 0, 80, '...')) ?>
                        </div>
                    </div>
                    <div style="text-align:right; flex-shrink:0">
                        <div style="font-size:1.125rem; font-weight:700; color:var(--primary)">
                            Rp <?= number_format($h['price'], 0, ',', '.') ?>
                        </div>
                        <div style="font-size:0.78rem; color:var(--muted)">/malam</div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Pager -->
        <div style="margin-top:1.25rem">
            <?= $pager->links() ?>
        </div>
        <?php endif; ?>

        <!-- CTA evaluasi -->
        <div class="card" style="margin-top:1.5rem; background:#eff6ff; border-color:#bfdbfe; text-align:center; padding:1.5rem">
            <div style="font-weight:600; margin-bottom:0.375rem">Bingung memilih?</div>
            <div style="font-size:0.875rem; color:var(--muted); margin-bottom:1rem">
                Gunakan fitur Evaluasi MABAC untuk membandingkan hotel secara ilmiah
            </div>
            <a href="/evaluation" class="btn btn-primary">Mulai Evaluasi →</a>
        </div>
    </div>

</div>

<?= $this->endSection() ?>
