<?php
session_start();
require_once '../includes/db.php'; // DB connection file path

$error = ""; // Initialize an error message variable

if (isset($_POST['login'])) {
    // Collecting email and password from form
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password.";
    } else {
         // Check if admin exists
        $sql = "SELECT * FROM admin WHERE username=? AND password=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            // Valid admin
            $_SESSION['admin'] = $username;
            header("Location: dashbord.php");
            exit();
        } else {
            // Invalid credentials
            $error = "Invalid username or password.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Admin Login</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
    <h2>Admin Login</h2>
    <form action="" method="POST">
        <?php if (!empty($error)): ?>
            <p class="error-message"><?php echo $error; ?></p>
        <?php endif; ?>

        <label for="username">Username:</label>
        <input type="text" id="username" name="username" ><br><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" ><br><br>

        <input type="submit" name="login" value="Login">
    </form>
    <a href="../index.php">Back</a>
    </div>
</body>
</html>