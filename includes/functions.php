<?php
// functions.php
require_once __DIR__ . '/db.php';

/**
 * Lekéri az aktív szolgáltatásokat.
 * @return array
 */
function getActiveServices(): array {
    $pdo = getPDO();
    $stmt = $pdo->query("SELECT * FROM services WHERE active = 1 ORDER BY name ASC");
    return $stmt->fetchAll();
}

/**
 * Lekéri az admin által beállított munkarendet (munkaidőket).
 * @return array — index weekday → ['start_time' => ..., 'end_time' => ..., 'active' => ...]
 */
function getWorkingHours(): array {
    $pdo = getPDO();
    $stmt = $pdo->query("SELECT * FROM working_hours");
    $all = $stmt->fetchAll();
    $out = [];
    foreach ($all as $row) {
        $out[$row['weekday']] = [
            'start_time' => $row['start_time'],
            'end_time' => $row['end_time'],
            'active' => (bool)$row['active']
        ];
    }
    return $out;
}

/**
 * Ellenőrzi, hogy egy adott szolgáltatás lefoglalható-e adott kezdő időpontban az adott dátumon.
 * Visszaad true-t, ha szabad.
 *
 * @param int $service_id
 * @param string $date — 'YYYY-MM-DD'
 * @param string $start_time — 'HH:MM:SS'
 * @return bool
 */
function isTimeSlotAvailable(int $service_id, string $date, string $start_time): bool {
    $pdo = getPDO();

    // Először lekérjük a szolgáltatás időtartamát
    $stmt = $pdo->prepare("SELECT duration_minutes FROM services WHERE id = :id AND active = 1");
    $stmt->execute([':id' => $service_id]);
    $svc = $stmt->fetch();
    if (!$svc) {
        return false;
    }
    $duration = intval($svc['duration_minutes']);

    // Kiszámoljuk az end időt
    $start = new DateTime("$date $start_time");
    $end = clone $start;
    $end->modify("+{$duration} minutes");
    $end_time = $end->format('H:i:s');

    // Keresünk ütköző foglalást
    $stmt2 = $pdo->prepare("
        SELECT * FROM bookings
        WHERE status = 'booked'
          AND booking_date = :bdate
          AND (
              (booking_start < :end_time AND booking_end > :start_time)
          )
    ");
    $stmt2->execute([
        ':bdate' => $date,
        ':start_time' => $start_time,
        ':end_time' => $end_time
    ]);
    $collision = $stmt2->fetch();
    return !$collision;
}

/**
 * Lekéri egy adott napra a lehetséges (szabad) induló időpontokat szolgáltatás alapján.
 *
 * @param int $service_id
 * @param string $date — 'YYYY-MM-DD'
 * @return array list of times in 'HH:MM:SS' formátumban
 */
function getAvailableTimesForDate(int $service_id, string $date): array {
    $hours = getWorkingHours();
    $weekday = (int)date('w', strtotime($date)); // 0–6

    if (!isset($hours[$weekday]) || !$hours[$weekday]['active']) {
        return [];
    }
    $start = $hours[$weekday]['start_time'];
    $end = $hours[$weekday]['end_time'];

    // Szolgáltatás időtartama
    $pdo = getPDO();
    $stmt = $pdo->prepare("SELECT duration_minutes FROM services WHERE id = :id AND active = 1");
    $stmt->execute([':id' => $service_id]);
    $svc = $stmt->fetch();
    if (!$svc) {
        return [];
    }
    $duration = intval($svc['duration_minutes']);

    $interval = new DateInterval('PT15M'); // 15 perces lépések
    $times = [];

    $dtStart = new DateTime("$date $start");
    $dtEndLimit = new DateTime("$date $end");
    // Az utolsó indulási időpont, hogy beleférjen a szolgáltatás
    $dtEndLimit->modify("-{$duration} minutes");

    for ($dt = clone $dtStart; $dt <= $dtEndLimit; $dt->add($interval)) {
        $ts = $dt->format('H:i:s');
        if (isTimeSlotAvailable($service_id, $date, $ts)) {
            $times[] = $ts;
        }
    }

    return $times;
}
