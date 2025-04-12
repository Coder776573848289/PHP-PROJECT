<?php
include 'includes/db.php'; // database connection file

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $phone = trim($_POST['phone']);

    // Validate form fields
    if (empty($name) || empty($email) || empty($password) || empty($phone)) {
        $error = "⚠️ All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "⚠️ Please enter a valid email address.";
    } elseif (strlen($password) < 6) {
        $error = "⚠️ Password must be at least 6 characters.";
    } elseif (!preg_match('/^[0-9]{10}$/', $phone)) {
        $error = "⚠️ Phone number must be 10 digits.";
    } else {
        // Check if email already exists
        $check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
        if (mysqli_num_rows($check) > 0) {
            $error = "⚠️ Email is already registered.";
        } else {
            // Insert into DB (Note: in real systems use password_hash())
            $query = "INSERT INTO users (name, email, password, phone, created_at)
                      VALUES ('$name', '$email', '$password', '$phone', NOW())";

            if (mysqli_query($conn, $query)) {
                $success = "✅ Registration successful. You can now <a href='login.php'>login</a>.";
            } else {
                $error = "❌ Something went wrong. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Registration</title>
    <style>
        body {
            font-family: sans-serif;
            background: #f2f2f2;
        }

        .register-box {
            max-width: 450px;
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

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            margin-top: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        input[type="submit"] {
            background-color: #007bff;
            color: white;
            padding: 12px;
            border: none;
            margin-top: 20px;
            width: 100%;
            cursor: pointer;
            border-radius: 5px;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }

        .error, .success {
            margin-top: 15px;
            text-align: center;
        }

        .error {
            color: red;
        }

        .success {
            color: green;
        }
    </style>
</head>
<body>

<div class="register-box">
    <h2>User Registration</h2>
    <form method="POST">
        <label>Name:</label>
        <input type="text" name="name" placeholder="Enter your name">

        <label>Email:</label>
        <input type="email" name="email" placeholder="Enter your email">

        <label>Password:</label>
        <input type="password" name="password" placeholder="Enter password (min 6 chars)">

        <label>Phone:</label>
        <input type="text" name="phone" placeholder="10-digit phone number">

        <input type="submit" value="Register">
    </form>

    <?php
    if (!empty($error)) {
        echo "<div class='error'>$error</div>";
    }

    if (!empty($success)) {
        echo "<div class='success'>$success</div>";
    }
    ?>
</div>

</body>
</html>
