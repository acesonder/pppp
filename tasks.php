<?php
require_once 'includes/config.php';

if (!is_logged_in()) {
    redirect('login.php');
}

$user = get_current_user();
$db = Database::getInstance()->getConnection();

// Handle task operations
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    $action = $_POST['action'];
    
    switch ($action) {
        case 'create':
            $title = sanitize_input($_POST['title'] ?? '');
            $description = sanitize_input($_POST['description'] ?? '');
            $priority = sanitize_input($_POST['priority'] ?? 'medium');
            $due_date = $_POST['due_date'] ?? null;
            
            if (empty($title)) {
                echo json_encode(['success' => false, 'message' => 'Title is required']);
                exit;
            }
            
            $stmt = $db->prepare("INSERT INTO tasks (user_id, title, description, priority, due_date) VALUES (?, ?, ?, ?, ?)");
            if ($stmt->execute([$_SESSION['user_id'], $title, $description, $priority, $due_date])) {
                log_activity('task_create', 'Created task: ' . $title);
                echo json_encode(['success' => true, 'message' => 'Task created successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to create task']);
            }
            exit;
            
        case 'update':
            $id = intval($_POST['id'] ?? 0);
            $title = sanitize_input($_POST['title'] ?? '');
            $description = sanitize_input($_POST['description'] ?? '');
            $priority = sanitize_input($_POST['priority'] ?? 'medium');
            $status = sanitize_input($_POST['status'] ?? 'pending');
            $due_date = $_POST['due_date'] ?? null;
            
            $stmt = $db->prepare("UPDATE tasks SET title = ?, description = ?, priority = ?, status = ?, due_date = ? WHERE id = ? AND user_id = ?");
            if ($stmt->execute([$title, $description, $priority, $status, $due_date, $id, $_SESSION['user_id']])) {
                log_activity('task_update', 'Updated task: ' . $title);
                echo json_encode(['success' => true, 'message' => 'Task updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update task']);
            }
            exit;
            
        case 'delete':
            $id = intval($_POST['id'] ?? 0);
            
            $stmt = $db->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
            if ($stmt->execute([$id, $_SESSION['user_id']])) {
                log_activity('task_delete', 'Deleted task ID: ' . $id);
                echo json_encode(['success' => true, 'message' => 'Task deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to delete task']);
            }
            exit;
            
        case 'toggle_status':
            $id = intval($_POST['id'] ?? 0);
            $status = $_POST['status'] === 'completed' ? 'completed' : 'pending';
            
            $stmt = $db->prepare("UPDATE tasks SET status = ? WHERE id = ? AND user_id = ?");
            if ($stmt->execute([$status, $id, $_SESSION['user_id']])) {
                echo json_encode(['success' => true, 'message' => 'Task status updated']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update status']);
            }
            exit;
    }
}

// Get all tasks
$filter = $_GET['filter'] ?? 'all';
$sql = "SELECT * FROM tasks WHERE user_id = ?";
$params = [$_SESSION['user_id']];

if ($filter === 'pending') {
    $sql .= " AND status = 'pending'";
} elseif ($filter === 'completed') {
    $sql .= " AND status = 'completed'";
}

$sql .= " ORDER BY created_at DESC";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$tasks = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tasks - CRUD Web App</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="dashboard-container">
        <?php include 'includes/sidebar.php'; ?>

        <div class="main-content">
            <?php include 'includes/topbar.php'; ?>

            <div class="dashboard-content">
                <div class="page-header">
                    <h1 class="page-title">Tasks</h1>
                    <button class="btn btn-primary" onclick="openModal('taskModal')">
                        <i class="fas fa-plus"></i> New Task
                    </button>
                </div>

                <!-- Filters -->
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex gap-2">
                            <a href="tasks.php?filter=all" class="btn <?php echo $filter === 'all' ? 'btn-primary' : 'btn-outline'; ?>">All</a>
                            <a href="tasks.php?filter=pending" class="btn <?php echo $filter === 'pending' ? 'btn-primary' : 'btn-outline'; ?>">Pending</a>
                            <a href="tasks.php?filter=completed" class="btn <?php echo $filter === 'completed' ? 'btn-primary' : 'btn-outline'; ?>">Completed</a>
                        </div>
                    </div>
                </div>

                <!-- Tasks List -->
                <div class="card">
                    <div class="card-body">
                        <?php if (count($tasks) > 0): ?>
                        <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr>
                                        <th style="width: 40px;"></th>
                                        <th>Title</th>
                                        <th>Priority</th>
                                        <th>Status</th>
                                        <th>Due Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($tasks as $task): ?>
                                    <tr>
                                        <td>
                                            <input type="checkbox" class="task-toggle" 
                                                   data-id="<?php echo $task['id']; ?>"
                                                   <?php echo $task['status'] === 'completed' ? 'checked' : ''; ?>>
                                        </td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($task['title']); ?></strong><br>
                                            <small style="color: #666;"><?php echo htmlspecialchars($task['description']); ?></small>
                                        </td>
                                        <td>
                                            <span class="task-priority <?php echo $task['priority']; ?>">
                                                <?php echo ucfirst($task['priority']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge <?php echo $task['status'] === 'completed' ? 'alert-success' : 'alert-warning'; ?>">
                                                <?php echo ucfirst($task['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php echo $task['due_date'] ? date('M d, Y', strtotime($task['due_date'])) : '-'; ?>
                                        </td>
                                        <td>
                                            <button class="btn btn-small btn-primary" onclick="editTask(<?php echo htmlspecialchars(json_encode($task)); ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-small btn-danger" onclick="deleteTask(<?php echo $task['id']; ?>)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php else: ?>
                        <p class="text-center" style="padding: 3rem; color: #999;">
                            <i class="fas fa-tasks fa-3x" style="display: block; margin-bottom: 1rem;"></i>
                            No tasks found. Create your first task!
                        </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Task Modal -->
    <div class="modal" id="taskModal">
        <div class="modal-dialog">
            <div class="modal-header">
                <h3 class="modal-title" id="modalTitle">New Task</h3>
                <button class="modal-close" onclick="closeModal('taskModal')">&times;</button>
            </div>
            <div class="modal-body">
                <form id="taskForm">
                    <input type="hidden" id="task_id" name="id">
                    <input type="hidden" id="task_action" name="action" value="create">
                    
                    <div class="form-group">
                        <label for="title">Title *</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="priority">Priority</label>
                        <select class="form-control" id="priority" name="priority">
                            <option value="low">Low</option>
                            <option value="medium" selected>Medium</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select class="form-control" id="status" name="status">
                            <option value="pending" selected>Pending</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="due_date">Due Date</label>
                        <input type="datetime-local" class="form-control" id="due_date" name="due_date">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline" onclick="closeModal('taskModal')">Cancel</button>
                <button class="btn btn-primary" onclick="submitTask()">Save Task</button>
            </div>
        </div>
    </div>

    <script src="assets/js/main.js"></script>
    <script src="assets/js/dashboard.js"></script>
    <script src="assets/js/tasks.js"></script>
</body>
</html>
