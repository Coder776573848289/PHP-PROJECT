<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Set timezone
date_default_timezone_set('Asia/Kolkata');

// Get today's date (only date part)
$today = date('Y-m-d');

// Fetch bookings with today's date
$sql = "SELECT * FROM bookings WHERE DATE(booking_time) = ? ORDER BY booking_time DESC";
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

        .status-unknown {
            color: gray;
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
                <th>Passenger Name</th>
                <th>Gender</th>
                <th>Age</th>
                <th>Seat No</th>
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
                        <td><?= htmlspecialchars($row['passenger_name'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($row['gender'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($row['age'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($row['seat_no'] ?? 'N/A') ?></td>

                        <?php
                            $status = strtolower(trim($row['payment_status']));
                            $statusClass = 'status-unknown';

                            if ($status === 'paid') $statusClass = 'status-paid';
                            elseif ($status === 'unpaid') $statusClass = 'status-unpaid';
                        ?>
                        <td class="<?= $statusClass ?>">
                            <?= htmlspecialchars(ucfirst($row['payment_status'] ?? 'Unknown')) ?>
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
<?php endif; ?>

<div class="back-link">
    <a href="dashbord.php">← Back to Dashboard</a>
</div>

</body>
</html>
