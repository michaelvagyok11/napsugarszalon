<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/mailer.php';

header('Content-Type: application/json');

$service_id = isset($_POST['service_id']) ? intval($_POST['service_id']) : 0;
$date = isset($_POST['booking_date']) ? $_POST['booking_date'] : '';
$time = isset($_POST['booking_time']) ? $_POST['booking_time'] : '';
$name = isset($_POST['client_name']) ? trim($_POST['client_name']) : '';
$email = isset($_POST['client_email']) ? trim($_POST['client_email']) : '';
$phone = isset($_POST['client_phone']) ? trim($_POST['client_phone']) : '';
$cancel_token = bin2hex(random_bytes(16)); // 32 karakter

// Egyszerű validáció
if (!$service_id || !$date || !$time || !$name || !$email || !$phone) {
    echo json_encode(['success' => false, 'message' => 'Minden mező kötelező.']);
    exit;
}

// Ellenőrizzük, hogy az időpont még elérhető-e
if (!isTimeSlotAvailable($service_id, $date, $time)) {
    echo json_encode(['success' => false, 'message' => 'Ez az időpont már foglalt.']);
    exit;
}

// Kiszámoljuk az end időpontot
$pdo = getPDO();
$stmt = $pdo->prepare("SELECT duration_minutes FROM services WHERE id = :id");
$stmt->execute([':id' => $service_id]);
$svc = $stmt->fetch();
if (!$svc) {
    echo json_encode(['success' => false, 'message' => 'Ismeretlen szolgáltatás.']);
    exit;
}
$duration = intval($svc['duration_minutes']);
$dtStart = new DateTime("$date $time");
$dtEnd = clone $dtStart;
$dtEnd->modify("+{$duration} minutes");
$end_time = $dtEnd->format('H:i:s');

try {
    $stmt2 = $pdo->prepare("
        INSERT INTO bookings
            (service_id, client_name, client_email, client_phone, booking_date, booking_start, booking_end, cancel_token)
        VALUES
            (:sid, :cn, :ce, :cp, :bdate, :bst, :bend, :ctoken)
    ");
    $stmt2->execute([
        ':sid'    => $service_id,
        ':cn'     => $name,
        ':ce'     => $email,
        ':cp'     => $phone,
        ':bdate'  => $date,
        ':bst'    => $time,
        ':bend'   => $end_time,
        ':ctoken' => $cancel_token
    ]);
    $bookingId = $pdo->lastInsertId();

    // Készítjük a lemondási linket
    $cancelLink = BASE_URL . 'cancel.php?token=' . urlencode($cancel_token);

    // Admin értesítés
    $adminBody = "<p>Új foglalás érkezett:</p>
        <ul>
            <li>Szolgáltatás ID: {$service_id}</li>
            <li>Időpont: {$date} {$time} – {$end_time}</li>
            <li>Név: " . htmlspecialchars($name) . "</li>
            <li>E-mail: " . htmlspecialchars($email) . "</li>
            <li>Telefon: " . htmlspecialchars($phone) . "</li>
        </ul>";
    sendEmail(ADMIN_EMAIL, 'Admin', 'Új foglalás', $adminBody);

    // Kliens visszaigazolás
    $clientBody = "<p>Kedves " . htmlspecialchars($name) . ",</p>
        <p>Foglalásod megerősítésre került:</p>
        <ul>
          <li>Szolgáltatás ID: {$service_id}</li>
          <li>Időpont: {$date} {$time} – {$end_time}</li>
        </ul>
        <p>Ha le szeretnéd mondani a foglalást, kattints ide:<br>
        <a href=\"{$cancelLink}\">Foglalás lemondása</a></p>
        <p>Üdvözlettel,<br>Napsugár Szalon</p>";
    sendEmail($email, $name, 'Foglalás megerősítése', $clientBody);

    echo json_encode(['success' => true, 'message' => 'Sikeres foglalás! Visszaigazoló e-mailt küldtünk.']);
    exit;

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Hiba történt az adatbázis művelet során.']);
    exit;
}
