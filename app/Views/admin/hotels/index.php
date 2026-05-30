<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <div>
        <h1>Hotel</h1>
        <p>Kelola data hotel dan koordinat lokasi</p>
    </div>
    <a href="/admin/hotels/create" class="btn btn-primary">+ Tambah Hotel</a>
</div>

<div class="card">
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Nama Hotel</th>
                <th>Harga/Malam</th>
                <th>Rating</th>
                <th>Diskon</th>
                <th>Fasilitas</th>
                <th>Avg. Jarak POI</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($hotels)): ?>
            <tr>
                <td colspan="8" style="text-align:center; color: var(--muted); padding: 2rem;">
                    Belum ada hotel. <a href="/admin/hotels/create">Tambah sekarang</a>
                </td>
            </tr>
            <?php else: ?>
            <?php foreach ($hotels as $i => $h): ?>
            <tr>
                <td style="color:var(--muted)"><?= $i + 1 ?></td>
                <td><strong><?= esc($h['name']) ?></strong></td>
                <td>Rp <?= number_format($h['price'], 0, ',', '.') ?></td>
                <td>⭐ <?= $h['rating'] ?></td>
                <td>
                    <?php if ($h['discount'] > 0): ?>
                        <span class="badge" style="background:#fef9c3;color:#854d0e"><?= $h['discount'] ?>% OFF</span>
                    <?php else: ?>
                        <span style="color:var(--muted)">—</span>
                    <?php endif; ?>
                </td>
                <td><?= $h['facilities_count'] ?> fasilitas</td>
                <td>
                    <?= isset($h['avg_distance']) ? number_format($h['avg_distance'], 2) . ' km' : '<span style="color:var(--muted)">—</span>' ?>
                </td>
                <td>
                    <div style="display:flex; gap:0.375rem">
                        <a href="/admin/hotels/<?= $h['id'] ?>/edit" class="btn btn-ghost btn-sm">Edit</a>
                        <form method="POST" action="/admin/hotels/<?= $h['id'] ?>" onsubmit="return confirm('Hapus hotel ini?')">
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
