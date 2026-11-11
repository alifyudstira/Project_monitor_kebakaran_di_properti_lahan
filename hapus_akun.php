<?php
require_once 'koneksi.php';

$id_akun = intval($_GET['id'] ?? 0);

if ($id_akun <= 0) {
    header("Location: akun.php");
    exit();
}

// Prepared Statement untuk DELETE
// Akan gagal secara otomatis jika masih ada lahan yang berelasi (FOREIGN KEY constraint)
$stmt = $koneksi->prepare("DELETE FROM akun WHERE ID_akun = ?");
$stmt->bind_param("i", $id_akun);

if ($stmt->execute()) {
    $stmt->close();
    header("Location: akun.php?pesan=hapus");
} else {
    $stmt->close();
    // Gagal karena ada data lahan yang masih berelasi
    header("Location: akun.php?pesan=gagal_hapus");
}
exit();
?>
