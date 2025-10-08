<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';

header('Content-Type: application/json');
requireAdminLogin();

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
if (!$id) {
    echo json_encode(['success' => false, 'message' => 'Érvénytelen ID.']);
    exit;
}

$pdo = getPDO();

try {
    $stmt = $pdo->prepare("DELETE FROM services WHERE id = :id");
    $stmt->execute([':id' => $id]);
    echo json_encode(['success' => true, 'message' => 'Szolgáltatás törölve.']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Hiba: ' . $e->getMessage()]);
}
