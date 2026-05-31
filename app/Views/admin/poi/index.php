<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <div>
        <h1>Point of Interest</h1>
        <p>Kelola lokasi-lokasi tujuan wisata / POI</p>
    </div>
    <a href="/admin/poi/create" class="btn btn-primary">+ Tambah POI</a>
</div>

<div class="card">
    <table>
            <tr>
                <th>#</th>
                <th>Nama POI</th>
                <th>Latitude</th>
                <th>Longitude</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($pois)): ?>
            <tr>
                <td colspan="5" style="text-align:center; color:var(--muted); padding:2rem">
                    Belum ada POI. <a href="/admin/poi/create">Tambah sekarang</a>
                </td>
            </tr>
            <?php else: ?>
            <?php foreach ($pois as $i => $poi): ?>
            <tr>
                <td style="color:var(--muted)"><?= $i + 1 ?></td>
                <td><strong><?= esc($poi['nama_poi']) ?></strong></td>
                <td><code style="font-size:0.8rem"><?= $poi['latitude'] ?></code></td>
                <td><code style="font-size:0.8rem"><?= $poi['longitude'] ?></code></td>
                <td>
                    <div style="display:flex; gap:0.375rem">
                        <a href="/admin/poi/<?= $poi['id'] ?>/edit" class="btn btn-ghost btn-sm">Edit</a>
                        <form method="POST" action="/admin/poi/<?= $poi['id'] ?>" onsubmit="return confirm('Hapus POI ini? Semua data jarak ke hotel juga akan dihapus.')">
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
