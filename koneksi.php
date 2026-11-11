<?php
// Koneksi database menggunakan MySQLi Object-Oriented
$host     = "localhost";
$user     = "root";
$password = "";
$database = "sistem_monitoring_lahan";

$koneksi = new mysqli($host, $user, $password, $database);

// Cek koneksi
if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

// Set charset
$koneksi->set_charset("utf8mb4");
?>
