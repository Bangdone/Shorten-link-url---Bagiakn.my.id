<?php 
session_start();
include 'config.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>bagikan.my.id - URL Shortener</title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🚀</text></svg>">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Selamat Datang</h1>
            <h2>bagikan.my.id</h2>
            <p>URL Shortener dengan Subdirektori Custom!</p>
            
            <!-- TAMBAHAN: MENU LOGIN/REGISTER -->
            <div class="auth-menu" style="margin-top: 20px;">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="user_dashboard.php" class="btn" style="background: #27ae60; margin: 5px; padding: 10px 20px; display: inline-block;">📊 Dashboard Saya</a>
                    <a href="user_logout.php" class="btn" style="background: #e74c3c; margin: 5px; padding: 10px 20px; display: inline-block;">🚪 Logout</a>
                <?php else: ?>
                    <a href="login.php" class="btn" style="background: #3498db; margin: 5px; padding: 10px 20px; display: inline-block;">Login</a>
                    <a href="register.php" class="btn" style="background: #9b59b6; margin: 5px; padding: 10px 20px; display: inline-block;">Register</a>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="form-container">
            <form action="create.php" method="POST">
                <div class="input-group">
                    <label for="original_url">URL Panjang:</label>
                    <input type="url" id="original_url" name="original_url" 
                           placeholder="https://website-anda.com/url-panjang.html" 
                           required>
                </div>
                
                <div class="input-group">
                    <label for="alias">Custom Link (opsional):</label>
                    <input type="text" id="alias" name="alias" 
                           placeholder="produkkeren" 
                           pattern="[A-Za-z0-9_-]+" 
                           title="Hanya huruf, angka, underscore (_), dan dash (-)">
                    <small>bagikan.my.id/<strong id="alias-preview">alias</strong></small>
                </div>
                
                <button type="submit" class="btn">Mulai Short Link!</button>
            </form>
        </div>

        <!-- FOOTER SANGAT SEDERHANA -->
        <div class="simple-footer">
            <p>&copy; 2025 bagikan.my.id - Manage by donzzz.com made from Indonesia</p>
        </div>
    </div>

    <script>
        // Preview alias
        const aliasInput = document.getElementById('alias');
        const aliasPreview = document.getElementById('alias-preview');
        
        aliasInput.addEventListener('input', function() {
            aliasPreview.textContent = this.value || 'alias';
        });
    </script>
</body>
</html>