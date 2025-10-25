<?php
session_start();
require_once 'config/config.php';
require_once 'config/database.php';

if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$db = new Database();
$conn = $db->getConnection();

$error = '';
$success = '';
$action = $_GET['action'] ?? 'view';
$event_id = $_GET['id'] ?? null;

// Handle form submissions
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(isset($_POST['create_event'])) {
        $title = trim($_POST['title']);
        $description = trim($_POST['description']);
        $event_date = $_POST['event_date'];
        $event_time = $_POST['event_time'] ?? null;
        $location = trim($_POST['location']);
        
        if(empty($title) || empty($event_date)) {
            $error = 'Title and date are required';
        } else {
            $stmt = $conn->prepare("INSERT INTO calendar_events (user_id, title, description, event_date, event_time, location) VALUES (?, ?, ?, ?, ?, ?)");
            if($stmt->execute([$_SESSION['user_id'], $title, $description, $event_date, $event_time, $location])) {
                $success = 'Event created successfully!';
                $action = 'view';
            } else {
                $error = 'Failed to create event';
            }
        }
    } elseif(isset($_POST['update_event'])) {
        $title = trim($_POST['title']);
        $description = trim($_POST['description']);
        $event_date = $_POST['event_date'];
        $event_time = $_POST['event_time'] ?? null;
        $location = trim($_POST['location']);
        
        if(empty($title) || empty($event_date)) {
            $error = 'Title and date are required';
        } else {
            $stmt = $conn->prepare("UPDATE calendar_events SET title = ?, description = ?, event_date = ?, event_time = ?, location = ? WHERE id = ? AND user_id = ?");
            if($stmt->execute([$title, $description, $event_date, $event_time, $location, $event_id, $_SESSION['user_id']])) {
                $success = 'Event updated successfully!';
                $action = 'view';
            } else {
                $error = 'Failed to update event';
            }
        }
    } elseif(isset($_POST['delete_event'])) {
        $stmt = $conn->prepare("DELETE FROM calendar_events WHERE id = ? AND user_id = ?");
        if($stmt->execute([$event_id, $_SESSION['user_id']])) {
            $success = 'Event deleted successfully!';
            $action = 'view';
        } else {
            $error = 'Failed to delete event';
        }
    }
}

// Get events for calendar
$current_month = $_GET['month'] ?? date('m');
$current_year = $_GET['year'] ?? date('Y');

$stmt = $conn->prepare("SELECT * FROM calendar_events WHERE user_id = ? AND MONTH(event_date) = ? AND YEAR(event_date) = ? ORDER BY event_date, event_time");
$stmt->execute([$_SESSION['user_id'], $current_month, $current_year]);
$events = $stmt->fetchAll();

// Get event for editing
if($action === 'edit' && $event_id) {
    $stmt = $conn->prepare("SELECT * FROM calendar_events WHERE id = ? AND user_id = ?");
    $stmt->execute([$event_id, $_SESSION['user_id']]);
    $event = $stmt->fetch();
    if(!$event) {
        $error = 'Event not found';
        $action = 'view';
    }
}

$page_title = 'Calendar';
include 'includes/header.php';
include 'includes/navbar.php';
?>

<style>
.calendar-container {
    max-width: 1200px;
    margin: 30px auto;
    padding: 0 20px;
}

.calendar-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}

.calendar-header h1 {
    font-size: 32px;
    color: var(--dark-color);
}

.calendar-nav {
    display: flex;
    gap: 15px;
    align-items: center;
}

.month-selector {
    display: flex;
    gap: 10px;
    align-items: center;
}

.month-selector button {
    padding: 8px 12px;
    border: 1px solid var(--gray-300);
    background: white;
    border-radius: var(--border-radius);
    cursor: pointer;
    transition: var(--transition);
}

.month-selector button:hover {
    background: var(--gray-100);
}

.calendar-grid {
    background: white;
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
    overflow: hidden;
}

.calendar-days {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    background: var(--primary-color);
    color: white;
    font-weight: 600;
}

.calendar-days div {
    padding: 15px;
    text-align: center;
}

.calendar-dates {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 1px;
    background: var(--gray-200);
}

.calendar-date {
    background: white;
    min-height: 100px;
    padding: 10px;
    cursor: pointer;
    transition: var(--transition);
    position: relative;
}

