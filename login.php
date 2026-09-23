```php
<?php
session_start();
require "config.php";

$username = trim($_POST["username"] ?? "");
$password = $_POST["password"] ?? "";

if ($username === "" || $password === "") {
    header("Location: index.php?error=" . urlencode("Please enter username and password."));
    exit;
}

$stmt = $conn->prepare(
    "SELECT id, name, username, password, role
     FROM users
     WHERE username = ?
     LIMIT 1"
);

$stmt->bind_param("s", $username);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user && password_verify($password, $user["password"])) {

    session_regenerate_id(true);

    $_SESSION["user_id"] = $user["id"];
    $_SESSION["name"] = $user["name"];
    $_SESSION["username"] = $user["username"];
    $_SESSION["role"] = $user["role"];

    header("Location: /library2_management/dashboard.php");
    exit;
}

header(
    "Location: /library2_management/index.php?error=" .
    urlencode("Invalid username or password.")
);

exit;
?>
```
