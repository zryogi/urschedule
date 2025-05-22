<?php
session_start(); // Ensure session is started
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

// Define a color map for event types
// These colors are just examples, feel free to adjust them
$eventTypeColors = [
    'lecture' => '#007bff', // Blue
    'seminar' => '#6610f2', // Indigo
    'talk' => '#6f42c1', // Purple
    'office_hours' => '#e83e8c', // Pink
    'meeting' => '#dc3545', // Red
    'conference_presentation' => '#fd7e14', // Orange
    'workshop' => '#ffc107', // Yellow
    'other' => '#28a745', // Green
    'default' => '#6c757d' // Gray for any other type
];

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
                campus.name as campus_name, -- Added campus_name
                u_creator.email as created_by_email,
                STRING_AGG(DISTINCT CONCAT(u_assignee.first_name, ' ', u_assignee.last_name), ', ') as assigned_to_names
            FROM events e
            LEFT JOIN event_types et ON e.event_type_id = et.id
            LEFT JOIN classroom c ON e.classroom_id = c.id
            LEFT JOIN campus ON c.campus_id = campus.id -- Joined campus table
            LEFT JOIN users u_creator ON e.created_by_user_id = u_creator.id
            LEFT JOIN user_events ue ON e.id = ue.event_id
            LEFT JOIN users u_assignee ON ue.user_id = u_assignee.id";

    // Add these fields to the SELECT statement
    $sql = str_replace(
        "STRING_AGG(DISTINCT CONCAT(u_assignee.first_name, ' ', u_assignee.last_name), ', ') as assigned_to_names",
        "STRING_AGG(DISTINCT CONCAT(u_assignee.first_name, ' ', u_assignee.last_name), ', ') as assigned_to_names, e.event_type_id, e.classroom_id, c.campus_id AS campus_id_for_event, STRING_AGG(DISTINCT CAST(ue.user_id AS VARCHAR), ',') as assigned_professor_ids",
        $sql
    );

    $params = [];

    if ($user_role === 'professor') {
        // Professors see only their own events
        // The main query already joins user_events ue, so we filter on that.
        $sql .= " WHERE ue.user_id = :user_id";
        $params[':user_id'] = $user_id;
    } elseif ($user_role === 'admin') {
        // Admins can filter by professor_id, or see all if not specified
        if ($professor_id) {
            // If a specific professor is selected, filter events to only those they are assigned to.
            // The STRING_AGG will still show all professors for that event.
            $sql .= " WHERE e.id IN (SELECT DISTINCT ue_filter.event_id FROM user_events ue_filter WHERE ue_filter.user_id = :professor_id_filter)";
            $params[':professor_id_filter'] = $professor_id;
        }
        // If no $professor_id (i.e., "All Professors"), no additional WHERE clause is added here,
        // so all events are fetched, and STRING_AGG lists their assigned professors.
    } else {
        http_response_code(403);
        echo json_encode(['error' => 'Invalid role']);
        exit;
    }

    // Added campus.name to GROUP BY
    $sql .= " GROUP BY e.id, et.name, c.name, campus.name, u_creator.email, c.campus_id ORDER BY e.start_datetime";

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Add color to each event based on its type
    foreach ($events as &$event) { // Use reference to modify array directly
        $typeKey = strtolower($event['event_type']);
        $event['color'] = isset($eventTypeColors[$typeKey]) ? $eventTypeColors[$typeKey] : $eventTypeColors['default'];
    }
    unset($event); // Unset reference to last element

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