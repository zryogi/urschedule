<?php
require_once 'db.php';
header('Content-Type: application/json');

try {
    $stmt = $db->prepare("
        SELECT u.id, u.first_name, u.last_name, u.email 
        FROM users u
        JOIN roles r ON u.role_id = r.id
        WHERE r.name = 'professor'
        ORDER BY u.last_name, u.first_name
    ");
    $stmt->execute();
    $professors = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($professors);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>