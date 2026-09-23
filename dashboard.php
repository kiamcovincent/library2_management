<?php
session_start();
require "config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$role = $_SESSION['role'];
$name = $_SESSION['name'];

$book_count = $conn->query(
    "SELECT COUNT(*) c FROM books"
)->fetch_assoc()['c'];

$available = $conn->query(
    "SELECT COALESCE(SUM(available),0) c FROM books"
)->fetch_assoc()['c'];

$borrowed = $conn->query(
    "SELECT COUNT(*) c FROM borrowings WHERE status='Borrowed'"
)->fetch_assoc()['c'];

$user_count = $conn->query(
    "SELECT COUNT(*) c FROM users"
)->fetch_assoc()['c'];
?>

<!DOCTYPE html>
<html>

<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<nav class="navbar">
    <div><b>📚 Library System</b></div>

    <div>
        <?= htmlspecialchars($name) ?>
        (<?= htmlspecialchars($role) ?>)
        |
        <a href="logout.php">Logout</a>
    </div>
</nav>

<div class="container">

    <h2><?= htmlspecialchars($role) ?> Dashboard</h2>

    <div class="cards">

        <div class="card">
            <h3><?= $book_count ?></h3>
            <p>Total Books</p>
        </div>

        <div class="card">
            <h3><?= $available ?></h3>
            <p>Available Copies</p>
        </div>

        <div class="card">
            <h3><?= $borrowed ?></h3>
            <p>Borrowed Books</p>
        </div>

        <?php if ($role === 'Administrator'): ?>

        <div class="card">
            <h3><?= $user_count ?></h3>
            <p>Total Users</p>
        </div>

        <?php endif; ?>

    </div>

    <?php if ($role === 'Administrator'): ?>

    <div class="menu-grid">

        <a class="menu" href="books.php">
            📚 Manage Books
        </a>

        <a class="menu" href="users.php">
            👥 Manage Users
        </a>

        <a class="menu" href="borrow_return.php">
            🔄 Borrow / Return
        </a>

        <a class="menu" href="reports.php">
            📊 Reports
        </a>

    </div>

    <?php else: ?>

    <div class="menu-grid">

        <a class="menu" href="search.php">
            🔎 Search Books
        </a>

        <a class="menu" href="book_details.php">
            📖 Book Details
        </a>

        <a class="menu" href="borrow.php">
            📚 Borrow Book
        </a>

        <a class="menu" href="records.php">
            🧾 View Records
        </a>

    </div>

    <?php endif; ?>

</div>

</body>
</html>