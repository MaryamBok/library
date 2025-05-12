<?php
// register_handler.php
require_once "config.php";

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    json_response(["message" => "Invalid request method."], 405);
}

// Get data from POST request (assuming JSON input for AJAX)
$input = json_decode(file_get_contents("php://input"), true);

$username = $input["username"] ?? null;
$email = $input["email"] ?? null;
$password = $input["password"] ?? null;
$role = $input["role"] ?? "user"; // Default role is user

// Validate input
if (empty($username) || empty($email) || empty($password)) {
    json_response(["message" => "Missing required fields: username, email, password."], 400);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    json_response(["message" => "Invalid email format."], 400);
}

// Validate role (allow only user, admin, seller)
$allowed_roles = ["user", "admin", "seller"];
if (!in_array($role, $allowed_roles)) {
    json_response(["message" => "Invalid role specified. Allowed roles: user, admin, seller."], 400);
}

// Hash the password
$password_hash = hash_password($password);

// Database connection
$conn = db_connect();

// Check if username or email already exists
try {
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = :username OR email = :email");
    $stmt->bindParam(":username", $username);
    $stmt->bindParam(":email", $email);
    $stmt->execute();

    if ($stmt->fetch()) {
        json_response(["message" => "Username or email already exists."], 409); // 409 Conflict
    }
} catch (PDOException $e) {
    error_log("Database Error: " . $e->getMessage());
    json_response(["message" => "An error occurred while checking user existence."], 500);
}

// Insert new user into the database
try {
    $stmt = $conn->prepare("INSERT INTO users (username, email, password_hash, role) VALUES (:username, :email, :password_hash, :role)");
    $stmt->bindParam(":username", $username);
    $stmt->bindParam(":email", $email);
    $stmt->bindParam(":password_hash", $password_hash);
    $stmt->bindParam(":role", $role);
    
    if ($stmt->execute()) {
        $user_id = $conn->lastInsertId();
        // Optionally log the user in immediately after registration
        // $_SESSION["user_id"] = $user_id;
        // $_SESSION["username"] = $username;
        // $_SESSION["user_role"] = $role;
        json_response(["message" => "User registered successfully.", "user_id" => $user_id], 201);
    } else {
        json_response(["message" => "Failed to register user."], 500);
    }
} catch (PDOException $e) {
    error_log("Database Error: " . $e->getMessage());
    json_response(["message" => "An error occurred during registration."], 500);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $email, $subject, $message]);

    echo "تم إرسال الرسالة بنجاح!";
}

?>
