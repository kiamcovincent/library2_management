```php
<?php
session_start();

$error = $_GET['error'] ?? '';
?>

<!DOCTYPE html>
<html>

<head>
    <title>Library Management System</title>
    <link rel="stylesheet" href="style.css">
</head>

<body class="login-page">

<div class="login-card">

    <h1>Library Management System</h1>

    <p class="subtitle">Login to continue</p>

    <?php if ($error): ?>
        <div class="alert error">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form action="login.php" method="POST">

        <label>Username</label>

        <input
            type="text"
            name="username"
            placeholder="Enter username"
            required
        >

        <label>Password</label>

        <input
            type="password"
            name="password"
            placeholder="Enter password"
            required
        >

        <div class="forgot-password">
            <a href="forgot_password.php">
                Forgot Password?
            </a>
        </div>

        <button type="submit">
            Login
        </button>

    </form>

    <div class="demo">
        <b>Demo accounts</b><br>

        Administrator:
        <code>admin / admin123</code><br>

        Student:
        <code>student / student123</code>
    </div>

</div>

</body>
</html>
```
