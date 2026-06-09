/**
 * Toggle navigation between dashboard sections.
 */
function showSection(sectionId, event) {
    const sections = document.querySelectorAll(".section");
    const buttons = document.querySelectorAll(".sidebar button");

    sections.forEach(sec => sec.classList.remove("active"));
    buttons.forEach(btn => btn.classList.remove("active"));

    document.getElementById(sectionId).classList.add("active");

    if (event && event.target) {
        event.target.classList.add("active");
    }
}

function logout() {
    if (confirm("Logout?")) {
        window.location.href = "../backend/logout.php";
    }
}

function displayUsername() {
    const user = window.LOGGED_IN_USER;
    document.getElementById("welcomeName")?.innerText = user;
    document.getElementById("setUserName")?.innerText = user;
}

/* ================= TASKS ================= */

function submitTask(event) {
    event.preventDefault();

    const id = document.getElementById("task-id").value;
    const task = document.getElementById("taskform").value;
    const date = document.getElementById("date").value;

    if (!task || !date) return alert("Fill all fields");

    const formData = new FormData();
    formData.append("taskform", task);
    formData.append("date", date);

    let url = "../backend/add_task.php";
    if (id) {
        url = "../backend/update_task.php";
        formData.append("id", id);
    }

    fetch(url, { method: "POST", body: formData })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                alert("Saved!");
                document.getElementById("task-form")?.reset();
                loadTasks();
            } else {
                alert(data.message);
            }
        });
}

function loadTasks() {
    return fetch("../backend/get_tasks.php")
        .then(r => r.json())
        .then(data => {
            if (!data.success) return;

            const red = document.getElementById("red-tasks");
            const orange = document.getElementById("orange-tasks");
            const yellow = document.getElementById("yellow-tasks");

            red.innerHTML = "";
            orange.innerHTML = "";
            yellow.innerHTML = "";

            data.tasks.forEach(t => {
                const div = document.createElement("div");
                div.className = "task-item";
                div.innerHTML = `
                    <span>${t.taskform} - ${t.date}</span>
                    <button onclick="completeTask(${t.id})">Done</button>
                `;

                const due = new Date(t.date);
                const now = new Date();
                const diff = (due - now) / (1000 * 60 * 60 * 24);

                if (diff <= 7) red.appendChild(div);
                else if (diff <= 14) orange.appendChild(div);
                else yellow.appendChild(div);
            });

            updateStats();
        });
}

function completeTask(id) {
    const formData = new FormData();
    formData.append("taskId", id);

    fetch("../backend/complete_task.php", {
        method: "POST",
        body: formData
    })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                loadTasks();
            } else {
                alert(data.message);
            }
        });
}

/* ================= STATS ================= */

function updateStats() {
    fetch("../backend/get_completed_count.php")
        .then(r => r.json())
        .then(data => {
            if (!data.success) return;

            const completed = data.completed_count;

            const pending =
                document.querySelectorAll("#red-tasks .task-item").length +
                document.querySelectorAll("#orange-tasks .task-item").length +
                document.querySelectorAll("#yellow-tasks .task-item").length;

            document.getElementById("completed-tasks").innerText = completed;
            document.getElementById("pending-tasks").innerText = pending;
            document.getElementById("total-tasks").innerText = pending + completed;

            const total = pending + completed;
            const percent = total ? (completed / total) * 100 : 0;

            document.getElementById("progress-bar").style.width = percent + "%";
            document.getElementById("progress-text").innerText =
                Math.round(percent) + "% completed";
        });
}

/* ================= CALENDAR ================= */

let currentMonth = new Date().getMonth();
let currentYear = new Date().getFullYear();
let userEvents = [];

function generateCalendar(month, year) {
    const grid = document.getElementById("calendar-grid");
    grid.innerHTML = "";

    const days = ["Mon","Tue","Wed","Thu","Fri","Sat","Sun"];
    days.forEach(d => {
        const h = document.createElement("div");
        h.innerText = d;
        grid.appendChild(h);
    });

    const first = new Date(year, month).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();

    for (let i = 0; i < first; i++) {
        grid.appendChild(document.createElement("div"));
    }

    for (let d = 1; d <= daysInMonth; d++) {
        const cell = document.createElement("div");
        cell.className = "calendar-day";
        cell.innerText = d;

        cell.onclick = () => selectCalendarDate(d);

        grid.appendChild(cell);
    }
}

function prevMonth() {
    currentMonth--;
    if (currentMonth < 0) {
        currentMonth = 11;
        currentYear--;
    }
    generateCalendar(currentMonth, currentYear);
}

function nextMonth() {
    currentMonth++;
    if (currentMonth > 11) {
        currentMonth = 0;
        currentYear++;
    }
    generateCalendar(currentMonth, currentYear);
}

function selectCalendarDate(day) {
    const date = `${currentYear}-${String(currentMonth+1).padStart(2,'0')}-${String(day).padStart(2,'0')}`;
    document.getElementById("event-date").value = date;
}

/* ================= EVENTS ================= */

function loadEvents() {
    return fetch("../backend/get_events.php")
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                userEvents = data.events;
                generateCalendar(currentMonth, currentYear);
            }
        });
}

function addEvent(event) {
    event.preventDefault();

    const date = document.getElementById("event-date").value;
    const title = document.getElementById("event-title").value;

    const formData = new FormData();
    formData.append("date", date);
    formData.append("title", title);

    fetch("../backend/add_event.php", {
        method: "POST",
        body: formData
    })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                loadEvents();
                alert("Event added");
            }
        });
}

/* ================= INIT ================= */

window.onload = function () {
    displayUsername();
    loadTasks();
    loadEvents();
    generateCalendar(currentMonth, currentYear);
};