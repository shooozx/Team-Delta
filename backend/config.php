<?php
/**
 * Database configuration and connection initialization.
 * Establishes connection to MySQL database for application use.
 */
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "balancebuddy";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $conn->connect_error]);
    exit;
}
?>
