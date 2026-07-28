<?php
include '../config/db.php';
session_start();

if ($_SESSION['role'] != 'walikelas') { header("Location: ../index.php"); exit(); }

$tipe = isset($_GET['tipe']) ? $_GET['tipe'] : 'siswa';
$tahun_pilihan = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');
?>

<!DOCTYPE html>
<html>
<head>
    <title>Laporan Tahunan <?php echo ucfirst($tipe); ?></title>
    <link rel="stylesheet" href="../style.css">
    <style>
        .scroll-wrapper { overflow-x: auto; margin-top: 20px; background: white; padding: 20px; border-radius: 12px; box-shadow: 0 5px 20px rgba(0,0,0,0.05); }
        th { text-align: center !important; background: #2c3e50 !important; color: white !important; }
        .sub-th { background: #34495e !important; font-size: 0.8rem; }
        td { text-align: center; }
        .nama-kiri { text-align: left; font-weight: bold; }
    </style>
</head>
<body style="display: block; background: #f4f7f6; padding: 40px;">

    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h2 style="margin:0; color:#2d3436;">Laporan Kehadiran <?php echo ucfirst($tipe); ?></h2>
            <form method="GET" style="margin-top: 10px;">
                <input type="hidden" name="tipe" value="<?php echo $tipe; ?>">
                <label>Tahun: </label>
                <select name="tahun" onchange="this.form.submit()" class="form-control" style="width: 120px; display:inline-block; padding: 5px;">
                    <?php 
                    for($i=2025; $i<=2030; $i++){
                        $sel = ($i == $tahun_pilihan) ? 'selected' : '';
                        echo "<option value='$i' $sel>$i</option>";
                    }
                    ?>
                </select>
            </form>
        </div>
        <a href="dashboard.php" class="btn-print-soft">⬅️ Kembali ke Dashboard</a>
    </div>

    <div class="scroll-wrapper">
        <table border="1" cellpadding="8" cellspacing="0" style="width: 100%; min-width: 1500px; border-collapse: collapse;">
            <thead>
                <tr>
                    <th rowspan="2" width="50">No</th>
                    <th rowspan="2" width="200">Nama Lengkap</th>
                    <?php 
                    $bulan = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
                    foreach($bulan as $b) { echo "<th colspan='2'>$b</th>"; }
                    ?>
                    <th colspan="2" style="background:#00b894 !important;">Laporan Tahun Ini</th>
                </tr>
                <tr>
                    <?php for($i=1; $i<=13; $i++): ?>
                        <th class="sub-th" style="color: #fff;">Hadir</th>
                        <th class="sub-th" style="color: #fff;">Tidak Hadir</th>
                    <?php endfor; ?>
                </tr>
            </thead>
            <tbody>
                <?php
                $user_query = mysqli_query($conn, "SELECT nama_lengkap FROM users WHERE role='$tipe' ORDER BY nama_lengkap ASC");
                $no = 1;

                if(mysqli_num_rows($user_query) == 0) {
                    echo "<tr><td colspan='28'>Tidak ada data master untuk kategori ini.</td></tr>";
                }

                while($user = mysqli_fetch_assoc($user_query)) {
                    $nama = $user['nama_lengkap'];
                    echo "<tr>";
                    echo "<td>".$no++."</td>";
                    echo "<td class='nama-kiri'>$nama</td>";

                    $total_hadir_setahun = 0;
                    $total_tidak_setahun = 0;

                    for($m=1; $m<=12; $m++) {
                        $q_absen = mysqli_query($conn, "SELECT COUNT(*) as total FROM presensi WHERE nama='$nama' AND YEAR(waktu_absen)='$tahun_pilihan' AND MONTH(waktu_absen)='$m'");
                        $data_absen = mysqli_fetch_assoc($q_absen);
                        $hadir = $data_absen['total'];

                        if ($hadir == 0) {
                            $display_hadir = "-";
                            $display_tidak = "-";
                        } else {
                            $display_hadir = $hadir;
                            $display_tidak = 25 - $hadir; // Asumsi 25 hari sekolah sebulan
                            if($display_tidak < 0) $display_tidak = 0;

                            $total_hadir_setahun += $display_hadir;
                            $total_tidak_setahun += $display_tidak;
                        }

                        echo "<td>$display_hadir</td>";
                        echo "<td>$display_tidak</td>";
                    }

                    echo "<td style='background:#e8f8f5; font-weight:bold;'>$total_hadir_setahun</td>";
                    echo "<td style='background:#fdf2e9; font-weight:bold;'>$total_tidak_setahun</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

</body>
</html>