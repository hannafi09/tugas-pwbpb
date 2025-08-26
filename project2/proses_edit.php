<?php
include 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $alamat = $_POST['alamat'];

    $query = "UPDATE siswa SET nama='$nama', email='$email', alamat='$alamat' WHERE id=$id";
    if ($koneksi->query($query) === TRUE) {
        echo "Data berhasil diperbarui.";
        echo "<br><a href='tampil_data.php'>Kembali ke Data Siswa</a>";
    } else {
    echo "Error: " . $query . "<br>". $koneksi->error;
    }
}
?>