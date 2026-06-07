/**
 * Toggle navigation between dashboard sections.
 */
function showSection(sectionId, clickEvent) {
    const sections = document.querySelectorAll('.section');
    const dashboardItems = document.querySelectorAll('.dashboard-item');
    
    sections.forEach(section => section.classList.remove('active'));
    dashboardItems.forEach(item => item.classList.remove('active'));
    
    document.getElementById(sectionId).classList.add('active');
    if (clickEvent && clickEvent.target) {
        clickEvent.target.classList.add('active');
    }
}

/**
 * Display the logged-in username and initialize dashboard content.
 */
function displayUsername() {
    const loggedInUser = window.LOGGED_IN_USER;
    document.getElementById('setUserName').innerText = `@${loggedInUser}`;
    document.getElementById('welcomeName').innerText = loggedInUser;
    if (loggedInUser) {
        loadTasks();
        loadEvents();
        generateCalendar(currentMonth, currentYear);
    }
}

/**
 * Submit a task to be created or updated with automatic priority calculation.
 */
function submitTask(event) {
    event.preventDefault();
    const taskId = document.getElementById('task-id').value;
    const taskform = document.getElementById('taskform').value;
    const date = document.getElementById('date').value;

    if (!taskform || !date) {
        alert('Please fill out all fields.');
        return;
    }

    // Calculate priority automatically based on days until due date
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    const dueDate = new Date(date);
    dueDate.setHours(0, 0, 0, 0);
    
    const daysUntilDue = Math.ceil((dueDate - today) / (1000 * 60 * 60 * 24));
    
    let priority = 'Easy';
    if (daysUntilDue <= 7) {
        priority = 'Hard'; // Urgent
    } else if (daysUntilDue <= 14) {
        priority = 'Normal'; // Medium
    }

    const formData = new FormData();
    formData.append('taskform', taskform);
    formData.append('date', date);
    formData.append('prior', priority);

    let endpoint = '../backend/add_task.php';
    let successMessage = 'Task added successfully!';

    if (taskId) {
        endpoint = '../backend/update_task.php';
        formData.append('id', taskId);
        successMessage = 'Task updated successfully!';
    }

    fetch(endpoint, {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.text();
    })
    .then(text => {
        try {
            const data = JSON.parse(text);
            if (data.success) {
                alert(successMessage);
                document.getElementById('task-form').reset();
                document.getElementById('task-id').value = '';
                document.getElementById('task-form-title').innerText = '➕ Add New Task';
                document.getElementById('submit-btn').innerText = 'Add Task';
                document.getElementById('cancel-btn').style.display = 'none';
                loadTasks().then(() => {
                    showSection('home', null);
                });
            } else {
                alert(data.message || 'Failed to save task.');
            }
        } catch (e) {
            console.error('Response text:', text);
            console.error('Parse error:', e);
            alert('Server error. Please try again.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred: ' + error.message);
    });
}

/**
 * Fetch incomplete tasks from the backend and organize by priority level.
 */
function loadTasks() {
    return fetch('../backend/get_tasks.php')
    .then(response => response.text())
    .then(text => {
        try {
            const data = JSON.parse(text);
            if (data.success) {
                document.getElementById('red-tasks').innerHTML = '';
                document.getElementById('orange-tasks').innerHTML = '';
                document.getElementById('yellow-tasks').innerHTML = '';

                let redCount = 0, orangeCount = 0, yellowCount = 0;

                data.tasks.forEach(task => {
                    const taskItem = document.createElement('div');
                    taskItem.className = 'task-item';
                    taskItem.setAttribute('data-task-id', task.id);
                    
                    taskItem.innerHTML = `
                        <span>${task.taskform} - ${task.date}</span>
                        <button style="background: #4caf50; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 12px;" onclick="completeTask(${task.id})">✓ Complete</button>
                    `;

                    const taskDate = new Date(task.date);
                    const currentDate = new Date();
                    const oneWeek = 7 * 24 * 60 * 60 * 1000;
                    const twoWeeks = 14 * 24 * 60 * 60 * 1000;

                    if (taskDate - currentDate <= oneWeek) {
                        document.getElementById('red-tasks').appendChild(taskItem);
                        redCount++;
                    } else if (taskDate - currentDate <= twoWeeks) {
                        document.getElementById('orange-tasks').appendChild(taskItem);
                        orangeCount++;
                    } else {
                        document.getElementById('yellow-tasks').appendChild(taskItem);
                        yellowCount++;
                    }
                });

                if (redCount === 0) {
                    document.getElementById('red-tasks').innerHTML = '<p style="color: #999; margin: 0;">No urgent tasks</p>';
                }
                if (orangeCount === 0) {
                    document.getElementById('orange-tasks').innerHTML = '<p style="color: #999; margin: 0;">No tasks</p>';
                }
                if (yellowCount === 0) {
                    document.getElementById('yellow-tasks').innerHTML = '<p style="color: #999; margin: 0;">No tasks</p>';
                }

                updateTaskOverview();
                updateTaskTracker();
            }
        } catch (e) {
        }
    })
    .catch(error => {
    });
}

/** Storage for calendar events retrieved from the backend. */
let userEvents = [];

/**
 * Fetch calendar events from the backend and refresh calendar display.
 */
function loadEvents() {
    return fetch('../backend/get_events.php')
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.text();
    })
    .then(text => {
        try {
            const data = JSON.parse(text);
            if (data.success) {
                userEvents = data.events;
                generateCalendar(currentMonth, currentYear);
            }
        } catch (e) {
        }
    })
    .catch(error => {
    });
}

