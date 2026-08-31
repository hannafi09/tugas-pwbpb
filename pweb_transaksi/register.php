<?php
session_start();
require_once 'config/koneksi.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username     = trim($_POST['username']);
    $nama_lengkap = trim($_POST['nama_lengkap']);
    $password     = $_POST['password'];

    if (!empty($username) && !empty($nama_lengkap) && !empty($password)) {
        // Cek apakah username sudah dipakai
        $stmt_check = $pdo->prepare("SELECT id_user FROM users WHERE username = ?");
        $stmt_check->execute([$username]);
        
        if ($stmt_check->rowCount() > 0) {
            $error = "Username sudah digunakan! Pilih username lain.";
        } else {
            // Hash password untuk keamanan
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert user baru dengan role default 'user'
            $stmt = $pdo->prepare("INSERT INTO users (username, password, nama_lengkap, role) VALUES (?, ?, ?, 'user')");
            if ($stmt->execute([$username, $hashed_password, $nama_lengkap])) {
                $success = "Pendaftaran berhasil! Silakan login.";
            } else {
                $error = "Gagal mendaftar, coba lagi.";
            }
        }
    } else {
        $error = "Semua kolom wajib diisi!";
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Registrasi User Baru</title></head>
<body>
    <h2>Form Tambah User / Registrasi</h2>
    <?php if ($error): ?><p style="color:red;"><?= $error ?></p><?php endif; ?>
    <?php if ($success): ?><p style="color:green;"><?= $success ?></p><?php endif; ?>

    <form method="POST">
        <label>Nama Lengkap:</label><br>
        <input type="text" name="nama_lengkap" required><br><br>
        
        <label>Username:</label><br>
        <input type="text" name="username" required><br><br>
        
        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>
        
        <button type="submit">Daftar Akun</button>
    </form>
    <br>
    <p>Sudah punya akun? <a href="login.php">Login di sini</a></p>
</body>
</html>