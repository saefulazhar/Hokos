<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HoKos - Cari Kos dengan Mudah</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            background: url('https://source.unsplash.com/1600x900/?room,rent') no-repeat center center fixed;
            background-size: cover;
        }
        .hero {
            text-align: center;
            color: white;
            padding: 100px 20px;
            background: rgba(0, 0, 0, 0.5);
            border-radius: 15px;
        }
    </style>
</head>
<body class="d-flex justify-content-center align-items-center vh-100">
    <div class="container">
        <div class="hero">
            <h1>Selamat Datang di HoKos</h1>
            <p class="lead">Cari, temukan, dan sewa kos dengan mudah!</p>
            <button class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#roleModal">Register</button>
            <a href="login" class="btn btn-outline-light btn-lg">Login</a>
        </div>
    </div>

    <!-- Modal Pilihan Role -->
    <div class="modal fade" id="roleModal" tabindex="-1" aria-labelledby="roleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="roleModalLabel">Pilih Jenis Akun</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <p>Silakan pilih jenis akun Anda:</p>
                    <a href="<?= base_url('register_view') ?>?role=pencari" class="btn btn-outline-primary btn-lg me-2">Pencari Kos</a>
                    <a href="<?= base_url('register_view') ?>" class="btn btn-outline-success btn-lg">Pemilik Kos</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
