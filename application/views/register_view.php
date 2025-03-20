<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>SB Admin 2 - Register</title>

    <!-- Custom fonts for this template-->
    <link href="<?= base_url("assets/Login/")?>vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="<?= base_url("assets/Login/")?>css/sb-admin-2.min.css" rel="stylesheet">

</head>

<body class="bg-gradient-primary">

    <div class="container">

        <div class="card o-hidden border-0 shadow-lg my-5">
            <div class="card-body p-0">
                <!-- Nested Row within Card Body -->
                <div class="row">
                    <div class="col-lg-5 d-none d-lg-block bg-register-image"></div>
                    <div class="col-lg-7">
                        <div class="p-5">
                            <div class="text-center">
                                <h1 class="h4 text-gray-900 mb-4">Create an Account!</h1>
                            </div>
                            <form class="user" id="registerForm">
    <input type="hidden" id="role" value="<?= htmlspecialchars($role) ?>"> <!-- Tambahkan role -->

    <div class="form-group">
        <input type="text" class="form-control form-control-user" id="name" placeholder="Name">
        <small id="nameError" class="text-danger"></small>
    </div>

    <div class="form-group">
        <input type="email" class="form-control form-control-user" id="email" placeholder="Email Address">
        <small id="emailError" class="text-danger"></small>
    </div>

    <div class="form-group row">
        <div class="col-sm-6 mb-3 mb-sm-0">
            <input type="password" class="form-control form-control-user" id="password1" placeholder="Password">
            <small id="passwordError" class="text-danger"></small>
        </div>
        <div class="col-sm-6">
            <input type="password" class="form-control form-control-user" id="password2" placeholder="Repeat Password">
        </div>
    </div>

    <button type="submit" class="btn btn-primary btn-user btn-block">
        Register Account
    </button>
</form>

                            <hr>
                            <div class="text-center">
                                <a class="small" href="<?= base_url("login_view")?>">Already have an account? Login!</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="<?= base_url("assets/Login/")?>vendor/jquery/jquery.min.js"></script>
    <script src="<?= base_url("assets/Login/")?>vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="<?= base_url("assets/Login/")?>vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="<?= base_url("assets/Login/")?>js/sb-admin-2.min.js"></script>

</body>

<script>
document.getElementById('registerForm').addEventListener('submit', async function(event) {
    event.preventDefault();

    const name = document.getElementById('name').value;
    const email = document.getElementById('email').value;
    const password = document.getElementById('password1').value;
    const confirm_password = document.getElementById('password2').value;
    const role = document.getElementById('role').value;

    // Reset error messages
    document.getElementById('nameError').textContent = "";
    document.getElementById('emailError').textContent = "";
    document.getElementById('passwordError').textContent = "";

    // Validasi sederhana
    if (!name) {
        document.getElementById('nameError').textContent = "Nama wajib diisi!";
        return;
    }
    if (!email) {
        document.getElementById('emailError').textContent = "Email wajib diisi!";
        return;
    }
    if (password.length < 6) {
        document.getElementById('passwordError').textContent = "Password minimal 6 karakter!";
        return;
    }
    if (password !== confirm_password) {
        document.getElementById('passwordError').textContent = "Password tidak sama!";
        return;
    }

    // Kirim ke API
    const response = await fetch("<?= base_url('auth/register') ?>", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ name, email, password, role }) // Kirim role juga
    });

    const data = await response.json();
    
    if (response.ok) {
        alert("Registrasi berhasil! Silakan login.");
        window.location.href = "<?= base_url('login_view') ?>";
    } else {
        document.getElementById('emailError').textContent = data.message || "Registrasi gagal!";
    }
});
</script>


</html>