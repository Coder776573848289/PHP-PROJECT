<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Get today's date in YYYY-MM-DD format
$today = date('Y-m-d');

// Fetch bookings for today only
$sql = "SELECT * FROM bookings WHERE DATE(booking_time) = '$today' ORDER BY booking_time DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Today's Bookings</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        h2 {
            text-align: center;
            margin-top: 30px;
        }

        table {
            width: 90%;
            margin: 20px auto;
            border-collapse: collapse;
        }

        th, td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
        }

        th {
            background-color: #343a40;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .status-paid {
            color: green;
            font-weight: bold;
        }

        .status-unpaid {
            color: red;
            font-weight: bold;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
        }

    </style>
</head>
<body>

<h2>Today's Bookings (<?= $today ?>)</h2>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>User ID</th>
            <th>Flight ID</th>
            <th>Passenger Name</th>
            <th>Gender</th>
            <th>Age</th>
            <th>Seat No</th>
            <th>Payment Status</th>
            <th>Booking Time</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['id']) ?></td>
                    <td><?= htmlspecialchars($row['user_id']) ?></td>
                    <td><?= htmlspecialchars($row['flight_id']) ?></td>
                    <td><?= htmlspecialchars($row['passenger_name']) ?></td>
                    <td><?= htmlspecialchars($row['gender']) ?></td>
                    <td><?= htmlspecialchars($row['age']) ?></td>
                    <td><?= htmlspecialchars($row['seat_no']) ?></td>
                    <td class="<?= strtolower($row['payment_status']) === 'paid' ? 'status-paid' : 'status-unpaid' ?>">
                        <?= htmlspecialchars(ucfirst($row['payment_status'])) ?>
                    </td>
                    <td><?= htmlspecialchars($row['booking_time']) ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="9">No bookings made today.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<div class="back-link">
<a href="dashbord.php">← Back to Dashboard</a>
</div>

</body>
</html>
