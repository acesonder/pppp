<?php
require_once 'includes/config.php';

if (!is_logged_in()) {
    redirect('login.php');
}

$user = get_current_user();
$db = Database::getInstance()->getConnection();

// Handle calendar operations
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    $action = $_POST['action'];
    
    switch ($action) {
        case 'create':
            $title = sanitize_input($_POST['title'] ?? '');
            $description = sanitize_input($_POST['description'] ?? '');
            $start_datetime = $_POST['start_datetime'] ?? null;
            $end_datetime = $_POST['end_datetime'] ?? null;
            $location = sanitize_input($_POST['location'] ?? '');
            $color = sanitize_input($_POST['color'] ?? '#3788d8');
            
            if (empty($title) || empty($start_datetime) || empty($end_datetime)) {
                echo json_encode(['success' => false, 'message' => 'Required fields missing']);
                exit;
            }
            
            $stmt = $db->prepare("INSERT INTO calendar_events (user_id, title, description, start_datetime, end_datetime, location, color) VALUES (?, ?, ?, ?, ?, ?, ?)");
            if ($stmt->execute([$_SESSION['user_id'], $title, $description, $start_datetime, $end_datetime, $location, $color])) {
                log_activity('event_create', 'Created event: ' . $title);
                echo json_encode(['success' => true, 'message' => 'Event created successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to create event']);
            }
            exit;
            
        case 'get_events':
            $stmt = $db->prepare("SELECT * FROM calendar_events WHERE user_id = ? ORDER BY start_datetime");
            $stmt->execute([$_SESSION['user_id']]);
            $events = $stmt->fetchAll();
            echo json_encode(['success' => true, 'events' => $events]);
            exit;
            
        case 'delete':
            $id = intval($_POST['id'] ?? 0);
            
            $stmt = $db->prepare("DELETE FROM calendar_events WHERE id = ? AND user_id = ?");
            if ($stmt->execute([$id, $_SESSION['user_id']])) {
                log_activity('event_delete', 'Deleted event ID: ' . $id);
                echo json_encode(['success' => true, 'message' => 'Event deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to delete event']);
            }
            exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendar - CRUD Web App</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .calendar-container {
            background: white;
            border-radius: 10px;
            padding: 2rem;
            box-shadow: var(--shadow);
        }
        
        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        
        .calendar-nav {
            display: flex;
            gap: 1rem;
            align-items: center;
        }
        
        .events-list {
            margin-top: 2rem;
        }
        
        .event-item {
            padding: 1rem;
            border-left: 4px solid var(--primary-color);
            background: #f9f9f9;
            margin-bottom: 1rem;
            border-radius: 5px;
        }
        
        .event-title {
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .event-meta {
            font-size: 0.9rem;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <?php include 'includes/sidebar.php'; ?>

        <div class="main-content">
            <?php include 'includes/topbar.php'; ?>

            <div class="dashboard-content">
                <div class="page-header">
                    <h1 class="page-title">Calendar</h1>
                    <button class="btn btn-primary" onclick="openModal('eventModal')">
                        <i class="fas fa-plus"></i> New Event
                    </button>
                </div>

                <div class="calendar-container">
                    <div class="calendar-header">
                        <h2 id="currentMonth"></h2>
                        <div class="calendar-nav">
                            <button class="btn btn-outline" onclick="previousMonth()">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <button class="btn btn-outline" onclick="nextMonth()">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                            <button class="btn btn-primary" onclick="goToToday()">Today</button>
                        </div>
                    </div>
                    
                    <div id="calendarView"></div>
                    
                    <div class="events-list">
                        <h3>Upcoming Events</h3>
                        <div id="eventsList"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Event Modal -->
    <div class="modal" id="eventModal">
        <div class="modal-dialog">
            <div class="modal-header">
                <h3 class="modal-title">New Event</h3>
                <button class="modal-close" onclick="closeModal('eventModal')">&times;</button>
            </div>
            <div class="modal-body">
                <form id="eventForm">
                    <input type="hidden" id="event_id" name="id">
                    <input type="hidden" id="event_action" name="action" value="create">
                    
                    <div class="form-group">
                        <label for="title">Title *</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="start_datetime">Start Date & Time *</label>
                        <input type="datetime-local" class="form-control" id="start_datetime" name="start_datetime" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="end_datetime">End Date & Time *</label>
                        <input type="datetime-local" class="form-control" id="end_datetime" name="end_datetime" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="location">Location</label>
                        <input type="text" class="form-control" id="location" name="location">
                    </div>
                    
                    <div class="form-group">
                        <label for="color">Color</label>
                        <input type="color" class="form-control" id="color" name="color" value="#3788d8">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline" onclick="closeModal('eventModal')">Cancel</button>
                <button class="btn btn-primary" onclick="submitEvent()">Save Event</button>
            </div>
        </div>
    </div>

    <script src="assets/js/main.js"></script>
    <script src="assets/js/dashboard.js"></script>
    <script src="assets/js/calendar.js"></script>
</body>
</html>
