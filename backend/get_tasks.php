<?php
/**
 * Retrieve incomplete tasks for the logged-in user.
 * Tasks are categorized by priority and returned as JSON.
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

/** Check if the completed column exists in tasks table. */
$checkColumns = $conn->query("DESCRIBE tasks");
$completedColumnExists = false;

while ($col = $checkColumns->fetch_assoc()) {
    if ($col['Field'] === 'completed') {
        $completedColumnExists = true;
    }
}

/** Add completed column if it does not exist. */
if (!$completedColumnExists) {
    $conn->query("ALTER TABLE tasks ADD COLUMN completed TINYINT DEFAULT 0");
}

/** Query to retrieve all incomplete tasks for the user. */
$stmt = $conn->prepare("SELECT id, taskform, date, prior FROM tasks WHERE username = ? AND (completed = 0 OR completed IS NULL) ORDER BY date");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

$tasks = [];
while ($row = $result->fetch_assoc()) {
    $tasks[] = $row;
}

echo json_encode(['success' => true, 'tasks' => $tasks]);

$stmt->close();
$conn->close();
?>
