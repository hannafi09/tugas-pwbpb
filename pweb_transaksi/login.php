<?php
session_start();
require_once 'config/koneksi.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Ambil input dari form login
    $input_username = trim($_POST['username'] ?? '');
    $input_password = $_POST['password'] ?? '';

    // Validasi input tidak boleh kosong
    if (!empty($input_username) && !empty($input_password)) {
        
        // 2. Query ke database dengan Prepared Statement
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$input_username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // 3. Verifikasi ketersediaan user dan kecocokan password
        if ($user && is_array($user) && password_verify($input_password, $user['password'])) {
            // Simpan data session
            $_SESSION['user_id']  = $user['id_user'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role']     = $user['role'];

            // Redirect ke dashboard
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Username atau password salah!";
        }
    } else {
        $error = "Username dan Password wajib diisi!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login System</title>
</head>
<body>
    <h2>Form Login</h2>
    
    <?php if ($error): ?>
        <p style="color: red; font-weight: bold;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <p>
            <label>Username:</label><br>
            <input type="text" name="username" required>
        </p>
        <p>
            <label>Password:</label><br>
            <input type="password" name="password" required>
        </p>
        <button type="submit">Login</button>
    </form>
    
    <br>
    <p>Belum punya akun? <a href="register.php">Daftar Akun Baru</a></p>
</body>
</html>