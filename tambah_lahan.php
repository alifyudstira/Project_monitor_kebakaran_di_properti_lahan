<?php
require_once 'koneksi.php';

$error_id_lahan = '';
$error_id_akun  = '';
$sukses         = '';

// Proses simpan jika form disubmit via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_lahan = trim($_POST['ID_lahan'] ?? '');
    $id_akun  = trim($_POST['ID_akun'] ?? '');

    // Validasi sisi server
    if (empty($id_lahan)) {
        $error_id_lahan = 'ID Lahan wajib diisi.';
    } elseif (strlen($id_lahan) > 4) {
        $error_id_lahan = 'ID Lahan maksimal 4 karakter.';
    }
    if (empty($id_akun)) {
        $error_id_akun = 'Akun pemilik wajib dipilih.';
    }

    if (empty($error_id_lahan) && empty($error_id_akun)) {
        // Prepared Statement untuk INSERT
        $stmt = $koneksi->prepare("INSERT INTO lahan (ID_lahan, ID_akun) VALUES (?, ?)");
        $stmt->bind_param("si", $id_lahan, $id_akun);

        if ($stmt->execute()) {
            $stmt->close();
            header("Location: index.php?pesan=tambah");
            exit();
        } else {
            $sukses = 'Gagal menyimpan data: ' . $stmt->error;
            $stmt->close();
        }
    }
}

// Ambil daftar akun untuk dropdown
$daftarAkun = $koneksi->query("SELECT ID_akun, nama FROM akun ORDER BY nama ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Lahan - Sistem Monitoring Lahan</title>
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
                <li class="nav-item"><a class="nav-link" href="akun.php">Data Akun</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Tambah Data Lahan</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($sukses)): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($sukses) ?></div>
                    <?php endif; ?>

                    <form id="formTambahLahan" method="POST" action="tambah_lahan.php" novalidate>
                        <!-- Field ID Lahan -->
                        <div class="mb-3">
                            <label for="ID_lahan" class="form-label fw-semibold">ID Lahan <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control <?= !empty($error_id_lahan) ? 'is-invalid' : '' ?>"
                                   id="ID_lahan"
                                   name="ID_lahan"
                                   maxlength="4"
                                   placeholder="Contoh: L001"
                                   value="<?= htmlspecialchars($_POST['ID_lahan'] ?? '') ?>">
                            <div class="invalid-feedback" id="errIdLahan">
                                <?= htmlspecialchars($error_id_lahan) ?>
                            </div>
                        </div>

                        <!-- Field ID Akun (Dropdown) -->
                        <div class="mb-3">
                            <label for="ID_akun" class="form-label fw-semibold">Pemilik (Akun) <span class="text-danger">*</span></label>
                            <select class="form-select <?= !empty($error_id_akun) ? 'is-invalid' : '' ?>"
                                    id="ID_akun"
                                    name="ID_akun">
                                <option value="">-- Pilih Pemilik --</option>
                                <?php if ($daftarAkun && $daftarAkun->num_rows > 0): ?>
                                    <?php while ($akun = $daftarAkun->fetch_assoc()): ?>
                                        <option value="<?= $akun['ID_akun'] ?>"
                                            <?= (isset($_POST['ID_akun']) && $_POST['ID_akun'] == $akun['ID_akun']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($akun['ID_akun'] . ' - ' . $akun['nama']) ?>
                                        </option>
                                    <?php endwhile; ?>
                                <?php endif; ?>
                            </select>
                            <div class="invalid-feedback" id="errIdAkun">
                                <?= htmlspecialchars($error_id_akun) ?>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success">Simpan</button>
                            <a href="index.php" class="btn btn-secondary">Batal</a>
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
    var form = document.querySelector('#formTambahLahan');

    form.addEventListener('submit', function(e) {
        var valid = true;

        var inputIdLahan = document.querySelector('#ID_lahan');
        var inputIdAkun  = document.querySelector('#ID_akun');
        var errIdLahan   = document.querySelector('#errIdLahan');
        var errIdAkun    = document.querySelector('#errIdAkun');

        // Reset state
        inputIdLahan.classList.remove('is-invalid');
        inputIdAkun.classList.remove('is-invalid');
        errIdLahan.textContent = '';
        errIdAkun.textContent  = '';

        // Validasi ID Lahan
        if (inputIdLahan.value.trim() === '') {
            e.preventDefault();
            inputIdLahan.classList.add('is-invalid');
            errIdLahan.textContent = 'ID Lahan wajib diisi.';
            valid = false;
        }

        // Validasi dropdown pemilik
        if (inputIdAkun.value === '') {
            e.preventDefault();
            inputIdAkun.classList.add('is-invalid');
            errIdAkun.textContent = 'Akun pemilik wajib dipilih.';
            valid = false;
        }
    });

    // Bersihkan error saat input berubah (real-time)
    document.querySelector('#ID_lahan').addEventListener('input', function() {
        this.classList.remove('is-invalid');
        document.querySelector('#errIdLahan').textContent = '';
    });

    document.querySelector('#ID_akun').addEventListener('change', function() {
        this.classList.remove('is-invalid');
        document.querySelector('#errIdAkun').textContent = '';
    });
</script>
</body>
</html>
