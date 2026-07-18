<?php
session_start();
require_once 'koneksi.php';
cek_login();

// Filter date & status
$tgl_mulai = isset($_GET['tgl_mulai']) ? mysqli_real_escape_string($koneksi, $_GET['tgl_mulai']) : date('Y-m-01');
$tgl_selesai = isset($_GET['tgl_selesai']) ? mysqli_real_escape_string($koneksi, $_GET['tgl_selesai']) : date('Y-m-t');
$status = isset($_GET['status']) ? mysqli_real_escape_string($koneksi, $_GET['status']) : 'Semua Status';

$where_simpanan = "s.tanggal_simpanan BETWEEN '$tgl_mulai' AND '$tgl_selesai'";
$where_anggota = "1=1";

if ($status != 'Semua Status') {
    $status_val = strtolower($status);
    $where_anggota .= " AND a.status_anggota = '$status_val'";
}

$query_laporan = "
    SELECT 
        a.id_anggota,
        a.nomor_anggota,
        a.nama_anggota,
        a.status_anggota,
        SUM(CASE WHEN s.jenis_simpanan = 'pokok' THEN s.jumlah ELSE 0 END) AS pokok,
        SUM(CASE WHEN s.jenis_simpanan = 'wajib' THEN s.jumlah ELSE 0 END) AS wajib,
        SUM(CASE WHEN s.jenis_simpanan = 'sukarela' THEN s.jumlah ELSE 0 END) AS sukarela,
        SUM(s.jumlah) AS total_simpanan
    FROM anggota a
    LEFT JOIN simpanan s ON a.id_anggota = s.id_anggota AND $where_simpanan
    WHERE $where_anggota
    GROUP BY a.id_anggota
    ORDER BY a.nama_anggota ASC
";
$result_laporan = mysqli_query($koneksi, $query_laporan);

$total_anggota_lap = 0;
$total_pokok_lap = 0;
$total_wajib_lap = 0;
$total_semua_lap = 0;

$data_laporan = [];
while($row = mysqli_fetch_assoc($result_laporan)) {
    $data_laporan[] = $row;
    $total_anggota_lap++;
    $total_pokok_lap += $row['pokok'];
    $total_wajib_lap += $row['wajib'];
    $total_semua_lap += $row['total_simpanan'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Simpanan | Sistem Koperasi</title>

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
                <h2>Laporan Simpanan</h2>
                <p>Rekap total simpanan pokok, wajib, sukarela, dan total saldo anggota.</p>
            </div>
            <div class="user-pill"><?= htmlspecialchars($_SESSION['nama_lengkap']) ?></div>
        </div>

        <div class="content-card mb-4">
            <div class="card-header">
                <h5>Filter Laporan</h5>
            </div>

            <div class="card-body">
                <form action="" method="get">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Tanggal Mulai</label>
                            <input type="date" name="tgl_mulai" class="form-control" value="<?= $tgl_mulai ?>">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Tanggal Selesai</label>
                            <input type="date" name="tgl_selesai" class="form-control" value="<?= $tgl_selesai ?>">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Status Anggota</label>
                            <select name="status" class="form-select">
                                <option value="Semua Status" <?= $status=='Semua Status'?'selected':'' ?>>Semua Status</option>
                                <option value="aktif" <?= strtolower($status)=='aktif'?'selected':'' ?>>Aktif</option>
                                <option value="nonaktif" <?= strtolower($status)=='nonaktif'?'selected':'' ?>>Nonaktif</option>
                            </select>
                        </div>

                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                Tampilkan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon bg-soft-primary">👥</div>
                    <h3><?= number_format($total_anggota_lap, 0, ',', '.') ?></h3>
                    <p>Total Anggota</p>
                </div>
            </div>

            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon bg-soft-blue">💳</div>
                    <h3>Rp <?= number_format($total_pokok_lap, 0, ',', '.') ?></h3>
                    <p>Total Pokok</p>
                </div>
            </div>

            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon bg-soft-yellow">📌</div>
                    <h3>Rp <?= number_format($total_wajib_lap, 0, ',', '.') ?></h3>
                    <p>Total Wajib</p>
                </div>
            </div>

            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon bg-soft-red">💰</div>
                    <h3>Rp <?= number_format($total_semua_lap, 0, ',', '.') ?></h3>
                    <p>Total Saldo</p>
                </div>
            </div>
        </div>

        <div class="content-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>Tabel Laporan Simpanan</h5>
                <a href="#" class="btn btn-sm btn-outline-primary">Cetak Laporan</a>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>No. Anggota</th>
                                <th>Nama Anggota</th>
                                <th>Simpanan Pokok</th>
                                <th>Simpanan Wajib</th>
                                <th>Simpanan Sukarela</th>
                                <th>Total Simpanan</th>
                                <th>Status</th>
                            </tr>
                        </thead>

                                <tbody>
                                    <?php 
                                    $no = 1;
                                    $g_pokok = 0; $g_wajib = 0; $g_sukarela = 0; $g_total = 0;
                                    foreach($data_laporan as $row): 
                                        $g_pokok += $row['pokok'];
                                        $g_wajib += $row['wajib'];
                                        $g_sukarela += $row['sukarela'];
                                        $g_total += $row['total_simpanan'];
                                    ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= htmlspecialchars($row['nomor_anggota']) ?></td>
                                        <td><?= htmlspecialchars($row['nama_anggota']) ?></td>
                                        <td class="money">Rp <?= number_format($row['pokok'], 0, ',', '.') ?></td>
                                        <td class="money">Rp <?= number_format($row['wajib'], 0, ',', '.') ?></td>
                                        <td class="money">Rp <?= number_format($row['sukarela'], 0, ',', '.') ?></td>
                                        <td class="money">Rp <?= number_format($row['total_simpanan'], 0, ',', '.') ?></td>
                                        <td><span class="badge-soft badge-<?= strtolower($row['status_anggota']) ?>"><?= ucfirst($row['status_anggota']) ?></span></td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php if(empty($data_laporan)): ?>
                                    <tr>
                                        <td colspan="8" class="text-center">Data tidak ditemukan.</td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>

                        <tfoot>
                            <tr>
                                <th colspan="3">Total Keseluruhan</th>
                                <th class="money">Rp <?= number_format($g_pokok ?? 0, 0, ',', '.') ?></th>
                                <th class="money">Rp <?= number_format($g_wajib ?? 0, 0, ',', '.') ?></th>
                                <th class="money">Rp <?= number_format($g_sukarela ?? 0, 0, ',', '.') ?></th>
                                <th class="money">Rp <?= number_format($g_total ?? 0, 0, ',', '.') ?></th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

    </main>

</div>

</body>
</html>