<?php
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

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../frontend/css/page2.css">
</head>

<body>

<!-- NAV -->
<nav>
    <div style="display:flex;align-items:center;gap:12px;">
        <div class="logo">B</div>
        <h1>BalanceBuddy</h1>
    </div>

    <button onclick="logout()">Logout</button>
</nav>

<div class="main-container">

    <!-- SIDEBAR -->
    <aside class="sidebar">

        <div class="username" id="setUserName">
            <?php echo htmlspecialchars($username); ?>
        </div>

        <div class="dashboard">
            <button class="dashboard-item" onclick="showSection('home', event)">🏡 Home</button>
            <button class="dashboard-item" onclick="showSection('task-details', event)">➕ Add Task</button>
            <button class="dashboard-item" onclick="showSection('priority-list', event)">⚡ Priority</button>
            <button class="dashboard-item" onclick="showSection('task-tracker', event)">📊 Progress</button>
            <button class="dashboard-item" onclick="showSection('calendar', event)">📅 Calendar</button>
        </div>

    </aside>

    <!-- CONTENT -->
    <main class="content-area">

        <!-- HOME -->
        <section id="home" class="section active">

            <h2>Welcome back, <span id="welcomeName"><?php echo htmlspecialchars($username); ?></span></h2>

            <!-- STATS -->
            <div class="stats-grid">

                <div class="stat-card">
                    <p>Total Tasks</p>
                    <h3 id="total-tasks">0</h3>
                </div>

                <div class="stat-card success">
                    <p>Completed</p>
                    <h3 id="completed-tasks">0</h3>
                </div>

                <div class="stat-card warning">
                    <p>Pending</p>
                    <h3 id="pending-tasks">0</h3>
                </div>

            </div>

            <!-- PROGRESS -->
            <h3>Progress Overview</h3>

            <div class="progress-container">
                <div class="progress-bar" id="progress-bar"></div>
            </div>

            <p id="progress-text">0% completed</p>

            <div class="info-box">
                Track your tasks and stay productive 🚀
            </div>

        </section>

        <!-- ADD TASK -->
        <section id="task-details" class="section">

            <h2>Add / Edit Task</h2>

            <form id="task-form" onsubmit="submitTask(event)">
                <input type="hidden" id="task-id">

                <div class="form-group">
                    <label>Task Subject</label>
                    <input type="text" id="taskform" placeholder="Enter task subject" required>
                </div>

                <div class="form-group">
                    <label>Due Date</label>
                    <input type="date" id="date" required data-today>
                </div>

                <div class="button-group">
                    <button type="submit" id="submit-btn">Save Task</button>
                    <button type="button" id="cancel-btn" onclick="cancelEdit()" style="display:none; opacity:0.6;">
                        Cancel
                    </button>
                </div>
            </form>

        </section>

        <!-- PRIORITY -->
        <section id="priority-list" class="section">

            <h2>Priority Tasks</h2>

            <div class="priority-category red" id="red-tasks">
                <h4>Urgent</h4>
                <p>No urgent tasks</p>
            </div>

            <div class="priority-category orange" id="orange-tasks">
                <h4>Medium</h4>
                <p>No tasks</p>
            </div>

            <div class="priority-category yellow" id="yellow-tasks">
                <h4>Low</h4>
                <p>No tasks</p>
            </div>

        </section>

        <!-- TRACKER -->
        <section id="task-tracker" class="section">

            <h2>Task Tracker</h2>
            <div id="task-tracker-list">
                <p>No tasks yet. Add one to begin.</p>
            </div>

        </section>

        <!-- CALENDAR -->
        <section id="calendar" class="section">

            <h2>Calendar</h2>

            <div class="calendar-controls">
                <button onclick="prevMonth()">←</button>
                <span id="current-month"></span>
                <button onclick="nextMonth()">→</button>
            </div>

            <div class="calendar-grid" id="calendar-grid"></div>

            <form id="event-form" onsubmit="addEvent(event)">

                <input type="hidden" id="event-date">

                <div id="selected-date-display" class="info-box" style="display:none;">
                    Selected: <span id="date-text"></span>
                </div>

                <div class="form-group">
                    <label>Event Title</label>
                    <input type="text" id="event-title" placeholder="Enter event" required>
                </div>

                <div class="button-group">
                    <button type="submit">Add Event</button>
                </div>

            </form>

        </section>

    </main>

</div>

<!-- FOOTER -->
<footer>

    <p style="font-weight:600;">💡 Pressure Is Privilege</p>

    <div class="social-links">
        <a href="#">f</a>
        <a href="#">𝕏</a>
        <a href="#">📷</a>
        <a href="#">⚙️</a>
    </div>

</footer>

<!-- JS BRIDGE -->
<script>
    window.LOGGED_IN_USER = "<?php echo addslashes($username); ?>";

    document.addEventListener("DOMContentLoaded", () => {
        const today = new Date().toISOString().split("T")[0];
        document.querySelectorAll("[data-today]").forEach(i => i.min = today);
    });
</script>

<script src="../frontend/js/page2.js"></script>

</body>
</html>