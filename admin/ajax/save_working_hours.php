<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';

header('Content-Type: application/json');
requireAdminLogin();

$pdo = getPDO();

// A bevitt adatokat tömbként kapjuk
$actives = $_POST['active'] ?? [];
$starts = $_POST['start_time'] ?? [];
$ends = $_POST['end_time'] ?? [];

try {
    foreach ($starts as $wday => $stime) {
        $etime = $ends[$wday];
        $active = isset($actives[$wday]) ? 1 : 0;

        // Ha van rekord, frissítjük, különben beszúrjuk
        $stmt = $pdo->prepare("
            INSERT INTO working_hours (weekday, start_time, end_time, active)
            VALUES (:wd, :st, :et, :act)
            ON DUPLICATE KEY UPDATE
              start_time = VALUES(start_time),
              end_time = VALUES(end_time),
              active = VALUES(active)
        ");
        $stmt->execute([
            ':wd' => intval($wday),
            ':st' => $stime,
            ':et' => $etime,
            ':act' => $active
        ]);
    }
    echo json_encode(['success' => true, 'message' => 'Munkaidők sikeresen mentve.']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Hiba: ' . $e->getMessage()]);
}
