<?php
session_start();
require_once 'koneksi.php';
cek_login();

// Ambil statistik
$q_anggota = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM anggota WHERE status_anggota = 'aktif'");
$total_anggota = mysqli_fetch_assoc($q_anggota)['total'] ?? 0;

$q_anggota_nonaktif = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM anggota WHERE status_anggota = 'nonaktif'");
$total_anggota_nonaktif = mysqli_fetch_assoc($q_anggota_nonaktif)['total'] ?? 0;

$q_trans_bulan_ini = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM simpanan WHERE MONTH(tanggal_simpanan) = MONTH(CURRENT_DATE()) AND YEAR(tanggal_simpanan) = YEAR(CURRENT_DATE())");
$transaksi_bulan_ini = mysqli_fetch_assoc($q_trans_bulan_ini)['total'] ?? 0;

$q_simpanan = mysqli_query($koneksi, "
    SELECT 
        SUM(CASE WHEN jenis_simpanan = 'pokok' THEN jumlah ELSE 0 END) as total_pokok,
        SUM(CASE WHEN jenis_simpanan = 'wajib' THEN jumlah ELSE 0 END) as total_wajib,
        SUM(jumlah) as total_semua
    FROM simpanan
");
$simpanan_stats = mysqli_fetch_assoc($q_simpanan);
$total_pokok = $simpanan_stats['total_pokok'] ?? 0;
$total_wajib = $simpanan_stats['total_wajib'] ?? 0;
$total_semua = $simpanan_stats['total_semua'] ?? 0;

$q_recent = mysqli_query($koneksi, "
    SELECT s.*, a.nama_anggota 
    FROM simpanan s
    JOIN anggota a ON s.id_anggota = a.id_anggota
    ORDER BY s.tanggal_simpanan DESC, s.id_simpanan DESC
    LIMIT 5
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Sistem Koperasi</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="app-layout">

    <aside class="sidebar">
        <div class="logo">
            <div class="logo-box">K</div>
            <div>
                <h4>KoperasiApp</h4>
                <small>Panel Administrator</small>
            </div>
        </div>

        <div class="menu-label">Menu Utama</div>
        <ul class="nav-menu">
            <li><a href="dashboard.php" class="active">🏠 Dashboard</a></li>
            <li><a href="anggota.php">👥 Data Anggota</a></li>
            <li><a href="simpanan.php">💰 Input Simpanan</a></li>
            <li><a href="laporan.php">📊 Laporan Simpanan</a></li>
        </ul>

        <div class="menu-label">Akun</div>
        <ul class="nav-menu">
            <li><a href="logout.php">🚪 Logout</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="topbar">
            <div>
                <h2>Dashboard Koperasi</h2>
                <p>Ringkasan data anggota dan simpanan koperasi.</p>
            </div>
            <div class="user-pill"><?= htmlspecialchars($_SESSION['nama_lengkap']) ?></div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon bg-soft-primary">👥</div>
                    <h3><?= number_format($total_anggota, 0, ',', '.') ?></h3>
                    <p>Total Anggota</p>
                </div>
            </div>

            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon bg-soft-blue">💳</div>
                    <h3>Rp <?= number_format($total_pokok, 0, ',', '.') ?></h3>
                    <p>Simpanan Pokok</p>
                </div>
            </div>

            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon bg-soft-yellow">📌</div>
                    <h3>Rp <?= number_format($total_wajib, 0, ',', '.') ?></h3>
                    <p>Simpanan Wajib</p>
                </div>
            </div>

            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon bg-soft-red">💰</div>
                    <h3>Rp <?= number_format($total_semua, 0, ',', '.') ?></h3>
                    <p>Total Simpanan</p>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="content-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5>Transaksi Simpanan Terbaru</h5>
                        <a href="simpanan.html" class="btn btn-sm btn-primary">Input Simpanan</a>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Anggota</th>
                                        <th>Tanggal</th>
                                        <th>Jenis</th>
                                        <th>Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $no = 1;
                                    while($row = mysqli_fetch_assoc($q_recent)): 
                                        $inisial = strtoupper(substr($row['nama_anggota'], 0, 2));
                                    ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><span class="avatar"><?= $inisial ?></span><?= htmlspecialchars($row['nama_anggota']) ?></td>
                                        <td><?= date('d M Y', strtotime($row['tanggal_simpanan'])) ?></td>
                                        <td><span class="badge-soft badge-<?= $row['jenis_simpanan'] ?>"><?= ucfirst($row['jenis_simpanan']) ?></span></td>
                                        <td class="money">Rp <?= number_format($row['jumlah'], 0, ',', '.') ?></td>
                                    </tr>
                                    <?php endwhile; ?>
                                    <?php if(mysqli_num_rows($q_recent) == 0): ?>
                                    <tr>
                                        <td colspan="5" class="text-center">Belum ada transaksi</td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="content-card">
                    <div class="card-header">
                        <h5>Ringkasan Koperasi</h5>
                    </div>

                    <div class="card-body">
                        <div class="summary-box mb-3">
                            <div class="d-flex justify-content-between">
                                <span>Anggota Aktif</span>
                                <strong><?= number_format($total_anggota, 0, ',', '.') ?></strong>
                            </div>
                        </div>

                        <div class="summary-box mb-3">
                            <div class="d-flex justify-content-between">
                                <span>Anggota Nonaktif</span>
                                <strong><?= number_format($total_anggota_nonaktif, 0, ',', '.') ?></strong>
                            </div>
                        </div>

                        <div class="summary-box mb-3">
                            <div class="d-flex justify-content-between">
                                <span>Transaksi Bulan Ini</span>
                                <strong><?= number_format($transaksi_bulan_ini, 0, ',', '.') ?></strong>
                            </div>
                        </div>

                        <div class="summary-box">
                            <div class="d-flex justify-content-between">
                                <span>Total Saldo Simpanan</span>
                                <strong>Rp <?= number_format($total_semua, 0, ',', '.') ?></strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </main>

</div>

</body>
</html>