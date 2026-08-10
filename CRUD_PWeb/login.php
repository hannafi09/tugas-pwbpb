<?php
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("location:index.php"); // Jika belum login, tendang kembali ke index.php
    exit();
}
include 'koneksi.php';
$query = mysqli_query($koneksi, "SELECT * FROM siswa");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Daftar Siswa 12 PPLG 2</title>
</head>
<body>
    <p>Selamat datang | <a href="logout.php">Logout</a></p>
    <h2>Daftar Siswa Kelas 12 PPLG 2</h2>
    <a href="tambah.php">+ Tambah Siswa</a><br><br>

    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>No</th>
            <th>Nama</th>
            <th>Kelas</th>
            <th>NISN</th>
            <th>Aksi</th>
        </tr>
        <?php
        $no = 1;
        while ($d = mysqli_fetch_array($query)) {
        ?>
        <tr>
            <td><?php echo $no++; ?></td>
            <td><?php echo $d['nama']; ?></td>
            <td><?php echo $d['kelas']; ?></td>
            <td><?php echo $d['nisn']; ?></td>
            <td>
                <a href="edit.php?id=<?php echo $d['id']; ?>">Edit</a> | 
                <a href="hapus.php?id=<?php echo $d['id']; ?>">Hapus</a>
            </td>
        </tr>
        <?php } ?>
    </table>
</body>
</html>