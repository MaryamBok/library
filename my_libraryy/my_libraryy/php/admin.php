<?php
session_start();

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    die("Access denied.");
}

$pdo = new PDO("mysql:host=localhost;dbname=db_library;charset=utf8mb4", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$page = $_GET['page'] ?? 'dashboard';

// To delete a message
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_message_id'])) {
    $stmtDelete = $pdo->prepare("DELETE FROM contact_messages WHERE id = ?");
    $stmtDelete->execute([$_POST['delete_message_id']]);
    header("Location: admin.php?page=messages");
    exit;
}

function getCount($table) {
    global $pdo;
    return $pdo->query("SELECT COUNT(*) FROM $table")->fetchColumn();
}

function getData($table) {
    global $pdo;
    return $pdo->query("SELECT * FROM $table ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel - Book Library</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="admin_styles.css"> <!-- Create a separate CSS file -->
    <style>
        body { font-family: 'Arial', sans-serif; background-color: #f9f9f9; margin: 0; padding: 0; }
        header { background-color: #333; color: white; padding: 20px 0; }
        header .container { width: 90%; margin: auto; }
        header h1 { margin: 0; }
        nav ul { list-style: none; padding: 0; margin: 10px 0 0; display: flex; gap: 15px; }
        nav li { display: inline; }
        nav a { color: white; text-decoration: none; font-size: 16px; }
        nav a:hover { text-decoration: underline; }
        .admin-content { padding: 20px; background: #fff; margin: 20px auto; width: 90%; border-radius: 5px; box-shadow: 0 2px 8px #ccc; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
        th { background: #eee; }
        .btn-delete { background: none; border: none; color: red; cursor: pointer; }
        footer { background: #222; color: #fff; padding: 10px 0; text-align: center; margin-top: 50px; }
    </style>
</head>
<body>
<header>
    <div class="container">
        <h1>Library Management Panel</h1>
        <nav>
            <ul>
                <li><a href="admin.php"><i class="fas fa-home"></i> Home</a></li>
                <li><a href="admin.php?page=books"><i class="fas fa-book"></i> Books</a></li>
                <li><a href="admin.php?page=messages"><i class="fas fa-envelope"></i> Messages</a></li>
                <li><a href="admin.php?page=users"><i class="fas fa-users"></i> Users</a></li>
                <li><a href="../Home.html"><i class="fas fa-arrow-left"></i> Back to Site</a></li>
            </ul>
        </nav>
    </div>
</header>

<section class="admin-content">
    <div class="container">
        <?php if ($page == 'dashboard'): ?>
            <h2>Overview</h2>
            <p>Number of books: <?= getCount('books') ?></p>
            <p>Number of users: <?= getCount('users') ?></p>
            <p>Number of messages: <?= getCount('contact_messages') ?></p>

        <?php elseif ($page == 'books'): ?>
            <h2>Book List</h2>
            <table>
                <tr>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Price</th>
                    <th>Image</th>
                    <th>Date</th>
                </tr>
                <?php foreach (getData('books') as $book): ?>
                    <tr>
                        <td><?= htmlspecialchars($book['title']) ?></td>
                        <td><?= htmlspecialchars($book['author']) ?></td>
                        <td><?= htmlspecialchars($book['price']) ?> $</td>
                        <td><img src="<?= htmlspecialchars($book['cover_image']) ?>" width="50"></td>
                        <td><?= $book['created_at'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>

        <?php elseif ($page == 'messages'): ?>
            <h2>Received Messages</h2>
            <p>Number of messages: <?= getCount('contact_messages') ?></p>
            <table>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Subject</th>
                    <th>Message</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
                <?php foreach (getData('contact_messages') as $msg): ?>
                    <tr>
                        <td><?= htmlspecialchars($msg['name']) ?></td>
                        <td><?= htmlspecialchars($msg['email']) ?></td>
                        <td><?= htmlspecialchars($msg['subject']) ?></td>
                        <td><?= nl2br(htmlspecialchars($msg['message'])) ?></td>
                        <td><?= $msg['created_at'] ?></td>
                        <td>
                            <form method="post" onsubmit="return confirm('Are you sure you want to delete this message?')">
                                <input type="hidden" name="delete_message_id" value="<?= $msg['id'] ?>">
                                <button type="submit" class="btn-delete"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>

        <?php elseif ($page == 'users'): ?>
            <h2>Users</h2>
            <p>Number of users: <?= getCount('users') ?></p>
            <table>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Date</th>
                </tr>
                <?php foreach (getData('users') as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['username']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= htmlspecialchars($user['role']) ?></td>
                        <td><?= $user['created_at'] ?? '-' ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>

        <?php endif; ?>
    </div>
</section>

<footer>
    <div class="container">
        <p>© 2025 Admin Panel - All rights reserved.</p>
    </div>
</footer>

</body>
</html>