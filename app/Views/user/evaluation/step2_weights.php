<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <div>
        <h1>Evaluasi MABAC</h1>
        <p>Langkah 2 dari 3 — Atur bobot kepentingan kriteria</p>
    </div>
</div>

<!-- Stepper -->
<div style="display:flex; align-items:center; gap:0; margin-bottom:1.75rem">
    <?php foreach ([['1','Pilih Hotel'],['2','Atur Bobot'],['3','Hasil']] as $i => [$num, $label]): ?>
    <div style="display:flex; align-items:center; gap:0; flex:<?= $i < 2 ? '1' : '0' ?>">
        <div style="display:flex; align-items:center; gap:0.5rem">
            <div style="width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:0.8rem;font-weight:600;background:<?= $num==='2' ? 'var(--primary)' : ($num==='1' ? '#dcfce7' : '#e5e7eb') ?>;color:<?= $num==='2' ? 'white' : ($num==='1' ? '#166534' : 'var(--muted)') ?>"><?= $num==='1' ? '✓' : $num ?></div>
            <span style="font-size:0.875rem;font-weight:<?= $num==='2' ? '600' : '400' ?>;color:<?= $num==='2' ? 'var(--text)' : 'var(--muted)' ?>"><?= $label ?></span>
        </div>
        <?php if ($i < 2): ?><div style="flex:1;height:1px;background:#e5e7eb;margin:0 0.75rem"></div><?php endif; ?>
    </div>
    <?php endforeach; ?>
</div>

<div style="display:grid; grid-template-columns: 1fr 320px; gap:1.25rem; align-items:start">

    <div class="card">
        <div style="margin-bottom:1.25rem">
            <div class="card-title">Bobot Kriteria</div>
            <div style="font-size:0.875rem; color:var(--muted); margin-top:0.25rem">
                Atur seberapa penting tiap kriteria. Total akan dinormalisasi otomatis.
            </div>
        </div>

        <form method="POST" action="/evaluation/calculate" id="weightForm">
            <?= csrf_field() ?>

            <?php foreach ($criterias as $c): ?>
            <div style="margin-bottom:1.375rem; padding-bottom:1.375rem; border-bottom:1px solid var(--border)">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.625rem">
                    <div>
                        <span style="font-weight:500; font-size:0.9rem"><?= esc($c['name']) ?></span>
                        <span class="badge badge-<?= $c['type'] ?>" style="margin-left:0.5rem; font-size:0.7rem">
                            <?= $c['type'] === 'benefit' ? '↑ Benefit' : '↓ Cost' ?>
                        </span>
                    </div>
                    <span id="display_<?= $c['code'] ?>" style="font-size:1rem; font-weight:700; color:var(--primary); min-width:2rem; text-align:right">
                        <?= $c['default_weight'] ?>
                    </span>
                </div>
                <input
                    type="range"
                    name="weights[<?= $c['code'] ?>]"
                    id="w_<?= $c['code'] ?>"
                    min="0" max="10" step="0.5"
                    value="<?= $c['default_weight'] ?>"
                    style="width:100%; accent-color:var(--primary)"
                    oninput="updateWeight('<?= $c['code'] ?>', this.value)"
                >
                <div style="display:flex; justify-content:space-between; font-size:0.7rem; color:var(--muted); margin-top:0.25rem">
                    <span>Tidak penting</span>
                    <span>Sangat penting</span>
                </div>
            </div>
            <?php endforeach; ?>

            <div style="display:flex; justify-content:space-between; align-items:center; margin-top:0.5rem">
                <div style="font-size:0.875rem; color:var(--muted)">
                    <?= $selectedCount ?> hotel dipilih
                </div>
                <button type="submit" class="btn btn-primary">Hitung Rekomendasi →</button>
            </div>
        </form>
    </div>

    <!-- Info panel -->
    <div style="display:flex; flex-direction:column; gap:0.875rem; position:sticky; top:70px">
        <div class="card" style="background:#f0fdf4; border-color:#bbf7d0">
            <div style="font-weight:600; font-size:0.875rem; margin-bottom:0.5rem">Bobot Anda</div>
            <div id="weightSummary" style="font-size:0.8rem; color:var(--muted)"></div>
            <div style="margin-top:0.75rem; padding-top:0.75rem; border-top:1px solid #bbf7d0; font-size:0.8rem">
                Total: <strong id="totalWeight" style="color:#166534">—</strong>
                <span style="color:var(--muted)"> (akan dinormalisasi)</span>
            </div>
        </div>

        <div class="card" style="background:#eff6ff; border-color:#bfdbfe">
            <div style="font-weight:600; font-size:0.875rem; margin-bottom:0.5rem">Tentang MABAC</div>
            <div style="font-size:0.78rem; color:var(--muted); line-height:1.6">
                Metode MABAC menghitung jarak tiap alternatif dari <em>Border Approximation Area</em>.
                Skor positif (+) berarti hotel lebih baik dari rata-rata, skor negatif (−) berarti di bawah rata-rata.
            </div>
        </div>
    </div>

</div>

<script>
function updateWeight(code, val) {
    document.getElementById('display_' + code).textContent = parseFloat(val).toFixed(1);
    recalcTotal();
}

function recalcTotal() {
    const sliders = document.querySelectorAll('input[type=range]');
    let total = 0;
    let html = '';
    sliders.forEach(s => {
        const v = parseFloat(s.value);
        total += v;
        const code = s.id.replace('w_', '');
        const label = s.closest('div').parentElement.querySelector('span[style*="font-weight:500"]').textContent;
        html += `<div style="display:flex;justify-content:space-between;padding:2px 0"><span>${label}</span><strong>${v.toFixed(1)}</strong></div>`;
    });
    document.getElementById('totalWeight').textContent = total.toFixed(1);
    document.getElementById('weightSummary').innerHTML = html;
}

recalcTotal();
</script>

<?= $this->endSection() ?>
