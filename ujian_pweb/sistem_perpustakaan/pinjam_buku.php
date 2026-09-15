<?php
session_start();
require_once 'config/koneksi.php';
require_once 'config/helper.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$pesan_sukses = '';
$pesan_error  = '';

if (isset($_POST['pinjam'])) {
    $nama_peminjam = trim($_POST['nama_peminjam']);
    $id_buku       = (int)$_POST['id_buku'];
    $id_user       = $_SESSION['user_id'];
    $tgl_pinjam    = $_POST['tgl_pinjam'];
    $tgl_selesai   = $_POST['tgl_selesai'];

    if (!empty($nama_peminjam) && $id_buku > 0 && !empty($tgl_pinjam) && !empty($tgl_selesai)) {
        $stmt_cek = $pdo->prepare("SELECT stok FROM buku WHERE id_buku = ?");
        $stmt_cek->execute([$id_buku]);
        $data_buku = $stmt_cek->fetch();

        if ($data_buku && $data_buku['stok'] > 0) {
            try {
                $pdo->beginTransaction();

                
                $stmt1 = $pdo->prepare("INSERT INTO peminjaman (id_user, id_buku, nama_peminjam, tgl_pinjam, tgl_kembali, status) VALUES (?, ?, ?, ?, ?, 'dipinjam')");
                $stmt1->execute([$id_user, $id_buku, $nama_peminjam, $tgl_pinjam, $tgl_selesai]);

                $stmt2 = $pdo->prepare("UPDATE buku SET stok = stok - 1 WHERE id_buku = ?");
                $stmt2->execute([$id_buku]);

                $pdo->commit();
                $pesan_sukses = "Peminjaman berhasil dicatat!";
            } catch (Exception $e) {
                $pdo->rollBack();
                $pesan_error = "Terjadi kesalahan: " . $e->getMessage();
            }
        } else {
            $pesan_error = "Stok buku habis!";
        }
    } else {
        $pesan_error = "Harap isi semua kolom form termasuk tanggal!";
    }
}

if (isset($_GET['kembali']) && $_SESSION['role'] === 'admin') {
    $id_pinjam   = (int)$_GET['kembali'];
    $tgl_kembali = tanggal_sekarang();

    $stmt = $pdo->prepare("SELECT * FROM peminjaman WHERE id_pinjam = ? AND status = 'dipinjam'");
    $stmt->execute([$id_pinjam]);
    $trx = $stmt->fetch();

    if ($trx) {
        $pdo->beginTransaction();
        $pdo->prepare("UPDATE peminjaman SET tgl_kembali = ?, status = 'dikembalikan' WHERE id_pinjam = ?")->execute([$tgl_kembali, $id_pinjam]);
        $pdo->prepare("UPDATE buku SET stok = stok + 1 WHERE id_buku = ?")->execute([$trx['id_buku']]);
        $pdo->commit();
        $pesan_sukses = "Buku berhasil dikembalikan!";
    }
}

if (isset($_GET['hilang']) && $_SESSION['role'] === 'admin') {
    $id_pinjam   = (int)$_GET['hilang'];
    $tgl_kembali = tanggal_sekarang();

    $stmt = $pdo->prepare("SELECT p.*, b.harga_buku FROM peminjaman p JOIN buku b ON p.id_buku = b.id_buku WHERE p.id_pinjam = ? AND p.status = 'dipinjam'");
    $stmt->execute([$id_pinjam]);
    $trx = $stmt->fetch();

    if ($trx) {
        $pdo->beginTransaction();
        $denda = $trx['harga_buku'];
        $pdo->prepare("UPDATE peminjaman SET tgl_kembali = ?, denda = ?, status = 'hilang' WHERE id_pinjam = ?")->execute([$tgl_kembali, $denda, $id_pinjam]);
        $pdo->commit();
        $pesan_sukses = "Buku dinyatakan hilang. Denda telah dicatat!";
    }
}

if (isset($_GET['hapus']) && $_SESSION['role'] === 'admin') {
    $id_pinjam = (int)$_GET['hapus'];
    $pdo->prepare("DELETE FROM peminjaman WHERE id_pinjam = ?")->execute([$id_pinjam]);
    $pesan_sukses = "Riwayat transaksi berhasil dihapus!";
}

