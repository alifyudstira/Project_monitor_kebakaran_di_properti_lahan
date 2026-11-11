<?php
require_once 'koneksi.php';

$error_id_akun = '';
$id_lahan_get  = trim($_GET['id'] ?? '');

// Validasi parameter GET
if (empty($id_lahan_get)) {
    header("Location: index.php");
    exit();
}

// Ambil data lahan saat ini menggunakan Prepared Statement
$stmt = $koneksi->prepare("SELECT ID_lahan, ID_akun FROM lahan WHERE ID_lahan = ?");
$stmt->bind_param("s", $id_lahan_get);
$stmt->execute();
$result = $stmt->get_result();
$data   = $result->fetch_assoc();
$stmt->close();

if (!$data) {
    header("Location: index.php");
    exit();
}

// Proses update jika form disubmit via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_akun_baru = trim($_POST['ID_akun'] ?? '');

    if (empty($id_akun_baru)) {
        $error_id_akun = 'Akun pemilik wajib dipilih.';
    } else {
        // Prepared Statement untuk UPDATE
        $stmtUpdate = $koneksi->prepare("UPDATE lahan SET ID_akun = ? WHERE ID_lahan = ?");
        $stmtUpdate->bind_param("is", $id_akun_baru, $id_lahan_get);

        if ($stmtUpdate->execute()) {
            $stmtUpdate->close();
            header("Location: index.php?pesan=edit");
            exit();
        } else {
            $error_id_akun = 'Gagal memperbarui data: ' . $stmtUpdate->error;
            $stmtUpdate->close();
        }
    }
}

// Ambil daftar akun untuk dropdown
$daftarAkun = $koneksi->query("SELECT ID_akun, nama FROM akun ORDER BY nama ASC");

// Nilai ID_akun yang ditampilkan di form
$current_id_akun = $_POST['ID_akun'] ?? $data['ID_akun'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Lahan - Sistem Monitoring Lahan</title>
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
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">Edit Data Lahan</h5>
                </div>
                <div class="card-body">
                    <form id="formEditLahan" method="POST" action="edit_lahan.php?id=<?= urlencode($id_lahan_get) ?>" novalidate>
                        <!-- ID Lahan (readonly, tidak bisa diubah) -->
                        <div class="mb-3">
                            <label for="ID_lahan" class="form-label fw-semibold">ID Lahan</label>
                            <input type="text"
                                   class="form-control"
                                   id="ID_lahan"
                                   value="<?= htmlspecialchars($data['ID_lahan']) ?>"
                                   readonly>
                            <div class="form-text">ID Lahan tidak dapat diubah.</div>
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
                                            <?= ($current_id_akun == $akun['ID_akun']) ? 'selected' : '' ?>>
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
                            <button type="submit" class="btn btn-warning fw-semibold">Perbarui</button>
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
    var form = document.querySelector('#formEditLahan');

    form.addEventListener('submit', function(e) {
        var inputIdAkun = document.querySelector('#ID_akun');
        var errIdAkun   = document.querySelector('#errIdAkun');

        // Reset state
        inputIdAkun.classList.remove('is-invalid');
        errIdAkun.textContent = '';

        // Validasi dropdown pemilik
        if (inputIdAkun.value === '') {
            e.preventDefault();
            inputIdAkun.classList.add('is-invalid');
            errIdAkun.textContent = 'Akun pemilik wajib dipilih.';
        }
    });

    // Bersihkan error saat dropdown berubah (real-time)
    document.querySelector('#ID_akun').addEventListener('change', function() {
        this.classList.remove('is-invalid');
        document.querySelector('#errIdAkun').textContent = '';
    });
</script>
</body>
</html>
