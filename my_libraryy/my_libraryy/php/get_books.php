<?php
// get_books.php
require_once "config.php";

// Database connection
$conn = db_connect();

// Fetch all books from the database
try {
    $stmt = $conn->query("SELECT id, title, author, price, category, description, cover_image FROM books ORDER BY created_at DESC");
    $books = $stmt->fetchAll();

    // Return books as JSON
    json_response($books, 200);

} catch (PDOException $e) {
    error_log("Database Error: " . $e->getMessage());
    json_response(["message" => "An error occurred while fetching books."], 500);
}

?>
