<?php
// 1. Panggil koneksi database dan aktifkan session
include '../config/db.php';
session_start();

// 2. Proteksi Halaman: Hanya walikelas yang bisa masuk
if ($_SESSION['role'] != 'walikelas') {
    header("Location: ../index.php");
    exit();
}

// 3. Logika Hapus Data Absen
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM presensi WHERE id='$id'");
    header("Location: dashboard.php"); // Refresh halaman setelah menghapus
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Wali Kelas</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body style="display: block; background: #f4f7f6;"> 

    <div style="background: #2c3e50; color: white; padding: 15px 40px; display: flex; justify-content: space-between; align-items: center;">
        <h2 style="margin:0; font-size: 1.2rem;">Panel Wali Kelas</h2>
        <span>Halo, <b><?php echo $_SESSION['user']; ?></b> | <a href="../index.php" style="color: #ff7675; text-decoration:none;">Logout</a></span>
    </div>

    <div style="padding: 40px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h1 style="color: #2d3436; margin: 0;">Laporan Absensi Real-Time</h1>
            
            <div style="display: flex; gap: 10px;">
                <div style="position: relative; display: inline-block;">
                    <button class="btn-print-soft" onclick="togglePrintMenu()">📄 Cetak Laporan</button>
                    <div id="printMenu" class="print-dropdown" style="display: none; position: absolute; top: 100%; right: 0; background: white; box-shadow: 0 8px 20px rgba(0,0,0,0.15); border-radius: 10px; z-index: 100; margin-top: 5px; min-width: 180px; overflow: hidden;">
                        <button onclick="printData('siswa')" style="width: 100%; padding: 12px 15px; border: none; background:  rgb(68, 114, 252); text-align: left; cursor: pointer; font-size: 0.85rem;">Khusus Siswa</button>
                        <button onclick="printData('guru')" style="width: 100%; padding: 12px 15px; border: none; background:   rgb(68, 114, 252); text-align: left; cursor: pointer; font-size: 0.85rem;">Khusus Guru</button>
                        <button onclick="printData('semua')" style="width: 100%; padding: 12px 15px; border: none; background:  rgb(68, 114, 252); text-align: left; cursor: pointer; font-size: 0.85rem;">Semua Data</button>
                    </div>
                </div>

                <a href="tambah_user.php" class="btn-add" style="margin-bottom:0;">+ Daftarkan Siswa/Guru</a>
            </div>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Waktu Absen</th>
                        <th>Nama Lengkap</th>
                        <th>Status</th>
                        <th>Mata Pelajaran</th>
                        <th>Foto</th> 
                        <th style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Query JOIN untuk mengambil data absensi sekaligus foto profil user secara real-time
                    $res = mysqli_query($conn, "SELECT presensi.*, users.foto FROM presensi LEFT JOIN users ON presensi.nama = users.nama_lengkap ORDER BY waktu_absen DESC");
                    
                    if (mysqli_num_rows($res) == 0) {
                        echo "<tr><td colspan='6' style='text-align:center;'>Belum ada data absensi hari ini.</td></tr>";
                    }
                    
                    while($d = mysqli_fetch_assoc($res)) {
                        $badgeClass = ($d['role'] == 'siswa') ? 'badge-siswa' : 'badge-guru';
                        $gambar = !empty($d['foto']) ? $d['foto'] : 'default.png';
                        ?>
                        <tr>
                            <td><?php echo date('d M Y, H:i', strtotime($d['waktu_absen'])); ?></td>
                            <td style="font-weight: 600; color: #2d3436;"><?php echo $d['nama']; ?></td>
                            <td><span class="badge <?php echo $badgeClass; ?>"><?php echo strtoupper($d['role']); ?></span></td>
                            <td><?php echo ($d['mapel'] ? $d['mapel'] : '<span style="color:#ccc;">-</span>'); ?></td>
                            
                            <td>
                                <img src="../uploads/<?php echo $gambar; ?>" style="width: 45px; height: 45px; border-radius: 50%; object-fit: cover; border: 1px solid #ddd;">
                            </td>
                            
                            <td style="text-align: center;">
                                <a href="dashboard.php?hapus=<?php echo $d['id']; ?>" 
                                   class="btn-delete" 
                                   onclick="return confirm('Yakin ingin menghapus data absen ini?')">Hapus</a>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <div style="margin-top: 30px; padding: 20px; background: #fff; border-radius: 12px; box-shadow: 0 5px 20px rgba(0,0,0,0.05);">
            <h3 style="margin-top:0; color:#2d3436;">Ingin Melihat Laporan Tahun Ini?</h3>
            <div style="display: flex; gap: 10px;">
                <a href="laporan_tahunan.php?tipe=siswa" class="btn-print-soft" style="background:#3498db; text-decoration:none;">📊 Laporan Tahunan Siswa</a>
                <a href="laporan_tahunan.php?tipe=guru" class="btn-print-soft" style="background:#9b59b6; text-decoration:none;">📊 Laporan Tahunan Guru</a>
            </div>
        </div>
    </div>

<script>
function togglePrintMenu() {
    var menu = document.getElementById("printMenu");
    menu.style.display = (menu.style.display === "block") ? "none" : "block";
}

function printData(type) {
    document.getElementById("printMenu").style.display = "none";
    var rows = document.querySelectorAll("tbody tr");
    
    rows.forEach(row => {
        // Cek status di kolom ke-3 (index 2)
        if(row.cells.length > 2) {
            var status = row.cells[2].innerText.toLowerCase();
            if (type === 'semua') {
                row.style.display = ""; 
            } else if (status.includes(type)) {
                row.style.display = ""; 
            } else {
                row.style.display = "none"; 
            }
        }
    });

    window.print();

    setTimeout(() => {
        rows.forEach(row => row.style.display = "");
    }, 1000);
}

// Menutup dropdown otomatis jika klik di luar tombol
window.onclick = function(event) {
    if (!event.target.matches('.btn-print-soft')) {
        document.getElementById("printMenu").style.display = "none";
    }
}
</script>

</body>
</html>