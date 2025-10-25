// Tasks page JavaScript

// Task checkbox toggle
document.addEventListener('DOMContentLoaded', function() {
    const taskToggles = document.querySelectorAll('.task-toggle');
    taskToggles.forEach(toggle => {
        toggle.addEventListener('change', function() {
            const taskId = this.dataset.id;
            const status = this.checked ? 'completed' : 'pending';
            
            const formData = new FormData();
            formData.append('action', 'toggle_status');
            formData.append('id', taskId);
            formData.append('status', status);
            
            fetch('tasks.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert(data.message, 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showAlert(data.message, 'error');
                    this.checked = !this.checked;
                }
            })
            .catch(error => {
                showAlert('An error occurred', 'error');
                this.checked = !this.checked;
            });
        });
    });
});

function editTask(task) {
    document.getElementById('modalTitle').textContent = 'Edit Task';
    document.getElementById('task_id').value = task.id;
    document.getElementById('task_action').value = 'update';
    document.getElementById('title').value = task.title;
    document.getElementById('description').value = task.description || '';
    document.getElementById('priority').value = task.priority;
    document.getElementById('status').value = task.status;
    
    if (task.due_date) {
        const date = new Date(task.due_date);
        const formattedDate = date.toISOString().slice(0, 16);
        document.getElementById('due_date').value = formattedDate;
    }
    
    openModal('taskModal');
}

function submitTask() {
    const form = document.getElementById('taskForm');
    const formData = new FormData(form);
    
    fetch('tasks.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert(data.message, 'success');
            closeModal('taskModal');
            setTimeout(() => location.reload(), 1000);
        } else {
            showAlert(data.message, 'error');
        }
    })
    .catch(error => {
        showAlert('An error occurred', 'error');
    });
}

function deleteTask(id) {
    if (!confirm('Are you sure you want to delete this task?')) {
        return;
    }
    
    const formData = new FormData();
    formData.append('action', 'delete');
    formData.append('id', id);
    
    fetch('tasks.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert(data.message, 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showAlert(data.message, 'error');
        }
    })
    .catch(error => {
        showAlert('An error occurred', 'error');
    });
}

// Reset form when opening new task modal
document.addEventListener('click', function(e) {
    if (e.target.matches('[onclick*="openModal(\'taskModal\')"]')) {
        document.getElementById('modalTitle').textContent = 'New Task';
        document.getElementById('taskForm').reset();
        document.getElementById('task_id').value = '';
        document.getElementById('task_action').value = 'create';
    }
});
