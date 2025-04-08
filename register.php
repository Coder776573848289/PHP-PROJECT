<!-- user register -->

<!-- register.php -->
<?php include 'includes/header.php'; ?>
<h2>User Registration</h2>
<form method="POST">
    <input type="text" name="name" placeholder="Full Name" required><br>
    <input type="email" name="email" placeholder="Email" required><br>
    <input type="password" name="password" placeholder="Password" required><br>
    <input type="submit" name="register" value="Register">
</form>
<?php include 'includes/footer.php'; ?>

<?php
require_once 'includes/db.php';

if (isset($_POST['register'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $checkEmail = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");
    if (mysqli_num_rows($checkEmail) > 0) {
        echo "⚠️ Email already exists!";
    } else {
        $insert = mysqli_query($conn, "INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$password')");
        if ($insert) {
            echo "✅ Registered successfully! <a href='login.php'>Login Now</a>";
        } else {
            echo "❌ Registration failed.";
        }
    }
}
?>