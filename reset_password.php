```php
<?php
session_start();
require "config.php";

$error = "";
$success = "";

if (!isset($_SESSION["reset_user_id"]) || !isset($_SESSION["otp_verified"])) {
    header("Location: forgot_password.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $password = $_POST["password"] ?? "";
    $confirm_password = $_POST["confirm_password"] ?? "";

    if (strlen($password) < 8) {

        $error = "Password must be at least 8 characters.";

    } elseif ($password !== $confirm_password) {

        $error = "Passwords do not match.";

    } else {

        $user_id = $_SESSION["reset_user_id"];

        // Securely hash the new password
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare(
            "UPDATE users
             SET password = ?,
                 reset_otp_hash = NULL,
                 reset_otp_expires = NULL
             WHERE id = ?"
        );

        $stmt->bind_param(
            "si",
            $password_hash,
            $user_id
        );

        if ($stmt->execute()) {

            // Remove password-reset session information
            unset($_SESSION["reset_user_id"]);
            unset($_SESSION["otp_verified"]);
            unset($_SESSION["test_otp"]);

            $success = "Your password has been successfully changed.";

        } else {

            $error = "Unable to change your password. Please try again.";

        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>

    <title>Reset Password</title>

    <style>

        body {
            font-family: Arial, sans-serif;
            background: #f2f4f7;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .box {
            width: 380px;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }

        h2 {
            text-align: center;
            margin-bottom: 10px;
        }

        p {
            text-align: center;
            color: #666;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }

        input {
            width: 100%;
            box-sizing: border-box;
            padding: 12px;
            margin-top: 8px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 15px;
        }

        button {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 6px;
            background: #2563eb;
            color: white;
            font-size: 16px;
            cursor: pointer;
            margin-top: 20px;
        }

        button:hover {
            background: #1d4ed8;
        }

        .error {
            background: #fee2e2;
            color: #b91c1c;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 15px;
            text-align: center;
        }

        .success {
            background: #dcfce7;
            color: #166534;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 15px;
            text-align: center;
        }

        .login {
            display: block;
            text-align: center;
            margin-top: 15px;
            text-decoration: none;
            color: #2563eb;
        }

    </style>

</head>

<body>

<div class="box">

    <h2>Reset Password</h2>

    <p>
        Create a new password for your account.
    </p>

    <?php if ($error): ?>

        <div class="error">
            <?= htmlspecialchars($error) ?>
        </div>

    <?php endif; ?>

    <?php if ($success): ?>

        <div class="success">
            <?= htmlspecialchars($success) ?>
        </div>

        <a class="login" href="index.php">
            Return to Login
        </a>

    <?php else: ?>

        <form method="POST">

            <label>New Password</label>

            <input
                type="password"
                name="password"
                placeholder="Enter new password"
                minlength="8"
                required
            >

            <label>Confirm New Password</label>

            <input
                type="password"
                name="confirm_password"
                placeholder="Confirm new password"
                minlength="8"
                required
            >

            <button type="submit">
                Change Password
            </button>

        </form>

    <?php endif; ?>

</div>

</body>
</html>
```
