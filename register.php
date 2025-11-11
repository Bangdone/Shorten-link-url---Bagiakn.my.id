<?php
session_start();
include 'config.php';

// Jika sudah login, redirect ke dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: user_dashboard.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validasi
    if (empty($username) || empty($email) || empty($password)) {
        $error = 'Semua field harus diisi!';
    } elseif ($password !== $confirm_password) {
        $error = 'Password tidak cocok!';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter!';
    } else {
        // Cek username dan email sudah ada atau belum
        $check_user = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $check_user->bind_param("ss", $username, $email);
        $check_user->execute();
        $check_user->store_result();

        if ($check_user->num_rows > 0) {
            $error = 'Username atau email sudah digunakan!';
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert user baru
            $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $email, $hashed_password);

            if ($stmt->execute()) {
                $success = 'Registrasi berhasil! Silakan login.';
                // Auto redirect ke login setelah 2 detik
                header('refresh:2;url=login.php');
            } else {
                $error = 'Terjadi kesalahan. Silakan coba lagi!';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - bagikan.my.id</title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🚀</text></svg>">
    <style>
        .auth-container {
            max-width: 400px;
            margin: 50px auto;
            padding: 40px;
        }
        .auth-container h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #2c3e50;
        }
        .auth-links {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container auth-container">
        <h2>📝 Daftar Akun Baru</h2>
        
        <?php if ($error): ?>
            <div class="error" style="margin-bottom: 20px;">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="result" style="margin-bottom: 20px; background: #d5f4e6; border-left: 4px solid #27ae60;">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="input-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
            </div>
            
            <div class="input-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
            </div>
            
            <div class="input-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <div class="input-group">
                <label for="confirm_password">Konfirmasi Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            
            <button type="submit" class="btn">Daftar</button>
        </form>
        
        <div class="auth-links">
            <p>Sudah punya akun? <a href="login.php" style="color: #667eea;">Login di sini</a></p>
            <p><a href="index.php" style="color: #667eea;">← Kembali ke Home</a></p>
        </div>
    </div>
</body>
</html>