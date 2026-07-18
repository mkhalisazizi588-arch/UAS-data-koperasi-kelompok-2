<?php
session_start();
require_once 'koneksi.php';
cek_login();

// Proses Hapus
if (isset($_GET['hapus'])) {
    $id_hapus = (int)$_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM anggota WHERE id_anggota = $id_hapus");
    header("Location: anggota.php?pesan=hapus_sukses");
    exit;
}

// Proses Tambah
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tambah'])) {
    $nomor_anggota = mysqli_real_escape_string($koneksi, $_POST['nomor_anggota']);
    $nama_anggota = mysqli_real_escape_string($koneksi, $_POST['nama_anggota']);
    $jenis_kelamin = mysqli_real_escape_string($koneksi, $_POST['jenis_kelamin']);
    $tempat_lahir = mysqli_real_escape_string($koneksi, $_POST['tempat_lahir']);
    $tanggal_lahir = mysqli_real_escape_string($koneksi, $_POST['tanggal_lahir']);
    $no_hp = mysqli_real_escape_string($koneksi, $_POST['no_hp']);
    $pekerjaan = mysqli_real_escape_string($koneksi, $_POST['pekerjaan']);
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $tanggal_daftar = mysqli_real_escape_string($koneksi, $_POST['tanggal_daftar']);
    
    $query_tambah = "INSERT INTO anggota (nomor_anggota, nama_anggota, jenis_kelamin, tempat_lahir, tanggal_lahir, no_hp, pekerjaan, alamat, tanggal_daftar) 
                     VALUES ('$nomor_anggota', '$nama_anggota', '$jenis_kelamin', '$tempat_lahir', '$tanggal_lahir', '$no_hp', '$pekerjaan', '$alamat', '$tanggal_daftar')";
    if (mysqli_query($koneksi, $query_tambah)) {
        header("Location: anggota.php?pesan=tambah_sukses");
        exit;
    } else {
        $error = "Gagal menambah data: " . mysqli_error($koneksi);
    }
}

// Data Anggota (Filter Pencarian)
$where = "WHERE 1=1";
$cari = '';
$status_filter = '';

if (isset($_GET['cari']) && $_GET['cari'] != '') {
    $cari = mysqli_real_escape_string($koneksi, $_GET['cari']);
    $where .= " AND (nama_anggota LIKE '%$cari%' OR nomor_anggota LIKE '%$cari%' OR no_hp LIKE '%$cari%')";
}

if (isset($_GET['status']) && $_GET['status'] != '') {
    $status_filter = mysqli_real_escape_string($koneksi, $_GET['status']);
    if ($status_filter != 'Semua Status') {
        $where .= " AND status_anggota = '" . strtolower($status_filter) . "'";
    }
}

$query_tampil = "SELECT * FROM anggota $where ORDER BY id_anggota DESC";
$result = mysqli_query($koneksi, $query_tampil);
$total_data = mysqli_num_rows($result);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Anggota | Sistem Koperasi</title>

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
                <h2>Data Anggota</h2>
                <p>Kelola data anggota koperasi secara rapi dan terstruktur.</p>
            </div>
            <div class="user-pill"><?= htmlspecialchars($_SESSION['nama_lengkap']) ?></div>
        </div>

        <div class="row g-4">
            <div class="col-lg-4">
                <div class="content-card">
                    <div class="card-header">
                        <h5>Tambah Anggota</h5>
                    </div>

                    <div class="card-body">
                        <?php if(isset($error)): ?>
                            <div class="alert alert-danger p-2 mb-3"><?= $error ?></div>
                        <?php endif; ?>
                        <?php if(isset($_GET['pesan'])): ?>
                            <div class="alert alert-success p-2 mb-3">Berhasil memperbarui data!</div>
                        <?php endif; ?>
                        <form action="" method="post">
                            <div class="mb-3">
                                <label class="form-label">Nomor Anggota</label>
                                <input type="text" name="nomor_anggota" class="form-control" placeholder="Contoh: KOP-2026-001" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nama Anggota</label>
                                <input type="text" name="nama_anggota" class="form-control" placeholder="Masukkan nama lengkap" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Jenis Kelamin</label>
                                <select name="jenis_kelamin" class="form-select" required>
                                    <option value="">Pilih jenis kelamin</option>
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Tempat Lahir</label>
                                <input type="text" name="tempat_lahir" class="form-control" placeholder="Contoh: Banda Aceh">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Tanggal Lahir</label>
                                <input type="date" name="tanggal_lahir" class="form-control">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">No. HP</label>
                                <input type="text" name="no_hp" class="form-control" placeholder="Contoh: 081234567890">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Pekerjaan</label>
                                <input type="text" name="pekerjaan" class="form-control" placeholder="Contoh: Wiraswasta">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Alamat</label>
                                <textarea name="alamat" class="form-control" rows="3" placeholder="Masukkan alamat lengkap"></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Tanggal Daftar</label>
                                <input type="date" name="tanggal_daftar" class="form-control" value="<?= date('Y-m-d') ?>" required>
                            </div>

                            <button type="submit" name="tambah" class="btn btn-primary w-100">
                                Simpan Anggota
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="content-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5>Daftar Anggota</h5>
                        <span class="badge bg-success rounded-pill"><?= $total_data ?> Anggota</span>
                    </div>

                    <div class="card-body">
                        <form action="" method="get" class="mb-3">
                            <div class="row g-3">
                                <div class="col-md-7">
                                    <input type="text" name="cari" class="form-control" placeholder="Cari nama, nomor anggota, atau no. HP" value="<?= htmlspecialchars($cari) ?>">
                                </div>
                                <div class="col-md-3">
                                    <select name="status" class="form-select">
                                        <option value="Semua Status" <?= $status_filter=='Semua Status'?'selected':'' ?>>Semua Status</option>
                                        <option value="aktif" <?= strtolower($status_filter)=='aktif'?'selected':'' ?>>Aktif</option>
                                        <option value="nonaktif" <?= strtolower($status_filter)=='nonaktif'?'selected':'' ?>>Nonaktif</option>
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
                                        <th>No. Anggota</th>
                                        <th>Nama</th>
                                        <th>JK</th>
                                        <th>No. HP</th>
                                        <th>Status</th>
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
                                        <td><?= htmlspecialchars($row['nomor_anggota']) ?></td>
                                        <td><?= htmlspecialchars($row['nama_anggota']) ?></td>
                                        <td><?= $row['jenis_kelamin'] ?></td>
                                        <td><?= htmlspecialchars($row['no_hp']) ?></td>
                                        <td><span class="badge-soft badge-<?= $row['status_anggota'] ?>"><?= ucfirst($row['status_anggota']) ?></span></td>
                                        <td>
                                            <!-- Fitur Edit disembunyikan sementara sesuai instruksi hanya fungsional minimum, tapi delete ada -->
                                            <a href="?hapus=<?= $row['id_anggota'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus data anggota ini?')">Hapus</a>
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