<?php
$host = getenv('DB_HOST') ?: 'db'; // Service name from docker-compose.yml
$port = getenv('DB_PORT') ?: '5432';
$dbname = getenv('DB_NAME') ?: 'urschedule';
$user = getenv('DB_USER') ?: 'urschedule_user';
$password = getenv('DB_PASSWORD') ?: 'urschedule_pass';

try {
    $db = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    die();
}
?>