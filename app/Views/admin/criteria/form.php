<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php $isEdit = isset($criteria); ?>

<div class="page-header">
    <div>
        <h1><?= $isEdit ? 'Edit Kriteria' : 'Tambah Kriteria' ?></h1>
        <p><?= $isEdit ? 'Perbarui data kriteria penilaian' : 'Tambahkan kriteria penilaian baru' ?></p>
    </div>
    <a href="/admin/criteria" class="btn btn-ghost">← Kembali</a>
</div>

<div class="card" style="max-width: 540px">
    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-error" style="margin-bottom:1rem">
            <?php foreach (session()->getFlashdata('errors') as $e): ?>
                <div>✕ <?= esc($e) ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="<?= $isEdit ? "/admin/criteria/{$criteria['id']}" : '/admin/criteria' ?>">
        <?= csrf_field() ?>
        <?php if ($isEdit): ?>
            <input type="hidden" name="_method" value="PUT">
        <?php endif; ?>

        <div class="form-group">
            <label class="form-label">Kode <span style="color:#ef4444">*</span></label>
            <input
                type="text"
                name="code"
                class="form-control"
                value="<?= old('code', $criteria['code'] ?? '') ?>"
                placeholder="C1, C2, ..."
                maxlength="5"
                <?= $isEdit ? '' : '' ?>
            >
            <div class="form-hint">Maksimal 5 karakter, unik (contoh: C1, C2)</div>
        </div>

        <div class="form-group">
            <label class="form-label">Nama Kriteria <span style="color:#ef4444">*</span></label>
            <input
                type="text"
                name="name"
                class="form-control"
                value="<?= old('name', $criteria['name'] ?? '') ?>"
                placeholder="contoh: Harga Per Malam"
                maxlength="100"
            >
        </div>

        <div class="form-group">
            <label class="form-label">Tipe <span style="color:#ef4444">*</span></label>
            <select name="type" class="form-control">
                <option value="benefit" <?= old('type', $criteria['type'] ?? '') === 'benefit' ? 'selected' : '' ?>>
                    Benefit — makin besar makin baik
                </option>
                <option value="cost" <?= old('type', $criteria['type'] ?? '') === 'cost' ? 'selected' : '' ?>>
                    Cost — makin kecil makin baik
                </option>
            </select>
            <div class="form-hint">Contoh Benefit: skor review pengguna, fasilitas. Contoh Cost: harga, jarak.</div>
        </div>

        <div class="form-group">
            <label class="form-label">Bobot Default <span style="color:#ef4444">*</span></label>
            <input
                type="number"
                name="default_weight"
                class="form-control"
                value="<?= old('default_weight', $criteria['default_weight'] ?? 1) ?>"
                step="0.1"
                min="0.1"
                placeholder="1"
            >
            <div class="form-hint">Bobot relatif antar kriteria. Akan dinormalisasi otomatis saat evaluasi.</div>
        </div>

        <div style="display:flex; gap:0.75rem; margin-top:1.5rem">
            <button type="submit" class="btn btn-primary">
                <?= $isEdit ? 'Simpan Perubahan' : 'Tambah Kriteria' ?>
            </button>
            <a href="/admin/criteria" class="btn btn-ghost">Batal</a>
        </div>
    </form>
</div>

<?= $this->endSection() ?>
