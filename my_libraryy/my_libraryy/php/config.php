<?php
// db_config.php

define("DB_HOST", getenv("DB_HOST") ?: "localhost");
define("DB_PORT", getenv("DB_PORT") ?: "3306");
define("DB_NAME", getenv("DB_NAME") ?: "db_library"); // Use the new DB name
define("DB_USER", getenv("DB_USERNAME") ?: "root");
define("DB_PASS", getenv("DB_PASSWORD") ?: "");

// Optional: Define base URL for the application
define("BASE_URL", "http://localhost:8080"); // Adjust if using a different port or domain

// Start session for user authentication
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Function to establish database connection
function db_connect() {
    static $conn;
    if ($conn === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $conn = new PDO($dsn, DB_USER, DB_PASS);
            // Set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // Set default fetch mode
            $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            // In a real application, log this error instead of echoing
            error_log("Database Connection Error: " . $e->getMessage());
            // Display a user-friendly error message
            die("Database connection failed. Please try again later."); 
        }
    }
    return $conn;
}

// Function to hash passwords
function hash_password($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

// Function to verify passwords
function verify_password($password, $hash) {
    return password_verify($password, $hash);
}

// Function to check if user is logged in
function is_logged_in() {
    return isset($_SESSION["user_id"]);
}

// Function to check user role
function check_role($allowed_roles) {
    if (!is_logged_in()) {
        return false;
    }
    if (!is_array($allowed_roles)) {
        $allowed_roles = [$allowed_roles]; // Convert single role string to array
    }
    return isset($_SESSION["user_role"]) && in_array($_SESSION["user_role"], $allowed_roles);
}

// Helper function for JSON responses (useful for AJAX)
function json_response($data, $status_code = 200) {
    http_response_code($status_code);
    header("Content-Type: application/json");
    echo json_encode($data);
    exit;
}

?>
