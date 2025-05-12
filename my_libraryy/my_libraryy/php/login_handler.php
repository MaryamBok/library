<?php
// login_handler.php
require_once "config.php";


// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    json_response(["message" => "Invalid request method."], 405);
}

// Get data from POST request (assuming JSON input for AJAX)
$input = json_decode(file_get_contents("php://input"), true);

$username = $input["username"] ?? null;
$password = $input["password"] ?? null;

// Validate input
if (empty($username) || empty($password)) {
    json_response(["message" => "Missing required fields: username, password."], 400);
}

// Database connection
$conn = db_connect();

// Find user by username
try {
    $stmt = $conn->prepare("SELECT id, username, password_hash, role FROM users WHERE username = :username");
    $stmt->bindParam(":username", $username);
    $stmt->execute();

    $user = $stmt->fetch();

    if ($user && verify_password($password, $user["password_hash"])) {
        // Password is correct, start session
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["username"] = $user["username"];
        $_SESSION["user_role"] = $user["role"];

        json_response([
            "message" => "Login successful.",
            "user" => [
                "id" => $user["id"],
                "username" => $user["username"],
                "role" => $user["role"]
            ]
        ], 200);
    } else {
        // Invalid username or password
        json_response(["message" => "Invalid username or password."], 401);
    }
} catch (PDOException $e) {
    error_log("Database Error: " . $e->getMessage());
    json_response(["message" => "An error occurred during login."], 500);
}

?>
