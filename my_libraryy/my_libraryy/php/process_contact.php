<?php
require_once "config.php";
header("Content-Type: application/json; charset=utf-8");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pdo = db_connect();
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $message = $_POST['message'] ?? '';

    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        echo json_encode(["message" => "يرجى تعبئة جميع الحقول."], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, subject, message) 
                           VALUES (:name, :email, :subject, :message)");
    $stmt->bindParam(":name", $name);
    $stmt->bindParam(":email", $email);
    $stmt->bindParam(":subject", $subject);
    $stmt->bindParam(":message", $message);

    if ($stmt->execute()) {
        echo json_encode(["message" => "تم إرسال الرسالة بنجاح!"], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode(["message" => "حدث خطأ أثناء إرسال الرسالة."], JSON_UNESCAPED_UNICODE);
    }
    exit;
} else {
    echo json_encode(["message" => "يرجى استخدام طريقة POST فقط."], JSON_UNESCAPED_UNICODE);
    exit;
}

?>
