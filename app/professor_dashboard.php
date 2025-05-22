<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'professor') {
    header("Location: login.php?error=Access denied");
    exit;
}
// Professor dashboard content goes here
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Professor Dashboard - URWorkPlan</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.17/index.global.min.js'></script>
    <link rel="stylesheet" href="css/styles.css">
</head>

<body class="dashboard-page">
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1>Professor Dashboard</h1>
            <p class="welcome-message">Welcome, <?php echo htmlspecialchars($_SESSION['email']); ?>! <a
                    href="logout.php" class="btn btn-danger btn-sm">Logout</a></p>
        </div>

        <div id='calendar'></div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek' // Added listWeek view
                },
                events: 'get_events.php', // Load events from the backend
                editable: false, // Professors should only read
                selectable: false // Professors should only read
            });
            calendar.render();
        });
    </script>
</body>

</html>