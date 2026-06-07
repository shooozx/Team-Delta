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
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

include 'config.php';

$username = $_SESSION['loggedInUser'];

/** Query database for count of completed tasks for the user. */
$stmt = $conn->prepare("SELECT COUNT(*) as completed_count FROM tasks WHERE username = ? AND completed = 1");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$completedCount = $row['completed_count'] ?? 0;

echo json_encode(['success' => true, 'completed_count' => $completedCount]);

$stmt->close();
$conn->close();
?>
