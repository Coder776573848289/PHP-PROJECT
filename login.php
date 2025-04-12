<?php
session_start();
include 'includes/db.php'; // database connection file

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Check if fields are empty
    if (empty($email) || empty($password)) {
        $error = "⚠️ Please fill in both email and password.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "⚠️ Please enter a valid email address.";
    } else {
        // Fetch user from DB
        $query = "SELECT * FROM temp_users WHERE email = '$email'";
        $result = mysqli_query($conn, $query);

        if ($result && mysqli_num_rows($result) == 1) {
            $user = mysqli_fetch_assoc($result);

            // Simple plain password match (for learning)
            if ($password === $user['password']) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];

                // ✅ Handle redirect back after login (like from confirm_booking.php)
                if (isset($_SESSION['redirect_after_login'])) {
                    $redirect = $_SESSION['redirect_after_login'];
                    unset($_SESSION['redirect_after_login']);

                    if (isset($_SESSION['post_data'])) {
                        $post_data = $_SESSION['post_data'];
                        unset($_SESSION['post_data']);
                        ?>
                        <form id="redirectForm" action="<?= htmlspecialchars($redirect) ?>" method="POST">
                            <?php foreach ($post_data as $key => $value): ?>
                                <?php if (is_array($value)): ?>
                                    <?php foreach ($value as $i => $sub): ?>
                                        <?php foreach ($sub as $subkey => $subvalue): ?>
                                            <input type="hidden" name="<?= $key ?>[<?= $i ?>][<?= $subkey ?>]" value="<?= htmlspecialchars($subvalue) ?>">
                                        <?php endforeach; ?>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <input type="hidden" name="<?= $key ?>" value="<?= htmlspecialchars($value) ?>">
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </form>
                        <script>document.getElementById('redirectForm').submit();</script>
                        <?php
                        exit;
                    }

                    header("Location: $redirect");
                    exit;
                }

                // Default: Go to dashboard
                header("Location: user_dashboard.php");
                exit();
            } else {
                $error = "❌ Incorrect password.";
            }
        } else {
            $error = "❌ User not found with this email.";
        }
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>User Login</title>
    <style>
        body {
            font-family: sans-serif;
            background: #f2f2f2;
        }

        .login-box {
            max-width: 400px;
            margin: 60px auto;
            padding: 30px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0px 0px 8px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            margin-top: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        input[type="submit"] {
            background-color: #28a745;
            color: white;
            padding: 12px;
            border: none;
            margin-top: 20px;
            width: 100%;
            cursor: pointer;
            border-radius: 5px;
        }

        input[type="submit"]:hover {
            background-color: #218838;
        }

        .error {
            color: red;
            margin-top: 10px;
            text-align: center;
        }

        .forgot-link {
            text-align: right;
            margin-top: 10px;
        }

        .forgot-link a {
            color: #007bff;
            text-decoration: none;
        }

        .forgot-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="login-box">
    <h2>User Login</h2>
    <form method="POST">
        <label>Email:</label>
        <input type="email" name="email" placeholder="Enter your email">

        <label>Password:</label>
        <input type="password" name="password" placeholder="Enter your password">

        <div class="forgot-link">
            <a href="forgot_password.php">Forgot Password?</a>
        </div>

        <input type="submit" value="Login">
    </form>

    <?php if (!empty($error)) {
        echo "<div class='error'>$error</div>";
    } ?>
</div>

</body>
</html>