.calendar-date:hover {
    background: var(--gray-100);
}

.calendar-date.other-month {
    background: var(--gray-100);
    color: var(--gray-500);
}

.calendar-date.today {
    background: rgba(74, 144, 226, 0.1);
}

.date-number {
    font-weight: 600;
    margin-bottom: 5px;
}

.event-dot {
    width: 8px;
    height: 8px;
    background: var(--primary-color);
    border-radius: 50%;
    display: inline-block;
    margin-right: 5px;
}

.calendar-event {
    background: var(--primary-color);
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    margin-bottom: 4px;
    cursor: pointer;
    transition: var(--transition);
}

.calendar-event:hover {
    background: #3a7bc8;
}

.events-list {
    background: white;
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
    padding: 20px;
    margin-top: 20px;
}

.event-item {
    padding: 15px;
    border-bottom: 1px solid var(--gray-200);
    display: flex;
    justify-content: space-between;
    align-items: start;
}

.event-item:last-child {
    border-bottom: none;
}

.event-form {
    background: white;
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
    padding: 30px;
    max-width: 800px;
    margin: 0 auto;
}

@media (max-width: 768px) {
    .calendar-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }
    
    .calendar-date {
        min-height: 80px;
        padding: 5px;
        font-size: 14px;
    }
}
</style>

