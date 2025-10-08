<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';

requireAdminLogin();
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Admin – Vezérlőpult</title>
</head>
<body>
    <h1>Üdv, <?= htmlspecialchars($_SESSION['admin_username']) ?></h1>
    <nav>
        <ul>
            <li><a href="working_hours.php">Munkaidő beállítás</a></li>
            <li><a href="services.php">Szolgáltatások kezelése</a></li>
            <li><a href="bookings.php">Foglalások kezelése</a></li>
            <li><a href="logout.php">Kijelentkezés</a></li>
        </ul>
    </nav>
</body>
</html>