$buku_tersedia = $pdo->query("SELECT * FROM buku WHERE stok > 0")->fetchAll();

if ($_SESSION['role'] === 'admin') {
    $query = "SELECT p.*, u.username, b.nama_buku FROM peminjaman p JOIN users u ON p.id_user = u.id_user JOIN buku b ON p.id_buku = b.id_buku ORDER BY p.id_pinjam DESC";
    $transaksi_list = $pdo->query($query)->fetchAll();
} else {
    $query = "SELECT p.*, u.username, b.nama_buku FROM peminjaman p JOIN users u ON p.id_user = u.id_user JOIN buku b ON p.id_buku = b.id_buku WHERE p.id_user = ? ORDER BY p.id_pinjam DESC";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$_SESSION['user_id']]);
    $transaksi_list = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Transaksi Peminjaman</title>
</head>
<body>
    <p><a href="dashboard.php">&laquo; Kembali ke Dashboard</a></p>
    <h2>Form Peminjaman Buku</h2>

    <?php if ($pesan_sukses): ?><p style="color: green;"><?= $pesan_sukses ?></p><?php endif; ?>
    <?php if ($pesan_error): ?><p style="color: red;"><?= $pesan_error ?></p><?php endif; ?>

    <fieldset>
        <legend>Input Peminjaman Baru</legend>
        <form method="POST">
            <p>
                <label>Nama Peminjam / Pelanggan:</label><br>
                <input type="text" name="nama_peminjam" required>
            </p>
            <p>
                <label>Pilih Buku:</label><br>
                <select name="id_buku" required>
                    <option value="">-- Pilih Buku --</option>
                    <?php foreach ($buku_tersedia as $b): ?>
                        <option value="<?= $b['id_buku'] ?>">
                            <?= htmlspecialchars($b['nama_buku']) ?> (Stok: <?= $b['stok'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </p>
            <p>
                <label>Tanggal Pinjam:</label><br>
                <input type="date" name="tgl_pinjam" value="<?= date('Y-m-d') ?>" required>
            </p>
            <p>
                <label>Tanggal Selesai (Target Kembali):</label><br>
                <input type="date" name="tgl_selesai" required>
            </p>
            <button type="submit" name="pinjam">Simpan Peminjaman</button>
        </form>
    </fieldset>

    <br>

    <h3>Riwayat & Status Transaksi</h3>
    <table border="1" cellpadding="5" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Peminjam</th>
                <th>Buku Dipinjam</th>
                <th>Tgl Pinjam</th>
                <th>Tgl Target / Pengembalian</th>
                <th>Status</th>
                <th>Denda</th>
                <th>Dicatat Oleh</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($transaksi_list) > 0): ?>
                <?php $no = 1; foreach ($transaksi_list as $t): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><strong><?= htmlspecialchars($t['nama_peminjam']) ?></strong></td>
                    <td><?= htmlspecialchars($t['nama_buku']) ?></td>
                    <td><?= tgl_indo($t['tgl_pinjam']) ?></td>
                    <td><?= tgl_indo($t['tgl_kembali']) ?></td>
                    <td><?= ucfirst($t['status']) ?></td>
                    <td>
                        <?php if ($t['status'] === 'hilang' && !empty($t['denda']) && $t['denda'] > 0): ?>
                            Rp <?= number_format($t['denda']) ?>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td><i><?= htmlspecialchars($t['username']) ?></i></td>
                    <td>
                        <?php if ($_SESSION['role'] === 'admin'): ?>
                            <?php if ($t['status'] === 'dipinjam'): ?>
                                <a href="pinjam_buku.php?kembali=<?= $t['id_pinjam'] ?>" onclick="return confirm('Proses pengembalian buku ini?')">[Kembalikan]</a> | 
                                <a href="pinjam_buku.php?hilang=<?= $t['id_pinjam'] ?>" onclick="return confirm('Nyatakan buku hilang?')">[Hilang]</a> |
                            <?php endif; ?>
                            <a href="pinjam_buku.php?hapus=<?= $t['id_pinjam'] ?>" onclick="return confirm('Hapus transaksi ini?')">[Hapus]</a>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9" align="center">Belum ada data peminjaman.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>