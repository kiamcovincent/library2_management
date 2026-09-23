<?php
session_start();
require "config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$results = null;

if (isset($_GET['search'])) {

    $search = $_GET['search'];

    $stmt = $conn->prepare(
        "SELECT * FROM books
         WHERE title LIKE ?
         OR author LIKE ?
         OR category LIKE ?"
    );

    $term = "%" . $search . "%";

    $stmt->bind_param(
        "sss",
        $term,
        $term,
        $term
    );

    $stmt->execute();

    $results = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Search Books</title>
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

    <h2>🔎 Search Books</h2>

    <form method="GET">

        <input
            type="text"
            name="search"
            placeholder="Enter title, author, or category"
            value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>"
            required
        >

        <button type="submit">
            Search
        </button>

    </form>

    <?php if ($results !== null): ?>

        <h3>Search Results</h3>

        <table border="1" cellpadding="10" cellspacing="0">

            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Author</th>
                <th>Category</th>
                <th>Available</th>
            </tr>

            <?php if ($results->num_rows > 0): ?>

                <?php while ($book = $results->fetch_assoc()): ?>

                <tr>

                    <td><?= $book['id'] ?></td>

                    <td>
                        <?= htmlspecialchars($book['title']) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($book['author']) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($book['category']) ?>
                    </td>

                    <td>
                        <?= $book['available'] ?>
                    </td>

                </tr>

                <?php endwhile; ?>

            <?php else: ?>

                <tr>
                    <td colspan="5">
                        No books found.
                    </td>
                </tr>

            <?php endif; ?>

        </table>

    <?php endif; ?>

</div>

</body>
</html>