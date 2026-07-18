<?php
session_start();
require_once 'koneksi.php';
cek_login();

// Proses Hapus
if (isset($_GET['hapus'])) {
    $id_hapus = (int)$_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM simpanan WHERE id_simpanan = $id_hapus");
    header("Location: simpanan.php?pesan=hapus_sukses");
    exit;
}

// Proses Tambah
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tambah'])) {
    $tanggal_simpanan = mysqli_real_escape_string($koneksi, $_POST['tanggal_simpanan']);
    $id_anggota = mysqli_real_escape_string($koneksi, $_POST['id_anggota']);
    $jenis_simpanan = mysqli_real_escape_string($koneksi, $_POST['jenis_simpanan']);
    $jumlah = mysqli_real_escape_string($koneksi, $_POST['jumlah']);
    $keterangan = mysqli_real_escape_string($koneksi, $_POST['keterangan']);
    $created_by = $_SESSION['id_user'];
    
    $query_tambah = "INSERT INTO simpanan (id_anggota, tanggal_simpanan, jenis_simpanan, jumlah, keterangan, created_by) 
                     VALUES ('$id_anggota', '$tanggal_simpanan', '$jenis_simpanan', '$jumlah', '$keterangan', '$created_by')";
    if (mysqli_query($koneksi, $query_tambah)) {
        header("Location: simpanan.php?pesan=tambah_sukses");
        exit;
    } else {
        $error = "Gagal menambah simpanan: " . mysqli_error($koneksi);
    }
}

// Ambil anggota aktif untuk dropdown
$q_anggota = mysqli_query($koneksi, "SELECT id_anggota, nomor_anggota, nama_anggota FROM anggota WHERE status_anggota = 'aktif' ORDER BY nama_anggota ASC");

// Data Simpanan (Filter Pencarian)
$where = "WHERE 1=1";
$cari = '';
$jenis_filter = '';

if (isset($_GET['cari']) && $_GET['cari'] != '') {
    $cari = mysqli_real_escape_string($koneksi, $_GET['cari']);
    $where .= " AND (a.nama_anggota LIKE '%$cari%' OR a.nomor_anggota LIKE '%$cari%')";
}

if (isset($_GET['jenis']) && $_GET['jenis'] != '') {
    $jenis_filter = mysqli_real_escape_string($koneksi, $_GET['jenis']);
    if ($jenis_filter != 'Semua Jenis') {
        $where .= " AND s.jenis_simpanan = '" . strtolower($jenis_filter) . "'";
    }
}

$query_tampil = "
    SELECT s.*, a.nama_anggota 
    FROM simpanan s
    JOIN anggota a ON s.id_anggota = a.id_anggota
    $where 
    ORDER BY s.tanggal_simpanan DESC, s.id_simpanan DESC
";
$result = mysqli_query($koneksi, $query_tampil);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Simpanan | Sistem Koperasi</title>

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
                <h2>Input Simpanan</h2>
                <p>Catat simpanan pokok, wajib, dan sukarela anggota koperasi.</p>
            </div>
            <div class="user-pill"><?= htmlspecialchars($_SESSION['nama_lengkap']) ?></div>
        </div>

        <div class="row g-4">
            <div class="col-lg-4">
                <div class="content-card">
                    <div class="card-header">
                        <h5>Form Simpanan</h5>
                    </div>

                    <div class="card-body">
                        <?php if(isset($error)): ?>
                            <div class="alert alert-danger p-2 mb-3"><?= $error ?></div>
                        <?php endif; ?>
                        <?php if(isset($_GET['pesan'])): ?>
                            <div class="alert alert-success p-2 mb-3">Berhasil menyimpan transaksi!</div>
                        <?php endif; ?>
                        <form action="" method="post">
                            <div class="mb-3">
                                <label class="form-label">Tanggal Simpanan</label>
                                <input type="date" name="tanggal_simpanan" class="form-control" value="<?= date('Y-m-d') ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Pilih Anggota</label>
                                <select name="id_anggota" class="form-select" required>
                                    <option value="">Pilih anggota koperasi</option>
                                    <?php while($ang = mysqli_fetch_assoc($q_anggota)): ?>
                                    <option value="<?= $ang['id_anggota'] ?>"><?= $ang['nomor_anggota'] ?> - <?= htmlspecialchars($ang['nama_anggota']) ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Jenis Simpanan</label>
                                <select name="jenis_simpanan" class="form-select" required>
                                    <option value="">Pilih jenis simpanan</option>
                                    <option value="pokok">Simpanan Pokok</option>
                                    <option value="wajib">Simpanan Wajib</option>
                                    <option value="sukarela">Simpanan Sukarela</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Jumlah Simpanan</label>
                                <input type="number" name="jumlah" class="form-control" placeholder="Contoh: 100000" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Keterangan</label>
                                <textarea name="keterangan" class="form-control" rows="3" placeholder="Keterangan opsional"></textarea>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="reset" class="btn btn-outline-secondary w-50">
                                    Reset
                                </button>
                                <button type="submit" name="tambah" class="btn btn-primary w-50">
                                    Simpan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="content-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5>Riwayat Simpanan</h5>
                        <span class="badge bg-success rounded-pill">Transaksi Terbaru</span>
                    </div>

                    <div class="card-body">
                        <form action="" method="get" class="mb-3">
                            <div class="row g-3">
                                <div class="col-md-7">
                                    <input type="text" name="cari" class="form-control" placeholder="Cari nama anggota atau nomor anggota" value="<?= htmlspecialchars($cari) ?>">
                                </div>
                                <div class="col-md-3">
                                    <select name="jenis" class="form-select">
                                        <option value="Semua Jenis" <?= $jenis_filter=='Semua Jenis'?'selected':'' ?>>Semua Jenis</option>
                                        <option value="pokok" <?= strtolower($jenis_filter)=='pokok'?'selected':'' ?>>Pokok</option>
                                        <option value="wajib" <?= strtolower($jenis_filter)=='wajib'?'selected':'' ?>>Wajib</option>
                                        <option value="sukarela" <?= strtolower($jenis_filter)=='sukarela'?'selected':'' ?>>Sukarela</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary w-100">Cari</button>
                                </div>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Anggota</th>
                                        <th>Jenis</th>
                                        <th>Jumlah</th>
                                        <th>Keterangan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php 
                                    $no = 1;
                                    while($row = mysqli_fetch_assoc($result)): 
                                    ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= date('d M Y', strtotime($row['tanggal_simpanan'])) ?></td>
                                        <td><?= htmlspecialchars($row['nama_anggota']) ?></td>
                                        <td><span class="badge-soft badge-<?= $row['jenis_simpanan'] ?>"><?= ucfirst($row['jenis_simpanan']) ?></span></td>
                                        <td class="money">Rp <?= number_format($row['jumlah'], 0, ',', '.') ?></td>
                                        <td><?= htmlspecialchars($row['keterangan'] ?? '-') ?></td>
                                        <td>
                                            <a href="?hapus=<?= $row['id_simpanan'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus transaksi ini?')">Hapus</a>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                    <?php if(mysqli_num_rows($result) == 0): ?>
                                    <tr>
                                        <td colspan="7" class="text-center">Data tidak ditemukan.</td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </main>

</div>

</body>
</html>