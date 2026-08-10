<?php
include 'koneksi.php';

$id = $_GET['id'];
$query = mysqli_query($koneksi, "SELECT * FROM siswa WHERE id='$id'");
$d = mysqli_fetch_array($query);

if (isset($_POST['update'])) {
    $nama  = $_POST['nama'];
    $kelas = $_POST['kelas'];
    $nisn  = $_POST['nisn'];

    mysqli_query($koneksi, "UPDATE siswa SET nama='$nama', kelas='$kelas', nisn='$nisn' WHERE id='$id'");
    header("location:login.php");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Siswa</title>
</head>
<body>
    <h2>Edit Siswa</h2>
    <a href="index.php">Kembali</a><br><br>

    <form method="POST" action="">
        <label>Nama:</label><br>
        <input type="text" name="nama" value="<?php echo $d['nama']; ?>" required><br><br>
        
        <label>Kelas:</label><br>
        <input type="text" name="kelas" value="<?php echo $d['kelas']; ?>" required><br><br>
        
        <label>NISN:</label><br>
        <input type="text" name="nisn" value="<?php echo $d['nisn']; ?>" required><br><br>
        
        <input type="submit" name="update" value="Update">
    </form>
</body>
</html>