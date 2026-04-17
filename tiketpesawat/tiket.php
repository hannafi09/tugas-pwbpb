<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }

$harga = [
    'GRD' => ['nama' => 'Garuda', 'Eksekutif' => 1500000, 'Bisnis' => 900000, 'Ekonomi' => 500000],
    'MPT' => ['nama' => 'Merpati', 'Eksekutif' => 1200000, 'Bisnis' => 800000, 'Ekonomi' => 400000],
    'BTV' => ['nama' => 'Batavia', 'Eksekutif' => 1000000, 'Bisnis' => 700000, 'Ekonomi' => 300000]
];

$result = null;
if (isset($_POST['simpan'])) {
    $kode = $_POST['kode'];
    $kelas = $_POST['kelas'];
    $jumlah = (int)$_POST['jumlah'];
    $hargaTiket = $harga[$kode][$kelas];
    $result = [
        'nama' => $_POST['nama'],
        'namaPesawat' => $harga[$kode]['nama'],
        'kelas' => $kelas,
        'harga' => $hargaTiket,
        'jumlah' => $jumlah,
        'total' => $hargaTiket * $jumlah
    ];
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Pesan Tiket - Soft Style</title>
    <style>
        :root { --soft-blue: #e1eef6; --accent: #8fb9aa; --text: #4a4a4a; }
        body { font-family: 'Segoe UI', sans-serif; background: #fdfbfb; color: var(--text); margin: 0; padding: 40px 20px; }
        .card { max-width: 500px; background: white; margin: 0 auto 30px; padding: 30px; border-radius: 25px; box-shadow: 0 15px 35px rgba(0,0,0,0.03); border: 1px solid #f0f0f0; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        h3 { margin: 0; color: #66b2b2; }
        .user-tag { font-size: 13px; background: var(--soft-blue); padding: 5px 15px; border-radius: 20px; }
        table { width: 100%; border-spacing: 0 15px; }
        input[type="text"], select { width: 100%; padding: 12px; border-radius: 12px; border: 1px solid #eee; background: #fafafa; }
        .radio-group { display: flex; gap: 10px; flex-wrap: wrap; }
        .radio-item { background: #f8f9fa; padding: 8px 15px; border-radius: 10px; font-size: 14px; cursor: pointer; }
        button { background: #8fb9aa; color: white; border: none; padding: 12px 25px; border-radius: 12px; cursor: pointer; font-weight: 600; transition: 0.3s; }
        button:hover { background: #7ba697; transform: scale(1.02); }
        .result-item { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px dashed #eee; }
        .total-box { background: #f0f7f4; padding: 20px; border-radius: 15px; margin-top: 20px; text-align: center; }
        .logout-link { color: #ff8b94; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>

<div class="card">
    <div class="header">
        <span class="user-tag">Halo, <b><?= $_SESSION['username'] ?></b></span>
        <a href="logout.php" class="logout-link">Keluar</a>
    </div>
    <h3>Pemesanan Tiket</h3>
    <form method="post">
        <table>
            <tr><td>Nama</td><td><input type="text" name="nama" required placeholder="Nama Lengkap"></td></tr>
            <tr><td>Pesawat</td><td>
                <select name="kode">
                    <option value="GRD">Garuda Indonesia</option>
                    <option value="MPT">Merpati Air</option>
                    <option value="BTV">Batavia Air</option>
                </select>
            </td></tr>
            <tr><td>Kelas</td><td class="radio-group">
                <label class="radio-item"><input type="radio" name="kelas" value="Eksekutif" checked> Ekssekutif</label>
                <label class="radio-item"><input type="radio" name="kelas" value="Bisnis"> Bisnis</label>
                <label class="radio-item"><input type="radio" name="kelas" value="Ekonomi"> Ekonomi</label>
            </td></tr>
            <tr><td>Jumlah</td><td>
                <select name="jumlah">
                    <?php for($i=1;$i<=5;$i++) echo "<option>$i</option>"; ?>
                </select>
            </td></tr>
            <tr><td colspan="2" style="text-align: center; padding-top: 20px;">
                <button type="submit" name="simpan">Proses Pemesanan</button>
            </td></tr>
        </table>
    </form>
</div>

<?php if ($result): ?>
<div class="card" style="border-left: 5px solid #8fb9aa;">
    <h3 style="color: #8fb9aa;">Detail Tiket</h3>
    <div class="result-item"><span>Nama Penumpang</span> <b><?= $result['nama'] ?></b></div>
    <div class="result-item"><span>Maskapai</span> <b><?= $result['namaPesawat'] ?></b></div>
    <div class="result-item"><span>Kelas</span> <b><?= $result['kelas'] ?></b></div>
    <div class="result-item"><span>Harga Satuan</span> <b>Rp <?= number_format($result['harga'],0,',','.') ?></b></div>
    <div class="result-item"><span>Jumlah Tiket</span> <b><?= $result['jumlah'] ?></b></div>
    <div class="total-box">
        <small>Total Bayar</small>
        <div style="font-size: 24px; font-weight: bold; color: #457b9d;">Rp <?= number_format($result['total'],0,',','.') ?></div>
    </div>
</div>
<?php endif; ?>

</body>
</html>