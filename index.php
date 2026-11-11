<?php
require_once 'koneksi.php';

// Ambil data lahan dengan INNER JOIN ke tabel akun
$sql    = "SELECT l.ID_lahan, a.ID_akun, a.nama, a.email
           FROM lahan l
           INNER JOIN akun a ON l.ID_akun = a.ID_akun
           ORDER BY l.ID_lahan ASC";
$result = $koneksi->query($sql);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Monitoring Lahan</title>
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f4f8;
        }
        .table thead {
            background-color: #198754;
            color: #fff;
        }
        .brand-title {
            font-weight: 700;
            letter-spacing: 1px;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-success">
    <div class="container">
        <a class="navbar-brand brand-title" href="index.php">🌿 Sistem Monitoring Lahan</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link active" href="index.php">Data Lahan</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="akun.php">Data Akun</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Konten Utama -->
<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Daftar Data Lahan</h5>
                    <a href="tambah_lahan.php" class="btn btn-light btn-sm fw-semibold">+ Tambah Lahan</a>
                </div>
                <div class="card-body">
                    <?php if (isset($_GET['pesan'])): ?>
                        <?php if ($_GET['pesan'] === 'tambah'): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                Data lahan berhasil ditambahkan.
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php elseif ($_GET['pesan'] === 'edit'): ?>
                            <div class="alert alert-info alert-dismissible fade show" role="alert">
                                Data lahan berhasil diperbarui.
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php elseif ($_GET['pesan'] === 'hapus'): ?>
                            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                Data lahan berhasil dihapus.
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th>ID Lahan</th>
                                    <th>ID Akun</th>
                                    <th>Nama Pemilik</th>
                                    <th>Email</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result && $result->num_rows > 0): ?>
                                    <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td class="text-center"><?= $no++ ?></td>
                                            <td><?= htmlspecialchars($row['ID_lahan']) ?></td>
                                            <td><?= htmlspecialchars($row['ID_akun']) ?></td>
                                            <td><?= htmlspecialchars($row['nama']) ?></td>
                                            <td><?= htmlspecialchars($row['email']) ?></td>
                                            <td class="text-center">
                                                <a href="edit_lahan.php?id=<?= urlencode($row['ID_lahan']) ?>" class="btn btn-warning btn-sm">Edit</a>
                                                <button class="btn btn-danger btn-sm btn-hapus"
                                                        data-id="<?= htmlspecialchars($row['ID_lahan']) ?>">
                                                    Hapus
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">Belum ada data lahan.</td>
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
<div class="modal fade" id="modalHapus" tabindex="-1" aria-labelledby="modalHapusLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalHapusLabel">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin menghapus lahan dengan ID <strong id="idLahanHapus"></strong>?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <a href="#" id="linkHapus" class="btn btn-danger">Ya, Hapus</a>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap 5 JS CDN -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Event listener untuk tombol hapus (konfirmasi modal)
    document.querySelectorAll('.btn-hapus').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var idLahan = this.getAttribute('data-id');
            document.querySelector('#idLahanHapus').textContent = idLahan;
            document.querySelector('#linkHapus').setAttribute('href', 'hapus_lahan.php?id=' + encodeURIComponent(idLahan));
            var modal = new bootstrap.Modal(document.querySelector('#modalHapus'));
            modal.show();
        });
    });
</script>
</body>
</html>
