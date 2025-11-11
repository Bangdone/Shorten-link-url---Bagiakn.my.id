<?php
session_start(); // TAMBAHIN INI
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $original_url = filter_var($_POST['original_url'], FILTER_SANITIZE_URL);
    $alias = $_POST['alias'];
    
    // TAMBAHIN: Ambil user_id dari session jika user login
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : NULL;
    
    // Validasi URL
    if (!filter_var($original_url, FILTER_VALIDATE_URL)) {
        echo "
        <!DOCTYPE html>
        <html lang='id'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Error - bagikan.my.id</title>
            <link rel='stylesheet' href='style.css'>
            <link rel=\"icon\" href=\"data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🚀</text></svg>\">
        </head>
        <body>
            <div class='container'>
                <div class='header' style='background: #e74c3c;'>
                    <h1>❌ Error</h1>
                    <p>URL tidak valid!</p>
                </div>
                <div class='form-container'>
                    <div class='result error'>
                        <strong>URL yang kamu masukkan tidak valid!</strong><br>
                        Pastikan URL dimulai dengan http:// atau https://
                    </div>
                    <br>
                    <a href='index.php' class='btn' style='background: #e74c3c;'>🔙 Kembali ke Home</a>
                </div>
            </div>
        </body>
        </html>";
        exit;
    }
    
    // Generate random alias jika kosong
    if (empty($alias)) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
        $alias = '';
        for ($i = 0; $i < 6; $i++) {
            $alias .= $characters[rand(0, strlen($characters) - 1)];
        }
    } else {
        // Validasi alias
        if (!preg_match('/^[A-Za-z0-9_-]+$/', $alias)) {
            echo "
            <!DOCTYPE html>
            <html lang='id'>
            <head>
                <meta charset='UTF-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <title>Error - bagikan.my.id</title>
                <link rel='stylesheet' href='style.css'>
                <link rel=\"icon\" href=\"data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🚀</text></svg>\">
            </head>
            <body>
                <div class='container'>
                    <div class='header' style='background: #e67e22;'>
                        <h1>⚠️ Format Error</h1>
                        <p>Alias tidak valid!</p>
                    </div>
                    <div class='form-container'>
                        <div class='result error'>
                            <strong>Alias '$alias' mengandung karakter tidak valid!</strong><br>
                            Hanya boleh menggunakan: huruf (A-Z, a-z), angka (0-9), underscore (_), dash (-)
                        </div>
                        <br>
                        <a href='index.php' class='btn' style='background: #e67e22;'>🔙 Kembali ke Home</a>
                    </div>
                </div>
            </body>
            </html>";
            exit;
        }
    }
    
    // Cek apakah alias sudah ada
    $check_stmt = $conn->prepare("SELECT id FROM short_links WHERE alias = ?");
    $check_stmt->bind_param("s", $alias);
    $check_stmt->execute();
    $check_stmt->store_result();
    
    if ($check_stmt->num_rows > 0) {
        echo "
        <!DOCTYPE html>
        <html lang='id'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Error - bagikan.my.id</title>
            <link rel='stylesheet' href='style.css'>
            <link rel=\"icon\" href=\"data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🚀</text></svg>\">
        </head>
        <body>
            <div class='container'>
                <div class='header' style='background: #e67e22;'>
                    <h1>🚫 Alias Sudah Dipakai</h1>
                    <p>Coba alias yang lain!</p>
                </div>
                <div class='form-container'>
                    <div class='result error'>
                        <strong>Alias '<span style='color: #e74c3c;'>$alias</span>' sudah digunakan!</strong><br>
                        Silakan pilih alias yang berbeda.
                    </div>
                    <br>
                    <a href='index.php' class='btn' style='background: #e67e22;'>🔙 Kembali ke Home</a>
                </div>
            </div>
        </body>
        </html>";
        exit;
    }
    $check_stmt->close();
    
    // UPDATE: Simpan ke database dengan user_id
    $stmt = $conn->prepare("INSERT INTO short_links (alias, original_url, user_id) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $alias, $original_url, $user_id);
    
    if ($stmt->execute()) {
        $short_url = "https://bagikan.my.id/" . $alias;
        $original_display = (strlen($original_url) > 50) ? substr($original_url, 0, 50) . "..." : $original_url;
        
        // TAMBAHIN: Info apakah user login atau tidak
        $user_info = $user_id ? " (Akun: " . $_SESSION['username'] . ")" : " (Guest)";
        
        echo "
        <!DOCTYPE html>
        <html lang='id'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Berhasil! - bagikan.my.id</title>
            <link rel='stylesheet' href='style.css'>
            <link rel=\"icon\" href=\"data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🚀</text></svg>\">
        </head>
        <body>
            <div class='container'>
                <div class='header' style='background: #27ae60;'>
                    <h1>🎉 Selamat Link kamu Berhasil Dibuat!</h1>
                    <p>Share link pendekmu sekarang!$user_info</p>
                </div>
                
                <div class='form-container'>
                    <!-- Info Link -->
                    <div class='result' style='text-align: center; background: #d5f4e6; border-left: 4px solid #27ae60;'>
                        <div style='font-size: 1.1em; margin-bottom: 10px; color: #2c3e50;'>
                            <strong>🔗 URL Pendek:</strong>
                        </div>
                        <div style='font-size: 1.4em; font-weight: bold; margin: 15px 0;'>
                            <a href='$short_url' target='_blank' style='color: #27ae60; text-decoration: none;'>$short_url</a>
                        </div>
                        <button onclick=\"copyToClipboard('$short_url', this)\" class='btn' style='background: #27ae60; margin: 10px 0;' id='copyBtn'>
                            📋 Copy Link
                        </button>
                    </div>

                    <!-- Info Asli -->
                    <div style='background: #f8f9fa; padding: 15px; border-radius: 8px; margin: 20px 0;'>
                        <strong>🌐 URL Asli:</strong><br>
                        <span style='color: #7f8c8d; font-size: 0.9em;'>$original_url</span>
                    </div>

                    <!-- Statistik -->
                    <div style='display: flex; justify-content: space-around; text-align: center; margin: 25px 0;'>
                        <div style='padding: 15px;'>
                            <div style='font-size: 2em; color: #27ae60;'>📊</div>
                            <div style='font-weight: bold;'>Click Count</div>
                            <div style='font-size: 1.5em; color: #2c3e50;'>0</div>
                        </div>
                        <div style='padding: 15px;'>
                            <div style='font-size: 2em; color: #3498db;'>🆔</div>
                            <div style='font-weight: bold;'>Alias</div>
                            <div style='font-size: 1.5em; color: #2c3e50;'>$alias</div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div style='display: flex; gap: 10px; margin-top: 20px;'>
                        <a href='$short_url' target='_blank' class='btn' style='background: #3498db; flex: 1; text-align: center;'>
                            🔗 Test Link
                        </a>
                        <a href='index.php' class='btn' style='background: #9b59b6; flex: 1; text-align: center;'>
                            🚀 Buat Link Lagi
                        </a>
                        " . ($user_id ? "<a href='user_dashboard.php' class='btn' style='background: #27ae60; flex: 1; text-align: center;'>📊 Dashboard Saya</a>" : "") . "
                    </div>

                    <!-- Share Options -->
                    <div style='margin-top: 25px; padding: 15px; background: #f8f9fa; border-radius: 8px;'>
                        <strong>📤 Share ke:</strong>
                        <div style='display: flex; gap: 10px; margin-top: 10px;'>
                            <a href='https://wa.me/?text=$short_url' target='_blank' style='flex: 1; padding: 10px; background: #25D366; color: white; text-align: center; border-radius: 5px; text-decoration: none;'>
                                WhatsApp
                            </a>
                            <a href='https://t.me/share/url?url=$short_url' target='_blank' style='flex: 1; padding: 10px; background: #0088cc; color: white; text-align: center; border-radius: 5px; text-decoration: none;'>
                                Telegram
                            </a>
                        </div>
                    </div>
                    
                    " . (!$user_id ? "
                    <!-- Info untuk guest -->
                    <div style='margin-top: 25px; padding: 15px; background: #fff3cd; border-radius: 8px; border-left: 4px solid #ffc107;'>
                        <strong>💡 Tips:</strong> 
                        <a href='register.php' style='color: #007bff;'>Daftar akun gratis</a> untuk menyimpan dan mengelola semua link Anda di satu tempat!
                    </div>
                    " : "") . "
                </div>
            </div>

            <script>
            function copyToClipboard(text, button) {
                // Method modern dengan Clipboard API
                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard.writeText(text).then(function() {
                        showCopySuccess(button);
                    }).catch(function(err) {
                        // Fallback untuk browser lama
                        copyFallback(text, button);
                    });
                } else {
                    // Fallback method
                    copyFallback(text, button);
                }
            }

            function copyFallback(text, button) {
                // Method fallback menggunakan textarea
                const textArea = document.createElement('textarea');
                textArea.value = text;
                textArea.style.position = 'fixed';
                textArea.style.left = '-999999px';
                textArea.style.top = '-999999px';
                document.body.appendChild(textArea);
                textArea.focus();
                textArea.select();
                
                try {
                    const successful = document.execCommand('copy');
                    if (successful) {
                        showCopySuccess(button);
                    } else {
                        alert('Gagal copy link. Silakan copy manual: ' + text);
                    }
                } catch (err) {
                    alert('Gagal copy link. Silakan copy manual: ' + text);
                }
                document.body.removeChild(textArea);
            }

            function showCopySuccess(button) {
                if (button) {
                    const originalText = button.innerHTML;
                    const originalBackground = button.style.background;
                    
                    button.innerHTML = '✅ Copied!';
                    button.style.background = '#2ecc71';
                    
                    setTimeout(function() {
                        button.innerHTML = originalText;
                        button.style.background = originalBackground;
                    }, 2000);
                }
            }

            // Auto select text untuk memudahkan copy manual
            document.addEventListener('DOMContentLoaded', function() {
                const linkElement = document.querySelector('a[href=\"$short_url\"]');
                if (linkElement) {
                    linkElement.addEventListener('click', function(e) {
                        // Biarkan CTRL+Click untuk buka tab baru
                        if (!e.ctrlKey && !e.metaKey) {
                            e.preventDefault();
                            copyToClipboard('$short_url', document.getElementById('copyBtn'));
                        }
                    });
                }
            });
            </script>
        </body>
        </html>";
    } else {
        echo "
        <!DOCTYPE html>
        <html lang='id'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Error - bagikan.my.id</title>
            <link rel='stylesheet' href='style.css'>
            <link rel=\"icon\" href=\"data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🚀</text></svg>\">
        </head>
        <body>
            <div class='container'>
                <div class='header' style='background: #e74c3c;'>
                    <h1>❌ Database Error</h1>
                    <p>Gagal menyimpan link!</p>
                </div>
                <div class='form-container'>
                    <div class='result error'>
                        <strong>Terjadi kesalahan sistem:</strong><br>
                        " . $conn->error . "
                    </div>
                    <br>
                    <a href='index.php' class='btn' style='background: #e74c3c;'>🔙 Kembali ke Home</a>
                </div>
            </div>
        </body>
        </html>";
    }
    
    $stmt->close();
    $conn->close();
}
?>