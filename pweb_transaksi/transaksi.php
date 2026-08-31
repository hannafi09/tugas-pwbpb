<?php
session_start();
require_once 'config/koneksi.php';
require_once 'config/helper.php';

// Cek autentikasi login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$pesan_sukses = '';
$pesan_error  = '';

// ------------------------------------------------------------------------
// 1. PROSES PINJAM BARANG
// ------------------------------------------------------------------------
if (isset($_POST['pinjam'])) {
    $nama_peminjam = trim($_POST['nama_peminjam']);
    $id_barang     = (int)$_POST['id_barang'];
    $id_user       = $_SESSION['user_id'];
    $tgl_pinjam    = tanggal_sekarang(); 

    if (!empty($nama_peminjam) && $id_barang > 0) {
        // Cek stok barang
        $stmt_cek = $pdo->prepare("SELECT stok FROM master_barang WHERE id_barang = ?");
        $stmt_cek->execute([$id_barang]);
        $data_barang = $stmt_cek->fetch();

        if ($data_barang && $data_barang['stok'] > 0) {
            try {
                $pdo->beginTransaction();

                // Simpan transaksi baru
                $stmt1 = $pdo->prepare("INSERT INTO peminjaman (id_user, nama_peminjam, id_barang, tgl_pinjam, status) VALUES (?, ?, ?, ?, 'dipinjam')");
                $stmt1->execute([$id_user, $nama_peminjam, $id_barang, $tgl_pinjam]);

                // Kurangi stok barang
                $stmt2 = $pdo->prepare("UPDATE master_barang SET stok = stok - 1 WHERE id_barang = ?");
                $stmt2->execute([$id_barang]);

                $pdo->commit();
                $pesan_sukses = "Peminjaman atas nama '$nama_peminjam' berhasil dicatat!";
            } catch (Exception $e) {
                $pdo->rollBack();
                $pesan_error = "Gagal memproses transaksi: " . $e->getMessage();
            }
        } else {
            $pesan_error = "Stok barang habis!";
        }
    } else {
        $pesan_error = "Semua kolom wajib diisi!";
    }
}

