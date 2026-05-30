<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <div>
        <h1>Kriteria</h1>
        <p>Kelola kriteria penilaian hotel</p>
    </div>
    <a href="/admin/criteria/create" class="btn btn-primary">
        + Tambah Kriteria
    </a>
</div>

<div class="card">
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Kode</th>
                <th>Nama</th>
                <th>Tipe</th>
                <th>Bobot Default</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($criterias)): ?>
            <tr>
                <td colspan="6" style="text-align:center; color: var(--muted); padding: 2rem;">
                    Belum ada kriteria. <a href="/admin/criteria/create">Tambah sekarang</a>
                </td>
            </tr>
            <?php else: ?>
            <?php foreach ($criterias as $i => $c): ?>
            <tr>
                <td style="color: var(--muted)"><?= $i + 1 ?></td>
                <td><code style="background:#f3f4f6; padding:2px 6px; border-radius:4px; font-size:0.8rem"><?= esc($c['code']) ?></code></td>
                <td><?= esc($c['name']) ?></td>
                <td>
                    <span class="badge badge-<?= $c['type'] ?>">
                        <?= $c['type'] === 'benefit' ? '↑ Benefit' : '↓ Cost' ?>
                    </span>
                </td>
                <td><?= $c['default_weight'] ?></td>
                <td>
                    <div style="display:flex; gap:0.375rem">
                        <a href="/admin/criteria/<?= $c['id'] ?>/edit" class="btn btn-ghost btn-sm">Edit</a>
                        <form method="POST" action="/admin/criteria/<?= $c['id'] ?>" onsubmit="return confirm('Hapus kriteria ini?')">
                            <?= csrf_field() ?>
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?= $this->endSection() ?>
