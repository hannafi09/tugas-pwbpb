<?php
session_start();
require_once 'koneksi.php';
$error = ''; $success = '';
$activeTab = isset($_POST['register']) ? 'register' : 'login';

if (isset($_POST['login'])) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $_POST['username']);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    if ($user && password_verify($_POST['password'], $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header("Location: tiket.php");
        exit();
    }
    $error = "Username atau password salah!";
}

if (isset($_POST['register'])) {
    if ($_POST['reg_password'] != $_POST['confirm_password']) {
        $error = "Password tidak cocok!";
    } elseif (strlen($_POST['reg_password']) < 6) {
        $error = "Password minimal 6 karakter!";
    } else {
        $hashed = password_hash($_POST['reg_password'], PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $_POST['reg_username'], $hashed, $_POST['email']);
        if ($stmt->execute()) {
            $success = "Registrasi berhasil! Silakan login.";
            $activeTab = 'login';
        } else { $error = "Username sudah digunakan!"; }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Tiket Online</title>
    <style>
        :root {
            --primary: #a8dadc;
            --secondary: #457b9d;
            --bg: #f1faee;
            --white: #ffffff;
            --text: #1d3557;
        }
        body { font-family: 'Segoe UI', sans-serif; background: var(--bg); color: var(--text); display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .container { width: 100%; max-width: 400px; padding: 20px; }
        .box { background: var(--white); border-radius: 20px; padding: 40px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
        h3 { text-align: center; font-weight: 600; color: var(--secondary); margin-bottom: 30px; line-height: 1.4; }
        .tab-group { display: flex; margin-bottom: 25px; background: #f8f9fa; border-radius: 12px; padding: 5px; }
        .tab-group button { flex: 1; border: none; padding: 12px; cursor: pointer; border-radius: 10px; background: transparent; transition: 0.3s; font-weight: 600; }
        .tab-group button.active { background: var(--white); color: var(--secondary); box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        input { width: 100%; padding: 12px 15px; margin: 8px 0 20px; border: 1px solid #e0e0e0; border-radius: 10px; box-sizing: border-box; outline: none; transition: 0.3s; }
        input:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(168, 218, 220, 0.3); }
        button[type="submit"] { width: 100%; background: var(--secondary); color: white; border: none; padding: 14px; border-radius: 10px; font-weight: 600; cursor: pointer; transition: 0.3s; }
        button[type="submit"]:hover { background: var(--text); transform: translateY(-2px); }
        .error, .success { padding: 12px; border-radius: 10px; margin-bottom: 20px; font-size: 14px; text-align: center; }
        .error { background: #ffe3e3; color: #e63946; }
        .success { background: #d4edda; color: #155724; }
        .pane { display: none; }
        .pane.active { display: block; animation: fadeIn 0.5s; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>
<div class="container">
    <div class="box">
        <h3>TIKET ONLINE<br><small style="font-size: 14px; opacity: 0.7;">Jakarta - Malaysia</small></h3>
        
        <?php if($error) echo "<div class='error'>$error</div>"; ?>
        <?php if($success) echo "<div class='success'>$success</div>"; ?>
        
        <div class="tab-group">
            <button class="<?= $activeTab=='login' ? 'active' : '' ?>" onclick="showTab('login', event)">LOGIN</button>
            <button class="<?= $activeTab=='register' ? 'active' : '' ?>" onclick="showTab('register', event)">DAFTAR</button>
        </div>
        
        <div id="login" class="pane <?= $activeTab=='login' ? 'active' : '' ?>">
            <form method="post">
                <label>Username</label>
                <input type="text" name="username" placeholder="Masukkan username" required>
                <label>Password</label>
                <input type="password" name="password" placeholder="••••••••" required>
                <button type="submit" name="login">Masuk Sekarang</button>
            </form>
        </div>
        
        <div id="register" class="pane <?= $activeTab=='register' ? 'active' : '' ?>">
            <form method="post">
                <input type="text" name="reg_username" placeholder="Username" required>
                <input type="email" name="email" placeholder="Email (Opsional)">
                <input type="password" name="reg_password" placeholder="Password Baru" required>
                <input type="password" name="confirm_password" placeholder="Konfirmasi Password" required>
                <button type="submit" name="register">Buat Akun</button>
            </form>
        </div>
    </div>
</div>

<script>
function showTab(tabId, event) {
    document.querySelectorAll('.pane').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.tab-group button').forEach(b => b.classList.remove('active'));
    document.getElementById(tabId).classList.add('active');
    event.currentTarget.classList.add('active');
}
</script>
</body>
</html>