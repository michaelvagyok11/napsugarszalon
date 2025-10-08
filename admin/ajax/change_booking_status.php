<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/mailer.php';

header('Content-Type: application/json');
requireAdminLogin();

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$status = $_POST['status'] ?? '';

if (!$id || !in_array($status, ['booked','cancelled'])) {
    echo json_encode(['success' => false, 'message' => 'Érvénytelen kérés.']);
    exit;
}

$pdo = getPDO();
$stmt = $pdo->prepare("SELECT * FROM bookings WHERE id = :id");
$stmt->execute([':id' => $id]);
$booking = $stmt->fetch();
if (!$booking) {
    echo json_encode(['success' => false, 'message' => 'Foglalás nem található.']);
    exit;
}

try {
    $stmt2 = $pdo->prepare("UPDATE bookings SET status = :st WHERE id = :id");
    $stmt2->execute([':st' => $status, ':id' => $id]);

    // E-mail értesítés kliensnek
    $clientBody = "<p>Kedves " . htmlspecialchars($booking['client_name']) . ",</p>";
    if ($status === 'cancelled') {
        $clientBody .= "<p>A foglalásod lemondásra került.</p>";
    }
    $clientBody .= "<p>Üdvözlettel,<br>Napsugár Szalon</p>";
    sendEmail($booking['client_email'], $booking['client_name'], 'Foglalás változás', $clientBody);

    echo json_encode(['success' => true, 'message' => 'Státusz frissítve, értesítés elküldve.']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Hiba: ' . $e->getMessage()]);
}
