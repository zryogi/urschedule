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
    <div id="loading-overlay">
        <p>Loading your schedule...</p>
    </div>
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1>Professor Dashboard</h1>
            <p class="welcome-message">Welcome, <?php echo htmlspecialchars($_SESSION['email']); ?>! <a
                    href="logout.php" class="btn btn-danger btn-sm">Logout</a></p>
        </div>

        <div id='calendar'></div>
    </div>

    <!-- Event Detail Modal -->
    <div class="modal fade" id="eventDetailModal" tabindex="-1" aria-labelledby="eventDetailModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eventDetailModalLabel">Event Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label"><strong>Title:</strong></label>
                        <div class="col-sm-9">
                            <input type="text" readonly class="form-control-plaintext" id="eventTitle" value="">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label"><strong>Description:</strong></label>
                        <div class="col-sm-9">
                            <textarea readonly class="form-control-plaintext" id="eventDescription" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label"><strong>Start:</strong></label>
                        <div class="col-sm-9">
                            <input type="text" readonly class="form-control-plaintext" id="eventStart" value="">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label"><strong>End:</strong></label>
                        <div class="col-sm-9">
                            <input type="text" readonly class="form-control-plaintext" id="eventEnd" value="">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label"><strong>Event Type:</strong></label>
                        <div class="col-sm-9">
                            <input type="text" readonly class="form-control-plaintext" id="eventType" value="">
                        </div>
                    </div>
                    <div class="form-group row" id="eventLocationRow">
                        <label class="col-sm-3 col-form-label"><strong>Location:</strong></label>
                        <div class="col-sm-9">
                            <input type="text" readonly class="form-control-plaintext" id="eventLocation" value="">
                        </div>
                    </div>
                    <div class="form-group row" id="eventCampusRow">
                        <label class="col-sm-3 col-form-label"><strong>Campus:</strong></label>
                        <div class="col-sm-9">
                            <input type="text" readonly class="form-control-plaintext" id="eventCampus" value="">
                        </div>
                    </div>
                    <div class="form-group row" id="eventClassroomRow">
                        <label class="col-sm-3 col-form-label"><strong>Classroom:</strong></label>
                        <div class="col-sm-9">
                            <input type="text" readonly class="form-control-plaintext" id="eventClassroom" value="">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label"><strong>Created By:</strong></label>
                        <div class="col-sm-9">
                            <input type="text" readonly class="form-control-plaintext" id="eventCreatedBy" value="">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label"><strong>Assigned To:</strong></label>
                        <div class="col-sm-9">
                            <input type="text" readonly class="form-control-plaintext" id="eventAssignedTo" value="">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var calendarEl = document.getElementById('calendar');
            var calendar; // Keep professorSelector if it exists elsewhere, or remove if only for admin
            var loadingOverlay = document.getElementById('loading-overlay');

            function showLoading() {
                loadingOverlay.classList.add('visible');
            }

            function hideLoading() {
                loadingOverlay.classList.remove('visible');
            }

            function initializeCalendar() {
                showLoading();
                let eventSourcesUrl = 'get_events.php'; // Professor dashboard always fetches own events

                if (calendar) {
                    calendar.destroy();
                }

                calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
                    },
                    events: {
                        url: eventSourcesUrl,
                        failure: function () {
                            alert('There was an error while fetching your schedule!');
                            hideLoading();
                        }
                    },
                    loading: function (isLoading) {
                        if (!isLoading) {
                            hideLoading();
                        }
                    },
                    editable: false, // Professor calendar is read-only
                    selectable: false, // Professor cannot select dates to add events
                    eventClick: function (info) {
                        document.getElementById('eventTitle').value = info.event.title;
                        document.getElementById('eventDescription').value = info.event.extendedProps.description || 'N/A';

                        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
                        document.getElementById('eventStart').value = info.event.start ? info.event.start.toLocaleString(undefined, options) : 'N/A';
                        document.getElementById('eventEnd').value = info.event.end ? info.event.end.toLocaleString(undefined, options) : 'N/A';

                        document.getElementById('eventType').value = info.event.extendedProps.event_type || 'N/A';

                        // Handle exclusive display of location or campus/classroom
                        const location = info.event.extendedProps.location;
                        const campusName = info.event.extendedProps.campus_name;
                        const classroomName = info.event.extendedProps.classroom_name;

                        if (location) {
                            document.getElementById('eventLocation').value = location;
                            document.getElementById('eventLocationRow').style.display = 'flex';
                            document.getElementById('eventCampusRow').style.display = 'none';
                            document.getElementById('eventClassroomRow').style.display = 'none';
                        } else {
                            document.getElementById('eventCampus').value = campusName || 'N/A';
                            document.getElementById('eventClassroom').value = classroomName || 'N/A';
                            document.getElementById('eventLocationRow').style.display = 'none';
                            document.getElementById('eventCampusRow').style.display = 'flex';
                            document.getElementById('eventClassroomRow').style.display = 'flex';
                        }

                        document.getElementById('eventCreatedBy').value = info.event.extendedProps.created_by_email || 'N/A';
                        // Assigned to is not strictly necessary for professor view if they only see their own events, but can be kept for consistency
                        document.getElementById('eventAssignedTo').value = info.event.extendedProps.assigned_to_names || 'N/A';

                        $('#eventDetailModal').modal('show');
                    }
                });
                calendar.render();
            }

            initializeCalendar();
        });
    </script>
</body>

</html>