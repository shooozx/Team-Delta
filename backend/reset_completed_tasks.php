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
$currentMonth = date('m');
$currentYear = date('Y');

/** Check if reset tracking columns exist in users table. */
$checkColumns = $conn->query("DESCRIBE users");
$monthYearColumnsExist = ['last_reset_month' => false, 'last_reset_year' => false];

while ($col = $checkColumns->fetch_assoc()) {
    if ($col['Field'] === 'last_reset_month') {
        $monthYearColumnsExist['last_reset_month'] = true;
    }
    if ($col['Field'] === 'last_reset_year') {
        $monthYearColumnsExist['last_reset_year'] = true;
    }
}

/** Add reset tracking columns if they do not exist. */
if (!$monthYearColumnsExist['last_reset_month']) {
    $conn->query("ALTER TABLE users ADD COLUMN last_reset_month INT DEFAULT 0");
}
if (!$monthYearColumnsExist['last_reset_year']) {
    $conn->query("ALTER TABLE users ADD COLUMN last_reset_year INT DEFAULT 0");
}

/** Retrieve user's last monthly reset tracking information. */
$stmt = $conn->prepare("SELECT last_reset_month, last_reset_year FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

$lastResetMonth = $user['last_reset_month'] ?? 0;
$lastResetYear = $user['last_reset_year'] ?? 0;

/** Check if the current month differs from last reset. */
if ($currentMonth != $lastResetMonth || $currentYear != $lastResetYear) {
    /** Clear all completed tasks for the user. */
    $updateStmt = $conn->prepare("UPDATE tasks SET completed = 0, completed_date = NULL WHERE username = ? AND completed = 1");
    $updateStmt->bind_param("s", $username);
    $updateStmt->execute();
    $updateStmt->close();

    /** Update user's reset tracking. */
    $updateUserStmt = $conn->prepare("UPDATE users SET last_reset_month = ?, last_reset_year = ? WHERE username = ?");
    $updateUserStmt->bind_param("iis", $currentMonth, $currentYear, $username);
    $updateUserStmt->execute();
    $updateUserStmt->close();

    echo json_encode(['success' => true, 'message' => 'Tasks reset for new month', 'reset' => true]);
} else {
    echo json_encode(['success' => true, 'message' => 'No reset needed', 'reset' => false]);
}

$conn->close();
?>
