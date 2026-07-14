<?php
session_start();
require_once 'config.php';

$isLoggedIn = isset($_SESSION['user_id']);
$message = '';

// Регистрация
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    if (!empty($username) && !empty($password)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        try {
            $stmt = $pdo->prepare("INSERT INTO users (username, password_hash) VALUES (?, ?)");
            $stmt->execute([$username, $password_hash]);
            $message = "Регистрация успешна! Теперь войдите.";
        } catch (PDOException $e) {
            $message = "Ошибка: такой пользователь уже существует.";
        }
    } else {
        $message = "Заполните все поля.";
    }
}

// Вход
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header("Location: dashboard.php");
        exit;
    } else {
        $message = "Неверный логин или пароль.";
    }
}

if ($isLoggedIn) {
    header("Location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Менеджер паролей</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            background: #0b0d15;
            font-family: 'Inter', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #e0e0e0;
        }
        .glass {
            background: rgba(20, 25, 40, 0.8);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            padding: 40px 35px;
            border-radius: 32px;
            border: 1px solid rgba(255, 255, 255, 0.06);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.7);
            width: 100%;
            max-width: 440px;
            transition: 0.3s;
        }
        .logo {
            font-size: 28px;
            font-weight: 700;
            letter-spacing: -0.5px;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .logo span { background: linear-gradient(135deg, #a78bfa, #7c3aed); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .sub { color: #7e849c; font-size: 14px; font-weight: 300; margin-bottom: 28px; }
        .tabs { display: flex; gap: 12px; margin-bottom: 24px; border-bottom: 1px solid #1e2337; padding-bottom: 12px; }
        .tab-btn {
            background: none; border: none; color: #7e849c; font-weight: 600; font-size: 15px; cursor: pointer; padding: 6px 0;
            transition: 0.2s; font-family: 'Inter', sans-serif; border-bottom: 2px solid transparent;
        }
        .tab-btn.active { color: #c4b5fd; border-bottom-color: #7c3aed; }
        .tab-content { display: none; animation: fade 0.3s ease; }
        .tab-content.active { display: block; }
        @keyframes fade { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: translateY(0); } }
        input {
            width: 100%; padding: 14px 16px; background: #131826; border: 1px solid #252b42; border-radius: 16px;
            color: #f0f0f0; font-size: 15px; transition: 0.2s; outline: none; font-family: 'Inter', sans-serif;
        }
        input:focus { border-color: #7c3aed; background: #181e2f; box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.15); }
        .field { margin-bottom: 16px; }
        .btn-primary {
            width: 100%; padding: 14px; background: linear-gradient(135deg, #7c3aed, #6d28d9); border: none; border-radius: 16px;
            color: white; font-weight: 700; font-size: 16px; cursor: pointer; transition: 0.2s; font-family: 'Inter', sans-serif;
            box-shadow: 0 6px 20px rgba(124, 58, 237, 0.25);
        }
        .btn-primary:hover { transform: scale(1.01); opacity: 0.9; }
        .msg { background: #1e1b3a; color: #c4b5fd; padding: 12px; border-radius: 12px; text-align: center; font-size: 14px; margin-bottom: 18px; border-left: 3px solid #7c3aed; }
        .footer-text { text-align: center; color: #4b5169; font-size: 12px; margin-top: 20px; }
        input:-webkit-autofill { -webkit-box-shadow: 0 0 0 1000px #131826 inset !important; -webkit-text-fill-color: #f0f0f0 !important; }
    </style>
</head>
<body>
<div class="glass">
    <div class="logo">🔐 <span>Vault</span></div>
    <div class="sub">Безопасное хранилище паролей</div>

    <?php if ($message): ?><div class="msg"><?= htmlspecialchars($message) ?></div><?php endif; ?>

    <div class="tabs">
        <button class="tab-btn active" data-tab="login">Вход</button>
        <button class="tab-btn" data-tab="register">Регистрация</button>
    </div>

    <div id="login" class="tab-content active">
        <form method="POST">
            <div class="field"><input type="text" name="username" placeholder="Логин" required></div>
            <div class="field"><input type="password" name="password" placeholder="Пароль" required></div>
            <button type="submit" name="login" class="btn-primary">Войти</button>
        </form>
    </div>

    <div id="register" class="tab-content">
        <form method="POST">
            <div class="field"><input type="text" name="username" placeholder="Придумайте логин" required></div>
            <div class="field"><input type="password" name="password" placeholder="Придумайте пароль" required></div>
            <button type="submit" name="register" class="btn-primary">Создать аккаунт</button>
        </form>
    </div>
    <div class="footer-text">Ваши данные надёжно зашифрованы</div>
</div>
<script>
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            this.classList.add('active');
            document.getElementById(this.dataset.tab).classList.add('active');
        });
    });
</script>
</body>
</html>