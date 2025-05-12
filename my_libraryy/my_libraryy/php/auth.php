<?php
session_start();
include 'db.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['action']) && $_POST['action'] === 'login') {
        $email = $conn->real_escape_string($_POST['email']);
        $password = $_POST['password'];

        $stmt = $conn->prepare("SELECT user_id, firstname, lastName, email, password, accountType FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['password'])) {
                $_SESSION['user_id'] = $row['user_id'];
                $_SESSION['account_type'] = $row['accountType'];
                $_SESSION['first_name'] = $row['firstname'];
                echo "success_login";
                exit;
            }
        }
        echo "Invalid email or password.";
        exit;
    }

    if (isset($_POST['action']) && $_POST['action'] === 'signup') {
        $firstname = $conn->real_escape_string($_POST['firstname']);
        $lastname = $conn->real_escape_string($_POST['lastname']);
        $email = $conn->real_escape_string($_POST['email']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $accountType = 'buyer'; // افتراضيًا مستخدم عادي

        try {
            $stmt = $conn->prepare("INSERT INTO users (firstname, lastName, email, password, accountType) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $firstname, $lastname, $email, $password, $accountType);
            if ($stmt->execute()) {
                $_SESSION['user_id'] = $stmt->insert_id;
                $_SESSION['account_type'] = $accountType;
                $_SESSION['first_name'] = $firstname;
                echo "success_signup";
                exit;
            }
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() == 1062) {
                echo "Email already exists.";
            } else {
                echo "Signup error: " . $e->getMessage();
            }
        }
    }
}
?>
