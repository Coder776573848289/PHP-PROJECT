<?php
require_once 'includes/db.php';

if (isset($_POST['email'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $result = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email' LIMIT 1");
    
    if (mysqli_num_rows($result) > 0) {
        $token = bin2hex(random_bytes(50)); // Generate random token
        mysqli_query($conn, "UPDATE users SET reset_token = '$token' WHERE email = '$email'");
        
        // Send reset email
        $reset_link = "http://yourdomain.com/reset_password_form.php?token=$token";
        $subject = "Password Reset Request";
        $message = "Click the link below to reset your password:\n$reset_link";
        mail($email, $subject, $message, "From: no-reply@pdpairlines.com");
        
        echo "✅ Password reset link sent to your email.";
    } else {
        echo "❌ Email not found.";
    }
}
?>

<form method="POST">
    <input type="email" name="email" placeholder="Enter your email" required><br>
    <input type="submit" value="Send Reset Link">
</form>
