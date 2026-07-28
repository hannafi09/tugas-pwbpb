<?php
include '../config/db.php';
session_start();

if ($_SESSION['role'] != 'walikelas') {
    header("Location: ../index.php");
    exit();
}

if (isset($_POST['simpan'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $user = mysqli_real_escape_string($conn, $_POST['user']);
    $pass = mysqli_real_escape_string($conn, $_POST['pass']);
    $role = $_POST['role'];
    $mapel = ($role == 'guru') ? mysqli_real_escape_string($conn, $_POST['mapel']) : NULL;

    // Logika Upload Foto
    $nama_foto = $_FILES['foto']['name'];
    $tmp_foto = $_FILES['foto']['tmp_name'];

    if(!empty($nama_foto)){
        $ekstensi = pathinfo($nama_foto, PATHINFO_EXTENSION);
        $foto_baru = $user . "_" . time() . "." . $ekstensi; // Nama file unik
        move_uploaded_file($tmp_foto, "../uploads/" . $foto_baru);
    } else {
        $foto_baru = "default.png";
    }

    $insert = mysqli_query($conn, "INSERT INTO users (username, password, role, nama_lengkap, mapel, foto) 
                                   VALUES ('$user', '$pass', '$role', '$nama', '$mapel', '$foto_baru')");
    if($insert) { echo "<script>alert('User Berhasil Ditambahkan!'); window.location='dashboard.php';</script>"; }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registrasi User Baru</title>
    <link rel="stylesheet" href="../style.css">
    <script>
        function toggleMapel() {
            var role = document.getElementById("role_select").value;
            var mapelDiv = document.getElementById("mapel_group");
            if (role === "guru") {
                mapelDiv.style.display = "block";
            } else {
                mapelDiv.style.display = "none";
            }
        }
    </script>
</head>
<body style="display: block; background: #f4f7f6; padding-top: 50px;">

    <div class="form-card">
        <h2 style="text-align:center; color: #2d3436; margin-bottom: 5px;">Tambah User Baru</h2>
        <p style="text-align:center; color: #b2bec3; margin-bottom: 30px;">Daftarkan Siswa atau Guru ke dalam sistem</p>

        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="nama" class="form-control" placeholder="Contoh: Ahmad Subardjo" required>
            </div>

            <div style="display: flex; gap: 15px;">
                <div class="form-group" style="flex: 1;">
                    <label>Username</label>
                    <input type="text" name="user" class="form-control" placeholder="Username login" required>
                </div>
                <div class="form-group" style="flex: 1;">
                    <label>Password</label>
                    <input type="password" name="pass" class="form-control" placeholder="Password" required>
                </div>
            </div>

            <div class="form-group">
                <label>Status / Role</label>
                <select name="role" id="role_select" class="form-control" onchange="toggleMapel()" required>
                    <option value="siswa">Siswa</option>
                    <option value="guru">Guru Pengajar</option>
                </select>
            </div>

            <div id="mapel_group" class="form-group" style="display:none;">
                <label>Mata Pelajaran</label>
                <input type="text" name="mapel" class="form-control" placeholder="Contoh: Fisika / Bahasa Indonesia">
            </div>

            <div class="form-group">
                <label>Foto Profil</label>
                <input type="file" name="foto" class="form-control" accept="image/*">
            </div>

            <button type="submit" name="simpan" class="btn-save">SIMPAN DATA</button>
            <a href="dashboard.php" class="btn-back">Batal dan Kembali</a>
        </form>
    </div>

</body>
</html>