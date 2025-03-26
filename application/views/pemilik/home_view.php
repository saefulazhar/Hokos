<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Dashboard Pemilik</title>

    <link href="<?= base_url("assets/Login/")?>vendor/fontawesome-free/css/all.min.css" rel="stylesheet" />
    <link href="<?= base_url("assets/Login/")?>css/sb-admin-2.min.css" rel="stylesheet" />

    <!-- Bootstrap core JavaScript (Muat jQuery dulu) -->
    <script src="<?= base_url("assets/Login/")?>vendor/jquery/jquery.min.js"></script>
    <script src="<?= base_url("assets/Login/")?>vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
</head>
<script>
    function initMap() {
        // Inisialisasi peta
        var map = L.map("map").setView([-6.200000, 106.816666], 13); // Jakarta default
        
        // Tambahkan tile layer dari OpenStreetMap
        L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
            attribution: "&copy; OpenStreetMap contributors",
        }).addTo(map);

        var marker; // Deklarasi marker di luar event agar bisa diupdate

        // Event klik pada peta untuk menambahkan/memindahkan marker
        map.on("click", function (e) {
            var lat = e.latlng.lat;
            var lng = e.latlng.lng;

            // Jika marker sudah ada, pindahkan ke lokasi baru
            if (marker) {
                marker.setLatLng([lat, lng]);
            } else {
                // Jika marker belum ada, buat yang baru
                marker = L.marker([lat, lng], { draggable: true }).addTo(map);
            }

            // Update input latitude & longitude
            document.getElementById("latitude").value = lat;
            document.getElementById("longitude").value = lng;

            // Event saat marker digeser manual
            marker.on("dragend", function (event) {
                var position = marker.getLatLng();
                document.getElementById("latitude").value = position.lat;
                document.getElementById("longitude").value = position.lng;
            });
        });
    }

    // Jalankan fungsi initMap saat halaman selesai dimuat
    document.addEventListener("DOMContentLoaded", function() {
        initMap();
    });
</script>

<body id="page-top">
    <div id="wrapper">
        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="#">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-home"></i>
                </div>
                <div class="sidebar-brand-text mx-3">Dashboard</div>
            </a>
            <hr class="sidebar-divider my-0" />
            <li class="nav-item active">
                <a class="nav-link" href="#" id="dataKosanLink">
                    <i class="fas fa-building"></i>
                    <span>Data Kosan</span>
                </a>
            </li>
        </ul>

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content" class="p-4">
                <h1 class="h3 mb-4 text-gray-800">Data Kosan</h1>

                <!-- Tombol Tambah Kosan -->
                <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#modalTambah">
                    Tambah Kosan
                </button>

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>Alamat</th>
                            <th>Harga</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($kos_list as $k) : ?>
                            <tr>
                                <td><?= $k->id ?></td>
                                <td><?= $k->name ?></td>
                                <td><?= $k->address ?></td>
                                <td><?= $k->price ?></td>
                                <td>
                                    <button class="btn btn-warning btnEdit" data-id="<?= $k->id ?>" 
                                            data-name="<?= $k->name ?>" data-address="<?= $k->address ?>" 
                                            data-price="<?= $k->price ?>" data-toggle="modal" 
                                            data-target="#modalEdit">
                                        Edit
                                    </button>
                                    <button class="btn btn-danger btnHapus" data-id="<?= $k->id ?>">
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Kosan -->
    <div class="modal fade" id="modalTambah" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Kosan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="<?= base_url('pemilik/tambah_kos') ?>" method="POST">
                    <div class="modal-body">
                        <input type="text" name="name" class="form-control" placeholder="Nama Kosan" required>
                        <input type="text" name="address" class="form-control mt-2" placeholder="Alamat" required>
                        <input type="number" name="price" class="form-control mt-2" placeholder="Harga" required>
                        <div id="map" style="height: 300px; width: 100%;"></div>
                        <input type="text" id="latitude" name="latitude" class="form-control mt-2" placeholder="Latitude" required>
                        <input type="text" id="longitude" name="longitude" class="form-control mt-2" placeholder="Longitude" required>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Kosan -->
    <div class="modal fade" id="modalEdit" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Kosan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="<?= base_url('pemilik/update_kos') ?>" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="id" id="edit_id">
                        <input type="text" name="name" id="edit_name" class="form-control" placeholder="Nama Kosan" required>
                        <input type="text" name="address" id="edit_address" class="form-control mt-2" placeholder="Alamat" required>
                        <input type="number" name="price" id="edit_price" class="form-control mt-2" placeholder="Harga" required>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-warning">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Event untuk tombol Edit
            $(".btnEdit").click(function() {
                let id = $(this).data("id");
                let name = $(this).data("name");
                let address = $(this).data("address");
                let price = $(this).data("price");

                $("#edit_id").val(id);
                $("#edit_name").val(name);
                $("#edit_address").val(address);
                $("#edit_price").val(price);
            });

            // Event untuk tombol Hapus
            $(".btnHapus").click(function() {
                let id = $(this).data("id");
                if (confirm("Yakin ingin menghapus?")) {
                    window.location.href = "<?= base_url('pemilik/hapus_kos/') ?>" + id;
                }
            });
        });
    </script>
     </script>

<!-- Bootstrap core JavaScript-->
<script src="<?= base_url("assets/Login/")?>vendor/jquery/jquery.min.js"></script>
<script src="<?= base_url("assets/Login/")?>vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- Core plugin JavaScript-->
<script src="<?= base_url("assets/Login/")?>vendor/jquery-easing/jquery.easing.min.js"></script>

<!-- Custom scripts for all pages-->
<script src="<?= base_url("assets/Login/")?>js/sb-admin-2.min.js"></script>
</body>
</body>
</html>
