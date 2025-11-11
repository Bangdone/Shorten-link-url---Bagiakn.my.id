<?php
$host = "localhost"; // Biasanya ini di Hostinger
$username = "u849873076_donzzz"; // Ganti dengan username database bang
$password = "J1QEjp?m/"; // Ganti dengan password database bang
$database = "u849873076_donzzz"; // Ganti dengan nama database bang

// Koneksi database
$conn = new mysqli($host, $username, $password, $database);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>