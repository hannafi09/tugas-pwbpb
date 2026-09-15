<?php
session_start();
require_once 'config/koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo "Akses ditolak! Halaman ini khusus untuk Admin.";
    echo "<br><a href='dashboard.php'>Kembali ke Dashboard</a>";
    exit;
}

$pesan = '';

if (isset($_POST['tambah_buku'])) {
    $nama_buku  = trim($_POST['nama_buku']);
    $stok       = (int)$_POST['stok'];
    $harga_buku = (int)$_POST['harga_buku'];

    if (!empty($nama_buku) && $stok >= 0 && $harga_buku >= 0) {
        $stmt = $pdo->prepare("INSERT INTO buku (nama_buku, stok, harga_buku) VALUES (?, ?, ?)");
        $stmt->execute([$nama_buku, $stok, $harga_buku]);
        $pesan = "Buku berhasil ditambahkan!";
    }
}

if (isset($_GET['hapus'])) {
    $id_buku = (int)$_GET['hapus'];
    $stmt = $pdo->prepare("DELETE FROM buku WHERE id_buku = ?");
    $stmt->execute([$id_buku]);
    header("Location: kelola_buku.php");
    exit;
}

$buku_list = $pdo->query("SELECT * FROM buku ORDER BY id_buku DESC")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Kelola Buku</title>
</head>
<body>
    <p><a href="dashboard.php">&laquo; Kembali ke Dashboard</a></p>
    <h2>Kelola Data Buku</h2>

    <?php if ($pesan): ?>
        <p style="color: green;"><?= $pesan ?></p>
    <?php endif; ?>

    <fieldset>
        <legend>Tambah Buku Baru</legend>
        <form method="POST">
            <p>
                <label>Nama Buku:</label><br>
                <input type="text" name="nama_buku" required>
            </p>
            <p>
                <label>Stok Buku:</label><br>
                <input type="number" name="stok" required>
            </p>
            <p>
                <label>Harga Buku (Nominal Denda Hilang):</label><br>
                <input type="number" name="harga_buku" required>
            </p>
            <button type="submit" name="tambah_buku">Simpan Buku</button>
        </form>
    </fieldset>

    <br>

    <h3>Daftar Buku Tersedia</h3>
    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama Buku</th>
                <th>Stok</th>
                <th>Harga Buku</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($buku_list as $b): ?>
            <tr>
                <td><?= $b['id_buku'] ?></td>
                <td><?= htmlspecialchars($b['nama_buku']) ?></td>
                <td><?= $b['stok'] ?></td>
                <td>Rp <?= number_format($b['harga_buku']) ?></td>
                <td>
                    <a href="kelola_buku.php?hapus=<?= $b['id_buku'] ?>" onclick="return confirm('Hapus buku ini?')">Hapus</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>