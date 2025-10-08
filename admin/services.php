<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

requireAdminLogin();

$pdo = getPDO();
$stmt = $pdo->query("SELECT * FROM services ORDER BY name");
$services = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Admin – Szolgáltatások</title>
</head>
<body>
    <h1>Szolgáltatások kezelése</h1>
    <form id="service-form">
        <h2>Új / Szerkeszt szolgáltatás</h2>
        <input type="hidden" name="id" id="svc-id" value="">
        <div>
            <label>Név:</label>
            <input type="text" name="name" id="svc-name" required>
        </div>
        <div>
            <label>Időtartam (perc):</label>
            <input type="number" name="duration_minutes" id="svc-duration" required>
        </div>
        <div>
            <label>Ár:</label>
            <input type="text" name="price" id="svc-price" required>
        </div>
        <div>
            <label>Aktív?</label>
            <input type="checkbox" name="active" id="svc-active" value="1" checked>
        </div>
        <button type="button" id="btn-save-service">Mentés</button>
    </form>

    <h2>Meglévő szolgáltatások</h2>
    <table border="1">
        <thead>
            <tr><th>ID</th><th>Név</th><th>Időtartam</th><th>Ár</th><th>Aktív?</th><th>Műveletek</th></tr>
        </thead>
        <tbody>
            <?php foreach ($services as $s): ?>
            <tr>
                <td><?= $s['id'] ?></td>
                <td><?= htmlspecialchars($s['name']) ?></td>
                <td><?= $s['duration_minutes'] ?></td>
                <td><?= $s['price'] ?></td>
                <td><?= $s['active'] ? 'Igen' : 'Nem' ?></td>
                <td>
                    <button class="btn-edit" data-id="<?= $s['id'] ?>"
                        data-name="<?= htmlspecialchars($s['name']) ?>"
                        data-duration="<?= $s['duration_minutes'] ?>"
                        data-price="<?= $s['price'] ?>"
                        data-active="<?= $s['active'] ?>">Szerkeszt</button>
                    <button class="btn-delete" data-id="<?= $s['id'] ?>">Törlés</button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div id="svc-msg"></div>

    <script>
        document.getElementById('btn-save-service').addEventListener('click', () => {
            const formData = new FormData(document.getElementById('service-form'));
            fetch('ajax/save_service.php', {
                method: 'POST',
                body: formData
            }).then(r => r.json()).then(obj => {
                document.getElementById('svc-msg').innerText = obj.message;
                if (obj.success) {
                    window.location.reload();
                }
            });
        });
        document.querySelectorAll('.btn-edit').forEach(btn => {
            btn.addEventListener('click', () => {
                document.getElementById('svc-id').value = btn.dataset.id;
                document.getElementById('svc-name').value = btn.dataset.name;
                document.getElementById('svc-duration').value = btn.dataset.duration;
                document.getElementById('svc-price').value = btn.dataset.price;
                document.getElementById('svc-active').checked = (btn.dataset.active == 1);
            });
        });
        document.querySelectorAll('.btn-delete').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.dataset.id;
                if (confirm('Biztos törlöd ezt a szolgáltatást?')) {
                    fetch('ajax/delete_service.php', {
                        method: 'POST',
                        body: new URLSearchParams({id: id})
                    }).then(r => r.json()).then(obj => {
                        alert(obj.message);
                        if (obj.success) window.location.reload();
                    });
                }
            });
        });
    </script>
</body>
</html>
