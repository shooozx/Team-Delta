# BalanceBuddy

A personal task and event management system for students with intelligent priority calculation and progress tracking.

## 🎯 Features

- **User Authentication** - Secure signup/login for all students
- **Auto-Priority Calculation** - Tasks automatically categorized as Urgent (≤7 days), Medium (8-14 days), or Low (15+ days)
- **Task Management** - Create, edit, and delete tasks with automatic due date tracking
- **Task Completion** - Mark tasks complete and track completion dates
- **Monthly Reset** - Completed tasks automatically reset each month for fresh starts
- **Calendar Events** - Manage events on an interactive calendar
- **Dashboard Statistics** - View total tasks, completed tasks, and pending tasks
- **Progress Tracker** - Monitor task completion with visual indicators

## 🛠️ Tech Stack

- **Frontend:** HTML5, CSS3, Vanilla JavaScript (no frameworks)
- **Backend:** PHP 7.x with MySQL/MariaDB
- **Server:** XAMPP (Apache + MySQL)
- **Security:** Prepared statements, password hashing (bcrypt), CORS headers

## 📋 Database Schema

### Users Table
- User authentication and profile information
- Registration date tracking

### Tasks Table
- Task description and due dates
- Auto-calculated priority levels
- Completion status and dates
- Foreign key relationship to Users

### Events Table
- Calendar event management
- Event title and date
- Color-coded display (light yellow)
- Foreign key relationship to Users

## 🚀 Installation

### Prerequisites
- XAMPP installed with PHP and MySQL
- Git (for cloning the repository)

### Steps

1. **Clone the Repository**
   ```bash
   cd c:\xampp\htdocs
   git clone https://github.com/YOUR_USERNAME/BalanceBuddy.git t
   ```

2. **Start XAMPP Services**
   - Open XAMPP Control Panel
   - Start Apache and MySQL services

3. **Create Database**
   - Open phpMyAdmin: `http://localhost/phpmyadmin`
   - Create new database: `balancebuddy`
   - Run the SQL schema

4. **Access Application**
   - Open browser: `http://localhost/t/frontend/page1.html`
   - Sign up with your information
   - Start managing your tasks

## 📁 Project Structure

```
BalanceBuddy/
├── frontend/
│   ├── page1.html          # Login/Signup page
│   ├── page2.php           # Dashboard (requires login)
│   ├── css/
│   │   ├── page1.css       # Login/Signup styling
│   │   └── page2.css       # Dashboard styling
│   └── js/
│       ├── page1.js        # Authentication logic
│       └── page2.js        # Dashboard functionality
├── backend/
│   ├── config.php          # Database configuration
│   ├── login.php           # Authentication endpoint
│   ├── signup.php          # User registration endpoint
│   ├── add_task.php        # Create task endpoint
│   ├── update_task.php     # Update task endpoint
│   ├── get_tasks.php       # Retrieve tasks endpoint
│   ├── delete_task.php     # Delete task endpoint
│   ├── complete_task.php   # Mark task complete endpoint
│   ├── add_event.php       # Create event endpoint
│   ├── get_events.php      # Retrieve events endpoint
│   ├── delete_event.php    # Delete event endpoint
│   ├── reset_completed_tasks.php  # Monthly reset endpoint
│   └── logout.php          # Logout endpoint
└── README.md               # Project documentation
```

## 📊 Task Priority System
- **Urgent (Red - Hard):** Due within 7 days
- **Medium (Orange - Normal):** Due within 8-14 days
- **Low (Yellow - Easy):** Due 15+ days away

Priority is automatically calculated based on the task due date - no manual selection needed.

## ✅ Task Completion
- Mark tasks as complete with one click
- Track completion date automatically
- Completed tasks hidden from main task list
- Monthly auto-reset on first login of new month

## 🖥️ API Endpoints

All endpoints return JSON responses and require user authentication (via session).

| Method | Endpoint | Purpose |
|--------|----------|---------|
| POST | `/backend/signup.php` | Register new user |
| POST | `/backend/login.php` | User authentication |
| POST | `/backend/logout.php` | End user session |
| POST | `/backend/add_task.php` | Create task |
| GET | `/backend/get_tasks.php` | Retrieve user's tasks |
| POST | `/backend/update_task.php` | Modify task |
| POST | `/backend/delete_task.php` | Remove task |
| POST | `/backend/complete_task.php` | Mark task complete |
| POST | `/backend/add_event.php` | Create calendar event |
| GET | `/backend/get_events.php` | Retrieve calendar events |
| POST | `/backend/delete_event.php` | Remove event |

## 🎨 UI/UX Design

- **Modern Minimalist Theme** - Clean white background with black text
- **Color Coding** - Visual priority indicators (Red/Orange/Yellow)
- **Responsive Layout** - Sidebar navigation with main content area
- **Interactive Calendar** - Click dates to create events
- **Dashboard Statistics** - Real-time task counts and progress

## 🔄 Workflow

1. **Sign Up** - Create account with your details
2. **Add Tasks** - Enter task subject and due date
3. **View Priority** - Tasks auto-sorted by urgency
4. **Track Progress** - Monitor completion percentage
5. **Mark Complete** - Click complete button to finish tasks
6. **Monthly Reset** - Completed tasks reset automatically each month

## 📝 Usage Examples

### Adding a Task
1. Navigate to "Add Task" section
2. Enter task subject (e.g., "Study for Exam")
3. Select due date
4. Priority automatically assigned based on due date
5. Task appears in appropriate priority section

### Editing a Task
1. Go to "Progress Tracker" section
2. Click EDIT button on any task
3. Modify task details
4. Click "Update Task"
5. Changes saved automatically

### Completing a Task
1. View task in Priority List
2. Click "✓ Complete" button
3. Task marked complete and hidden from list
4. Completion date recorded automatically

## 🐛 Known Issues & Limitations

None currently identified. All features working as intended.

## 🚧 Future Enhancements

- Task categories/tags
- Recurring tasks (daily, weekly, monthly)
- Task dependencies/subtasks
- Email notifications for due dates
- Task notes/attachments
- Search and filter functionality
- Backup and restore database
- Dark mode theme

## 📧 Support

For issues or questions, please open an issue in the GitHub repository.

## 📄 License

This project is created for educational purposes.

---

**Version:** 1.0  
**Last Updated:** April 2026  
**Status:** Complete and Functional
