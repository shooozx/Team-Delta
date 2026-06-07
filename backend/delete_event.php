<?php
/**
 * Event deletion endpoint.
 * Removes a calendar event from the database for the logged-in user.
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
    $eventId = isset($_POST['eventId']) ? $_POST['eventId'] : '';

    if (empty($eventId)) {
        echo json_encode(['success' => false, 'message' => 'Event ID is required']);
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM events WHERE id = ? AND username = ?");
    
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
        exit;
    }

    $stmt->bind_param("is", $eventId, $username);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Event deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete event: ' . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();
?>
