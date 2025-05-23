<?php
// logout.php
require_once "config.php"; // To start session if not already started

// Unset all session variables
$_SESSION = array();

// If it's desired to kill the session, also delete the session cookie.
// Note: This will destroy the session, and not just the session data!
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), 
              '', 
              time() - 42000,
              $params["path"], 
              $params["domain"],
              $params["secure"], 
              $params["httponly"]
    );
}

// Finally, destroy the session.
session_destroy();

// Return a JSON response for AJAX calls
http_response_code(200);
header("Content-Type: application/json");
echo json_encode(["message" => "Logout successful."]);
exit;
?>