/**
 * Update dashboard progress overview and statistics based on current tasks.
 */
function updateTaskOverview() {
    // Update dashboard statistics display
    updateDashboardStats();
}

/**
 * Update all dashboard statistics including task counts and progress percentage.
 */
function updateDashboardStats() {
    // Retrieve actual completed task count from backend
    fetch('../backend/get_completed_count.php')
    .then(response => response.text())
    .then(text => {
        try {
            const data = JSON.parse(text);
            if (data.success) {
                const completedCount = data.completed_count;
                const redTasks = document.querySelectorAll('#red-tasks .task-item').length;
                const orangeTasks = document.querySelectorAll('#orange-tasks .task-item').length;
                const yellowTasks = document.querySelectorAll('#yellow-tasks .task-item').length;
                
                const pendingCount = redTasks + orangeTasks + yellowTasks;
                
                // Update statistics display elements
                // Total tasks = pending/incomplete tasks only (decreases when you complete tasks)
                document.getElementById('total-tasks').textContent = pendingCount;
                document.getElementById('completed-tasks').textContent = completedCount;
                document.getElementById('pending-tasks').textContent = pendingCount;
                
                // Update progress bar
                const totalCount = completedCount + pendingCount;
                const progressPercentage = totalCount > 0 ? (completedCount / totalCount) * 100 : 0;
                document.getElementById('progress-bar').style.width = Math.min(progressPercentage, 100) + '%';
                document.getElementById('progress-text').innerText = `${Math.round(progressPercentage)}% completed`;
            }
        } catch (e) {
            console.error('Error fetching completed count:', e);
        }
    })
    .catch(error => console.error('Error:', error));
}

/**
 * Build task tracker display with all tasks and action buttons for management.
 */
function updateTaskTracker() {
    const taskTrackerList = document.getElementById('task-tracker-list');
    taskTrackerList.innerHTML = '';

    const allTaskItems = document.querySelectorAll('#red-tasks .task-item, #orange-tasks .task-item, #yellow-tasks .task-item');

    if (allTaskItems.length === 0) {
        taskTrackerList.innerHTML = '<p style="color: #999;">No tasks to track yet. Add a task to get started!</p>';
        return;
    }

    allTaskItems.forEach(taskItem => {
        const taskId = taskItem.getAttribute('data-task-id');
        const taskText = taskItem.querySelector('span').textContent;
        const parent = taskItem.parentElement;
        let indicator = '🔴';
        
        if (parent.id === 'orange-tasks') indicator = '🟠';
        else if (parent.id === 'yellow-tasks') indicator = '🟡';

        const trackItem = document.createElement('div');
        trackItem.className = 'task-item';
        trackItem.setAttribute('data-task-id', taskId);
        
        const [taskName, taskDate] = taskText.split(' - ');
        let taskPriority = 'Easy';
        if (parent.id === 'red-tasks') taskPriority = 'Hard';
        else if (parent.id === 'orange-tasks') taskPriority = 'Normal';
        
        const editBtn = `<button onclick="editTask(${taskId}, '${taskName.trim().replace(/'/g, "\\'")}', '${taskDate.trim()}', '${taskPriority}')" style="background: none; border: none; color: #667eea; cursor: pointer; font-weight: 600; font-size: 0.85em;">EDIT</button>`;
        const deleteBtn = `<button onclick="deleteTask(${taskId})" style="background: none; border: none; color: #999; cursor: pointer; font-weight: 600; font-size: 0.85em;">DELETE</button>`;
        const closeBtn = `<button onclick="deleteTask(${taskId})" style="background: #f44336; color: white; border: none; border-radius: 50%; width: 28px; height: 28px; cursor: pointer; font-weight: bold; font-size: 0.9em; padding: 0;">×</button>`;
        
        trackItem.innerHTML = `<div style="display: flex; align-items: center; justify-content: space-between; width: 100%;"><div style="display: flex; align-items: center; gap: 10px; flex: 1;"><span style="font-size: 1.2em;">${indicator}</span><span style="color: #333; font-weight: 500;">${taskText}</span></div><div style="display: flex; gap: 8px; align-items: center;">${editBtn} ${deleteBtn} ${closeBtn}</div></div>`;
        
        taskTrackerList.appendChild(trackItem);
    });
}

