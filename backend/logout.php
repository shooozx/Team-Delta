<?php
/**
 * User logout endpoint.
 * Destroys the user session and redirects to login page.
 */
session_start();
session_destroy();

/** Set CORS headers for cross-origin requests. */
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

/** Handle preflight OPTIONS requests. */
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

header('Location: ../frontend/page1.html');
exit;
?>
