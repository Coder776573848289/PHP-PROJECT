<?php
require_once 'includes/db.php';

if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    
    $result = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email' LIMIT 1");
    
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            header("Location: flight_search.php");
        } else {
            echo "❌ Incorrect password.";
        }
    } else {
        echo "❌ User not found.";
    }
}
?>

<form method="POST">
    <input type="email" name="email" placeholder="Email" required><br>
    <input type="password" name="password" placeholder="Password" required><br>
    <input type="submit" name="login" value="Login">
</form>

<a href="reset_password.php">Forgot Password?</a>
