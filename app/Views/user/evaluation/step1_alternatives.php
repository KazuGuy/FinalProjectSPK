<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <div>
        <h1>Evaluasi MABAC</h1>
        <p>Langkah 1 dari 3 — Pilih hotel yang ingin dibandingkan</p>
    </div>
</div>

<!-- Stepper -->
<div style="display:flex; align-items:center; gap:0; margin-bottom:1.75rem">
    <?php foreach ([['1','Pilih Hotel'],['2','Atur Bobot'],['3','Hasil']] as $i => [$num, $label]): ?>
    <div style="display:flex; align-items:center; gap:0; flex:<?= $i < 2 ? '1' : '0' ?>">
        <div style="display:flex; align-items:center; gap:0.5rem">
            <div style="width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:0.8rem;font-weight:600;background:<?= $num==='1' ? 'var(--primary)' : '#e5e7eb' ?>;color:<?= $num==='1' ? 'white' : 'var(--muted)' ?>"><?= $num ?></div>
            <span style="font-size:0.875rem;font-weight:<?= $num==='1' ? '600' : '400' ?>;color:<?= $num==='1' ? 'var(--text)' : 'var(--muted)' ?>"><?= $label ?></span>
        </div>
        <?php if ($i < 2): ?><div style="flex:1;height:1px;background:#e5e7eb;margin:0 0.75rem"></div><?php endif; ?>
    </div>
    <?php endforeach; ?>
</div>

<form method="POST" action="/evaluation/weights">
    <?= csrf_field() ?>

    <!-- Filter ringkas -->
    <div class="card" style="margin-bottom:1rem; padding:1rem 1.25rem">
        <div style="display:flex; gap:0.75rem; align-items:center">
            <input type="text" id="searchHotel" placeholder="Cari nama hotel..." class="form-control" style="max-width:280px" oninput="filterCards()">
            <select id="sortHotel" class="form-control" style="max-width:200px" onchange="filterCards()">
                <option value="">Urutkan</option>
                <option value="price_asc">Harga Terendah</option>
                <option value="rating_desc">Rating Tertinggi</option>
                <option value="discount_desc">Diskon Terbesar</option>
            </select>
        </div>
    </div>

    <!-- Grid hotel -->
    <div id="hotelGrid" style="display:grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap:0.875rem; margin-bottom:5rem">
        <?php foreach ($hotels as $h): ?>
        <label class="hotel-card" data-name="<?= strtolower(esc($h['name'])) ?>" data-price="<?= $h['price'] ?>" data-rating="<?= $h['rating'] ?>" data-discount="<?= $h['discount'] ?>"
            style="display:block; cursor:pointer; border:2px solid var(--border); border-radius:8px; padding:1rem; background:white; transition:border-color 0.15s, background 0.15s; position:relative">
            <input type="checkbox" name="hotel_ids[]" value="<?= $h['id'] ?>"
                style="position:absolute; top:0.75rem; right:0.75rem"
                onchange="updateCounter()">
            <div style="font-weight:600; font-size:0.9375rem; margin-bottom:0.375rem; padding-right:1.5rem"><?= esc($h['name']) ?></div>
            <div style="font-size:0.8rem; color:var(--muted); margin-bottom:0.5rem">
                ⭐ <?= $h['rating'] ?> &nbsp;·&nbsp; <?= $h['facilities_count'] ?> fasilitas
                <?php if ($h['discount'] > 0): ?>&nbsp;·&nbsp; <span style="color:#b45309"><?= $h['discount'] ?>% OFF</span><?php endif; ?>
            </div>
            <div style="font-size:1rem; font-weight:700; color:var(--primary)">
                Rp <?= number_format($h['price'], 0, ',', '.') ?>
                <span style="font-size:0.75rem; font-weight:400; color:var(--muted)">/malam</span>
            </div>
            <?php if (isset($h['avg_distance'])): ?>
            <div style="font-size:0.78rem; color:var(--muted); margin-top:0.375rem">
                📍 <?= number_format($h['avg_distance'], 1) ?> km dari POI
            </div>
            <?php endif; ?>
        </label>
        <?php endforeach; ?>
    </div>

    <!-- Sticky bottom bar -->
    <div style="position:fixed; bottom:0; left:240px; right:0; background:white; border-top:1px solid var(--border); padding:0.875rem 1.75rem; display:flex; align-items:center; justify-content:space-between; z-index:99">
        <span id="counter" style="font-size:0.875rem; color:var(--muted)">0 hotel dipilih (min. 2)</span>
        <button type="submit" id="btnNext" class="btn btn-primary" disabled>
            Lanjut Atur Bobot →
        </button>
    </div>
</form>

<style>
.hotel-card:has(input:checked) {
    border-color: var(--primary);
    background: #eff6ff;
}
</style>

<script>
function updateCounter() {
    const n = document.querySelectorAll('input[name="hotel_ids[]"]:checked').length;
    document.getElementById('counter').textContent = n + ' hotel dipilih (min. 2)';
    document.getElementById('btnNext').disabled = n < 2;
}

function filterCards() {
    const q    = document.getElementById('searchHotel').value.toLowerCase();
    const sort = document.getElementById('sortHotel').value;
    const grid = document.getElementById('hotelGrid');
    const cards = [...grid.querySelectorAll('.hotel-card')];

    cards.forEach(c => {
        c.style.display = c.dataset.name.includes(q) ? '' : 'none';
    });

    if (sort) {
        const visible = cards.filter(c => c.style.display !== 'none');
        visible.sort((a, b) => {
            if (sort === 'price_asc')     return +a.dataset.price - +b.dataset.price;
            if (sort === 'rating_desc')   return +b.dataset.rating - +a.dataset.rating;
            if (sort === 'discount_desc') return +b.dataset.discount - +a.dataset.discount;
            return 0;
        });
        visible.forEach(c => grid.appendChild(c));
    }
}
</script>

<?= $this->endSection() ?>
