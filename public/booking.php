<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

$services = getActiveServices();
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Foglalás – Napsugár Szalon</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <h1>Foglalj időpontot</h1>
    <form id="booking-form" method="post" action="cancel.php"> <!-- ide módosítás: submit AJAX -->
        <div>
            <label for="service">Szolgáltatás:</label>
            <select name="service_id" id="service">
                <option value="">-- válassz --</option>
                <?php foreach ($services as $s): ?>
                    <option value="<?= htmlspecialchars($s['id']) ?>">
                        <?= htmlspecialchars($s['name']) ?> — <?= htmlspecialchars($s['price']) ?> Ft
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label for="date">Dátum:</label>
            <input type="date" id="date" name="booking_date" min="<?= date('Y-m-d') ?>">
        </div>
        <div>
            <label for="time-slot">Elérhető időpontok:</label>
            <select name="booking_time" id="time-slot">
                <option value="">-- először válassz dátumot és szolgáltatást --</option>
            </select>
        </div>
        <div>
            <label for="name">Teljes név:</label>
            <input type="text" id="name" name="client_name" required>
        </div>
        <div>
            <label for="email">E-mail:</label>
            <input type="email" id="email" name="client_email" required>
        </div>
        <div>
            <label for="phone">Telefonszám:</label>
            <input type="text" id="phone" name="client_phone" required>
        </div>
        <button type="button" id="btn-submit">Foglalás</button>
    </form>

    <div id="message"></div>

    <script src="assets/js/main.js"></script>
</body>
</html>
