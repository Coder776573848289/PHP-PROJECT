<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user bookings
$sql = "
SELECT 
    b.id AS booking_id,
    b.class_type,
    b.total_amount,
    b.payment_status,
    b.booking_time,
    f1.airline_name AS onward_airline,
    f1.from_location AS onward_from,
    f1.to_location AS onward_to,
    f1.departure AS onward_departure,
    f1.arrival AS onward_arrival,
    f2.airline_name AS return_airline,
    f2.from_location AS return_from,
    f2.to_location AS return_to,
    f2.departure AS return_departure,
    f2.arrival AS return_arrival
FROM bookings2 b
JOIN flights2 f1 ON b.flight_id = f1.id
LEFT JOIN flights2 f2 ON b.return_flight_id = f2.id
WHERE b.user_id = ?
ORDER BY b.booking_time DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$bookings = [];

while ($row = $result->fetch_assoc()) {
    // Get passengers for each booking
    $booking_id = $row['booking_id'];
    $pass_stmt = $conn->prepare("SELECT name, gender, age, seat_no FROM passengers WHERE booking_id = ?");
    $pass_stmt->bind_param("i", $booking_id);
    $pass_stmt->execute();
    $pass_result = $pass_stmt->get_result();
    $passengers = $pass_result->fetch_all(MYSQLI_ASSOC);
    $row['passengers'] = $passengers;
    $bookings[] = $row;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Dashboard</title>
    <style>
        body { font-family: Arial; background: #f5f5f5; padding: 30px; }
        .dashboard-container { max-width: 1000px; margin: auto; }
        .booking-card {
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 { text-align: center; margin-bottom: 30px; }
        .flight-info { margin-bottom: 10px; }
        .label { font-weight: bold; }
        .passenger-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .passenger-table th, .passenger-table td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }
        .status-paid { color: green; font-weight: bold; }
        .status-pending { color: red; font-weight: bold; }
    </style>
</head>
<body>
<div style="text-align: right; margin-bottom: 20px;">
    <form action="logout_user.php" method="POST">
        <button type="submit" style="background-color: #dc3545; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer;">
            Logout
        </button>
    </form>
</div>

<div class="dashboard-container">
    <h2>Welcome to Your Dashboard</h2>
    <a href="search_flights.php"> Search Flight</a>  &nbsp; &nbsp; &nbsp; 
    <?php if (isset($_GET['success'])): ?>
        <p style="color: green; text-align: center;">✅ Booking Successful!</p>
    <?php endif; ?>

    <?php if (empty($bookings)): ?>
        <p>No bookings found.</p>
    <?php else: ?>
        <?php foreach ($bookings as $b): ?>
            <div class="booking-card">
                <div class="flight-info">
                    <p><span class="label">Booking ID:</span> <?= $b['booking_id'] ?></p>
                    <p><span class="label">Class:</span> <?= ucfirst($b['class_type']) ?></p>
                    <p><span class="label">Amount:</span> ₹<?= number_format($b['total_amount'], 2) ?></p>
                    <p><span class="label">Payment:</span> 
                        <span class="<?= $b['payment_status'] === 'Paid' ? 'status-paid' : 'status-pending' ?>">
                            <?= $b['payment_status'] ?>
                        </span>
                    </p>
                    <p><span class="label">Booked on:</span> <?= $b['booking_time'] ?></p>
                </div>

                <div class="flight-info">
                    <h4>✈️ Onward Flight:</h4>
                    <p><?= $b['onward_airline'] ?> — <?= $b['onward_from'] ?> ➡ <?= $b['onward_to'] ?></p>
                    <p>Departure: <?= $b['onward_departure'] ?> | Arrival: <?= $b['onward_arrival'] ?></p>
                </div>

                <?php if ($b['return_airline']): ?>
                    <div class="flight-info">
                        <h4>🔁 Return Flight:</h4>
                        <p><?= $b['return_airline'] ?> — <?= $b['return_from'] ?> ➡ <?= $b['return_to'] ?></p>
                        <p>Departure: <?= $b['return_departure'] ?> | Arrival: <?= $b['return_arrival'] ?></p>
                    </div>
                <?php endif; ?>

                <?php if (!empty($b['passengers'])): ?>
                    <h4>👥 Passengers:</h4>
                    <table class="passenger-table">
                        <tr><th>Name</th><th>Gender</th><th>Age</th><th>Seat No</th></tr>
                        <?php foreach ($b['passengers'] as $p): ?>
                            <tr>
                                <td><?= htmlspecialchars($p['name']) ?></td>
                                <td><?= $p['gender'] ?></td>
                                <td><?= $p['age'] ?></td>
                                <td><?= $p['seat_no'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
</body>
</html>
