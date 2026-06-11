<?php
session_start();

/** Set CORS headers for cross-origin requests. */
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

/** Handle preflight OPTIONS requests. */
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

/** Verify user is logged in before processing. */
if (!isset($_SESSION['loggedInUser'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

include 'config.php';

$username = $_SESSION['loggedInUser'];
$taskId = $_POST['taskId'] ?? null;

if (!$taskId) {
    echo json_encode(['success' => false, 'message' => 'Task ID is required']);
    exit;
}

/** Check if required columns exist in tasks table. */
$checkColumns = $conn->query("DESCRIBE tasks");
$columnsExist = ['completed' => false, 'completed_date' => false];

while ($col = $checkColumns->fetch_assoc()) {
    if ($col['Field'] === 'completed') {
        $columnsExist['completed'] = true;
    }
    if ($col['Field'] === 'completed_date') {
        $columnsExist['completed_date'] = true;
    }
}

/** Add missing columns to tasks table if they do not exist. */
if (!$columnsExist['completed']) {
    $conn->query("ALTER TABLE tasks ADD COLUMN completed TINYINT DEFAULT 0");
}
if (!$columnsExist['completed_date']) {
    $conn->query("ALTER TABLE tasks ADD COLUMN completed_date DATE");
}

/** Update task record to mark as completed. */
$completedDate = date('Y-m-d');
$stmt = $conn->prepare("UPDATE tasks SET completed = 1, completed_date = ? WHERE id = ? AND username = ?");
$stmt->bind_param("sis", $completedDate, $taskId, $username);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Task marked as complete']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update task']);
}

$stmt->close();
$conn->close();
?>
