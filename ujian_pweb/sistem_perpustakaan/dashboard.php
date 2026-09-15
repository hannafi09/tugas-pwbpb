<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
</head>
<body>
    <h2>Selamat Datang, <?= htmlspecialchars($_SESSION['username']) ?>!</h2>
    <p>Role Akun: <strong><?= htmlspecialchars($_SESSION['role']) ?></strong></p>

    <hr>

    <h3>Menu Navigasi:</h3>
    <ul>
        <?php if ($_SESSION['role'] === 'admin'): ?>
            <li><a href="kelola_buku.php">Kelola Data Buku (Halaman Admin)</a></li>
        <?php endif; ?>
        <li><a href="pinjam_buku.php">Peminjaman & Riwayat Transaksi</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</body>
</html>