<?php
session_start();
require "config.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Administrator') {
    header("Location: index.php");
    exit;
}

$total_books = $conn->query(
    "SELECT COUNT(*) AS total FROM books"
)->fetch_assoc()['total'];

$total_copies = $conn->query(
    "SELECT COALESCE(SUM(quantity), 0) AS total FROM books"
)->fetch_assoc()['total'];

$available_copies = $conn->query(
    "SELECT COALESCE(SUM(available), 0) AS total FROM books"
)->fetch_assoc()['total'];

$borrowed_books = $conn->query(
    "SELECT COUNT(*) AS total
     FROM borrowings
     WHERE status = 'Borrowed'"
)->fetch_assoc()['total'];

$returned_books = $conn->query(
    "SELECT COUNT(*) AS total
     FROM borrowings
     WHERE status = 'Returned'"
)->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html>

<head>

    <title>Reports</title>

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

    <h2>📊 Library Reports</h2>


    <div class="cards">

        <div class="card">

            <h3><?= $total_books ?></h3>

            <p>Different Books</p>

        </div>


        <div class="card">

            <h3><?= $total_copies ?></h3>

            <p>Total Copies</p>

        </div>


        <div class="card">

            <h3><?= $available_copies ?></h3>

            <p>Available Copies</p>

        </div>


        <div class="card">

            <h3><?= $borrowed_books ?></h3>

            <p>Currently Borrowed</p>

        </div>


        <div class="card">

            <h3><?= $returned_books ?></h3>

            <p>Returned Books</p>

        </div>

    </div>

</div>

</body>

</html>