<?php
session_start();
require "config.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Administrator') {
    header("Location: index.php");
    exit;
}

$message = "";

if (isset($_POST['add_user'])) {

    $username = $_POST['username'];
    $password = $_POST['password'];
    $name = $_POST['name'];
    $role = $_POST['role'];

    $stmt = $conn->prepare(
        "INSERT INTO users (username, password, name, role)
         VALUES (?, ?, ?, ?)"
    );

    $stmt->bind_param(
        "ssss",
        $username,
        $password,
        $name,
        $role
    );

    if ($stmt->execute()) {
        $message = "User added successfully.";
    } else {
        $message = "Error adding user.";
    }
}

$users = $conn->query("SELECT * FROM users ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>

<head>
    <title>Manage Users</title>
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

    <h2>Manage Users</h2>

    <?php if ($message): ?>
        <p><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <h3>Add New User</h3>

    <form method="POST">

        <input
            type="text"
            name="username"
            placeholder="Username"
            required
        >

        <input
            type="password"
            name="password"
            placeholder="Password"
            required
        >

        <input
            type="text"
            name="name"
            placeholder="Full Name"
            required
        >

        <select name="role" required>

            <option value="Student">
                Student
            </option>

            <option value="Administrator">
                Administrator
            </option>

        </select>

        <button type="submit" name="add_user">
            Add User
        </button>

    </form>

    <h3>User List</h3>

    <table border="1" cellpadding="10" cellspacing="0">

        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Name</th>
            <th>Role</th>
        </tr>

        <?php while ($user = $users->fetch_assoc()): ?>

        <tr>

            <td><?= $user['id'] ?></td>

            <td>
                <?= htmlspecialchars($user['username']) ?>
            </td>

            <td>
                <?= htmlspecialchars($user['name']) ?>
            </td>

            <td>
                <?= htmlspecialchars($user['role']) ?>
            </td>

        </tr>

        <?php endwhile; ?>

    </table>

</div>

</body>
</html>