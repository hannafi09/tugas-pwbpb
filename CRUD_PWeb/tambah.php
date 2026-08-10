<?php
include 'koneksi.php';

if (isset($_POST['submit'])) {
    $nama  = $_POST['nama'];
    $kelas = $_POST['kelas'];
    $nisn  = $_POST['nisn'];

    mysqli_query($koneksi, "INSERT INTO siswa VALUES('', '$nama', '$kelas', '$nisn')");
    header("location:login.php");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Tambah Siswa</title>
</head>
<body>
    <h2>Tambah Siswa</h2>
    <a href="index.php">Kembali</a><br><br>

    <form method="POST" action="">
        <label>Nama:</label><br>
        <input type="text" name="nama" required><br><br>
        
        <label>Kelas:</label><br>
        <input type="text" name="kelas" value="12 PPLG 2" required><br><br>
        
        <label>NISN:</label><br>
        <input type="text" name="nisn" required><br><br>
        
        <input type="submit" name="submit" value="Simpan">
    </form>
</body>
</html>