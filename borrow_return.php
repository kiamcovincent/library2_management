<?php
session_start();
require "config.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Administrator') {
    header("Location: index.php");
    exit;
}

$message = "";

if (isset($_POST['return_book'])) {

    $borrowing_id = (int)$_POST['borrowing_id'];

    $stmt = $conn->prepare(
        "SELECT book_id, status
         FROM borrowings
         WHERE id = ?"
    );

    $stmt->bind_param("i", $borrowing_id);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {

        $record = $result->fetch_assoc();

        if ($record['status'] === 'Borrowed') {

            $book_id = $record['book_id'];

            $conn->begin_transaction();

            try {

                $stmt = $conn->prepare(
                    "UPDATE borrowings
                     SET returned_at = CURDATE(),
                         status = 'Returned'
                     WHERE id = ?"
                );

                $stmt->bind_param("i", $borrowing_id);
                $stmt->execute();

                $stmt = $conn->prepare(
                    "UPDATE books
                     SET available = available + 1
                     WHERE id = ?"
                );

                $stmt->bind_param("i", $book_id);
                $stmt->execute();

                $conn->commit();

                $message = "Book returned successfully.";

            } catch (Exception $e) {

                $conn->rollback();

                $message = "Error returning book.";
            }

        } else {

            $message = "This book has already been returned.";
        }

    } else {

        $message = "Borrowing record not found.";
    }
}


$records = $conn->query(
    "SELECT
        borrowings.id,
        users.name,
        books.title,
        borrowings.borrowed_at,
        borrowings.returned_at,
        borrowings.status
     FROM borrowings
     JOIN users
        ON borrowings.user_id = users.id
     JOIN books
        ON borrowings.book_id = books.id
     ORDER BY borrowings.id DESC"
);
?>

<!DOCTYPE html>
<html>

<head>

    <title>Borrow and Return</title>

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

    <h2>🔄 Borrow / Return</h2>


    <?php if ($message): ?>

        <p>
            <?= htmlspecialchars($message) ?>
        </p>

    <?php endif; ?>


    <table>

        <tr>

            <th>ID</th>

            <th>Student</th>

            <th>Book</th>

            <th>Borrow Date</th>

            <th>Return Date</th>

            <th>Status</th>

            <th>Action</th>

        </tr>


        <?php while ($record = $records->fetch_assoc()): ?>

        <tr>

            <td>
                <?= $record['id'] ?>
            </td>

            <td>
                <?= htmlspecialchars($record['name']) ?>
            </td>

            <td>
                <?= htmlspecialchars($record['title']) ?>
            </td>

            <td>
                <?= $record['borrowed_at'] ?>
            </td>

            <td>
                <?= $record['returned_at'] ?? '-' ?>
            </td>

            <td>
                <?= htmlspecialchars($record['status']) ?>
            </td>

            <td>

                <?php if ($record['status'] === 'Borrowed'): ?>

                    <form method="POST">

                        <input
                            type="hidden"
                            name="borrowing_id"
                            value="<?= $record['id'] ?>"
                        >

                        <button
                            type="submit"
                            name="return_book"
                        >
                            Return
                        </button>

                    </form>

                <?php else: ?>

                    Already Returned

                <?php endif; ?>

            </td>

        </tr>

        <?php endwhile; ?>

    </table>

</div>

</body>

</html>