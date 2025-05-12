<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "db_library";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("فشل الاتصال بقاعدة البيانات: " . $conn->connect_error);
}
?>