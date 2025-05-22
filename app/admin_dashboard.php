<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php?error=Access denied");
    exit;
}
// Admin dashboard content goes here
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - URWorkPlan</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.17/index.global.min.js'></script>
    <link rel="stylesheet" href="css/styles.css">
</head>

<body class="dashboard-page">
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1>Admin Dashboard</h1>
            <p class="welcome-message">Welcome, <?php echo htmlspecialchars($_SESSION['email']); ?>! <a
                    href="logout.php" class="btn btn-danger btn-sm">Logout</a></p>
        </div>

        <div class="dashboard-controls">
            <div class="form-group">
                <label for="professorSelector">Select Professor:</label>
                <select id="professorSelector" class="form-control">
                    <option value="">All Professors</option>
                    <!-- Options will be populated by JavaScript -->
                </select>
            </div>
        </div>

        <div id='calendar'></div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var calendarEl = document.getElementById('calendar');
            var professorSelector = document.getElementById('professorSelector');
            var calendar;

            function fetchProfessors() {
                fetch('get_professors.php')
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            console.error('Error fetching professors:', data.error);
                            alert('Could not load professors.');
                            return;
                        }
                        data.forEach(function (professor) {
                            var option = document.createElement('option');
                            option.value = professor.id;
                            option.textContent = professor.first_name + ' ' + professor.last_name + ' (' + professor.email + ')';
                            professorSelector.appendChild(option);
                        });
                        // Initialize Select2 after options are loaded
                        $(professorSelector).select2({
                            placeholder: "Select a professor",
                            allowClear: true
                        });
                    })
                    .catch(error => {
                        console.error('Error fetching professors:', error);
                        alert('Could not load professors.');
                    });
            }

            function initializeCalendar(professorId = null) {
                let eventSourcesUrl = 'get_events.php';
                if (professorId) {
                    eventSourcesUrl += '?professor_id=' + professorId;
                }

                if (calendar) {
                    calendar.destroy(); // Destroy existing calendar instance if any
                }

                calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    headerToolbar: {
                        left: 'prev,next today addEventButton', // Added addEventButton
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
                    },
                    editable: true, // Important for admin
                    selectable: true, // Important for admin
                    events: eventSourcesUrl,
                    customButtons: {
                        addEventButton: {
                            text: 'Add Event',
                            click: function () {
                                // Implement your add event logic here
                                // For example, open a modal or redirect to an add event page
                                alert('Add Event button clicked! Implement functionality.');
                                // var dateStr = prompt('Enter a date in YYYY-MM-DD format');
                                // var titleStr = prompt('Enter event title');
                                // var date = new Date(dateStr + 'T00:00:00'); // will be in local time

                                // if (!isNaN(date.valueOf()) && titleStr) { // valid date
                                // calendar.addEvent({
                                // title: titleStr,
                                // start: date,
                                // allDay: true
                                // });
                                // alert('Great. Now, update your database...');
                                // } else {
                                // alert('Invalid date or title.');
                                // }
                            }
                        }
                    },
                    select: function (info) {
                        // Implement logic for when a date/time is selected
                        alert('Selected ' + info.startStr + ' to ' + info.endStr);
                        // You could open a modal here to create a new event
                        // Example:
                        // var title = prompt('Enter Event Title:');
                        // if (title) {
                        //     calendar.addEvent({
                        //         title: title,
                        //         start: info.startStr,
                        //         end: info.endStr,
                        //         allDay: info.allDay
                        //     });
                        //     // Here you would also make an AJAX call to save the event to the database
                        // }
                        // calendar.unselect();
                    },
                    eventClick: function (info) {
                        // Implement logic for when an event is clicked
                        alert('Event: ' + info.event.title + '\\nDescription: ' + (info.event.extendedProps.description || 'N/A') + '\\nAssigned to: ' + (info.event.extendedProps.assigned_to_names || 'N/A'));
                        // You could open a modal here to edit the event
                        // info.jsEvent.preventDefault(); // prevent browser from navigating to event's URL (if it has one)
                    },
                    eventDrop: function (info) {
                        // Implement logic for when an event is dragged and dropped
                        alert(info.event.title + " was dropped on " + info.event.start.toISOString() + ". Update database.");
                        // Here you would make an AJAX call to update the event's date/time in the database
                        // if (!confirm("Are you sure about this change?")) {
                        //  info.revert();
                        // }
                    },
                    eventResize: function (info) {
                        // Implement logic for when an event is resized
                        alert(info.event.title + " end is now " + info.event.end.toISOString() + ". Update database.");
                        // Here you would make an AJAX call to update the event's duration in the database
                        // if (!confirm("Are you sure about this change?")) {
                        //  info.revert();
                        // }
                    }
                    // You might want to add more FullCalendar options like:
                    // eventDidMount: function(info) {
                    //    // Modify event element, e.g., add a tooltip
                    // },
                });
                calendar.render();
            }

            professorSelector.addEventListener('change', function () {
                initializeCalendar(this.value);
            });

            fetchProfessors();
            initializeCalendar(); // Initialize with all professors' events initially
        });
    </script>
</body>

</html>