<?php
// cart_handler.php
require_once "config.php";

if (!is_logged_in()) {
    json_response(["message" => "Unauthorized: Please login to manage your cart."], 401);
}

$user_id = $_SESSION["user_id"];
$conn = db_connect();
$method = $_SERVER["REQUEST_METHOD"];

// Handle different request methods
switch ($method) {
    case "GET":
        // Get cart items for the current user
        try {
            $stmt = $conn->prepare(
                "SELECT c.id, c.book_id, c.quantity, c.added_at, b.title, b.author, b.price, b.cover_image 
                 FROM cart c 
                 JOIN books b ON c.book_id = b.id 
                 WHERE c.user_id = :user_id"
            );
            $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
            $stmt->execute();
            $cart_items = $stmt->fetchAll();
            json_response($cart_items, 200);
        } catch (PDOException $e) {
            error_log("Database Error (Get Cart): " . $e->getMessage());
            json_response(["message" => "An error occurred while fetching the cart."], 500);
        }
        break;

    case "POST":
        // Add item to cart
        $input = json_decode(file_get_contents("php://input"), true);
        $book_id = $input["book_id"] ?? null;
        $quantity = isset($input["quantity"]) ? (int)$input["quantity"] : 1;

        if (empty($book_id) || $quantity <= 0) {
            json_response(["message" => "Missing or invalid fields: book_id (required), quantity (must be positive integer, defaults to 1)."], 400);
        }

        try {
            // Check if book exists
            $stmt_check_book = $conn->prepare("SELECT id FROM books WHERE id = :book_id");
            $stmt_check_book->bindParam(":book_id", $book_id, PDO::PARAM_INT);
            $stmt_check_book->execute();
            if (!$stmt_check_book->fetch()) {
                 json_response(["message" => "Book not found."], 404);
            }

            // Check if item already in cart
            $stmt_check = $conn->prepare("SELECT id, quantity FROM cart WHERE user_id = :user_id AND book_id = :book_id");
            $stmt_check->bindParam(":user_id", $user_id, PDO::PARAM_INT);
            $stmt_check->bindParam(":book_id", $book_id, PDO::PARAM_INT);
            $stmt_check->execute();
            $existing_item = $stmt_check->fetch();

            if ($existing_item) {
                // Update quantity
                $new_quantity = $existing_item["quantity"] + $quantity;
                $stmt_update = $conn->prepare("UPDATE cart SET quantity = :quantity WHERE id = :id");
                $stmt_update->bindParam(":quantity", $new_quantity, PDO::PARAM_INT);
                $stmt_update->bindParam(":id", $existing_item["id"], PDO::PARAM_INT);
                $stmt_update->execute();
                $cart_item_id = $existing_item["id"];
            } else {
                // Insert new item
                $stmt_insert = $conn->prepare("INSERT INTO cart (user_id, book_id, quantity) VALUES (:user_id, :book_id, :quantity)");
                $stmt_insert->bindParam(":user_id", $user_id, PDO::PARAM_INT);
                $stmt_insert->bindParam(":book_id", $book_id, PDO::PARAM_INT);
                $stmt_insert->bindParam(":quantity", $quantity, PDO::PARAM_INT);
                $stmt_insert->execute();
                $cart_item_id = $conn->lastInsertId();
            }
            
            // Fetch the added/updated item details to return
             $stmt_fetch_item = $conn->prepare(
                "SELECT c.id, c.book_id, c.quantity, c.added_at, b.title, b.author, b.price, b.cover_image 
                 FROM cart c 
                 JOIN books b ON c.book_id = b.id 
                 WHERE c.id = :cart_item_id"
            );
            $stmt_fetch_item->bindParam(":cart_item_id", $cart_item_id, PDO::PARAM_INT);
            $stmt_fetch_item->execute();
            $item_details = $stmt_fetch_item->fetch();

            json_response($item_details, 201); // 201 Created or 200 OK if updated

        } catch (PDOException $e) {
            error_log("Database Error (Add to Cart): " . $e->getMessage());
            json_response(["message" => "An error occurred while adding to the cart."], 500);
        }
        break;

        case "PUT":
            // Update item quantity in cart
            $cart_item_id = $_GET["id"] ?? null;
    
            if (!is_numeric($cart_item_id)) {
                json_response(["message" => "Invalid or missing cart item ID in query string."], 400);
            }
            $cart_item_id = (int)$cart_item_id;
    
            $input = json_decode(file_get_contents("php://input"), true);
            $new_quantity = isset($input["quantity"]) ? (int)$input["quantity"] : null;
    
            if ($new_quantity === null || $new_quantity <= 0) {
                json_response(["message" => "Missing or invalid field: quantity (must be a positive integer)."], 400);
            }
    
            try {
                $stmt = $conn->prepare("UPDATE cart SET quantity = :quantity WHERE id = :id AND user_id = :user_id");
                $stmt->bindParam(":quantity", $new_quantity, PDO::PARAM_INT);
                $stmt->bindParam(":id", $cart_item_id, PDO::PARAM_INT);
                $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
                $stmt->execute();
    
                if ($stmt->rowCount() > 0) {
                    $stmt_fetch_item = $conn->prepare(
                        "SELECT c.id, c.book_id, c.quantity, c.added_at, b.title, b.author, b.price, b.cover_image 
                         FROM cart c 
                         JOIN books b ON c.book_id = b.id 
                         WHERE c.id = :cart_item_id"
                    );
                    $stmt_fetch_item->bindParam(":cart_item_id", $cart_item_id, PDO::PARAM_INT);
                    $stmt_fetch_item->execute();
                    $item_details = $stmt_fetch_item->fetch();
                    json_response($item_details, 200);
                } else {
                    json_response(["message" => "Cart item not found or does not belong to user."], 404);
                }
            } catch (PDOException $e) {
                error_log("Database Error (Update Cart): " . $e->getMessage());
                json_response(["message" => "An error occurred while updating the cart item."], 500);
            }
            break;
    
        case "DELETE":
            $cart_item_id = $_GET["id"] ?? null;
    
            if (!is_numeric($cart_item_id)) {
                json_response(["message" => "Invalid or missing cart item ID in query string."], 400);
            }
            $cart_item_id = (int)$cart_item_id;
    
            try {
                $stmt = $conn->prepare("DELETE FROM cart WHERE id = :id AND user_id = :user_id");
                $stmt->bindParam(":id", $cart_item_id, PDO::PARAM_INT);
                $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
                $stmt->execute();
    
                if ($stmt->rowCount() > 0) {
                    json_response(["message" => "Item removed from cart."], 200);
                } else {
                    json_response(["message" => "Cart item not found or does not belong to user."], 404);
                }
            } catch (PDOException $e) {
                error_log("Database Error (Delete Cart Item): " . $e->getMessage());
                json_response(["message" => "An error occurred while removing the item from the cart."], 500);
            }
            break;
    

    default:
        json_response(["message" => "Method not supported."], 405);
        break;
}

?>
