// Calendar page JavaScript

let currentDate = new Date();
let events = [];

document.addEventListener('DOMContentLoaded', function() {
    loadEvents();
    renderCalendar();
});

function loadEvents() {
    const formData = new FormData();
    formData.append('action', 'get_events');
    
    fetch('calendar.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            events = data.events;
            renderCalendar();
            displayUpcomingEvents();
        }
    })
    .catch(error => {
        console.error('Error loading events:', error);
    });
}

function renderCalendar() {
    const year = currentDate.getFullYear();
    const month = currentDate.getMonth();
    
    // Update header
    const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                       'July', 'August', 'September', 'October', 'November', 'December'];
    document.getElementById('currentMonth').textContent = `${monthNames[month]} ${year}`;
    
    // Simple calendar view (can be enhanced)
    const calendarView = document.getElementById('calendarView');
    calendarView.innerHTML = `
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> 
            Calendar view for ${monthNames[month]} ${year}. 
            See upcoming events below.
        </div>
    `;
}

function displayUpcomingEvents() {
    const eventsList = document.getElementById('eventsList');
    
    if (events.length === 0) {
        eventsList.innerHTML = '<p class="text-center" style="padding: 2rem; color: #999;">No upcoming events</p>';
        return;
    }
    
    // Filter future events
    const now = new Date();
    const upcomingEvents = events.filter(e => new Date(e.start_datetime) >= now);
    
    if (upcomingEvents.length === 0) {
        eventsList.innerHTML = '<p class="text-center" style="padding: 2rem; color: #999;">No upcoming events</p>';
        return;
    }
    
    let html = '';
    upcomingEvents.forEach(event => {
        const start = new Date(event.start_datetime);
        const end = new Date(event.end_datetime);
        
        html += `
            <div class="event-item" style="border-left-color: ${event.color}">
                <div class="event-title">${event.title}</div>
                <div class="event-meta">
                    <i class="fas fa-clock"></i> 
                    ${start.toLocaleDateString()} ${start.toLocaleTimeString('en-US', {hour: '2-digit', minute: '2-digit'})}
                    - ${end.toLocaleTimeString('en-US', {hour: '2-digit', minute: '2-digit'})}
                </div>
                ${event.location ? `<div class="event-meta"><i class="fas fa-map-marker-alt"></i> ${event.location}</div>` : ''}
                ${event.description ? `<div class="event-meta">${event.description}</div>` : ''}
                <div style="margin-top: 0.5rem;">
                    <button class="btn btn-small btn-danger" onclick="deleteEvent(${event.id})">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </div>
            </div>
        `;
    });
    
    eventsList.innerHTML = html;
}

function submitEvent() {
    const form = document.getElementById('eventForm');
    const formData = new FormData(form);
    
    fetch('calendar.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert(data.message, 'success');
            closeModal('eventModal');
            loadEvents();
        } else {
            showAlert(data.message, 'error');
        }
    })
    .catch(error => {
        showAlert('An error occurred', 'error');
    });
}

function deleteEvent(id) {
    if (!confirm('Are you sure you want to delete this event?')) {
        return;
    }
    
    const formData = new FormData();
    formData.append('action', 'delete');
    formData.append('id', id);
    
    fetch('calendar.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert(data.message, 'success');
            loadEvents();
        } else {
            showAlert(data.message, 'error');
        }
    })
    .catch(error => {
        showAlert('An error occurred', 'error');
    });
}

function previousMonth() {
    currentDate.setMonth(currentDate.getMonth() - 1);
    renderCalendar();
}

function nextMonth() {
    currentDate.setMonth(currentDate.getMonth() + 1);
    renderCalendar();
}

function goToToday() {
    currentDate = new Date();
    renderCalendar();
}