// ------------------------------------------------------------------------
// 2. PROSES PENGEMBALIAN BARANG (LETAK LOGIKA SEWA & DENDA DI SINI)
// ------------------------------------------------------------------------
if (isset($_GET['kembali'])) {
    $id_pinjam   = (int)$_GET['kembali'];
    $tgl_kembali = tanggal_sekarang(); 

    // Ambil data transaksi beserta harga sewa barangnya (JOIN)
    $stmt = $pdo->prepare("SELECT p.*, b.harga_sewa 
                           FROM peminjaman p 
                           JOIN master_barang b ON p.id_barang = b.id_barang 
                           WHERE p.id_pinjam = ?");
    $stmt->execute([$id_pinjam]);
    $trx = $stmt->fetch();

    if ($trx && $trx['status'] === 'dipinjam') {
        // ----------------------------------------------------------------
        // KODE PERHITUNGAN LOGIKA SEWA & DENDA
        // ----------------------------------------------------------------
        $tgl_awal  = new DateTime($trx['tgl_pinjam']);
        $tgl_akhir = new DateTime($tgl_kembali);
        $durasi    = $tgl_awal->diff($tgl_akhir)->days;

        // Jika dikembalikan pada hari yang sama, minimal dihitung 1 hari sewa
        if ($durasi == 0) {
            $durasi = 1;
        }

        // 1. Hitung Biaya Sewa Pokok (Durasi x Harga Sewa per Hari)
        $biaya_sewa = $durasi * $trx['harga_sewa'];

        // 2. Hitung Denda Keterlambatan (jika lebih dari 3 hari)
        $batas_hari     = 3;
        $denda_per_hari = 5000;
        $denda          = 0;

        if ($durasi > $batas_hari) {
            $hari_terlambat = $durasi - $batas_hari;
            $denda          = $hari_terlambat * $denda_per_hari;
        }

        // 3. Hitung Total Pembayaran Keseluruhan
        $total_bayar = $biaya_sewa + $denda;
        // ----------------------------------------------------------------

        try {
            $pdo->beginTransaction();

            // Simpan tanggal kembali, denda, total bayar, dan ubah status transaksi
            $stmt1 = $pdo->prepare("UPDATE peminjaman SET tgl_kembali = ?, denda = ?, total_bayar = ?, status = 'dikembalikan' WHERE id_pinjam = ?");
            $stmt1->execute([$tgl_kembali, $denda, $total_bayar, $id_pinjam]);

            // Kembalikan stok barang (+1)
            $stmt2 = $pdo->prepare("UPDATE master_barang SET stok = stok + 1 WHERE id_barang = ?");
            $stmt2->execute([$trx['id_barang']]);

            $pdo->commit();
            $pesan_sukses = "Barang dikembalikan! Durasi: $durasi hari. Biaya Sewa: Rp " . number_format($biaya_sewa) . " | Denda: Rp " . number_format($denda) . " | Total Bayar: Rp " . number_format($total_bayar);
        } catch (Exception $e) {
            $pdo->rollBack();
            $pesan_error = "Gagal memproses pengembalian: " . $e->getMessage();
        }
    }
}

// ------------------------------------------------------------------------
// 3. FITUR HAPUS RIWAYAT (ADMIN)
// ------------------------------------------------------------------------
if (isset($_GET['hapus']) && $_SESSION['role'] === 'admin') {
    $id_pinjam = (int)$_GET['hapus'];

    $stmt_cek = $pdo->prepare("SELECT * FROM peminjaman WHERE id_pinjam = ?");
    $stmt_cek->execute([$id_pinjam]);
    $trx = $stmt_cek->fetch();

    if ($trx) {
        try {
            $pdo->beginTransaction();

            if ($trx['status'] === 'dipinjam') {
                $stmt_stok = $pdo->prepare("UPDATE master_barang SET stok = stok + 1 WHERE id_barang = ?");
                $stmt_stok->execute([$trx['id_barang']]);
            }

            $stmt_hapus = $pdo->prepare("DELETE FROM peminjaman WHERE id_pinjam = ?");
            $stmt_hapus->execute([$id_pinjam]);

            $pdo->commit();
            $pesan_sukses = "Riwayat peminjaman berhasil dihapus!";
        } catch (Exception $e) {
            $pdo->rollBack();
            $pesan_error = "Gagal menghapus riwayat: " . $e->getMessage();
        }
    }
}

// ------------------------------------------------------------------------
// 4. AMBIL DATA UNTUK TAMPILAN
// ------------------------------------------------------------------------
$barang_tersedia = $pdo->query("SELECT * FROM master_barang WHERE stok > 0")->fetchAll();

$query_trx = "SELECT p.*, u.username, b.nama_barang, b.harga_sewa 
              FROM peminjaman p 
              JOIN users u ON p.id_user = u.id_user 
              JOIN master_barang b ON p.id_barang = b.id_barang 
              ORDER BY p.id_pinjam DESC";
$transaksi_list = $pdo->query($query_trx)->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Transaksi Peminjaman</title>
</head>
<body>

    <p><a href="dashboard.php">&laquo; Kembali ke Dashboard</a></p>
    <h2>Form Transaksi Peminjaman</h2>

    <?php if ($pesan_sukses): ?>
        <p style="color: green; font-weight: bold;"><?= htmlspecialchars($pesan_sukses) ?></p>
    <?php endif; ?>
    <?php if ($pesan_error): ?>
        <p style="color: red; font-weight: bold;"><?= htmlspecialchars($pesan_error) ?></p>
    <?php endif; ?>

    <!-- FORM INPUT PEMINJAMAN -->
    <fieldset>
        <legend><strong>Tambah Peminjaman Baru</strong></legend>
        <form method="POST">
            <p>
                <label>Nama Peminjam / Pelanggan:</label><br>
                <input type="text" name="nama_peminjam" placeholder="Masukkan nama peminjam..." required style="width: 300px;">
            </p>
            <p>
                <label>Pilih Barang:</label><br>
                <select name="id_barang" required style="width: 308px;">
                    <option value="">-- Pilih Barang --</option>
                    <?php foreach ($barang_tersedia as $b): ?>
                        <option value="<?= $b['id_barang'] ?>">
                            <?= htmlspecialchars($b['nama_barang']) ?> (Stok: <?= $b['stok'] ?> | Rp <?= number_format($b['harga_sewa']) ?>/hari)
                        </option>
                    <?php endforeach; ?>
                </select>
            </p>
            <button type="submit" name="pinjam">Simpan Peminjaman</button>
        </form>
    </fieldset>

    <br><br>

    <!-- RIWAYAT & STATUS TRANSAKSI -->
    <h3>Riwayat & Status Transaksi</h3>
    <table border="1" cellpadding="8" cellspacing="0" width="100%">
        <thead>
            <tr bgcolor="#f2f2f2">
                <th>No</th>
                <th>Nama Peminjam</th>
                <th>Barang Dipinjam</th>
                <th>Tgl Pinjam</th>
                <th>Tgl Kembali</th>
                <th>Status</th>
                <th>Denda</th>
                <th>Total Bayar</th>
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
                    <td><?= htmlspecialchars($t['nama_barang']) ?></td>
                    <td><?= tgl_indo($t['tgl_pinjam']) ?></td>
                    <td><?= tgl_indo($t['tgl_kembali']) ?></td>
                    <td>
                        <?php if ($t['status'] === 'dipinjam'): ?>
                            <span style="color: orange; font-weight: bold;">Dipinjam</span>
                        <?php else: ?>
                            <span style="color: green; font-weight: bold;">Dikembalikan</span>
                        <?php endif; ?>
                    </td>
                    <td>Rp <?= number_format($t['denda']) ?></td>
                    <td><strong>Rp <?= number_format($t['total_bayar'] ?? 0) ?></strong></td>
                    <td><small><i><?= htmlspecialchars($t['username']) ?></i></small></td>
                    <td>
                        <?php if ($t['status'] === 'dipinjam'): ?>
                            <a href="transaksi.php?kembali=<?= $t['id_pinjam'] ?>" 
                               onclick="return confirm('Proses pengembalian barang ini?')">
                                [Kembalikan]
                            </a>
                        <?php else: ?>
                            <span style="color: gray;">Selesai</span>
                        <?php endif; ?>

                        <?php if ($_SESSION['role'] === 'admin'): ?>
                            | <a href="transaksi.php?hapus=<?= $t['id_pinjam'] ?>" 
                               style="color: red;" 
                               onclick="return confirm('Hapus data transaksi ini?')">
                                [Hapus]
                            </a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="10" align="center">Belum ada data peminjaman.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

</body>
</html>