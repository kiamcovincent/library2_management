```php
<?php
session_start();
require "config.php";

$error = "";

if (!isset($_SESSION["reset_user_id"])) {
    header("Location: forgot_password.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $otp = trim($_POST["otp"] ?? "");

    if (!preg_match("/^[0-9]{6}$/", $otp)) {

        $error = "Please enter the 6-digit OTP.";

    } else {

        $user_id = $_SESSION["reset_user_id"];

        $stmt = $conn->prepare(
            "SELECT reset_otp_hash, reset_otp_expires
             FROM users
             WHERE id = ?
             LIMIT 1"
        );

        $stmt->bind_param("i", $user_id);
        $stmt->execute();

        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if (!$user) {

            $error = "User account not found.";

        } elseif (empty($user["reset_otp_hash"])) {

            $error = "No OTP was generated. Please request a new OTP.";

        } elseif (strtotime($user["reset_otp_expires"]) < time()) {

            $error = "The OTP has expired. Please request a new one.";

        } elseif (!password_verify($otp, $user["reset_otp_hash"])) {

            $error = "Incorrect OTP.";

        } else {

            // OTP is correct
            $_SESSION["otp_verified"] = true;

            header("Location: reset_password.php");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>

    <title>Verify OTP</title>

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
            font-size: 18px;
            text-align: center;
            letter-spacing: 5px;
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
            text-align: center;
        }

        .test-otp {
            background: #fef3c7;
            color: #92400e;
            padding: 12px;
            border-radius: 6px;
            margin-top: 15px;
            text-align: center;
        }

    </style>

</head>

<body>

<div class="box">

    <h2>Verify OTP</h2>

    <p>
        Enter the 6-digit verification code.
    </p>

    <?php if ($error): ?>

        <div class="error">
            <?= htmlspecialchars($error) ?>
        </div>

    <?php endif; ?>

    <form method="POST">

        <label>OTP Code</label>

        <input
            type="text"
            name="otp"
            maxlength="6"
            pattern="[0-9]{6}"
            placeholder="000000"
            required
            autofocus
        >

        <button type="submit">
            Verify OTP
        </button>

    </form>

    <?php if (isset($_SESSION["test_otp"])): ?>

        <div class="test-otp">

            <strong>TEST OTP:</strong><br>

            <?= htmlspecialchars($_SESSION["test_otp"]) ?>

            <br><br>

            <small>
                This is temporary. Later we will send the OTP
                directly to the user's email.
            </small>

        </div>

    <?php endif; ?>

</div>

</body>
</html>
```