/**
 * Delete a task from the database and refresh task display.
 */
function deleteTask(taskId) {
    if (confirm('Are you sure you want to delete this task?')) {
        fetch(`../backend/delete_task.php?id=${taskId}`, {
            method: 'GET'
        })
        .then(response => response.text())
        .then(text => {
            try {
                const data = JSON.parse(text);
                if (data.success) {
                    loadTasks();
                } else {
                    alert(data.message || 'Failed to delete task.');
                }
            } catch (e) {
                alert('Error deleting task.');
            }
        })
        .catch(error => {
            alert('An error occurred. Please try again.');
        });
    }
}

/**
 * Populate the task form with existing task data for editing.
 */
function editTask(taskId, taskText, taskDate, taskPriority) {
    document.getElementById('task-id').value = taskId;
    document.getElementById('taskform').value = taskText;
    document.getElementById('date').value = taskDate;
    // Priority is automatically calculated and not user-selectable
    document.getElementById('task-form-title').innerText = '✏️ Edit Task';
    document.getElementById('submit-btn').innerText = 'Update Task';
    document.getElementById('cancel-btn').style.display = 'inline-block';
    showSection('task-details', null);
    document.getElementById('taskform').focus();
}

/**
 * Reset the task form to add mode, clearing any edit state.
 */
function cancelEdit() {
    document.getElementById('task-form').reset();
    document.getElementById('task-id').value = '';
    document.getElementById('task-form-title').innerText = '➕ Add New Task';
    document.getElementById('submit-btn').innerText = 'Add Task';
    document.getElementById('cancel-btn').style.display = 'none';
    showSection('home', null);
}

/**
 * Handle user logout by confirming and redirecting to login page.
 */
function logout() {
    if (confirm('Are you sure you want to logout?')) {
        window.location.href = '../backend/logout.php';
    }
}

// Calendar Generation and Navigation
/** Current month for calendar display. */
let currentMonth = new Date().getMonth();
/** Current year for calendar display. */
let currentYear = new Date().getFullYear();

/**
 * Handle calendar date selection and populate event form.
 */
function selectCalendarDate(day) {
    // Format date as YYYY-MM-DD using local timezone
    const month = String(currentMonth + 1).padStart(2, '0');
    const dayStr = String(day).padStart(2, '0');
    const dateString = `${currentYear}-${month}-${dayStr}`;
    
    document.getElementById('event-date').value = dateString;
    
    const dateDisplay = document.getElementById('selected-date-display');
    const dateText = document.getElementById('date-text');
    const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
    dateText.textContent = `${monthNames[currentMonth]} ${day}, ${currentYear}`;
    dateDisplay.style.display = 'block';
    
    // Display existing events for this date with delete buttons
    const eventsForDay = userEvents.filter(event => {
        const eventDate = new Date(event.date);
        return eventDate.getDate() === day && eventDate.getMonth() === currentMonth && eventDate.getFullYear() === currentYear;
    });
    
    if (eventsForDay.length > 0) {
        let eventsHTML = '<div style="margin-top: 12px; padding: 12px; background: #ffd54f; border-radius: 6px;"><strong>Events:</strong><ul style="margin: 8px 0 0 0; padding-left: 20px;">';
        eventsForDay.forEach(evt => {
            eventsHTML += '<li style="margin: 6px 0;">' + evt.title + ' <button type="button" onclick="deleteEvent(' + evt.id + ')" style="background: #e53935; color: white; border: none; padding: 4px 8px; border-radius: 3px; cursor: pointer; font-size: 11px; margin-left: 6px;">Cancel</button></li>';
        });
        eventsHTML += '</ul></div>';
        
        let existingEvents = document.getElementById('existing-events-display');
        if (!existingEvents) {
            existingEvents = document.createElement('div');
            existingEvents.id = 'existing-events-display';
            dateDisplay.parentNode.insertBefore(existingEvents, dateDisplay.nextSibling);
        }
        existingEvents.innerHTML = eventsHTML;
    } else {
        const existingEvents = document.getElementById('existing-events-display');
        if (existingEvents) {
            existingEvents.innerHTML = '';
        }
    }
}

