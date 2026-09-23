```php
<?php
session_start();
require "config.php";

$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"] ?? "");

    if ($email === "") {
        $error = "Please enter your email address.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } else {

        $stmt = $conn->prepare(
            "SELECT id, name, email FROM users WHERE email = ? LIMIT 1"
        );

        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user) {

            // Generate a 6-digit OTP
            $otp = random_int(100000, 999999);

            // Store a secure hash of the OTP
            $otp_hash = password_hash($otp, PASSWORD_DEFAULT);

            // OTP expires after 5 minutes
            $expires = date("Y-m-d H:i:s", time() + 300);

            $update = $conn->prepare(
                "UPDATE users
                 SET reset_otp_hash = ?, reset_otp_expires = ?
                 WHERE id = ?"
            );

            $update->bind_param(
                "ssi",
                $otp_hash,
                $expires,
                $user["id"]
            );

            if ($update->execute()) {

                /*
                 * TEMPORARY TEST MODE
                 *
                 * We will connect this to email sending after
                 * the OTP system itself is working.
                 */
                $_SESSION["reset_user_id"] = $user["id"];
                $_SESSION["test_otp"] = $otp;

                header("Location: verify_otp.php");
                exit;

            } else {
                $error = "Something went wrong. Please try again.";
            }

        } else {
            $error = "No account was found with that email address.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>

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

        input {
            width: 100%;
            box-sizing: border-box;
            padding: 12px;
            margin-top: 8px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
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
        }

        .back {
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

    <h2>Forgot Password?</h2>

    <p>
        Enter your registered email address.
    </p>

    <?php if ($error): ?>
        <div class="error">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST">

        <label>Email Address</label>

        <input
            type="email"
            name="email"
            placeholder="Enter your email"
            required
        >

        <button type="submit">
            Send OTP
        </button>

    </form>

    <a class="back" href="index.php">
        ← Back to Login
    </a>

</div>

</body>
</html>
```
