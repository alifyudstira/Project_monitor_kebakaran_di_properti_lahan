<?php
require_once 'koneksi.php';

$id_lahan = trim($_GET['id'] ?? '');

if (empty($id_lahan)) {
    header("Location: index.php");
    exit();
}

// Prepared Statement untuk DELETE
$stmt = $koneksi->prepare("DELETE FROM lahan WHERE ID_lahan = ?");
$stmt->bind_param("s", $id_lahan);
$stmt->execute();
$stmt->close();

header("Location: index.php?pesan=hapus");
exit();
?>
