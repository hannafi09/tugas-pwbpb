<?php
include 'config/db.php';
session_start();

$role_login = isset($_GET['role']) ? $_GET['role'] : 'siswa';

if (isset($_POST['login'])) {
    $user = $_POST['username'];
    $pass = $_POST['password'];
    $role = $_POST['role_hidden']; 

    $query = mysqli_query($conn, "SELECT * FROM users WHERE username='$user' AND password='$pass' AND role='$role'");
    $data = mysqli_fetch_assoc($query);

    if ($data) {
        $_SESSION['user'] = $data['nama_lengkap'];
        $_SESSION['role'] = $data['role'];
        $_SESSION['mapel'] = $data['mapel'];
        $_SESSION['foto'] = $data['foto']; // Menyimpan nama file foto ke session
        header("Location: " . $data['role'] . "/dashboard.php");
        exit();
    } else {
        echo "<script>alert('Login Gagal! Pastikan Username & Password sesuai dengan kategori.');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>E-Absensi Sekolah</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="sidebar">
        <h2>SISTEM ABSENSI</h2>
        <ul>
            <li><a href="?role=walikelas" class="<?php echo $role_login == 'walikelas' ? 'active' : ''; ?>">Wali Kelas</a></li>
            <li><a href="?role=guru" class="<?php echo $role_login == 'guru' ? 'active' : ''; ?>">Guru Pengajar</a></li>
            <li><a href="?role=siswa" class="<?php echo $role_login == 'siswa' ? 'active' : ''; ?>">Siswa</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="login-card">
            <h3>Login <?php echo ucfirst($role_login); ?></h3>
            <p style="text-align:center; font-size: 0.8rem; color: #777;">Silahkan masukkan kode akses anda</p>
            
            <form method="POST">
                <input type="hidden" name="role_hidden" value="<?php echo $role_login; ?>">
                
                <input type="text" name="username" placeholder="Username / NISN" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" name="login">MASUK SISTEM</button>
            </form>
        </div>
    </div>

</body>
</html>