<div class="calendar-container">
    <?php if($action === 'view'): ?>
        <div class="calendar-header">
            <h1><i class="fas fa-calendar-alt"></i> Calendar</h1>
            <div class="calendar-nav">
                <div class="month-selector">
                    <button onclick="changeMonth(-1)"><i class="fas fa-chevron-left"></i></button>
                    <span style="font-weight: 600; min-width: 150px; text-align: center;">
                        <?php echo date('F Y', mktime(0, 0, 0, $current_month, 1, $current_year)); ?>
                    </span>
                    <button onclick="changeMonth(1)"><i class="fas fa-chevron-right"></i></button>
                </div>
                <a href="?action=create" class="btn btn-primary">
                    <i class="fas fa-plus"></i> New Event
                </a>
            </div>
        </div>

        <?php if($error): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <div class="calendar-grid">
            <div class="calendar-days">
                <div>Sun</div>
                <div>Mon</div>
                <div>Tue</div>
                <div>Wed</div>
                <div>Thu</div>
                <div>Fri</div>
                <div>Sat</div>
            </div>
            
            <div class="calendar-dates">
                <?php
                $first_day = mktime(0, 0, 0, $current_month, 1, $current_year);
                $days_in_month = date('t', $first_day);
                $day_of_week = date('w', $first_day);
                
                // Previous month days
                $prev_month_days = date('t', mktime(0, 0, 0, $current_month - 1, 1, $current_year));
                for($i = $day_of_week - 1; $i >= 0; $i--) {
                    $day = $prev_month_days - $i;
                    echo "<div class='calendar-date other-month'><div class='date-number'>$day</div></div>";
                }
                
                // Current month days
                for($day = 1; $day <= $days_in_month; $day++) {
                    $date = sprintf('%04d-%02d-%02d', $current_year, $current_month, $day);
                    $is_today = ($date === date('Y-m-d')) ? 'today' : '';
                    
                    // Get events for this day
                    $day_events = array_filter($events, function($e) use ($date) {
                        return $e['event_date'] === $date;
                    });
                    
                    echo "<div class='calendar-date $is_today' data-date='$date'>";
                    echo "<div class='date-number'>$day</div>";
                    
                    foreach($day_events as $evt) {
                        $title = htmlspecialchars(substr($evt['title'], 0, 15));
                        echo "<div class='calendar-event' onclick='viewEvent({$evt['id']})'>$title</div>";
                    }
                    
                    echo "</div>";
                }
                
                // Next month days
                $total_cells = $day_of_week + $days_in_month;
                $remaining_cells = (7 - ($total_cells % 7)) % 7;
                for($day = 1; $day <= $remaining_cells; $day++) {
                    echo "<div class='calendar-date other-month'><div class='date-number'>$day</div></div>";
                }
                ?>
            </div>
        </div>

        <?php if(count($events) > 0): ?>
            <div class="events-list">
                <h3 style="margin-bottom: 20px;">Events this month</h3>
                <?php foreach($events as $evt): ?>
                    <div class="event-item">
                        <div>
                            <h4><?php echo htmlspecialchars($evt['title']); ?></h4>
                            <p style="color: var(--gray-600); font-size: 14px;">
                                <i class="fas fa-calendar"></i> <?php echo date('M d, Y', strtotime($evt['event_date'])); ?>
                                <?php if($evt['event_time']): ?>
                                    at <?php echo date('h:i A', strtotime($evt['event_time'])); ?>
                                <?php endif; ?>
                            </p>
                            <?php if($evt['location']): ?>
                                <p style="color: var(--gray-600); font-size: 14px;">
                                    <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($evt['location']); ?>
                                </p>
                            <?php endif; ?>
                            <?php if($evt['description']): ?>
                                <p style="color: var(--gray-700); margin-top: 10px;">
                                    <?php echo nl2br(htmlspecialchars($evt['description'])); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                        <div style="display: flex; gap: 10px;">
                            <a href="?action=edit&id=<?php echo $evt['id']; ?>" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" style="display: inline;" onsubmit="return confirm('Delete this event?');">
                                <input type="hidden" name="event_id" value="<?php echo $evt['id']; ?>">
                                <button type="submit" name="delete_event" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    <?php elseif($action === 'create'): ?>
        <div class="calendar-header">
            <h1><i class="fas fa-plus"></i> Create Event</h1>
            <a href="calendar.php" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>

        <form method="POST" class="event-form">
            <div class="form-group">
                <label for="title" class="form-label">Event Title *</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="4"></textarea>
            </div>

            <div class="form-group">
                <label for="event_date" class="form-label">Date *</label>
                <input type="date" class="form-control" id="event_date" name="event_date" required>
            </div>

            <div class="form-group">
                <label for="event_time" class="form-label">Time</label>
                <input type="time" class="form-control" id="event_time" name="event_time">
            </div>

            <div class="form-group">
                <label for="location" class="form-label">Location</label>
                <input type="text" class="form-control" id="location" name="location">
            </div>

            <div class="form-group">
                <button type="submit" name="create_event" class="btn btn-primary">
                    <i class="fas fa-save"></i> Create Event
                </button>
                <a href="calendar.php" class="btn btn-outline">Cancel</a>
            </div>
        </form>

    <?php elseif($action === 'edit' && isset($event)): ?>
        <div class="calendar-header">
            <h1><i class="fas fa-edit"></i> Edit Event</h1>
            <a href="calendar.php" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>

        <form method="POST" class="event-form">
            <div class="form-group">
                <label for="title" class="form-label">Event Title *</label>
                <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($event['title']); ?>" required>
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="4"><?php echo htmlspecialchars($event['description']); ?></textarea>
            </div>

            <div class="form-group">
                <label for="event_date" class="form-label">Date *</label>
                <input type="date" class="form-control" id="event_date" name="event_date" value="<?php echo $event['event_date']; ?>" required>
            </div>

            <div class="form-group">
                <label for="event_time" class="form-label">Time</label>
                <input type="time" class="form-control" id="event_time" name="event_time" value="<?php echo $event['event_time']; ?>">
            </div>

            <div class="form-group">
                <label for="location" class="form-label">Location</label>
                <input type="text" class="form-control" id="location" name="location" value="<?php echo htmlspecialchars($event['location']); ?>">
            </div>

            <div class="form-group">
                <button type="submit" name="update_event" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Event
                </button>
                <a href="calendar.php" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    <?php endif; ?>
</div>

<script>
function changeMonth(delta) {
    const url = new URL(window.location);
    let month = parseInt(url.searchParams.get('month') || <?php echo date('m'); ?>);
    let year = parseInt(url.searchParams.get('year') || <?php echo date('Y'); ?>);
    
    month += delta;
    if(month > 12) {
        month = 1;
        year++;
    } else if(month < 1) {
        month = 12;
        year--;
    }
    
    url.searchParams.set('month', month);
    url.searchParams.set('year', year);
    window.location = url.toString();
}

function viewEvent(id) {
    window.location.href = '?action=edit&id=' + id;
}
</script>

<?php include 'includes/footer.php'; ?>
