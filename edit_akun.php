<?php
require_once 'koneksi.php';

$error_nama  = '';
$error_email = '';
$id_akun_get = intval($_GET['id'] ?? 0);

// Validasi parameter GET
if ($id_akun_get <= 0) {
    header("Location: akun.php");
    exit();
}

// Ambil data akun saat ini menggunakan Prepared Statement
$stmt = $koneksi->prepare("SELECT ID_akun, nama, email FROM akun WHERE ID_akun = ?");
$stmt->bind_param("i", $id_akun_get);
$stmt->execute();
$result = $stmt->get_result();
$data   = $result->fetch_assoc();
$stmt->close();

if (!$data) {
    header("Location: akun.php");
    exit();
}

// Proses update jika form disubmit via POST
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
        // Prepared Statement untuk UPDATE
        $stmtUpdate = $koneksi->prepare("UPDATE akun SET nama = ?, email = ? WHERE ID_akun = ?");
        $stmtUpdate->bind_param("ssi", $nama, $email, $id_akun_get);

        if ($stmtUpdate->execute()) {
            $stmtUpdate->close();
            header("Location: akun.php?pesan=edit");
            exit();
        } else {
            $error_nama = 'Gagal memperbarui data: ' . $stmtUpdate->error;
            $stmtUpdate->close();
        }
    }
}

// Nilai yang ditampilkan di form (prioritaskan POST jika ada error)
$current_nama  = $_POST['nama']  ?? $data['nama'];
$current_email = $_POST['email'] ?? $data['email'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Akun - Sistem Monitoring Lahan</title>
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
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">Edit Data Akun</h5>
                </div>
                <div class="card-body">
                    <form id="formEditAkun" method="POST" action="edit_akun.php?id=<?= urlencode($id_akun_get) ?>" novalidate>
                        <!-- ID Akun (readonly) -->
                        <div class="mb-3">
                            <label for="ID_akun" class="form-label fw-semibold">ID Akun</label>
                            <input type="text"
                                   class="form-control"
                                   id="ID_akun"
                                   value="<?= htmlspecialchars($data['ID_akun']) ?>"
                                   readonly>
                            <div class="form-text">ID Akun tidak dapat diubah.</div>
                        </div>

                        <!-- Field Nama -->
                        <div class="mb-3">
                            <label for="nama" class="form-label fw-semibold">Nama <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control <?= !empty($error_nama) ? 'is-invalid' : '' ?>"
                                   id="nama"
                                   name="nama"
                                   maxlength="50"
                                   placeholder="Masukkan nama lengkap"
                                   value="<?= htmlspecialchars($current_nama) ?>">
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
                                   value="<?= htmlspecialchars($current_email) ?>">
                            <div class="invalid-feedback" id="errEmail">
                                <?= htmlspecialchars($error_email) ?>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-warning fw-semibold">Perbarui</button>
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
    var form = document.querySelector('#formEditAkun');

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
