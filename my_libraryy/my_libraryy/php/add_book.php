<?php
// add_book.php
require_once "config.php";

// Check if user is logged in and has the correct role (seller or admin)
if (!check_role(["seller", "admin"])) {
    json_response(["message" => "Unauthorized: Only sellers or admins can add books."], 403);
}

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    json_response(["message" => "Invalid request method."], 405);
}

// Get data from POST request (assuming JSON input for AJAX)
$input = json_decode(file_get_contents("php://input"), true);

$title = $input["title"] ?? null;
$author = $input["author"] ?? null;
$price = $input["price"] ?? null;
$category = $input["category"] ?? null;
$description = $input["description"] ?? null;
$cover_image = $input["cover_image"] ?? null; // User will handle image upload separately

// Validate input
if (empty($title) || empty($author) || $price === null) {
    json_response(["message" => "Missing required fields: title, author, price."], 400);
}

if (!is_numeric($price) || $price < 0) {
     json_response(["message" => "Invalid price format."], 400);
}

// Get the user ID of the person adding the book
$added_by_user_id = $_SESSION["user_id"];

// Database connection
$conn = db_connect();

// Insert new book into the database
try {
    $stmt = $conn->prepare("INSERT INTO books (title, author, price, category, description, cover_image, added_by_user_id) VALUES (:title, :author, :price, :category, :description, :cover_image, :added_by)");
    $stmt->bindParam(":title", $title);
    $stmt->bindParam(":author", $author);
    $stmt->bindParam(":price", $price);
    $stmt->bindParam(":category", $category);
    $stmt->bindParam(":description", $description);
    $stmt->bindParam(":cover_image", $cover_image);
    $stmt->bindParam(":added_by", $added_by_user_id);
    
    if ($stmt->execute()) {
        $book_id = $conn->lastInsertId();
        // Fetch the newly added book to return its details
        $stmt_fetch = $conn->prepare("SELECT * FROM books WHERE id = :id");
        $stmt_fetch->bindParam(":id", $book_id);
        $stmt_fetch->execute();
        $new_book = $stmt_fetch->fetch();

        json_response($new_book, 201);
    } else {
        json_response(["message" => "Failed to add book."], 500);
    }
} catch (PDOException $e) {
    error_log("Database Error: " . $e->getMessage());
    json_response(["message" => "An error occurred while adding the book."], 500);
}

?>
