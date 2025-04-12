<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

date_default_timezone_set('Asia/Kolkata');
$today = date('Y-m-d');

// Updated query for bookings2
$sql = "SELECT * FROM bookings2 WHERE DATE(booking_time) = ? ORDER BY booking_time DESC";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("s", $today);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    die("Query preparation failed: " . $conn->error);
}
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
            width: 95%;
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

        .status-pending {
            color: red;
            font-weight: bold;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
        }

        .error-msg {
            text-align: center;
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>

<h2>Today's Bookings (<?= htmlspecialchars($today) ?>)</h2>

<?php if (!$result): ?>
    <p class="error-msg">Failed to retrieve bookings. Please try again later.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>User ID</th>
                <th>Flight ID</th>
                <th>Return Flight ID</th>
                <th>Class</th>
                <th>Total Amount</th>
                <th>Payment Status</th>
                <th>Booking Time</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td><?= htmlspecialchars($row['user_id']) ?></td>
                        <td><?= htmlspecialchars($row['flight_id']) ?></td>
                        <td><?= $row['return_flight_id'] ?? 'N/A' ?></td>
                        <td><?= ucfirst(htmlspecialchars($row['class_type'])) ?></td>
                        <td>₹<?= number_format($row['total_amount'], 2) ?></td>
                        <td class="<?= strtolower($row['payment_status']) === 'paid' ? 'status-paid' : 'status-pending' ?>">
                            <?= htmlspecialchars($row['payment_status']) ?>
                        </td>
                        <td><?= htmlspecialchars($row['booking_time']) ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8">No bookings made today.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
<?php endif; ?>

<div class="back-link">
    <a href="dashbord.php">← Back to Dashboard</a>
</div>

</body>
</html>
