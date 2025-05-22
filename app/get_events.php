<?php
require_once 'db.php';
header('Content-Type: application/json');

$professor_id = isset($_GET['professor_id']) ? (int) $_GET['professor_id'] : null;
$user_id = isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
$user_role = isset($_SESSION['role']) ? $_SESSION['role'] : null;

if (!$user_id) {
    http_response_code(401);
    echo json_encode(['error' => 'User not authenticated']);
    exit;
}

try {
    $sql = "SELECT 
                e.id, 
                e.title, 
                e.description, 
                e.start_datetime AS start, 
                e.end_datetime AS end, 
                et.name as event_type, 
                e.location, 
                c.name as classroom_name,
                u_creator.email as created_by_email,
                GROUP_CONCAT(DISTINCT CONCAT(u_assignee.first_name, ' ', u_assignee.last_name)) as assigned_to_names
            FROM events e
            LEFT JOIN event_types et ON e.event_type_id = et.id
            LEFT JOIN classroom c ON e.classroom_id = c.id
            LEFT JOIN users u_creator ON e.created_by_user_id = u_creator.id
            LEFT JOIN user_events ue ON e.id = ue.event_id
            LEFT JOIN users u_assignee ON ue.user_id = u_assignee.id";

    $params = [];

    if ($user_role === 'professor') {
        // Professors see only their own events
        $sql .= " WHERE ue.user_id = :user_id";
        $params[':user_id'] = $user_id;
    } elseif ($user_role === 'admin') {
        // Admins can filter by professor_id, or see all if not specified
        if ($professor_id) {
            $sql .= " WHERE ue.user_id = :professor_id";
            $params[':professor_id'] = $professor_id;
        }
    } else {
        http_response_code(403);
        echo json_encode(['error' => 'Invalid role']);
        exit;
    }

    $sql .= " GROUP BY e.id ORDER BY e.start_datetime";

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($events);

} catch (PDOException $e) {
    http_response_code(500);
    // Consider logging the detailed error and returning a generic message to the client
    echo json_encode(['error' => 'Database error while fetching events: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'An unexpected error occurred: ' . $e->getMessage()]);
}
?>