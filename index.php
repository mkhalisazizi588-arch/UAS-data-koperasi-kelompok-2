<?php
session_start();
require_once 'koneksi.php';

// Jika sudah login, redirect ke dashboard
if (isset($_SESSION['id_user'])) {
    header("Location: dashboard.php");
    exit;
}

$error = '';
// Proses login
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($koneksi, $query);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        
        // Cek jika password masih dummy
        if ($user['password_hash'] === 'password_hash_diisi_backend') {
            // Update password default ('admin123') jika masih dummy
            if ($password === 'admin123' || $password === $username) {
                $new_hash = password_hash($password, PASSWORD_DEFAULT);
                mysqli_query($koneksi, "UPDATE users SET password_hash = '$new_hash' WHERE id_user = {$user['id_user']}");
                $user['password_hash'] = $new_hash;
            }
        }
        
        if (password_verify($password, $user['password_hash'])) {
            if ($user['status'] == 'aktif') {
                $_SESSION['id_user'] = $user['id_user'];
                $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
                $_SESSION['role'] = $user['role'];
                header("Location: dashboard.php");
                exit;
            } else {
                $error = 'Akun Anda nonaktif. Hubungi administrator.';
            }
        } else {
            $error = 'Username atau password salah.';
        }
    } else {
        $error = 'Username atau password salah.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Sistem Koperasi</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="login-page">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <div class="card login-card">
                    <div class="row g-0">
                        <div class="col-md-6">
                            <div class="login-info">
                                <div class="login-icon">K</div>
                                <h1 class="fw-bold">Sistem Koperasi</h1>
                                <p class="mt-3">
                                    Aplikasi pengelolaan data anggota koperasi, input simpanan,
                                    pencarian anggota, dan laporan simpanan sederhana.
                                </p>

                                <div class="mt-5">
 
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="login-form">
                                <h3 class="fw-bold mb-2">Login Admin</h3>
                                <p class="text-muted mb-4">Masuk untuk mengelola data koperasi.</p>

                                <?php if($error): ?>
                                    <div class="alert alert-danger p-2 mb-3"><?= $error ?></div>
                                <?php endif; ?>
                                <form action="" method="post">
                                    <div class="mb-3">
                                        <label class="form-label">Username</label>
                                        <input type="text" name="username" class="form-control" placeholder="Masukkan username" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Password</label>
                                        <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="ingat">
                                            <label class="form-check-label" for="ingat">
                                                Ingat saya
                                            </label>
                                        </div>
                                        <a href="#" class="fw-semibold text-success">Lupa password?</a>
                                    </div>

                                    <button type="submit" name="login" class="btn btn-primary w-100">
                                        Masuk Aplikasi
                                    </button>
                                </form>

 
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

</body>
</html>