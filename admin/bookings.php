<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

requireAdminLogin();

$pdo = getPDO();
$stmt = $pdo->query("
    SELECT b.*, s.name AS service_name
    FROM bookings b
    JOIN services s ON b.service_id = s.id
    ORDER BY b.booking_date DESC, b.booking_start DESC
");
$bookings = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Admin – Foglalások kezelése</title>
</head>
<body>
    <h1>Foglalások</h1>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th><th>Szolgáltatás</th><th>Dátum</th><th>Idő</th><th>Név</th><th>Email</th><th>Telefon</th><th>Státusz</th><th>Műveletek</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($bookings as $b): ?>
            <tr>
                <td><?= $b['id'] ?></td>
                <td><?= htmlspecialchars($b['service_name']) ?></td>
                <td><?= htmlspecialchars($b['booking_date']) ?></td>
                <td><?= htmlspecialchars($b['booking_start']) ?> – <?= htmlspecialchars($b['booking_end']) ?></td>
                <td><?= htmlspecialchars($b['client_name']) ?></td>
                <td><?= htmlspecialchars($b['client_email']) ?></td>
                <td><?= htmlspecialchars($b['client_phone']) ?></td>
                <td><?= htmlspecialchars($b['status']) ?></td>
                <td>
                    <?php if ($b['status'] === 'booked'): ?>
                        <button class="btn-cancel" data-id="<?= $b['id'] ?>">Lemond</button>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div id="book-msg"></div>
    <script>
        document.querySelectorAll('.btn-cancel').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.dataset.id;
                if (confirm('Biztos lemondod ezt a foglalást?')) {
                    fetch('ajax/change_booking_status.php', {
                        method: 'POST',
                        body: new URLSearchParams({id: id, status: 'cancelled'})
                    }).then(r => r.json()).then(obj => {
                        document.getElementById('book-msg').innerText = obj.message;
                        if (obj.success) {
                            window.location.reload();
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>
