<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Kos</title>
</head>
<body>
    <h1>Edit Kos</h1>

    <form action="<?= base_url('pemilik/update_kos/' . $kos->id) ?>" method="POST">
        <input type="text" name="name" value="<?= $kos->name ?>" required><br>
        <textarea name="address"><?= $kos->address ?></textarea><br>
        <input type="text" name="latitude" value="<?= $kos->latitude ?>" required><br>
        <input type="text" name="longitude" value="<?= $kos->longitude ?>" required><br>
        <input type="number" name="price" value="<?= $kos->price ?>" required><br>
        <textarea name="description"><?= $kos->description ?></textarea><br>
        <select name="status">
            <option value="tersedia" <?= $kos->status == 'tersedia' ? 'selected' : '' ?>>Tersedia</option>
            <option value="penuh" <?= $kos->status == 'penuh' ? 'selected' : '' ?>>Penuh</option>
        </select><br>
        <button type="submit">Update</button>
    </form>
</body>
</html>
