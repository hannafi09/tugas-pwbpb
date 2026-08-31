<?php
session_start();
require_once 'config/koneksi.php';

if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }

// Tambah Barang
if (isset($_POST['tambah'])) {
    $nama  = trim($_POST['nama_barang']);
    $stok  = (int)$_POST['stok'];
    $harga = (int)$_POST['harga_sewa'];

    $stmt = $pdo->prepare("INSERT INTO master_barang (nama_barang, stok, harga_sewa) VALUES (?, ?, ?)");
    $stmt->execute([$nama, $stok, $harga]);
    header("Location: barang.php");
    exit;
}

// Hapus Barang (Hanya Admin)
if (isset($_GET['hapus']) && $_SESSION['role'] === 'admin') {
    $id = (int)$_GET['hapus'];
    $stmt = $pdo->prepare("DELETE FROM master_barang WHERE id_barang = ?");
    $stmt->execute([$id]);
    header("Location: barang.php");
    exit;
}

$barang = $pdo->query("SELECT * FROM master_barang")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head><title>Kelola Barang</title></head>
<body>
    <a href="dashboard.php">KEMBALI KE DASHBOARD</a>
    <h2>Kelola Data Barang</h2>

    <form method="POST">
        <h3>Tambah Barang</h3>
        <input type="text" name="nama_barang" placeholder="Nama Barang" required>
        <input type="number" name="stok" placeholder="Stok" required>
        <input type="number" name="harga_sewa" placeholder="Harga Sewa" required>
        <button type="submit" name="tambah">Simpan</button>
    </form>

    <br>
    <table border="1" cellpadding="8">
        <tr>
            <th>ID</th><th>Nama Barang</th><th>Stok</th><th>Harga Sewa</th><th>Aksi</th>
        </tr>
        <?php foreach ($barang as $b): ?>
        <tr>
            <td><?= $b['id_barang'] ?></td>
            <td><?= htmlspecialchars($b['nama_barang']) ?></td>
            <td><?= $b['stok'] ?></td>
            <td>Rp<?= number_format($b['harga_sewa']) ?></td>
            <td>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <a href="barang.php?hapus=<?= $b['id_barang'] ?>" onclick="return confirm('Hapus barang?')">Hapus</a>
                <?php else: ?>
                    No Access
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>