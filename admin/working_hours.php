<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

requireAdminLogin();

$hours = getWorkingHours();
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Admin – Munkaidő beállítás</title>
</head>
<body>
    <h1>Munkaidő beállítás</h1>
    <form id="working-hours-form">
        <table border="1">
            <thead>
                <tr><th>Nap</th><th>Dolgozik?</th><th>Kezdő idő</th><th>Záró idő</th></tr>
            </thead>
            <tbody>
                <?php
                $days = ['Vas','Hét','Ked','Sze','Csü','Pén','Szo'];
                for ($d = 0; $d < 7; $d++):
                    $row = $hours[$d] ?? ['start_time' => '09:00:00', 'end_time' => '17:00:00', 'active' => 0];
                ?>
                <tr>
                    <td><?= $days[$d] ?></td>
                    <td>
                        <input type="checkbox" name="active[<?= $d ?>]" value="1" <?= $row['active'] ? 'checked' : '' ?>>
                    </td>
                    <td>
                        <input type="time" name="start_time[<?= $d ?>]" value="<?= htmlspecialchars($row['start_time']) ?>">
                    </td>
                    <td>
                        <input type="time" name="end_time[<?= $d ?>]" value="<?= htmlspecialchars($row['end_time']) ?>">
                    </td>
                </tr>
                <?php endfor; ?>
            </tbody>
        </table>
        <button type="button" id="btn-save-hours">Mentés</button>
    </form>
    <div id="msg"></div>
    <script>
        document.getElementById('btn-save-hours').addEventListener('click', () => {
            const formData = new FormData(document.getElementById('working-hours-form'));
            fetch('ajax/save_working_hours.php', {
                method: 'POST',
                body: formData
            })
            .then(resp => resp.json())
            .then(obj => {
                document.getElementById('msg').innerText = obj.message;
            });
        });
    </script>
</body>
</html>
