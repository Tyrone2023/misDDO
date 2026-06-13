<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Calendar of Activities</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
            padding: 20px;
        }

        #calendar {
            max-width: 100%;
            margin: 0 auto;
            background: #ffffff;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
        }
    </style>
</head>

<body>

    <div id="calendar"></div>

    <!-- Bootstrap Modal for Add/Edit -->
    <div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-info">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="eventModalLabel">Event Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="eventForm" method="POST" action="<?= site_url('calendar/save_event') ?>" autocomplete="off">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="eventTitle" class="form-label">Event Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="eventTitle" name="title" required placeholder="Enter event title">
                            </div>

                            <div class="col-md-12">
                                <label for="eventDescription" class="form-label">Description / Notes</label>
                                <textarea class="form-control" id="eventDescription" name="description" rows="3" placeholder="Optional details..."></textarea>
                            </div>

                            <div class="col-md-6">
                                <label for="eventStatus" class="form-label">Visibility</label>
                                <select id="eventStatus" name="status" class="form-select">
                                    <option value="private">Private</option>
                                    <option value="public">Public</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="eventColor" class="form-label">Event Color</label>
                                <input type="color" class="form-control form-control-color" id="eventColor" name="color" value="#17a2b8" title="Choose a color">
                            </div>

                            <input type="hidden" id="start" name="start">
                            <input type="hidden" id="end" name="end">
                            <input type="hidden" id="eventId" name="id"> <!-- For updates -->
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info" id="saveEventBtn">Save Event</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                selectable: true,
                editable: true,
                events: '<?= site_url("calendar/fetch_events") ?>',

                select: function(info) {
                    resetForm();
                    document.getElementById('start').value = info.startStr;
                    document.getElementById('end').value = info.endStr;

                    var modal = new bootstrap.Modal(document.getElementById('eventModal'));
                    modal.show();

                    document.getElementById('saveEventBtn').onclick = function() {
                        document.getElementById('eventForm').submit();
                    };
                },

                eventClick: function(info) {
                    resetForm();
                    var event = info.event;

                    document.getElementById('eventTitle').value = event.title;
                    document.getElementById('eventDescription').value = event.extendedProps.description || '';
                    document.getElementById('eventStatus').value = event.extendedProps.status || 'private';
                    document.getElementById('eventColor').value = event.backgroundColor || '#17a2b8';
                    document.getElementById('start').value = event.startStr;
                    document.getElementById('end').value = event.endStr;
                    document.getElementById('eventId').value = event.id;

                    var modal = new bootstrap.Modal(document.getElementById('eventModal'));
                    modal.show();

                    document.getElementById('saveEventBtn').onclick = function() {
                        document.getElementById('eventForm').submit();
                    };
                }
            });

            calendar.render();

            // Reset modal when hidden
            document.getElementById('eventModal').addEventListener('hidden.bs.modal', function() {
                resetForm();
            });

            function resetForm() {
                document.getElementById('eventForm').reset();
                document.getElementById('eventId').value = '';
            }
        });
    </script>

</body>

</html>