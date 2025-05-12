<?php
// update_book.php
require_once "config.php";

// Check if user is logged in and has the correct role (seller or admin)
if (!check_role(["seller", "admin"])) {
    json_response(["message" => "Unauthorized: Only sellers or admins can update books."], 403);
}

// Check if the request method is PUT
if ($_SERVER["REQUEST_METHOD"] !== "PUT") {
    json_response(["message" => "Invalid request method. Use PUT to update."], 405);
}

// Get book ID from URL path (e.g., /api/books/123)
$path_parts = explode("/", trim($_SERVER["PATH_INFO"] ?? "", "/"));
$book_id = end($path_parts);

if (!is_numeric($book_id)) {
     json_response(["message" => "Invalid book ID in URL."], 400);
}
$book_id = (int)$book_id;

// Get data from PUT request body (assuming JSON input for AJAX)
$input = json_decode(file_get_contents("php://input"), true);

// Fields to update (allow updating any subset)
$title = $input["title"] ?? null;
$author = $input["author"] ?? null;
$price = $input["price"] ?? null;
$category = $input["category"] ?? null;
$description = $input["description"] ?? null;
$cover_image = $input["cover_image"] ?? null;

// Validate price if provided
if ($price !== null && (!is_numeric($price) || $price < 0)) {
     json_response(["message" => "Invalid price format."], 400);
}

// Database connection
$conn = db_connect();

// Check if the book exists before trying to update
try {
    $stmt_check = $conn->prepare("SELECT id FROM books WHERE id = :id");
    $stmt_check->bindParam(":id", $book_id, PDO::PARAM_INT);
    $stmt_check->execute();
    if (!$stmt_check->fetch()) {
        json_response(["message" => "Book not found."], 404);
    }
} catch (PDOException $e) {
    error_log("Database Error (Check Book Exists): " . $e->getMessage());
    json_response(["message" => "An error occurred while checking book existence."], 500);
}

// Build the update query dynamically based on provided fields
$update_fields = [];
$params = [":id" => $book_id];

if ($title !== null) { $update_fields[] = "title = :title"; $params[":title"] = $title; }
if ($author !== null) { $update_fields[] = "author = :author"; $params[":author"] = $author; }
if ($price !== null) { $update_fields[] = "price = :price"; $params[":price"] = $price; }
if ($category !== null) { $update_fields[] = "category = :category"; $params[":category"] = $category; }
if ($description !== null) { $update_fields[] = "description = :description"; $params[":description"] = $description; }
if ($cover_image !== null) { $update_fields[] = "cover_image = :cover_image"; $params[":cover_image"] = $cover_image; }

if (empty($update_fields)) {
    json_response(["message" => "No fields provided to update."], 400);
}

$sql = "UPDATE books SET " . implode(", ", $update_fields) . " WHERE id = :id";

// Execute the update query
try {
    $stmt = $conn->prepare($sql);
    if ($stmt->execute($params)) {
        // Fetch the updated book details to return
        $stmt_fetch = $conn->prepare("SELECT * FROM books WHERE id = :id");
        $stmt_fetch->bindParam(":id", $book_id, PDO::PARAM_INT);
        $stmt_fetch->execute();
        $updated_book = $stmt_fetch->fetch();
        json_response($updated_book, 200);
    } else {
        json_response(["message" => "Failed to update book."], 500);
    }
} catch (PDOException $e) {
    error_log("Database Error (Update Book): " . $e->getMessage());
    json_response(["message" => "An error occurred while updating the book."], 500);
}

?>
