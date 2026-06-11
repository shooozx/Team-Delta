<?php
/**
 * Task deletion endpoint.
 * Removes a task from the database for the logged-in user.
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

if ($_SERVER['REQUEST_METHOD'] === 'GET' || $_SERVER['REQUEST_METHOD'] === 'POST') {
    $taskId = ($_SERVER['REQUEST_METHOD'] === 'GET') ? $_GET['id'] : $_POST['taskId'];

    if (!isset($taskId) || empty($taskId)) {
        echo json_encode(['success' => false, 'message' => 'Task ID is required']);
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM tasks WHERE id = ? AND username = ?");
    $stmt->bind_param("is", $taskId, $username);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Task deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete task: ' . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();
?>
