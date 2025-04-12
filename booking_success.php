<?php
$booking_id = $_GET['booking_id'] ?? 'N/A';
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Booking Confirmed</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #e9f7ef;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 90vh;
        }

        .card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }

        h2 {
            color: #27ae60;
        }

        p {
            font-size: 18px;
            margin: 10px 0;
        }

        a {
            text-decoration: none;
            color: #3498db;
        }
    </style>
</head>
<body>
    <div class="card">
        <h2>🎉 Booking Confirmed!</h2>
        <p>Your booking ID is <strong>#<?= htmlspecialchars($booking_id) ?></strong></p>
        <p>Check your email or dashboard for details.</p>
        <p><a href="user_dashboard.php">User Dashboard</a></p>
    </div>
</body>
</html>
