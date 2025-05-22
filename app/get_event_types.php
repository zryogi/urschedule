<?php
// filepath: /Users/khamsin/Development/urschedule/app/get_event_types.php
require_once 'db.php';
header('Content-Type: application/json');

try {
    $pdo = getDbConnection();
    $stmt = $pdo->query("SELECT id, type_name FROM event_types ORDER BY type_name");
    $event_types = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($event_types);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>