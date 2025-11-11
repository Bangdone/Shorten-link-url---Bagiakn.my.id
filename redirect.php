<?php
include 'config.php';

// Ambil path dari URL
$request_uri = $_SERVER['REQUEST_URI'];
$alias = ltrim($request_uri, '/');

// Hapus query string jika ada
if (strpos($alias, '?') !== false) {
    $alias = substr($alias, 0, strpos($alias, '?'));
}

// Jika alias kosong atau file tertentu, arahkan ke homepage
if (empty($alias) || in_array($alias, ['index.php', 'create.php', 'style.css'])) {
    header('Location: index.php');
    exit;
}

// Cari alias di database
$stmt = $conn->prepare("SELECT original_url FROM short_links WHERE alias = ?");
$stmt->bind_param("s", $alias);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->bind_result($original_url);
    $stmt->fetch();
    
    // Update hit counter
    $update_stmt = $conn->prepare("UPDATE short_links SET click_count = click_count + 1 WHERE alias = ?");
    $update_stmt->bind_param("s", $alias);
    $update_stmt->execute();
    $update_stmt->close();
    
    // Redirect ke URL asli
    header('Location: ' . $original_url, true, 301);
    exit;
} else {
    // Alias tidak ditemukan
    http_response_code(404);
    echo "
    <div class='container'>
        <div class='header'>
            <h1>Link Tidak Ditemukan</h1>
        </div>
        <div class='form-container'>
            <div class='result error'>
                Link <strong>bagikan.my.id/$alias</strong> tidak ditemukan!
            </div>
            <br>
            <a href='index.php' class='btn'>Buat Link Pendek</a>
        </div>
    </div>";
}

$stmt->close();
$conn->close();
?>