function generateCalendar(month, year) {
    const calendarGrid = document.getElementById('calendar-grid');
    calendarGrid.innerHTML = '';

    const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
    const dayNames = ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"];
    document.getElementById('current-month').innerText = `${monthNames[month]} ${year}`;

    dayNames.forEach(day => {
        const dayHeader = document.createElement('div');
        dayHeader.className = 'calendar-header';
        dayHeader.innerText = day;
        calendarGrid.appendChild(dayHeader);
    });

    const firstDay = (new Date(year, month).getDay() + 6) % 7;
    const daysInMonth = new Date(year, month + 1, 0).getDate();

    for (let i = 0; i < firstDay; i++) {
        const emptyCell = document.createElement('div');
        emptyCell.className = 'calendar-day';
        emptyCell.style.background = 'transparent';
        emptyCell.style.border = 'none';
        calendarGrid.appendChild(emptyCell);
    }

    for (let day = 1; day <= daysInMonth; day++) {
        const dayCell = document.createElement('div');
        dayCell.className = 'calendar-day';
        dayCell.innerText = day;
        dayCell.style.cursor = 'pointer';

        const eventForDay = userEvents.filter(event => {
            const eventDate = new Date(event.date);
            return eventDate.getDate() === day && eventDate.getMonth() === month && eventDate.getFullYear() === year;
        });

        if (eventForDay.length > 0) {
            dayCell.style.background = '#ffd54f';
            dayCell.style.borderColor = '#ffd54f';
            dayCell.style.fontWeight = 'bold';
            dayCell.title = eventForDay.map(e => e.title).join(', ');
        }
        
        dayCell.addEventListener('click', () => selectCalendarDate(day));

        calendarGrid.appendChild(dayCell);
    }
}

/**
 * Display calendar for the previous month.
 */
function prevMonth() {
    currentMonth--;
    if (currentMonth < 0) {
        currentMonth = 11;
        currentYear--;
    }
    generateCalendar(currentMonth, currentYear);
}

/**
 * Display calendar for the next month.
 */
function nextMonth() {
    currentMonth++;
    if (currentMonth > 11) {
        currentMonth = 0;
        currentYear++;
    }
    generateCalendar(currentMonth, currentYear);
}

/**
 * Create a new calendar event for the selected date.
 */
function addEvent(event) {
    event.preventDefault();
    const eventDate = document.getElementById('event-date').value;
    const eventTitle = document.getElementById('event-title').value;

    if (!eventDate || !eventTitle) {
        alert('Please select a date and enter an event title.');
        return;
    }

    const formData = new FormData();
    formData.append('date', eventDate);
    formData.append('title', eventTitle);
    formData.append('color', '#ffd54f');

    fetch('../backend/add_event.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.text();
    })
    .then(text => {
        try {
            const data = JSON.parse(text);
            if (data.success) {
                document.getElementById('event-date').value = '';
                document.getElementById('event-title').value = '';
                document.getElementById('selected-date-display').style.display = 'none';
                
                loadEvents();
                alert('Event added successfully!');
            } else {
                alert(data.message || 'Failed to add event.');
            }
        } catch (e) {
            console.error('Response text:', text);
            alert('Server error. Please try again.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred: ' + error.message);
    });
}

/**
 * Delete a calendar event after user confirmation.
 */
function deleteEvent(eventId) {
    if (confirm('Are you sure you want to delete this event?')) {
        const formData = new FormData();
        formData.append('eventId', eventId);

        fetch('../backend/delete_event.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(text => {
            try {
                const data = JSON.parse(text);
                if (data.success) {
                    loadEvents().then(() => {
                        // Refresh the calendar to show updated events
                        generateCalendar(currentMonth, currentYear);
                        alert('Event deleted successfully!');
                    });
                } else {
                    alert(data.message || 'Failed to delete event.');
                }
            } catch (e) {
                alert('Error deleting event.');
            }
        })
        .catch(error => {
            alert('An error occurred. Please try again.');
        });
    }
}

/**
 * Mark a task as completed and update dashboard statistics.
 */
function completeTask(taskId) {
    const formData = new FormData();
    formData.append('taskId', taskId);

    fetch('../backend/complete_task.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(text => {
        try {
            const data = JSON.parse(text);
            if (data.success) {
                // Wait for loadTasks() to complete before updating dashboard stats
                loadTasks().then(() => {
                    updateDashboardStats();
                    alert('Task marked as complete!');
                });
            } else {
                alert(data.message || 'Failed to mark task as complete.');
            }
        } catch (e) {
            console.error('Response text:', text);
            alert('Server error. Please try again.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred: ' + error.message);
    });
}

/**\n * Initialize dashboard on page load and set up event listeners.\n */
window.onload = function() {
    // Check and reset completed tasks if new month
    // Temporarily disabled due to session handling issues
    /*
    fetch('../backend/reset_completed_tasks.php', {
        method: 'POST'
    })
    .then(response => response.text())
    .then(text => {
        try {
            const data = JSON.parse(text);
            console.log('Reset check result:', data);
        } catch (e) {
            console.error('Reset check failed:', e);
        }
    })
    .catch(error => console.error('Error checking reset:', error));
    */

    displayUsername();
    loadTasks();
    loadEvents();
    generateCalendar(currentMonth, currentYear);
};
