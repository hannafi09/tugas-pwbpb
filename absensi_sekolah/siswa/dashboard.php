<?php
include '../config/db.php';
session_start();

if ($_SESSION['role'] != 'siswa') {
    header("Location: ../index.php");
    exit();
}

$nama_user = $_SESSION['user'];

// Validasi jika string gambar session kosong, gunakan default.png
$foto_profile = (!empty($_SESSION['foto'])) ? $_SESSION['foto'] : 'default.png';

if (isset($_POST['absen'])) {
    $hari_ini = date('Y-m-d');
    $cek = mysqli_query($conn, "SELECT * FROM presensi WHERE nama='$nama_user' AND DATE(waktu_absen)='$hari_ini'");
    
    if (mysqli_num_rows($cek) > 0) {
        echo "<script>alert('Anda sudah absen hari ini!');</script>";
    } else {
        mysqli_query($conn, "INSERT INTO presensi (nama, role, waktu_absen) VALUES ('$nama_user', 'siswa', NOW())");
        echo "<script>alert('Absen Berhasil!'); window.location='dashboard.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Presensi Siswa</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        .container-absen { max-width: 800px; margin: 40px auto; padding: 0 20px; }
        .card-absen { background: white; padding: 40px; border-radius: 20px; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.05); margin-bottom: 30px; }
        .btn-main-absen { background: #3498db; color: white; border: none; padding: 20px 40px; border-radius: 50px; font-size: 1.2rem; font-weight: bold; cursor: pointer; transition: 0.3s; box-shadow: 0 5px 15px rgba(52, 152, 219, 0.3); width: 100%; max-width: 400px; margin: 15px 0; }
        .btn-main-absen:hover { background: #2980b9; transform: scale(1.05); }
    </style>
</head>
<body style="display: block; background: #f4f7f6;">

    <div style="background: #2c3e50; color: white; padding: 15px 40px; display: flex; justify-content: space-between; align-items: center;">
        <h2 style="margin:0; font-size: 1.1rem;">E-Absensi Siswa</h2>
        <span><b><?php echo $nama_user; ?></b> | <a href="../index.php" style="color: #ff7675; text-decoration:none;">Logout</a></span>
    </div>

    <div class="container-absen">
        <div class="card-absen">
            <h1 style="color: #2d3436; margin-bottom: 10px;">Selamat Pagi, <?php echo $nama_user; ?>!</h1>
            
            <div style="margin: 25px 0;">
                <img src="../uploads/<?php echo $foto_profile; ?>" alt="Foto Profil" style="width: 140px; height: 140px; border-radius: 50%; object-fit: cover; border: 4px solid #f4f7f6; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
            </div>
            
            <p style="color: #b2bec3; margin-bottom: 10px;">Silahkan tekan tombol di bawah untuk mencatat kehadiran hari ini.</p>
            
            <form method="POST">
                <button type="submit" name="absen" class="btn-main-absen">KLIK UNTUK ABSEN SEKARANG</button>
            </form>

            <small style="color: #2ecc71; font-weight: bold; display: block; margin-top: 10px;">Waktu server: <?php echo date('H:i'); ?> WIB</small>
        </div>

        <div class="table-container">
            <h3 style="margin-bottom: 20px; color: #2d3436;">Riwayat Kehadiran Hari Ini</h3>
            <table>
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>Nama</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $res = mysqli_query($conn, "SELECT * FROM presensi WHERE DATE(waktu_absen) = CURDATE() ORDER BY waktu_absen DESC");
                    if(mysqli_num_rows($res) == 0) {
                        echo "<tr><td colspan='3' style='text-align:center; color:#b2bec3;'>Belum ada data absensi masuk hari ini.</td></tr>";
                    }
                    while($row = mysqli_fetch_assoc($res)) {
                        echo "<tr>
                                <td>".date('H:i', strtotime($row['waktu_absen']))." WIB</td>
                                <td style='font-weight:600;'>{$row['nama']}</td>
                                <td><span class='badge badge-siswa'>HADIR</span></td>
                              </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>