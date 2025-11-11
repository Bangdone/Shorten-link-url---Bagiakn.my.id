<?php
session_start();
include 'config.php';

// Cek login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Ambil statistik user
$user_links = $conn->query("SELECT COUNT(*) as total FROM short_links WHERE user_id = $user_id")->fetch_assoc()['total'];
$user_clicks = $conn->query("SELECT SUM(click_count) as total FROM short_links WHERE user_id = $user_id")->fetch_assoc()['total'];
$user_clicks = $user_clicks ? $user_clicks : 0;

// Ambil data links user
$links_result = $conn->query("SELECT * FROM short_links WHERE user_id = $user_id ORDER BY created_at DESC LIMIT 10");

// Handle delete link
if (isset($_GET['delete'])) {
    $delete_id = (int)$_GET['delete'];
    // Pastikan link milik user yang login
    $conn->query("DELETE FROM short_links WHERE id = $delete_id AND user_id = $user_id");
    header('Location: user_dashboard.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - bagikan.my.id</title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🚀</text></svg>">
    <style>
        .user-header {
            background: #27ae60;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        .stat-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            border-left: 4px solid #3498db;
        }
        .stat-number {
            font-size: 2em;
            font-weight: bold;
            color: #2c3e50;
        }
        .stat-label {
            color: #7f8c8d;
            font-size: 0.9em;
        }
        .links-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .links-table th, .links-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }
        .links-table th {
            background: #34495e;
            color: white;
        }
        .links-table tr:hover {
            background: #f5f5f5;
        }
        .delete-btn {
            background: #e74c3c;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 3px;
            cursor: pointer;
            text-decoration: none;
            font-size: 0.8em;
        }
        .delete-btn:hover {
            background: #c0392b;
        }
        .user-actions {
            text-align: center;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="user-header">
            <h1>👋 Halo, <?php echo htmlspecialchars($username); ?>!</h1>
            <p>Dashboard User - bagikan.my.id</p>
        </div>

        <div class="user-actions">
            <a href="index.php" class="btn" style="background: #3498db; margin-right: 10px;">🏠 Buat Link Baru</a>
            <a href="user_dashboard.php" class="btn" style="background: #27ae60; margin-right: 10px;">🔄 Refresh</a>
            <a href="user_logout.php" class="btn" style="background: #e74c3c;">🚪 Logout</a>
        </div>

        <!-- Statistik User -->
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-number"><?php echo $user_links; ?></div>
                <div class="stat-label">Total Links Anda</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $user_clicks; ?></div>
                <div class="stat-label">Total Klik</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $user_links > 0 ? round($user_clicks / $user_links, 2) : 0; ?></div>
                <div class="stat-label">Rata-rata Klik/Link</div>
            </div>
        </div>

        <!-- Tabel Links User -->
        <h3 style="text-align: center; margin: 30px 0 20px 0;">📋 Link Anda</h3>
        
        <?php if ($links_result->num_rows > 0): ?>
            <table class="links-table">
                <thead>
                    <tr>
                        <th>Short URL</th>
                        <th>Original URL</th>
                        <th>Klik</th>
                        <th>Dibuat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($link = $links_result->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <a href="https://bagikan.my.id/<?php echo $link['alias']; ?>" target="_blank">
                                bagikan.my.id/<?php echo $link['alias']; ?>
                            </a>
                        </td>
                        <td style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                            <?php echo $link['original_url']; ?>
                        </td>
                        <td><?php echo $link['click_count']; ?></td>
                        <td><?php echo date('d M Y', strtotime($link['created_at'])); ?></td>
                        <td>
                            <a href="user_dashboard.php?delete=<?php echo $link['id']; ?>" 
                               class="delete-btn" 
                               onclick="return confirm('Yakin hapus link ini?')">
                                🗑️ Hapus
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div style="text-align: center; padding: 40px;">
                <p>Anda belum membuat link. <a href="index.php">Buat link pertama Anda!</a></p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>