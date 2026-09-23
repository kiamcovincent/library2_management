<?php
session_start();
require "config.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Student') {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare(
    "SELECT borrowings.*, books.title
     FROM borrowings
     JOIN books ON borrowings.book_id = books.id
     WHERE borrowings.user_id = ?
     ORDER BY borrowings.id DESC"
);

$stmt->bind_param("i", $user_id);
$stmt->execute();

$records = $stmt->get_result();
?>

<!DOCTYPE html>
<html>

<head>
    <title>My Records</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<nav class="navbar">

    <div>
        <b>📚 Library System</b>
    </div>

    <div>
        <a href="dashboard.php">Dashboard</a> |
        <a href="logout.php">Logout</a>
    </div>

</nav>

<div class="container">

    <h2>🧾 My Borrowing Records</h2>

    <table border="1" cellpadding="10" cellspacing="0">

        <tr>
            <th>ID</th>
            <th>Book</th>
            <th>Borrow Date</th>
            <th>Return Date</th>
            <th>Status</th>
        </tr>

        <?php if ($records->num_rows > 0): ?>

            <?php while ($record = $records->fetch_assoc()): ?>

            <tr>

                <td>
                    <?= $record['id'] ?>
                </td>

                <td>
                    <?= htmlspecialchars($record['title']) ?>
                </td>

                <td>
                    <?= $record['borrow_date'] ?>
                </td>

                <td>
                    <?= $record['return_date'] ?? '-' ?>
                </td>

                <td>
                    <?= htmlspecialchars($record['status']) ?>
                </td>

            </tr>

            <?php endwhile; ?>

        <?php else: ?>

            <tr>
                <td colspan="5">
                    No borrowing records found.
                </td>
            </tr>

        <?php endif; ?>

    </table>

</div>

</body>
</html>