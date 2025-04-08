<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['admin'];
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .dashboard {
            text-align: center;
            margin-top: 50px;
        }
        .dashboard h2 {
            color: #333;
        }
        .dashboard a {
            display: inline-block;
            margin: 20px;
            padding: 15px 25px;
            background-color: #3498db;
            color: #fff;
            text-decoration: none;
            border-radius: 8px;
            transition: 0.3s ease;
        }
        .dashboard a:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
    
    <div class="dashboard">
        <h2>Welcome, <?php echo htmlspecialchars($username); ?>!</h2>
        
        
        <a href="manage_flight.php">Total Flights</a>
        <a href="booking.php">Booking</a>
        <a href="pending-payments.php">Pending Payments</a>
        <a href="todaybookings.php">Today's Booking</a>
        <a href="manageusers.php">Manage All Users</a>
    </div>
    <h3><a href="logout.php"><- LogOut</a></h3>
</body>
</html>
