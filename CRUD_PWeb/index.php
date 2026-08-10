<?php
session_start();

$pesan_error = "";

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($username == "Admin" && $password == "daftarsiswa") {
        $_SESSION['status'] = "login";
        $_SESSION['user']   = $username;
        header("location:login.php");
        exit();
    } else {
        $pesan_error = "Username atau Password salah!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login Siswa</title>
</head>
<body>

    <!-- Tabel Luar untuk Posisi Tengah Layar -->
    <table width="100%" style="height: 100vh;">
        <tr>
            <td align="center" valign="middle">
                
                <!-- Tabel Form Login (Ukuran diperbesar dengan width="400" & cellpadding="20") -->
                <table border="1" cellpadding="20" cellspacing="0" width="400">
                    <tr>
                        <th align="center">
                            <h2>Login</h2>
                        </th>
                    </tr>
                    <tr>
                        <td>
                            <?php 
                            if ($pesan_error != "") {
                                echo "<p><font color='red'><b>" . $pesan_error . "</b></font></p>";
                            }
                            ?>
                            <form method="POST" action="">
                                <p>
                                    <label><b>Masukkan Username:</b></label><br><br>
                                    <input type="text" name="username" size="35" style="padding: 8px;" required>
                                </p>
                                
                                <p>
                                    <label><b>Masukkan Password:</b></label><br><br>
                                    <input type="password" name="password" size="35" style="padding: 8px;" required>
                                </p>
                                <br>
                                
                                <p align="center">
                                    <input type="submit" name="login" value="Masuk" style="padding: 8px 30px; font-size: 16px;">
                                </p>
                            </form>
                        </td>
                    </tr>
                </table>

            </td>
        </tr>
    </table>

</body>
</html>