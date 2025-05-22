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
    <div id="loading-overlay">
        <p>Loading events...</p>
    </div>
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

    <!-- Event Detail Modal -->
    <div class="modal fade" id="eventDetailModal" tabindex="-1" aria-labelledby="eventDetailModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eventDetailModalLabel">Event Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3 row">
                        <label class="col-sm-4 col-form-label">Title:</label>
                        <div class="col-sm-8">
                            <input type="text" readonly class="form-control-plaintext" id="eventTitle">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label class="col-sm-4 col-form-label">Description:</label>
                        <div class="col-sm-8">
                            <textarea readonly class="form-control-plaintext" id="eventDescription" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label class="col-sm-4 col-form-label">Start:</label>
                        <div class="col-sm-8">
                            <input type="date" readonly class="form-control-plaintext" id="eventStartDate">
                            <input type="time" readonly class="form-control-plaintext mt-1" id="eventStartTime">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label class="col-sm-4 col-form-label">End:</label>
                        <div class="col-sm-8">
                            <input type="date" readonly class="form-control-plaintext" id="eventEndDate">
                            <input type="time" readonly class="form-control-plaintext mt-1" id="eventEndTime">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label class="col-sm-4 col-form-label">Type:</label>
                        <div class="col-sm-8">
                            <input type="text" readonly class="form-control-plaintext" id="eventType">
                        </div>
                    </div>
                    <div class="mb-3 row" id="eventLocationRow">
                        <label class="col-sm-4 col-form-label">Location:</label>
                        <div class="col-sm-8">
                            <input type="text" readonly class="form-control-plaintext" id="eventLocation">
                        </div>
                    </div>
                    <div class="mb-3 row" id="eventCampusRow">
                        <label class="col-sm-4 col-form-label">Campus:</label>
                        <div class="col-sm-8">
                            <input type="text" readonly class="form-control-plaintext" id="eventCampus">
                        </div>
                    </div>
                    <div class="mb-3 row" id="eventClassroomRow">
                        <label class="col-sm-4 col-form-label">Classroom:</label>
                        <div class="col-sm-8">
                            <input type="text" readonly class="form-control-plaintext" id="eventClassroom">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label class="col-sm-4 col-form-label">Created By:</label>
                        <div class="col-sm-8">
                            <input type="text" readonly class="form-control-plaintext" id="eventCreatedBy">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label class="col-sm-4 col-form-label">Assigned To:</label>
                        <div class="col-sm-8">
                            <input type="text" readonly class="form-control-plaintext" id="eventAssignedTo">
                        </div>
                    </div>
                    <!-- Admin-specific fields for editing can be added here later -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <!-- Admin-specific buttons like Edit/Delete can be added here later -->
                    <button type="button" class="btn btn-primary" id="saveEventButton">Save changes</button>
                    <button type="button" class="btn btn-danger" id="deleteEventButton">Delete Event</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Event Modal -->
    <div class="modal fade" id="addEditEventModal" tabindex="-1" aria-labelledby="addEditEventModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addEditEventModalLabel">Add/Edit Event</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="addEditEventForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="editEventTitle" class="form-label">Title</label>
                            <input type="text" class="form-control" id="editEventTitle" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="editEventDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="editEventDescription" name="description"
                                rows="3"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="editEventStartDate" class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="editEventStartDate"
                                    name="editEventStartDate_form" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="editEventStartTime" class="form-label">Start Time</label>
                                <input type="time" class="form-control" id="editEventStartTime"
                                    name="editEventStartTime_form" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="editEventEndDate" class="form-label">End Date</label>
                                <input type="date" class="form-control" id="editEventEndDate"
                                    name="editEventEndDate_form" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="editEventEndTime" class="form-label">End Time</label>
                                <input type="time" class="form-control" id="editEventEndTime"
                                    name="editEventEndTime_form" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="editEventType" class="form-label">Event Type</label>
                            <select class="form-control" id="editEventType" name="event_type_id" required>
                                <!-- Options will be populated by JavaScript -->
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editEventLocationType" class="form-label">Location Type</label>
                            <select class="form-control" id="editEventLocationType" name="location_type">
                                <option value="specific_location">Specific Location</option>
                                <option value="campus_classroom">Campus & Classroom</option>
                            </select>
                        </div>
                        <div id="specificLocationGroup" class="mb-3">
                            <label for="editEventLocation" class="form-label">Specific Location</label>
                            <input type="text" class="form-control" id="editEventLocation" name="location">
                        </div>
                        <div id="campusClassroomGroup" class="mb-3" style="display: none;">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="editEventCampus" class="form-label">Campus</label>
                                    <select class="form-control" id="editEventCampus" name="campus_id">
                                        <option value="">Select Campus</option>
                                        <!-- Options will be populated by JavaScript -->
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="editEventClassroom" class="form-label">Classroom</label>
                                    <select class="form-control" id="editEventClassroom" name="classroom_id">
                                        <option value="">Select Classroom</option>
                                        <!-- Options will be populated by JavaScript -->
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="editEventProfessors" class="form-label">Assign to Professors</label>
                            <select multiple class="form-control" id="editEventProfessors" name="professor_ids[]">
                                <!-- Options will be populated by JavaScript -->
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" id="editEventId" name="eventId">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="saveEventButton">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="loading-overlay">
        <div class="spinner"></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- Added jQuery -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var calendarEl = document.getElementById('calendar');
            var professorSelector = document.getElementById('professorSelector');
            var calendar;
            var loadingOverlay = document.getElementById('loading-overlay');
            let currentEditingEvent = null; // To store event data when editing

            function showLoading() {
                loadingOverlay.classList.add('visible');
            }

            function hideLoading() {
                loadingOverlay.classList.remove('visible');
            }

            function fetchProfessors() {
                fetch('get_professors.php')
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            console.error('Error fetching professors:', data.error);
                            alert('Could not load professors.');
                            return;
                        }
                        // Populate main professor selector
                        data.forEach(function (professor) {
                            var option = document.createElement('option');
                            option.value = professor.id;
                            option.textContent = professor.first_name + ' ' + professor.last_name + ' (' + professor.email + ')';
                            professorSelector.appendChild(option.cloneNode(true)); // Clone for main selector
                            document.getElementById('editEventProfessors').appendChild(option); // Use original for modal selector
                        });
                        $(professorSelector).select2({
                            placeholder: "Select a professor",
                            allowClear: true
                        });
                        $('#editEventProfessors').select2({
                            placeholder: "Assign to professors",
                            allowClear: true,
                            dropdownParent: $('#addEditEventModal') // Ensure dropdown is visible within modal
                        });
                    })
                    .catch(error => {
                        console.error('Error fetching professors:', error);
                        alert('Could not load professors.');
                    });
            }

            function fetchEventTypes() {
                fetch('get_event_types.php')
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            console.error('Error fetching event types:', data.error);
                            alert('Could not load event types.');
                            return;
                        }
                        const eventTypeSelect = document.getElementById('editEventType');
                        eventTypeSelect.innerHTML = ''; // Clear existing options
                        data.forEach(function (type) {
                            var option = document.createElement('option');
                            option.value = type.id;
                            option.textContent = type.type_name;
                            eventTypeSelect.appendChild(option);
                        });
                    })
                    .catch(error => {
                        console.error('Error fetching event types:', error);
                        alert('Could not load event types.');
                    });
            }

            function fetchCampuses() {
                fetch('get_campuses.php')
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            console.error('Error fetching campuses:', data.error);
                            alert('Could not load campuses.');
                            return;
                        }
                        const campusSelect = document.getElementById('editEventCampus');
                        campusSelect.innerHTML = '<option value="">Select Campus</option>'; // Clear existing options
                        data.forEach(function (campus) {
                            var option = document.createElement('option');
                            option.value = campus.id;
                            option.textContent = campus.campus_name;
                            campusSelect.appendChild(option);
                        });
                    })
                    .catch(error => {
                        console.error('Error fetching campuses:', error);
                        alert('Could not load campuses.');
                    });
            }

            function fetchClassrooms(campusId) {
                const classroomSelect = document.getElementById('editEventClassroom');
                classroomSelect.innerHTML = '<option value="">Loading...</option>'; // Clear existing options
                if (!campusId) {
                    classroomSelect.innerHTML = '<option value="">Select Campus First</option>';
                    $(classroomSelect).prop('disabled', true);
                    return;
                }
                $(classroomSelect).prop('disabled', false);
                fetch(`get_classrooms.php?campus_id=${campusId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            console.error('Error fetching classrooms:', data.error);
                            alert('Could not load classrooms for the selected campus.');
                            classroomSelect.innerHTML = '<option value="">Error loading</option>';
                            return;
                        }
                        classroomSelect.innerHTML = '<option value="">Select Classroom</option>';
                        data.forEach(function (classroom) {
                            var option = document.createElement('option');
                            option.value = classroom.id;
                            option.textContent = classroom.classroom_name;
                            classroomSelect.appendChild(option);
                        });
                    })
                    .catch(error => {
                        console.error('Error fetching classrooms:', error);
                        alert('Could not load classrooms.');
                        classroomSelect.innerHTML = '<option value="">Error loading</option>';
                    });
            }

            document.getElementById('editEventCampus').addEventListener('change', function () {
                fetchClassrooms(this.value);
            });

            document.getElementById('editEventLocationType').addEventListener('change', function () {
                const specificGroup = document.getElementById('specificLocationGroup');
                const campusClassroomGroup = document.getElementById('campusClassroomGroup');
                if (this.value === 'specific_location') {
                    specificGroup.style.display = 'block';
                    campusClassroomGroup.style.display = 'none';
                    document.getElementById('editEventLocation').required = true;
                    document.getElementById('editEventCampus').required = false;
                    // document.getElementById('editEventClassroom').required = false; // Not strictly needed if campus is not selected
                } else {
                    specificGroup.style.display = 'none';
                    campusClassroomGroup.style.display = 'block';
                    document.getElementById('editEventLocation').required = false;
                    document.getElementById('editEventCampus').required = true;
                    // document.getElementById('editEventClassroom').required = true; // Only if campus is selected
                }
            });

            function formatDateForDateInput(dateString) {
                if (!dateString) return '';
                const date = new Date(dateString);
                if (isNaN(date.getTime())) {
                    console.error("Invalid date provided to formatDateForDateInput:", dateString);
                    return '';
                }
                const year = date.getFullYear();
                const month = (date.getMonth() + 1).toString().padStart(2, '0');
                const day = date.getDate().toString().padStart(2, '0');
                return `${year}-${month}-${day}`;
            }

            function formatDateForTimeInput(dateString) {
                if (!dateString) return '';
                const date = new Date(dateString);
                if (isNaN(date.getTime())) {
                    console.error("Invalid date provided to formatDateForTimeInput:", dateString);
                    return '';
                }
                const hours = date.getHours().toString().padStart(2, '0');
                const minutes = date.getMinutes().toString().padStart(2, '0');
                return `${hours}:${minutes}`;
            }

            function openAddEventModal(info) {
                document.getElementById('addEditEventForm').reset();
                $('#editEventProfessors').val(null).trigger('change'); // Clear select2
                document.getElementById('editEventId').value = '';
                document.getElementById('addEditEventModalLabel').textContent = 'Add New Event';

                if (info && info.startStr) {
                    document.getElementById('editEventStartDate').value = formatDateForDateInput(info.startStr);
                    document.getElementById('editEventStartTime').value = formatDateForTimeInput(info.startStr);
                } else {
                    document.getElementById('editEventStartDate').value = '';
                    document.getElementById('editEventStartTime').value = '';
                }

                if (info && info.endStr && info.startStr !== info.endStr) { // If endStr is provided and different from startStr
                    let endDate = new Date(info.endStr);
                    if (info.allDay) { // For allday events, FC gives next day midnight.
                        // For a click on a day cell, startStr and endStr might be like '2024-05-23' and '2024-05-24'
                        // We want the end date to be the same as start date for a default duration.
                        endDate = new Date(info.startStr);
                        endDate.setHours(endDate.getHours() + 1); // Default 1 hour duration from start
                    }
                    document.getElementById('editEventEndDate').value = formatDateForDateInput(endDate);
                    document.getElementById('editEventEndTime').value = formatDateForTimeInput(endDate);
                } else if (info && info.startStr) { // If only start is provided, or start is same as end (click on timeline)
                    let endDate = new Date(info.startStr);
                    endDate.setHours(endDate.getHours() + 1); // Default end to start + 1 hour
                    document.getElementById('editEventEndDate').value = formatDateForDateInput(endDate);
                    document.getElementById('editEventEndTime').value = formatDateForTimeInput(endDate);
                } else {
                    document.getElementById('editEventEndDate').value = '';
                    document.getElementById('editEventEndTime').value = '';
                }

                // Reset location type to default and show/hide fields accordingly
                document.getElementById('editEventLocationType').value = 'specific_location';
                document.getElementById('specificLocationGroup').style.display = 'block';
                document.getElementById('campusClassroomGroup').style.display = 'none';
                document.getElementById('editEventLocation').required = true;
                document.getElementById('editEventCampus').required = false;
                fetchEventTypes(); // Ensure event types are loaded
                fetchCampuses(); // Ensure campuses are loaded
                fetchClassrooms(null); // Reset classrooms
                $('#addEditEventModal').modal('show');
            }

            function openEditEventModal(event) {
                currentEditingEvent = event; // Store the event object
                document.getElementById('addEditEventForm').reset();
                document.getElementById('addEditEventModalLabel').textContent = 'Edit Event';

                document.getElementById('editEventId').value = event.id;
                document.getElementById('editEventTitle').value = event.title;
                document.getElementById('editEventDescription').value = event.extendedProps.description || '';
                // document.getElementById('editEventStart').value = formatDateForInput(event.start);
                // document.getElementById('editEventEnd').value = formatDateForInput(event.end);
                document.getElementById('editEventStartDate').value = formatDateForDateInput(event.start);
                document.getElementById('editEventStartTime').value = formatDateForTimeInput(event.start);
                document.getElementById('editEventEndDate').value = formatDateForDateInput(event.end);
                document.getElementById('editEventEndTime').value = formatDateForTimeInput(event.end);

                fetchEventTypes().then(() => { // Ensure types are loaded before setting value
                    document.getElementById('editEventType').value = event.extendedProps.event_type_id;
                });

                const location = event.extendedProps.location;
                const campusId = event.extendedProps.campus_id;
                const classroomId = event.extendedProps.classroom_id;

                fetchCampuses().then(() => { // Ensure campuses are loaded before setting value
                    if (campusId) {
                        document.getElementById('editEventCampus').value = campusId;
                        fetchClassrooms(campusId).then(() => { // Ensure classrooms are loaded before setting value
                            if (classroomId) document.getElementById('editEventClassroom').value = classroomId;
                        });
                        document.getElementById('editEventLocationType').value = 'campus_classroom';
                        document.getElementById('specificLocationGroup').style.display = 'none';
                        document.getElementById('campusClassroomGroup').style.display = 'block';
                        document.getElementById('editEventLocation').required = false;
                        document.getElementById('editEventCampus').required = true;
                    } else {
                        document.getElementById('editEventLocation').value = location || '';
                        document.getElementById('editEventLocationType').value = 'specific_location';
                        document.getElementById('specificLocationGroup').style.display = 'block';
                        document.getElementById('campusClassroomGroup').style.display = 'none';
                        document.getElementById('editEventLocation').required = true;
                        document.getElementById('editEventCampus').required = false;
                        fetchClassrooms(null); // Reset classrooms if specific location
                    }
                });

                // Populate assigned professors
                const assignedProfessorIds = event.extendedProps.assigned_professor_ids || [];
                $('#editEventProfessors').val(assignedProfessorIds).trigger('change');

                $('#addEditEventModal').modal('show');
            }

            document.getElementById('addEditEventForm').addEventListener('submit', function (e) {
                e.preventDefault();
                const form = e.target;
                const formData = new FormData(form);
                const eventId = formData.get('eventId');
                const url = eventId ? 'update_event.php' : 'add_event.php';

                const startDateVal = document.getElementById('editEventStartDate').value;
                const startTimeVal = document.getElementById('editEventStartTime').value;
                const endDateVal = document.getElementById('editEventEndDate').value;
                const endTimeVal = document.getElementById('editEventEndTime').value;

                const combinedStartDateTime = startDateVal && startTimeVal ? `${startDateVal}T${startTimeVal}` : '';
                const combinedEndDateTime = endDateVal && endTimeVal ? `${endDateVal}T${endTimeVal}` : '';

                const submissionFormData = new FormData();
                for (let [key, value] of formData.entries()) {
                    if (key !== 'editEventStartDate_form' &&
                        key !== 'editEventStartTime_form' &&
                        key !== 'editEventEndDate_form' &&
                        key !== 'editEventEndTime_form') {
                        submissionFormData.append(key, value);
                    }
                }

                if (!combinedStartDateTime) {
                    alert('Start date and time are required.'); return;
                }
                submissionFormData.set('start_datetime', combinedStartDateTime);

                if (!combinedEndDateTime) {
                    alert('End date and time are required.'); return;
                }
                submissionFormData.set('end_datetime', combinedEndDateTime);

                // Clear and re-append professor_ids to ensure it's correct from Select2
                submissionFormData.delete('professor_ids[]');
                const selectedProfessors = $('#editEventProfessors').val();
                if (selectedProfessors && selectedProfessors.length > 0) {
                    selectedProfessors.forEach(profId => {
                        submissionFormData.append('professor_ids[]', profId);
                    });
                }

                // Handle location_type specific fields
                const locationType = submissionFormData.get('location_type');
                if (locationType === 'specific_location') {
                    submissionFormData.delete('campus_id');
                    submissionFormData.delete('classroom_id');
                    if (!submissionFormData.get('location')) {
                        alert('Specific location is required.');
                        return;
                    }
                } else { // campus_classroom
                    submissionFormData.delete('location');
                    if (!submissionFormData.get('campus_id')) {
                        alert('Campus is required.');
                        return;
                    }
                    // Classroom might be optional depending on your DB schema, adjust if needed
                    // if (!formData.get('classroom_id')) {
                    //     alert('Classroom is required.');
                    //     return;
                    // }
                }

                fetch(url, {
                    method: 'POST',
                    body: submissionFormData
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            $('#addEditEventModal').modal('hide');
                            calendar.refetchEvents();
                            alert(data.message || 'Event saved successfully!');
                        } else {
                            alert('Error: ' + (data.message || 'Could not save event.'));
                        }
                    })
                    .catch(error => {
                        console.error('Error saving event:', error);
                        alert('An unexpected error occurred. Please try again.');
                    });
            });

            function setupCalendar() {
                let initialEventSourcesUrl = 'get_events.php'; // Default: All professors

                calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    headerToolbar: {
                        left: 'prev,next today addEventButton',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
                    },
                    eventSources: [
                        {
                            id: 'mainEvents', // Assign an ID to the event source
                            url: initialEventSourcesUrl,
                            failure: function () {
                                alert('There was an error while fetching events!');
                                // hideLoading(); // Handled by the main loading callback
                            }
                        }
                    ],
                    loading: function (isLoading) { // Centralized loading indicator management
                        if (isLoading) {
                            showLoading();
                        } else {
                            hideLoading();
                        }
                    },
                    editable: true,
                    selectable: true,
                    customButtons: {
                        addEventButton: {
                            text: 'Add Event',
                            click: function () {
                                openAddEventModal();
                            }
                        }
                    },
                    select: function (info) {
                        // alert('Selected ' + info.startStr + ' to ' + info.endStr);
                        openAddEventModal(info);
                    },
                    eventClick: function (info) {
                        // Store event for potential edit/delete from detail modal
                        currentEditingEvent = info.event;

                        document.getElementById('eventTitle').value = info.event.title;
                        document.getElementById('eventDescription').value = info.event.extendedProps.description || 'N/A';
                        // const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
                        // document.getElementById('eventStart').value = info.event.start ? info.event.start.toLocaleString(undefined, options) : 'N/A';
                        // document.getElementById('eventEnd').value = info.event.end ? info.event.end.toLocaleString(undefined, options) : 'N/A';
                        // document.getElementById('eventStart').value = formatDateForInput(info.event.start);
                        // document.getElementById('eventEnd').value = formatDateForInput(info.event.end);
                        document.getElementById('eventStartDate').value = formatDateForDateInput(info.event.start);
                        document.getElementById('eventStartTime').value = formatDateForTimeInput(info.event.start);
                        document.getElementById('eventEndDate').value = formatDateForDateInput(info.event.end);
                        document.getElementById('eventEndTime').value = formatDateForTimeInput(info.event.end);
                        document.getElementById('eventType').value = info.event.extendedProps.event_type || 'N/A';

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
                        document.getElementById('eventAssignedTo').value = info.event.extendedProps.assigned_to_names || 'N/A';
                        $('#eventDetailModal').modal('show');
                    },
                    eventDrop: function (info) {
                        // alert(info.event.title + " was dropped on " + info.event.start.toISOString() + ". Update database.");
                        const eventData = {
                            eventId: info.event.id,
                            title: info.event.title, // FC might not pass all extendedProps here, be careful
                            start: info.event.start.toISOString(),
                            end: info.event.end ? info.event.end.toISOString() : null, // End might be null for all-day events if not handled
                            allDay: info.event.allDay
                            // You might need to fetch full event details if your update_event.php needs more than this
                        };
                        // Optimistically update UI, or wait for server confirmation
                        if (!confirm(info.event.title + " was moved. Save changes?")) {
                            info.revert();
                            return;
                        }
                        fetch('update_event.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({
                                eventId: info.event.id,
                                start: info.event.start.toISOString(),
                                end: info.event.end ? info.event.end.toISOString() : null,
                                // IMPORTANT: update_event.php needs to handle these partial updates
                                // or you need to fetch the full event details first to send a complete object.
                                partialUpdate: true // Add a flag to indicate it's a drag/drop update
                            })
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    calendar.refetchEvents(); // Or just update the specific event if possible
                                    alert(data.message || 'Event updated!');
                                } else {
                                    alert('Error: ' + (data.message || 'Could not update event.'));
                                    info.revert(); // Revert on failure
                                }
                            })
                            .catch(error => {
                                console.error('Error updating event (drop):', error);
                                alert('An error occurred while updating the event.');
                                info.revert();
                            });
                    },
                    eventResize: function (info) {
                        // alert(info.event.title + " end is now " + info.event.end.toISOString() + ". Update database.");
                        if (!confirm(info.event.title + " was resized. Save changes?")) {
                            info.revert();
                            return;
                        }
                        fetch('update_event.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({
                                eventId: info.event.id,
                                start: info.event.start.toISOString(), // Start might also change if resizing from the start
                                end: info.event.end.toISOString(),
                                partialUpdate: true // Add a flag
                            })
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    calendar.refetchEvents();
                                    alert(data.message || 'Event updated!');
                                } else {
                                    alert('Error: ' + (data.message || 'Could not update event duration.'));
                                    info.revert();
                                }
                            })
                            .catch(error => {
                                console.error('Error updating event (resize):', error);
                                alert('An error occurred while updating the event duration.');
                                info.revert();
                            });
                    }
                });
                calendar.render();
            }

            function updateCalendarEvents(professorId = null) {
                if (!calendar) return;

                let newEventSourcesUrl = 'get_events.php';
                if (professorId && professorId !== "") { // Check for actual ID, not empty string for "All"
                    newEventSourcesUrl += '?professor_id=' + professorId;
                }

                var source = calendar.getEventSourceById('mainEvents');
                if (source) {
                    source.remove();
                }
                calendar.addEventSource({
                    id: 'mainEvents',
                    url: newEventSourcesUrl,
                    failure: function () {
                        alert('There was an error while fetching updated events!');
                    }
                });
                // FullCalendar's loading callback will handle showLoading/hideLoading
            }

            // professorSelector.addEventListener('change', function () {
            //     updateCalendarEvents(this.value);
            // });
            // Use jQuery for Select2 event handling
            $(professorSelector).on('change', function () {
                updateCalendarEvents(this.value);
            });

            fetchProfessors(); // Load professors for the dropdown
            setupCalendar();   // Initialize the calendar structure and load initial events
            // Initial fetch for dropdowns in the Add/Edit modal, can be deferred until modal is opened if preferred
            // fetchEventTypes(); 
            // fetchCampuses();
        });
    </script>
</body>

</html>