<?php
session_start();
require_once 'config/koneksi.php';

$pesan_sukses = '';
$pesan_error  = '';

if (isset($_POST['register'])) {
    $username     = trim($_POST['username']);
    $nama_lengkap = trim($_POST['nama_lengkap']);
    $password     = $_POST['password'];
    $role         = $_POST['role'];

    if (!empty($username) && !empty($password) && !empty($nama_lengkap) && !empty($role)) {
        
        $stmt_cek = $pdo->prepare("SELECT id_user FROM users WHERE username = ?");
        $stmt_cek->execute([$username]);

        if ($stmt_cek->rowCount() > 0) {
            $pesan_error = "Username '$username' sudah digunakan! Silakan pilih username lain.";
        } else {
            
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            try {
                $stmt_insert = $pdo->prepare("INSERT INTO users (username, password, nama_lengkap, role) VALUES (?, ?, ?, ?)");
                $stmt_insert->execute([$username, $password_hash, $nama_lengkap, $role]);

                $pesan_sukses = "Akun berhasil dibuat! Silakan login.";
            } catch (Exception $e) {
                $pesan_error = "Gagal membuat akun: " . $e->getMessage();
            }
        }
    } else {
        $pesan_error = "Semua kolom form wajib diisi!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Registrasi Akun Baru</title>
</head>
<body>

    <h2>Form Registrasi Akun</h2>

    <?php if ($pesan_sukses): ?>
        <p style="color: green; font-weight: bold;"><?= htmlspecialchars($pesan_sukses) ?></p>
        <p><a href="index.php">Klik di sini untuk Login</a></p>
    <?php endif; ?>

    <?php if ($pesan_error): ?>
        <p style="color: red; font-weight: bold;"><?= htmlspecialchars($pesan_error) ?></p>
    <?php endif; ?>

    <form method="POST">
        <p>
            <label>Username (NIP / NIS / ID):</label><br>
            <input type="text" name="username" placeholder="Masukkan username..." required>
        </p>
        <p>
            <label>Nama Lengkap:</label><br>
            <input type="text" name="nama_lengkap" placeholder="Masukkan nama lengkap..." required>
        </p>
        <p>
            <label>Password:</label><br>
            <input type="password" name="password" placeholder="Masukkan password..." required>
        </p>
        <p>
            <label>Pilih Role / Hak Akses:</label><br>
            <select name="role" required>
                <option value="user">User / Siswa</option>
                <option value="admin">Admin Perpustakaan</option>
            </select>
        </p>
        <button type="submit" name="register">Daftar Akun Baru</button>
    </form>

    <br>
    <p>Sudah punya akun? <a href="index.php">Kembali ke Halaman Login</a></p>

</body>
</html>