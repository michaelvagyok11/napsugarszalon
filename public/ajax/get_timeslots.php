<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/functions.php';

header('Content-Type: application/json');

$service_id = isset($_GET['service_id']) ? intval($_GET['service_id']) : 0;
$date = isset($_GET['date']) ? $_GET['date'] : '';

if (!$service_id || !$date) {
    echo json_encode(['timeslots' => []]);
    exit;
}

$times = getAvailableTimesForDate($service_id, $date);
echo json_encode(['timeslots' => $times]);
