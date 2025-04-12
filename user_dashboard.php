<?php
session_start();
require_once 'includes/db.php';

// Dummy session (replace with real login logic)
$_SESSION['user_id'] = 1;
$user_id = $_SESSION['user_id'];

// Fetch user info (replace with actual user table if needed)
$user_name = "John Doe"; // Placeholder name

// Fetch bookings for user
$sql = "SELECT b.id AS booking_id, b.booking_date, b.total_amount, 
               f.airline_name, f.from_location, f.to_location, f.departure, f.arrival, 
               p.name AS passenger_name, p.gender, p.age, p.seat_no
        FROM bookings2 b
        JOIN flights2 f ON b.flight_id = f.id
        JOIN passengers p ON b.id = p.booking_id
        WHERE b.user_id = ?
        ORDER BY f.departure DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$upcoming = [];
$past = [];

while ($row = $result->fetch_assoc()) {
    $departure = strtotime($row['departure']);
    $now = time();
    if ($departure > $now) {
        $upcoming[] = $row;
    } else {
        $past[] = $row;
    }
}

$stmt->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Dashboard</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f6f9;
            padding: 40px;
        }

        .container {
            max-width: 1200px;
            margin: auto;
            background-color: white;
            padding: 25px 30px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th, table td {
            padding: 12px 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        table th {
            background-color: #3498db;
            color: white;
        }

        .section-title {
            margin-top: 40px;
            font-size: 20px;
            color: #333;
            border-bottom: 2px solid #3498db;
            display: inline-block;
            padding-bottom: 5px;
        }

        .user-info {
            font-size: 18px;
            background-color: #ecf0f1;
            padding: 15px;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Welcome, <?= htmlspecialchars($user_name) ?></h2>
        <div class="user-info">
            <strong>User ID:</strong> <?= htmlspecialchars($user_id) ?><br>
            <strong>Email:</strong> johndoe@example.com <!-- update if needed -->
        </div>

        <div class="section-title">✈️ Upcoming Bookings</div>
        <?php if (count($upcoming) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Passenger</th>
                        <th>Flight</th>
                        <th>From → To</th>
                        <th>Departure</th>
                        <th>Arrival</th>
                        <th>Seat</th>
                        <th>Amount (₹)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($upcoming as $b): ?>
                        <tr>
                            <td><?= $b['booking_id'] ?></td>
                            <td><?= htmlspecialchars($b['passenger_name']) ?> (<?= $b['gender'] ?>, <?= $b['age'] ?>)</td>
                            <td><?= htmlspecialchars($b['airline_name']) ?></td>
                            <td><?= htmlspecialchars($b['from_location']) ?> → <?= htmlspecialchars($b['to_location']) ?></td>
                            <td><?= $b['departure'] ?></td>
                            <td><?= $b['arrival'] ?></td>
                            <td><?= $b['seat_no'] ?></td>
                            <td>₹<?= number_format($b['total_amount'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No upcoming bookings found.</p>
        <?php endif; ?>

        <div class="section-title">📜 Booking History</div>
        <?php if (count($past) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Passenger</th>
                        <th>Flight</th>
                        <th>From → To</th>
                        <th>Departure</th>
                        <th>Arrival</th>
                        <th>Seat</th>
                        <th>Amount (₹)</th>
                        <th>E-Ticket</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($past as $b): ?>
                        <tr>
                            <td><?= $b['booking_id'] ?></td>
                            <td><?= htmlspecialchars($b['passenger_name']) ?> (<?= $b['gender'] ?>, <?= $b['age'] ?>)</td>
                            <td><?= htmlspecialchars($b['airline_name']) ?></td>
                            <td><?= htmlspecialchars($b['from_location']) ?> → <?= htmlspecialchars($b['to_location']) ?></td>
                            <td><?= $b['departure'] ?></td>
                            <td><?= $b['arrival'] ?></td>
                            <td><?= $b['seat_no'] ?></td>
                            <td>₹<?= number_format($b['total_amount'], 2) ?></td>
                            <td>
                                <form action="view_e_ticket.php" method="GET" target="_blank">
                                    <input type="hidden" name="booking_id" value="<?= $b['booking_id'] ?>">
                                    <button type="submit">View E-Ticket</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No past bookings found.</p>
        <?php endif; ?>
    </div>
</body>
</html>
