<?php
require_once 'config/koneksi.php';

// Password teks biasa yang ingin kamu gunakan
$pass_admin = 'admin123';
$pass_user  = 'user123';

// Hash menggunakan fungsi bawaan PHP yang pasti cocok dengan password_verify()
$hash_admin = password_hash($pass_admin, PASSWORD_DEFAULT);
$hash_user  = password_hash($pass_user, PASSWORD_DEFAULT);

try {
    // Matikan FK sementara & kosongkan tabel users
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    $pdo->exec("TRUNCATE TABLE users");
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

    // Insert Akun Admin
    $stmt1 = $pdo->prepare("INSERT INTO users (username, password, nama_lengkap, role) VALUES (?, ?, ?, 'admin')");
    $stmt1->execute(['admin', $hash_admin, 'Administrator']);

    // Insert Akun User
    $stmt2 = $pdo->prepare("INSERT INTO users (username, password, nama_lengkap, role) VALUES (?, ?, ?, 'user')");
    $stmt2->execute(['user1', $hash_user, 'Pengguna Biasa']);

    echo "<h2 style='color:green;'>Berhasil Reset Akun!</h2>";
    echo "<p>Sekarang silakan login dengan:</p>";
    echo "<ul>";
    echo "<li><b>Admin:</b> Username = <code>admin</code> | Password = <code>admin123</code></li>";
    echo "<li><b>User:</b> Username = <code>user1</code> | Password = <code>user123</code></li>";
    echo "</ul>";
    echo "<a href='login.php'>Buka Halaman Login</a>";

} catch (PDOException $e) {
    echo "<h2 style='color:red;'>Gagal Reset Akun:</h2> " . $e->getMessage();
}
?>