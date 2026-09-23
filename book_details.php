<?php
session_start();
require "config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$book = null;

if (isset($_GET['id'])) {

    $id = (int)$_GET['id'];

    $stmt = $conn->prepare(
        "SELECT * FROM books WHERE id = ?"
    );

    $stmt->bind_param("i", $id);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $book = $result->fetch_assoc();
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Book Details</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<nav class="navbar">

    <div>
        <b>📚 Library System</b>
    </div>

    <div>
        <a href="dashboard.php">Dashboard</a> |
        <a href="search.php">Search Books</a> |
        <a href="logout.php">Logout</a>
    </div>

</nav>

<div class="container">

    <h2>📖 Book Details</h2>

    <?php if ($book): ?>

        <div class="card">

            <h3>
                <?= htmlspecialchars($book['title']) ?>
            </h3>

            <p>
                <b>Author:</b>
                <?= htmlspecialchars($book['author']) ?>
            </p>

            <p>
                <b>Category:</b>
                <?= htmlspecialchars($book['category']) ?>
            </p>

            <p>
                <b>Total Quantity:</b>
                <?= $book['quantity'] ?>
            </p>

            <p>
                <b>Available:</b>
                <?= $book['available'] ?>
            </p>

        </div>

    <?php else: ?>

        <p>
            Book not found.
        </p>

        <p>
            Go to <a href="search.php">Search Books</a>
            and select a book.
        </p>

    <?php endif; ?>

</div>

</body>
</html>