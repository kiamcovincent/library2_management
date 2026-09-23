<?php
session_start();
require "config.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Administrator') {
    header("Location: index.php");
    exit;
}

$message = "";

/* ADD BOOK */
if (isset($_POST['add_book'])) {

    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $quantity = (int)$_POST['quantity'];

    if ($title !== "" && $author !== "" && $quantity > 0) {

        $stmt = $conn->prepare(
            "INSERT INTO books (title, author, quantity, available)
             VALUES (?, ?, ?, ?)"
        );

        $available = $quantity;

        $stmt->bind_param(
            "ssii",
            $title,
            $author,
            $quantity,
            $available
        );

        $stmt->execute();

        $message = "Book added successfully.";

    } else {

        $message = "Please enter valid book information.";

    }
}


/* DELETE BOOK */
if (isset($_POST['delete_book'])) {

    $book_id = (int)$_POST['book_id'];

    $stmt = $conn->prepare(
        "DELETE FROM books WHERE id = ?"
    );

    $stmt->bind_param("i", $book_id);
    $stmt->execute();

    $message = "Book deleted successfully.";
}


/* GET BOOKS */
$books = $conn->query(
    "SELECT id, title, author, quantity, available
     FROM books
     ORDER BY id DESC"
);
?>

<!DOCTYPE html>
<html>

<head>

    <title>Manage Books</title>

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

    <h2>📚 Manage Books</h2>


    <?php if ($message): ?>

        <p>
            <?= htmlspecialchars($message) ?>
        </p>

    <?php endif; ?>


    <h3>Add New Book</h3>


    <form method="POST">

        <label>Book Title</label>

        <input
            type="text"
            name="title"
            placeholder="Enter book title"
            required
        >


        <label>Author</label>

        <input
            type="text"
            name="author"
            placeholder="Enter author"
            required
        >


        <label>Quantity</label>

        <input
            type="number"
            name="quantity"
            min="1"
            placeholder="Enter quantity"
            required
        >


        <button
            type="submit"
            name="add_book"
        >
            Add Book
        </button>

    </form>


    <h3>Book List</h3>


    <table>

        <tr>

            <th>ID</th>

            <th>Title</th>

            <th>Author</th>

            <th>Quantity</th>

            <th>Available</th>

            <th>Action</th>

        </tr>


        <?php while ($book = $books->fetch_assoc()): ?>

        <tr>

            <td>
                <?= $book['id'] ?>
            </td>

            <td>
                <?= htmlspecialchars($book['title']) ?>
            </td>

            <td>
                <?= htmlspecialchars($book['author']) ?>
            </td>

            <td>
                <?= $book['quantity'] ?>
            </td>

            <td>
                <?= $book['available'] ?>
            </td>

            <td>

                <form method="POST">

                    <input
                        type="hidden"
                        name="book_id"
                        value="<?= $book['id'] ?>"
                    >

                    <button
                        type="submit"
                        name="delete_book"
                        onclick="return confirm('Delete this book?')"
                    >
                        Delete
                    </button>

                </form>

            </td>

        </tr>

        <?php endwhile; ?>

    </table>

</div>

</body>

</html>