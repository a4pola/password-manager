<?php
session_start();
require_once 'config.php';
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); exit; }

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_password'])) {
    $site_name = $_POST['site_name'];
    $site_url = $_POST['site_url'] ?? '';
    $login = $_POST['login'];
    $password = $_POST['password'];
    $encrypted = base64_encode($password);
    $stmt = $pdo->prepare("INSERT INTO passwords (user_id, site_name, site_url, login, encrypted_password) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $site_name, $site_url, $login, $encrypted]);
    header("Location: dashboard.php"); exit;
}
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM passwords WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $user_id]);
    header("Location: dashboard.php"); exit;
}
$stmt = $pdo->prepare("SELECT * FROM passwords WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$passwords = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Vault — Мои пароли</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            background: #0b0d15;
            font-family: 'Inter', sans-serif;
            color: #e0e0e0;
            padding: 30px 20px;
            min-height: 100vh;
            display: flex;
            justify-content: center;
        }
        .container { max-width: 880px; width: 100%; }
        .header {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 32px; flex-wrap: wrap; gap: 15px;
        }
        .logo { font-size: 24px; font-weight: 700; letter-spacing: -0.3px; }
        .logo span { background: linear-gradient(135deg, #a78bfa, #7c3aed); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .user-box {
            display: flex; align-items: center; gap: 16px;
            background: #131826; padding: 8px 16px 8px 20px; border-radius: 40px;
            border: 1px solid #1e2337;
        }
        .user-box .name { font-weight: 500; font-size: 14px; color: #c4b5fd; }
        .btn-outline {
            background: transparent; border: 1px solid #2a3050; color: #b0b8d4; padding: 8px 16px;
            border-radius: 30px; font-size: 13px; font-weight: 600; cursor: pointer; transition: 0.2s;
            text-decoration: none; font-family: 'Inter', sans-serif;
        }
        .btn-outline:hover { background: #2a3050; color: white; }
        .glass-card {
            background: rgba(18, 23, 40, 0.6);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255,255,255,0.04);
            border-radius: 28px;
            padding: 28px 30px;
            margin-bottom: 28px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.5);
        }
        .glass-card h5 { font-weight: 600; font-size: 16px; margin-bottom: 18px; color: #c8cee8; letter-spacing: 0.3px; }
        
        .row-form {
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
            align-items: center;
        }
        .row-form .input-dark {
            flex: 1 1 180px;
            background: #0f131f;
            border: 1px solid #20273f;
            border-radius: 16px;
            padding: 14px 16px;
            color: #f0f0f0;
            font-size: 14px;
            outline: none;
            transition: 0.2s;
            min-width: 130px;
            font-family: 'Inter', sans-serif;
        }
        .row-form .input-dark:focus {
            border-color: #7c3aed;
            background: #151c2e;
            box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
        }
        .btn-glow {
            background: linear-gradient(135deg, #7c3aed, #6d28d9);
            border: none;
            border-radius: 16px;
            color: white;
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
            transition: 0.2s;
            padding: 14px 24px;
            font-family: 'Inter', sans-serif;
            box-shadow: 0 6px 20px rgba(124, 58, 237, 0.2);
            flex: auto;
        }
        .btn-glow:hover { opacity: 0.9; transform: scale(1.01); }

        .list-item {
            display: flex; justify-content: space-between; align-items: center;
            padding: 16px 18px; background: #0f131f; border-radius: 18px; margin-bottom: 10px;
            border: 1px solid #181f32; transition: 0.2s;
        }
        .list-item:hover { border-color: #2d3555; background: #131a2a; }
        .list-item .info .name-site { font-weight: 600; font-size: 15px; color: #f0f0f0; }
        .list-item .info .meta { font-size: 13px; color: #6d759b; margin-top: 3px; }
        .list-item .actions { display: flex; align-items: center; gap: 14px; flex-wrap: wrap; }
        .badge-pass { background: #1e253f; color: #a0a9cf; padding: 5px 14px; border-radius: 40px; font-size: 12px; font-weight: 500; }
        .btn-del {
            background: transparent; border: 1px solid #3f2a3a; color: #b07a8a; padding: 6px 14px;
            border-radius: 30px; font-size: 12px; font-weight: 600; cursor: pointer; transition: 0.2s;
            font-family: 'Inter', sans-serif; text-decoration: none; display: inline-block;
        }
        .btn-del:hover { background: #2f1a24; border-color: #a05060; color: #ff8a9e; }
        .empty { text-align: center; color: #5a6180; padding: 30px 0; font-weight: 300; }
        .empty span { font-size: 28px; display: block; margin-bottom: 10px; }

        @media (max-width: 700px) {
            .row-form .input-dark { flex: 1 1 100%; }
            .btn-glow { width: 100%; justify-content: center; }
            .header { flex-direction: column; align-items: start; }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <div class="logo">🔐 <span>Vault</span></div>
        <div class="user-box">
            <span class="name"><?= htmlspecialchars($_SESSION['username']) ?></span>
            <a href="logout.php" class="btn-outline">Выйти</a>
        </div>
    </div>

    <div class="glass-card">
        <h5>➕ Добавить новый пароль</h5>
        <form method="POST" class="row-form">
            <input type="text" name="site_name" class="input-dark" placeholder="Название сайта" required>
            <input type="text" name="site_url" class="input-dark" placeholder="URL (необязательно)">
            <input type="text" name="login" class="input-dark" placeholder="Логин" required>
            <input type="password" name="password" class="input-dark" placeholder="Пароль" required>
            <button type="submit" name="add_password" class="btn-glow">Сохранить</button>
        </form>
    </div>

    <div class="glass-card">
        <h5>📂 Сохранённые пароли</h5>
        <?php if (count($passwords) === 0): ?>
            <div class="empty"><span>🔒</span> Паролей пока нет. Добавьте первый!</div>
        <?php else: ?>
            <?php foreach ($passwords as $item): ?>
                <div class="list-item">
                    <div class="info">
                        <div class="name-site"><?= htmlspecialchars($item['site_name']) ?></div>
                        <div class="meta">
                            <?= htmlspecialchars($item['login']) ?>
                            <?php if ($item['site_url']): ?> • <?= htmlspecialchars($item['site_url']) ?><?php endif; ?>
                        </div>
                    </div>
                    <div class="actions">
    <span class="badge-pass" id="pass-<?= $item['id'] ?>">••••••••</span>
    <button class="btn-outline" style="padding:4px 12px; font-size:12px;" onclick="togglePassword(<?= $item['id'] ?>, '<?= base64_decode($item['encrypted_password']) ?>')">Показать</button>
    <a href="?delete=<?= $item['id'] ?>" class="btn-del" onclick="return confirm('Точно удалить?')">Удалить</a>
</div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
<script>
function togglePassword(id, password) {
    const span = document.getElementById('pass-' + id);
    if (span.textContent === '••••••••') {
        span.textContent = password;
    } else {
        span.textContent = '••••••••';
    }
}
</script>
</body>
</html>