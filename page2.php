<?php
// BalanceBuddy - User Dashboard with Task Management and Calendar
session_start();
if (!isset($_SESSION['loggedInUser'])) {
    header('Location: ../frontend/page1.html');
    exit;
}
$username = $_SESSION['loggedInUser'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BalanceBuddy Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../frontend/css/page2.css">
</head>
<body>
    <nav>
        <div style="display: flex; align-items: center; gap: 15px;">
            <div class="logo">B</div>
            <h1>BalanceBuddy</h1>
        </div>
        <button onclick="logout()">Logout</button>
    </nav>

    <div class="main-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="username" id="setUserName"></div>
            <div class="dashboard">
                <button class="dashboard-item" onclick="showSection('home', event)">🏡 Home</button>
                <button class="dashboard-item" onclick="showSection('task-details', event)">➕ Add Task</button>
                <button class="dashboard-item" onclick="showSection('priority-list', event)">⚡ Priority List</button>
                <button class="dashboard-item" onclick="showSection('task-tracker', event)">📊 Progress</button>
                <button class="dashboard-item" onclick="showSection('calendar', event)">📅 Calendar</button>

                <button class="dashboard-item" onclick="showSection('visual-board', event)">📋 Visual Task Board</button>
                <button class="dashboard-item" onclick="showSection('analytics-dashboard', event)">📈 Analytics & Reports</button>
            </div>
        </div>

        <!-- Content Area -->
        <div class="content-area">
            <!-- Home Section -->
            <div id="home" class="section active">
                <h2>Welcome Back, <span id="welcomeName"></span>!</h2>
                <div style="margin-top: 30px;">
                    <h3 style="color: #1a1a1a; margin-bottom: 20px; font-size: 16px;">📊 Dashboard Stats</h3>
                    
                    <!-- Stats Cards -->
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 30px;">
                        <div style="background: #f5f5f5; padding: 20px; border-radius: 8px; border-left: 4px solid #1a1a1a;">
                            <p style="color: #999; font-size: 12px; margin: 0 0 8px 0;">Total Tasks</p>
                            <p style="font-size: 28px; font-weight: 700; color: #1a1a1a; margin: 0;" id="total-tasks">0</p>
                        </div>
                        
                        <div style="background: #f5f5f5; padding: 20px; border-radius: 8px; border-left: 4px solid #4caf50;">
                            <p style="color: #999; font-size: 12px; margin: 0 0 8px 0;">Completed Tasks</p>
                            <p style="font-size: 28px; font-weight: 700; color: #4caf50; margin: 0;" id="completed-tasks">0</p>
                        </div>
                        
                        <div style="background: #f5f5f5; padding: 20px; border-radius: 8px; border-left: 4px solid #ff9800;">
                            <p style="color: #999; font-size: 12px; margin: 0 0 8px 0;">Pending Tasks</p>
                            <p style="font-size: 28px; font-weight: 700; color: #ff9800; margin: 0;" id="pending-tasks">0</p>
                        </div>
                    </div>
                    
                    <h3 style="color: #1a1a1a; margin-bottom: 15px; font-size: 16px;">📈 Progress Overview</h3>
                    <div class="progress-container">
                        <div class="progress-bar" id="progress-bar" style="width: 0%;"></div>
                    </div>
                    <p style="color: #999; font-size: 0.9em; margin-top: 10px;" id="progress-text">0% completed</p>
                    
                    <div id="task-summary" style="margin-top: 20px; padding: 16px; background: #f5f5f5; border-radius: 8px;">
                        <p style="color: #666; margin: 0;">Track your tasks and stay productive!</p>
                    </div>
                </div>
            </div>

            <!-- Task Details Section -->
            <div id="task-details" class="section">
                <h2 id="task-form-title">➕ Add New Task</h2>
                <form id="task-form" onsubmit="submitTask(event)" style="margin-top: 25px;">
                    <input type="hidden" id="task-id" value="">
                    <div class="form-group">
                        <label for="taskform">Task Subject</label>
                        <input type="text" id="taskform" placeholder="Enter task subject" required>
                    </div>
                    <div class="form-group">
                        <label for="date">Due Date</label>
                        <input type="date" id="date" required min="" data-today>
                    </div>
                    <div class="button-group">
                        <button type="submit" id="submit-btn">Add Task</button>
                        <button type="button" id="cancel-btn" style="display:none; background: #999;" onclick="cancelEdit()">Cancel Edit</button>
                    </div>
                </form>
            </div>

            <!-- Priority List Section -->
            <div id="priority-list" class="section">
                <h2>⚡ Priority Tasks</h2>
                <div style="margin-top: 25px;">
                    <h3 style="color: #f44336; font-size: 1.1em; margin-bottom: 10px;">🔴 Urgent (Due within 7 days)</h3>
                    <div class="priority-category red" id="red-tasks">
                        <p style="color: #999; margin: 0;">No urgent tasks</p>
                    </div>

                    <h3 style="color: #ff9800; font-size: 1.1em; margin-bottom: 10px; margin-top: 20px;">🟠 Medium Priority (Due within 14 days)</h3>
                    <div class="priority-category orange" id="orange-tasks">
                        <p style="color: #999; margin: 0;">No tasks</p>
                    </div>

                    <h3 style="color: #ffeb3b; font-size: 1.1em; margin-bottom: 10px; margin-top: 20px;">🟡 Low Priority (Due after 14 days)</h3>
                    <div class="priority-category yellow" id="yellow-tasks">
                        <p style="color: #999; margin: 0;">No tasks</p>
                    </div>
                </div>
            </div>

            <!-- Task Tracker Section -->
            <div id="task-tracker" class="section">
                <h2>📊 Task Progress Tracker</h2>
                <div id="task-tracker-list" style="margin-top: 25px;">
                    <p style="color: #999;">No tasks to track yet. Add a task to get started!</p>
                </div>
            </div>

            <!-- Calendar Section -->
            <div id="calendar" class="section">
                <h2>📅 Calendar & Events</h2>
                <div class="calendar-controls" style="margin-top: 25px;">
                    <button onclick="prevMonth()">← Previous</button>
                    <span id="current-month" style="font-size: 1.2em; font-weight: bold; color: #667eea;"></span>
                    <button onclick="nextMonth()">Next →</button>
                </div>
                <div class="calendar-grid" id="calendar-grid">
                    <!-- Calendar will be dynamically generated here -->
                </div>
                <form id="event-form" onsubmit="addEvent(event)" style="margin-top: 30px;">
                    <input type="hidden" id="event-date" value="">
                    <p id="selected-date-display" style="color: #1a1a1a; font-weight: 600; margin-bottom: 16px; padding: 12px; background: #f5f5f5; border-radius: 6px; text-align: center; display: none;">
                        Selected Date: <span id="date-text"></span>
                    </p>
                    <div class="form-group">
                        <label for="event-title">Event Title</label>
                        <input type="text" id="event-title" placeholder="Enter event title" required>
                    </div>
                    <div class="button-group">
                        <button type="submit">Add Event</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<div id="visual-board" class="section" style="display: none;">
    <h2>📋 Visual Task Board</h2>
    <p style="color: #666; margin-bottom: 20px;">Kargahin at ilipat ang mga gawain para baguhin ang kanilang status sa workflow.</p>
    
    <div class="kanban-wrapper">
        <div class="kanban-column" id="todo-col" ondragover="allowDrop(event)" ondrop="drop(event, 'todo')">
            <h3>To Do</h3>
            <div class="kanban-cards-container" id="todo-cards">
                <div class="task-card" id="task-sample" draggable="true" ondragstart="drag(event)">
                    <strong>Ayusin ang Frontend Architecture</strong>
                    <p style="font-size: 11px; color: #888; margin-top: 5px;">Priority: High</p>
                </div>
            </div>
        </div>

        <div class="kanban-column" id="progress-col" ondragover="allowDrop(event)" ondrop="drop(event, 'inprogress')">
            <h3>In Progress</h3>
            <div class="kanban-cards-container" id="progress-cards"></div>
        </div>

        <div class="kanban-column" id="done-col" ondragover="allowDrop(event)" ondrop="drop(event, 'done')">
            <h3>Done</h3>
            <div class="kanban-cards-container" id="done-cards"></div>
        </div>
    </div>
</div>

<div id="analytics-dashboard" class="section" style="display: none;">
    <div class="analytics-header">
        <div>
            <h2>📈 Interactive Analytics Dashboard</h2>
            <p style="color: #666; margin-top: 5px;">Multidimensional insights and business intelligence reports.</p>
        </div>
        <div class="export-buttons">
            <button onclick="exportReport('csv')" class="btn-export csv-btn">📥 Export CSV</button>
            <button onclick="exportReport('pdf')" class="btn-export pdf-btn">📄 Export PDF</button>
        </div>
    </div>
    
    <div class="analytics-summary-cards">
        <div class="summary-card">
            <h4>Total Tasks Completed</h4>
            <p class="summary-number">24</p>
        </div>
        <div class="summary-card">
            <h4>Productivity Rate</h4>
            <p class="summary-number">85%</p>
        </div>
    </div>

    <div class="charts-grid">
        <div class="chart-container">
            <h3>Task Completion Trend (Roll-up Analysis)</h3>
            <canvas id="trendChart"></canvas>
        </div>
        <div class="chart-container">
            <h3>Productivity by Priority (Slice & Dice)</h3>
            <canvas id="priorityChart"></canvas>
        </div>
    </div>
</div>

    <footer>
        <p style="font-size: 1.1em; font-weight: 600; margin-bottom: 10px;">💡 "Pressure Is Privilege"</p>
        <div class="social-links">
            <a href="https://facebook.com" target="_blank" title="Facebook">f</a>
            <a href="https://twitter.com" target="_blank" title="Twitter">𝕏</a>
            <a href="https://instagram.com" target="_blank" title="Instagram">📷</a>
            <a href="https://github.com" target="_blank" title="GitHub">⚙️</a>
        </div>
    </footer>

    <!-- Pass PHP session username to JS safely -->
    <script>
        window.LOGGED_IN_USER = '<?php echo addslashes($username); ?>';
        
        // Set minimum date to today for date inputs
        function setMinDateToToday() {
            const today = new Date().toISOString().split('T')[0];
            const dateInputs = document.querySelectorAll('input[data-today]');
            dateInputs.forEach(input => {
                input.setAttribute('min', today);
            });
        }
        
        // Run when DOM is loaded
        document.addEventListener('DOMContentLoaded', setMinDateToToday);
    </script>
    <script src="../frontend/js/page2.js"></script>
</body>
</html>
