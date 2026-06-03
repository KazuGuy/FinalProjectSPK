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

    <form method="POST" action="/admin/criteria/<?= $criteria['id'] ?>">
        <?= csrf_field() ?>

        <div class="form-group">
            <label class="form-label">Kode</label>
            <input type="text" class="form-control" value="<?= esc($criteria['code']) ?>" disabled>
        </div>

        <div class="form-group">
            <label class="form-label">Nama Kriteria</label>
            <input type="text" class="form-control" value="<?= esc($criteria['name']) ?>" disabled>
        </div>

        <div class="form-group">
            <label class="form-label">Tipe *</label>
            <select name="type" class="form-control">
                <option value="benefit" <?= $criteria['type'] === 'benefit' ? 'selected' : '' ?>>Benefit — makin besar makin baik</option>
                <option value="cost"    <?= $criteria['type'] === 'cost'    ? 'selected' : '' ?>>Cost — makin kecil makin baik</option>
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">Bobot Default *</label>
            <input type="number" name="default_weight" class="form-control"
                value="<?= $criteria['default_weight'] ?>"
                step="any" min="0.1">
        </div>

        <div style="display:flex; gap:0.75rem; margin-top:1.5rem">
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            <a href="/admin/criteria" class="btn btn-ghost">Batal</a>
        </div>
    </form>
</div>

<?= $this->endSection() ?>
