<?php
/**
 * Task creation endpoint.
 * Creates a new task for the logged-in user with automatic priority calculation.
 */
include 'config.php';
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

if (!isset($_SESSION['loggedInUser'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$username = $_SESSION['loggedInUser'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $taskform = isset($_POST['taskform']) ? trim($_POST['taskform']) : '';
    $date = isset($_POST['date']) ? trim($_POST['date']) : '';
    $prior = isset($_POST['prior']) ? trim($_POST['prior']) : '';

    if (empty($taskform) || empty($date) || empty($prior)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit;
    }

    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
        echo json_encode(['success' => false, 'message' => 'Invalid date format']);
        exit;
    }

    $valid_priorities = ['Easy', 'Normal', 'Hard'];
    if (!in_array($prior, $valid_priorities)) {
        echo json_encode(['success' => false, 'message' => 'Invalid priority level']);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO tasks (username, taskform, date, prior) VALUES (?, ?, ?, ?)");
    
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
        exit;
    }

    $stmt->bind_param("ssss", $username, $taskform, $date, $prior);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Task added successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add task: ' . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();
?>
