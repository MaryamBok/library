<?php
// delete_book.php
require_once "config.php";

if (!check_role(["seller", "admin"])) {
    json_response(["message" => "Unauthorized: Only sellers or admins can delete books."], 403);
}

// Check if the request method is DELETE
if ($_SERVER["REQUEST_METHOD"] !== "DELETE") {
    json_response(["message" => "Invalid request method. Use DELETE to remove."], 405);
}

// Get book ID from URL path (e.g., /api/books/123)
$path_parts = explode("/", trim($_SERVER["PATH_INFO"] ?? "", "/"));
$book_id = end($path_parts);

if (!is_numeric($book_id)) {
     json_response(["message" => "Invalid book ID in URL."], 400);
}
$book_id = (int)$book_id;

// Database connection
$conn = db_connect();

// Execute the delete query
try {
    $stmt = $conn->prepare("DELETE FROM books WHERE id = :id");
    $stmt->bindParam(":id", $book_id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        json_response(["message" => "Book deleted successfully."], 200);
    } else {
        // If no rows were affected, the book likely didn't exist
        json_response(["message" => "Book not found."], 404);
    }
} catch (PDOException $e) {
    // Handle potential foreign key constraint errors if books are in carts
    if ($e->getCode() == '23000') { // Integrity constraint violation
         json_response(["message" => "Cannot delete book: It might be present in user carts."], 409); // 409 Conflict
    } else {
        error_log("Database Error (Delete Book): " . $e->getMessage());
        json_response(["message" => "An error occurred while deleting the book."], 500);
    }
}

?>
