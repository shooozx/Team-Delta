<?php
/**
 * Event creation endpoint.
 * Creates a calendar event for the logged-in user with hardcoded light yellow color.
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
    $date = isset($_POST['date']) ? trim($_POST['date']) : '';
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $color = isset($_POST['color']) ? trim($_POST['color']) : '#1a1a1a';

    if (empty($date) || empty($title)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit;
    }

    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
        echo json_encode(['success' => false, 'message' => 'Invalid date format']);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO events (username, date, title, color) VALUES (?, ?, ?, ?)");
    
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
        exit;
    }

    $stmt->bind_param("ssss", $username, $date, $title, $color);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Event added successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add event: ' . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();
?>
