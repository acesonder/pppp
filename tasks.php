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
$action = $_GET['action'] ?? 'list';
$task_id = $_GET['id'] ?? null;

// Handle form submissions
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(isset($_POST['create_task'])) {
        $title = trim($_POST['title']);
        $description = trim($_POST['description']);
        $priority = $_POST['priority'];
        $due_date = $_POST['due_date'] ?? null;
        
        if(empty($title)) {
            $error = 'Title is required';
        } else {
            $stmt = $conn->prepare("INSERT INTO tasks (user_id, title, description, priority, due_date) VALUES (?, ?, ?, ?, ?)");
            if($stmt->execute([$_SESSION['user_id'], $title, $description, $priority, $due_date])) {
                $success = 'Task created successfully!';
                $action = 'list';
            } else {
                $error = 'Failed to create task';
            }
        }
    } elseif(isset($_POST['update_task'])) {
        $title = trim($_POST['title']);
        $description = trim($_POST['description']);
        $priority = $_POST['priority'];
        $status = $_POST['status'];
        $due_date = $_POST['due_date'] ?? null;
        
        if(empty($title)) {
            $error = 'Title is required';
        } else {
            $stmt = $conn->prepare("UPDATE tasks SET title = ?, description = ?, priority = ?, status = ?, due_date = ? WHERE id = ? AND user_id = ?");
            if($stmt->execute([$title, $description, $priority, $status, $due_date, $task_id, $_SESSION['user_id']])) {
                $success = 'Task updated successfully!';
                $action = 'list';
            } else {
                $error = 'Failed to update task';
            }
        }
    } elseif(isset($_POST['delete_task'])) {
        $stmt = $conn->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
        if($stmt->execute([$task_id, $_SESSION['user_id']])) {
            $success = 'Task deleted successfully!';
            $action = 'list';
        } else {
            $error = 'Failed to delete task';
        }
    }
}

// Get tasks
$tasks = [];
if($action === 'list') {
    $stmt = $conn->prepare("SELECT * FROM tasks WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$_SESSION['user_id']]);
    $tasks = $stmt->fetchAll();
} elseif($action === 'edit' && $task_id) {
    $stmt = $conn->prepare("SELECT * FROM tasks WHERE id = ? AND user_id = ?");
    $stmt->execute([$task_id, $_SESSION['user_id']]);
    $task = $stmt->fetch();
    if(!$task) {
        $error = 'Task not found';
        $action = 'list';
    }
}

$page_title = 'Tasks';
include 'includes/header.php';
include 'includes/navbar.php';
?>

<style>
.tasks-container {
    max-width: 1200px;
    margin: 30px auto;
    padding: 0 20px;
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
}

.page-header h1 {
    font-size: 32px;
    color: var(--dark-color);
}

.tasks-grid {
    display: grid;
    gap: 20px;
}

.task-card {
    background: white;
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
    padding: 20px;
    transition: var(--transition);
    border-left: 4px solid var(--primary-color);
}

.task-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--box-shadow-lg);
}

.task-card.priority-urgent {
    border-left-color: #721c24;
}

.task-card.priority-high {
    border-left-color: var(--danger-color);
}

.task-card.priority-medium {
    border-left-color: var(--warning-color);
}

.task-card.priority-low {
    border-left-color: var(--gray-400);
}

.task-header {
    display: flex;
    justify-content: space-between;
    align-items: start;
    margin-bottom: 15px;
}

.task-title {
    font-size: 20px;
    font-weight: 600;
    color: var(--dark-color);
    margin-bottom: 5px;
}

.task-meta {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    margin-bottom: 15px;
}

.task-description {
    color: var(--gray-700);
    line-height: 1.6;
    margin-bottom: 15px;
}

.task-actions {
    display: flex;
    gap: 10px;
}

.task-actions button {
    padding: 8px 16px;
    font-size: 14px;
}

.filters {
    display: flex;
    gap: 15px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}

.filter-btn {
    padding: 8px 16px;
    border: 1px solid var(--gray-300);
    background: white;
    border-radius: var(--border-radius);
    cursor: pointer;
    transition: var(--transition);
}

.filter-btn:hover, .filter-btn.active {
    background: var(--primary-color);
    color: white;
    border-color: var(--primary-color);
}

.task-form {
    background: white;
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
    padding: 30px;
    max-width: 800px;
    margin: 0 auto;
}

@media (max-width: 768px) {
    .page-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }
}
</style>

