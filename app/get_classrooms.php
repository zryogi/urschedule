<?php
// filepath: /Users/khamsin/Development/urschedule/app/get_classrooms.php
require_once 'db.php';
header('Content-Type: application/json');

$campus_id = isset($_GET['campus_id']) ? (int) $_GET['campus_id'] : null;

if (!$campus_id) {
    echo json_encode([]); // Return empty if no campus_id is provided
    exit;
}

try {
    $pdo = getDbConnection();
    $stmt = $pdo->prepare("SELECT id, classroom_name FROM classroom WHERE campus_id = :campus_id ORDER BY classroom_name");
    $stmt->bindParam(':campus_id', $campus_id, PDO::PARAM_INT);
    $stmt->execute();
    $classrooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($classrooms);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>