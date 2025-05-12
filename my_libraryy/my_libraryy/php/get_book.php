<?php
// get_book.php
require_once "config.php";

// Get book ID from query parameter
$book_id = $_GET["id"] ?? null;

if (empty($book_id) || !is_numeric($book_id)) {
    json_response(["message" => "Missing or invalid book ID parameter."], 400);
}

$book_id = (int)$book_id;

// Database connection
$conn = db_connect();

// Fetch the specific book from the database
try {
    $stmt = $conn->prepare("SELECT id, title, author, price, category, description, cover_image FROM books WHERE id = :id");
    $stmt->bindParam(":id", $book_id, PDO::PARAM_INT);
    $stmt->execute();
    
    $book = $stmt->fetch();

    if ($book) {
        // Return book as JSON
        json_response($book, 200);
    } else {
        json_response(["message" => "Book not found."], 404);
    }

} catch (PDOException $e) {
    error_log("Database Error (Get Book): " . $e->getMessage());
    json_response(["message" => "An error occurred while fetching the book."], 500);
}

?>
