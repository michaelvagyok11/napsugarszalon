<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    if (adminLogin($username, $password)) {
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Hibás felhasználónév vagy jelszó.';
    }
}
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Admin – bejelentkezés</title>
</head>
<body>
    <h1>Admin Bejelentkezés</h1>
    <?php if ($error): ?>
        <p style="color: red;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <form method="post" action="">
        <div>
            <label for="username">Felhasználónév:</label>
            <input type="text" id="username" name="username" required>
        </div>
        <div>
            <label for="password">Jelszó:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit">Belépés</button>
    </form>
</body>
</html>
