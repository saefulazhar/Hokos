<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>SB Admin 2 - Login</title>
    
    <link href="<?= base_url("assets/Login/")?>vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="<?= base_url("assets/Login/")?>css/sb-admin-2.min.css" rel="stylesheet">
</head>

<body class="bg-gradient-primary">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-12 col-md-9">
                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <div class="row">
                            <div class="col-lg-6 d-none d-lg-block bg-login-image"></div>
                            <div class="col-lg-6">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">Welcome Back!</h1>
                                    </div>
                                    <form class="user" id="loginForm">
                                        <div class="form-group">
                                            <input type="email" class="form-control form-control-user" id="email" placeholder="Enter Email Address...">
                                        </div>
                                        <div class="form-group">
                                            <input type="password" class="form-control form-control-user" id="password" placeholder="Password">
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-user btn-block">Login</button>
                                    </form>
                                    <hr>
                                    <div class="text-center">
                                        <a class="small" href="forgot-password.html">Forgot Password?</a>
                                    </div>
                                    <div class="text-center">
                                        <a class="small" href="#" data-toggle="modal" data-target="#roleModal">Create an Account!</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal Pilihan Role -->
    <div class="modal fade" id="roleModal" tabindex="-1" role="dialog" aria-labelledby="roleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="roleModalLabel">Pilih Role</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <p>Silakan pilih role Anda:</p>
                    <button class="btn btn-primary btn-user btn-block" onclick="redirectToRegister('pencari')">Daftar sebagai Pencari</button>
                    <button class="btn btn-secondary btn-user btn-block" onclick="redirectToRegister('pemilik')">Daftar sebagai Pemilik</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', async function(event) {
    event.preventDefault(); // Mencegah reload halaman

    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;

    const response = await fetch("<?= base_url('auth/login') ?>", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ email, password })
    });

    const data = await response.json();

    if (response.ok) {
        alert("Login berhasil!");
        localStorage.setItem("token", data.token); // Simpan token JWT
        window.location.href = "<?= base_url('home_view') ?>";
    } else {
        alert(data.message);
    }
});
    function redirectToRegister(role) {
        window.location.href = "<?= base_url('register_view?role=') ?>" + role;
    }
    </script>
    
    <script src="<?= base_url("assets/Login/")?>vendor/jquery/jquery.min.js"></script>
    <script src="<?= base_url("assets/Login/")?>vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?= base_url("assets/Login/")?>vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="<?= base_url("assets/Login/")?>js/sb-admin-2.min.js"></script>
</body>
</html>
