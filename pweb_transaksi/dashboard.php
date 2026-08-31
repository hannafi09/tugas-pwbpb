<?php
session_start();
require_once 'config/koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Fitur Dashboard: Ringkasan Transaksi Aktif
$stmt = $pdo->query("SELECT COUNT(*) AS total_aktif FROM peminjaman WHERE status = 'dipinjam'");
$transaksi_aktif = $stmt->fetch()['total_aktif'];
?>
<!DOCTYPE html>
<html>
<head><title>Dashboard</title></head>
<body>
    <h1>Selamat Datang, <?= htmlspecialchars($_SESSION['username']) ?>! (<?= $_SESSION['role'] ?>)</h1>
    <nav>
        <a href="dashboard.php">Dashboard</a> | 
        <a href="barang.php">CRUD Data Barang</a> | 
        <a href="transaksi.php">Transaksi Peminjaman</a> | 
        <a href="logout.php">Logout</a>
    </nav>
    <hr>
    <h3>Ringkasan Sistem</h3>
    <p>Transaksi Berjalan/Aktif (Belum Dikembalikan): <strong><?= $transaksi_aktif ?></strong></p>
</body>
</html>