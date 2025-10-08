<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';

header('Content-Type: application/json');
requireAdminLogin();

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$name = trim($_POST['name'] ?? '');
$duration = intval($_POST['duration_minutes'] ?? 0);
$price = trim($_POST['price'] ?? '');
$active = isset($_POST['active']) ? 1 : 0;

if (!$name || !$duration || !$price) {
    echo json_encode(['success' => false, 'message' => 'Minden mező kitöltése kötelező.']);
    exit;
}

$pdo = getPDO();
try {
    if ($id) {
        // frissítés
        $stmt = $pdo->prepare("
            UPDATE services
              SET name = :nm, duration_minutes = :dur, price = :pr, active = :act
            WHERE id = :id
        ");
        $stmt->execute([
            ':nm' => $name,
            ':dur' => $duration,
            ':pr' => $price,
            ':act' => $active,
            ':id' => $id
        ]);
        echo json_encode(['success' => true, 'message' => 'Szolgáltatás frissítve.']);
    } else {
        // beszúrás
        $stmt = $pdo->prepare("
            INSERT INTO services (name, duration_minutes, price, active)
            VALUES (:nm, :dur, :pr, :act)
        ");
        $stmt->execute([
            ':nm' => $name,
            ':dur' => $duration,
            ':pr' => $price,
            ':act' => $active
        ]);
        echo json_encode(['success' => true, 'message' => 'Szolgáltatás hozzáadva.']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Hiba: ' . $e->getMessage()]);
}
