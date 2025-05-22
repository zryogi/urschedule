<?php
// filepath: /Users/khamsin/Development/urschedule/app/get_campuses.php
require_once 'db.php';
header('Content-Type: application/json');

try {
    $pdo = getDbConnection();
    $stmt = $pdo->query("SELECT id, campus_name FROM campus ORDER BY campus_name");
    $campuses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($campuses);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>