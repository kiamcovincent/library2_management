<?php
session_start();
require "config.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Student') {
    header("Location: index.php");
    exit;
}

$message = "";

if (isset($_POST['borrow'])) {

    $book_id = (int)$_POST['book_id'];
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare(
        "SELECT available FROM books WHERE id = ?"
    );

    $stmt->bind_param("i", $book_id);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows == 0) {

        $message = "Book not found.";

    } else {

        $book = $result->fetch_assoc();

        if ($book['available'] <= 0) {

            $message = "This book is not available.";

        } else {

            $conn->begin_transaction();

            try {

                $stmt = $conn->prepare(
                    "INSERT INTO borrowings
                    (user_id, book_id, borrow_date, status)
                    VALUES (?, ?, CURDATE(), 'Borrowed')"
                );

                $stmt->bind_param(
                    "ii",
                    $user_id,
                    $book_id
                );

                $stmt->execute();

                $stmt = $conn->prepare(
                    "UPDATE books
                     SET available = available - 1
                     WHERE id = ?"
                );

                $stmt->bind_param("i", $book_id);

                $stmt->execute();

                $conn->commit();

                $message = "Book borrowed successfully.";

            } catch (Exception $e) {

                $conn->rollback();

                $message = "Error borrowing book.";
            }
        }
    }
}

$books = $conn->query(
    "SELECT * FROM books
     WHERE available > 0
     ORDER BY title"
);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Borrow Book</title>
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

    <h2>📚 Borrow Book</h2>

    <?php if ($message): ?>

        <p>
            <?= htmlspecialchars($message) ?>
        </p>

    <?php endif; ?>

    <form method="POST">

        <select name="book_id" required>

            <option value="">
                Select a book
            </option>

            <?php while ($book = $books->fetch_assoc()): ?>

                <option value="<?= $book['id'] ?>">

                    <?= htmlspecialchars($book['title']) ?>

                    -
                    <?= $book['available'] ?>
                    available

                </option>

            <?php endwhile; ?>

        </select>

        <button type="submit" name="borrow">
            Borrow Book
        </button>

    </form>

</div>

</body>
</html>