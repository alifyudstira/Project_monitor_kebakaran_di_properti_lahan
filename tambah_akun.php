<?php
require_once 'koneksi.php';

$error_nama  = '';
$error_email = '';
$sukses      = '';

// Proses simpan jika form disubmit via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama  = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');

    // Validasi sisi server
    if (empty($nama)) {
        $error_nama = 'Nama wajib diisi.';
    }
    if (empty($email)) {
        $error_email = 'Email wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_email = 'Format email tidak valid.';
    }

    if (empty($error_nama) && empty($error_email)) {
        // Prepared Statement untuk INSERT
        $stmt = $koneksi->prepare("INSERT INTO akun (nama, email) VALUES (?, ?)");
        $stmt->bind_param("ss", $nama, $email);

        if ($stmt->execute()) {
            $stmt->close();
            header("Location: akun.php?pesan=tambah");
            exit();
        } else {
            $sukses = 'Gagal menyimpan data: ' . $stmt->error;
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Akun - Sistem Monitoring Lahan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f0f4f8; }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-success">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">🌿 Sistem Monitoring Lahan</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="index.php">Data Lahan</a></li>
                <li class="nav-item"><a class="nav-link active" href="akun.php">Data Akun</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Tambah Data Akun</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($sukses)): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($sukses) ?></div>
                    <?php endif; ?>

                    <form id="formTambahAkun" method="POST" action="tambah_akun.php" novalidate>
                        <!-- Field Nama -->
                        <div class="mb-3">
                            <label for="nama" class="form-label fw-semibold">Nama <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control <?= !empty($error_nama) ? 'is-invalid' : '' ?>"
                                   id="nama"
                                   name="nama"
                                   maxlength="50"
                                   placeholder="Masukkan nama lengkap"
                                   value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>">
                            <div class="invalid-feedback" id="errNama">
                                <?= htmlspecialchars($error_nama) ?>
                            </div>
                        </div>

                        <!-- Field Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                            <input type="email"
                                   class="form-control <?= !empty($error_email) ? 'is-invalid' : '' ?>"
                                   id="email"
                                   name="email"
                                   maxlength="50"
                                   placeholder="contoh@email.com"
                                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                            <div class="invalid-feedback" id="errEmail">
                                <?= htmlspecialchars($error_email) ?>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                            <a href="akun.php" class="btn btn-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Validasi form sisi klien dengan JavaScript Vanilla
    var form = document.querySelector('#formTambahAkun');

    form.addEventListener('submit', function(e) {
        var valid = true;

        var inputNama  = document.querySelector('#nama');
        var inputEmail = document.querySelector('#email');
        var errNama    = document.querySelector('#errNama');
        var errEmail   = document.querySelector('#errEmail');

        // Reset state
        inputNama.classList.remove('is-invalid');
        inputEmail.classList.remove('is-invalid');
        errNama.textContent  = '';
        errEmail.textContent = '';

        // Validasi Nama
        if (inputNama.value.trim() === '') {
            e.preventDefault();
            inputNama.classList.add('is-invalid');
            errNama.textContent = 'Nama wajib diisi.';
            valid = false;
        }

        // Validasi Email
        if (inputEmail.value.trim() === '') {
            e.preventDefault();
            inputEmail.classList.add('is-invalid');
            errEmail.textContent = 'Email wajib diisi.';
            valid = false;
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(inputEmail.value.trim())) {
            e.preventDefault();
            inputEmail.classList.add('is-invalid');
            errEmail.textContent = 'Format email tidak valid.';
            valid = false;
        }
    });

    // Bersihkan error saat input berubah (real-time)
    document.querySelector('#nama').addEventListener('input', function() {
        this.classList.remove('is-invalid');
        document.querySelector('#errNama').textContent = '';
    });

    document.querySelector('#email').addEventListener('input', function() {
        this.classList.remove('is-invalid');
        document.querySelector('#errEmail').textContent = '';
    });
</script>
</body>
</html>
