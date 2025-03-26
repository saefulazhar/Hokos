<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pemilik</title>
</head>
<body>
    <h1>Dashboard Pemilik</h1>

    <h2>Tambah Kos</h2>
    <form action="<?= base_url('pemilik/tambah_kos') ?>" method="POST">
        <input type="text" name="name" placeholder="Nama Kos" required><br>
        <textarea name="address" placeholder="Alamat Kos" required></textarea><br>
        <input type="text" name="latitude" placeholder="Latitude" required><br>
        <input type="text" name="longitude" placeholder="Longitude" required><br>
        <input type="number" name="price" placeholder="Harga" required><br>
        <textarea name="description" placeholder="Deskripsi"></textarea><br>
        <button type="submit">Tambah Kos</button>
    </form>

    <h2>Daftar Kos Anda</h2>
    <table border="1">
        <tr>
            <th>Nama</th>
            <th>Alamat</th>
            <th>Harga</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
        <?php foreach ($kos_list as $kos) : ?>
            <tr>
                <td><?= $kos->name ?></td>
                <td><?= $kos->address ?></td>
                <td><?= $kos->price ?></td>
                <td><?= $kos->status ?></td>
                <td>
                    <a href="<?= base_url('pemilik/edit_kos/' . $kos->id) ?>">Edit</a>
                    <a href="<?= base_url('pemilik/hapus_kos/' . $kos->id) ?>" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
