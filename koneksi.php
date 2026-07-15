<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_koperasi_anggota";

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Set zona waktu
date_default_timezone_set('Asia/Jakarta');

// Fungsi untuk mengecek login (bisa di-include di tiap halaman)
function cek_login() {
    if (!isset($_SESSION['id_user'])) {
        header("Location: index.php");
        exit;
    }
}
?>