<div class="tasks-container">
    <?php if($action === 'list'): ?>
        <div class="page-header">
            <h1><i class="fas fa-tasks"></i> My Tasks</h1>
            <a href="?action=create" class="btn btn-primary">
                <i class="fas fa-plus"></i> New Task
            </a>
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

        <div class="filters">
            <button class="filter-btn active" onclick="filterTasks('all')">All Tasks</button>
            <button class="filter-btn" onclick="filterTasks('pending')">Pending</button>
            <button class="filter-btn" onclick="filterTasks('in_progress')">In Progress</button>
            <button class="filter-btn" onclick="filterTasks('completed')">Completed</button>
        </div>

        <div class="tasks-grid" id="tasksGrid">
            <?php if(count($tasks) > 0): ?>
                <?php foreach($tasks as $task): ?>
                    <div class="task-card priority-<?php echo $task['priority']; ?>" data-status="<?php echo $task['status']; ?>">
                        <div class="task-header">
                            <div>
                                <h3 class="task-title"><?php echo htmlspecialchars($task['title']); ?></h3>
                                <div class="task-meta">
                                    <span class="task-status <?php echo $task['status']; ?>">
                                        <?php echo str_replace('_', ' ', ucfirst($task['status'])); ?>
                                    </span>
                                    <span class="priority-badge <?php echo $task['priority']; ?>">
                                        <?php echo ucfirst($task['priority']); ?>
                                    </span>
                                    <?php if($task['due_date']): ?>
                                        <span style="color: var(--gray-600); font-size: 14px;">
                                            <i class="fas fa-clock"></i> <?php echo date('M d, Y', strtotime($task['due_date'])); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <?php if($task['description']): ?>
                            <p class="task-description"><?php echo nl2br(htmlspecialchars($task['description'])); ?></p>
                        <?php endif; ?>
                        
                        <div class="task-actions">
                            <a href="?action=edit&id=<?php echo $task['id']; ?>" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this task?');">
                                <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                <button type="submit" name="delete_task" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="card">
                    <div class="empty-state">
                        <i class="fas fa-tasks"></i>
                        <p>No tasks yet. Create your first task!</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>

    <?php elseif($action === 'create'): ?>
        <div class="page-header">
            <h1><i class="fas fa-plus"></i> Create New Task</h1>
            <a href="tasks.php" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>

        <?php if($error): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="task-form">
            <div class="form-group">
                <label for="title" class="form-label">Task Title *</label>
                <input type="text" class="form-control" id="title" name="title" required>
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="5"></textarea>
            </div>

            <div class="form-group">
                <label for="priority" class="form-label">Priority *</label>
                <select class="form-control form-select" id="priority" name="priority" required>
                    <option value="low">Low</option>
                    <option value="medium" selected>Medium</option>
                    <option value="high">High</option>
                    <option value="urgent">Urgent</option>
                </select>
            </div>

            <div class="form-group">
                <label for="due_date" class="form-label">Due Date</label>
                <input type="date" class="form-control" id="due_date" name="due_date">
            </div>

            <div class="form-group">
                <button type="submit" name="create_task" class="btn btn-primary">
                    <i class="fas fa-save"></i> Create Task
                </button>
                <a href="tasks.php" class="btn btn-outline">Cancel</a>
            </div>
        </form>

    <?php elseif($action === 'edit' && isset($task)): ?>
        <div class="page-header">
            <h1><i class="fas fa-edit"></i> Edit Task</h1>
            <a href="tasks.php" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>

        <?php if($error): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="task-form">
            <div class="form-group">
                <label for="title" class="form-label">Task Title *</label>
                <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($task['title']); ?>" required>
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="5"><?php echo htmlspecialchars($task['description']); ?></textarea>
            </div>

            <div class="form-group">
                <label for="status" class="form-label">Status *</label>
                <select class="form-control form-select" id="status" name="status" required>
                    <option value="pending" <?php echo $task['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="in_progress" <?php echo $task['status'] === 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                    <option value="completed" <?php echo $task['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                    <option value="cancelled" <?php echo $task['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                </select>
            </div>

            <div class="form-group">
                <label for="priority" class="form-label">Priority *</label>
                <select class="form-control form-select" id="priority" name="priority" required>
                    <option value="low" <?php echo $task['priority'] === 'low' ? 'selected' : ''; ?>>Low</option>
                    <option value="medium" <?php echo $task['priority'] === 'medium' ? 'selected' : ''; ?>>Medium</option>
                    <option value="high" <?php echo $task['priority'] === 'high' ? 'selected' : ''; ?>>High</option>
                    <option value="urgent" <?php echo $task['priority'] === 'urgent' ? 'selected' : ''; ?>>Urgent</option>
                </select>
            </div>

            <div class="form-group">
                <label for="due_date" class="form-label">Due Date</label>
                <input type="date" class="form-control" id="due_date" name="due_date" value="<?php echo $task['due_date']; ?>">
            </div>

            <div class="form-group">
                <button type="submit" name="update_task" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Task
                </button>
                <a href="tasks.php" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    <?php endif; ?>
</div>

<script>
function filterTasks(status) {
    const tasks = document.querySelectorAll('.task-card');
    const buttons = document.querySelectorAll('.filter-btn');
    
    buttons.forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');
    
    tasks.forEach(task => {
        if(status === 'all' || task.dataset.status === status) {
            task.style.display = 'block';
        } else {
            task.style.display = 'none';
        }
    });
}
</script>

<?php include 'includes/footer.php'; ?>
