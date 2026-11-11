<?php
require_once 'koneksi.php';

// Ambil semua data akun
$sql    = "SELECT ID_akun, nama, email FROM akun ORDER BY ID_akun ASC";
$result = $koneksi->query($sql);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Akun - Sistem Monitoring Lahan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f0f4f8; }
        .table thead {
            background-color: #0d6efd;
            color: #fff;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-success">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">🌿 Sistem Monitoring Lahan</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Data Lahan</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="akun.php">Data Akun</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Daftar Data Akun</h5>
                    <a href="tambah_akun.php" class="btn btn-light btn-sm fw-semibold">+ Tambah Akun</a>
                </div>
                <div class="card-body">
                    <?php if (isset($_GET['pesan'])): ?>
                        <?php if ($_GET['pesan'] === 'tambah'): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                Data akun berhasil ditambahkan.
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php elseif ($_GET['pesan'] === 'edit'): ?>
                            <div class="alert alert-info alert-dismissible fade show" role="alert">
                                Data akun berhasil diperbarui.
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php elseif ($_GET['pesan'] === 'hapus'): ?>
                            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                Data akun berhasil dihapus.
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php elseif ($_GET['pesan'] === 'gagal_hapus'): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                Gagal menghapus akun. Akun masih memiliki data lahan yang berelasi.
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th>ID Akun</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result && $result->num_rows > 0): ?>
                                    <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td class="text-center"><?= $no++ ?></td>
                                            <td><?= htmlspecialchars($row['ID_akun']) ?></td>
                                            <td><?= htmlspecialchars($row['nama']) ?></td>
                                            <td><?= htmlspecialchars($row['email']) ?></td>
                                            <td class="text-center">
                                                <a href="edit_akun.php?id=<?= urlencode($row['ID_akun']) ?>" class="btn btn-warning btn-sm">Edit</a>
                                                <button class="btn btn-danger btn-sm btn-hapus-akun"
                                                        data-id="<?= htmlspecialchars($row['ID_akun']) ?>"
                                                        data-nama="<?= htmlspecialchars($row['nama']) ?>">
                                                    Hapus
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">Belum ada data akun.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="modalHapusAkun" tabindex="-1" aria-labelledby="modalHapusAkunLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalHapusAkunLabel">Konfirmasi Hapus Akun</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin menghapus akun <strong id="namaAkunHapus"></strong>?
                <br><small class="text-danger">Perhatian: menghapus akun akan gagal jika masih memiliki data lahan terkait.</small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <a href="#" id="linkHapusAkun" class="btn btn-danger">Ya, Hapus</a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Event listener untuk tombol hapus akun
    document.querySelectorAll('.btn-hapus-akun').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var idAkun   = this.getAttribute('data-id');
            var namaAkun = this.getAttribute('data-nama');
            document.querySelector('#namaAkunHapus').textContent = namaAkun;
            document.querySelector('#linkHapusAkun').setAttribute('href', 'hapus_akun.php?id=' + encodeURIComponent(idAkun));
            var modal = new bootstrap.Modal(document.querySelector('#modalHapusAkun'));
            modal.show();
        });
    });
</script>
</body>
</html>
