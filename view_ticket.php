<?php
require_once 'includes/db.php';

if (!isset($_GET['booking_id'])) {
    die("Booking ID is required.");
}

$booking_id = $_GET['booking_id'];

// Fetch E-ticket data
$sql = "SELECT b.id AS booking_id, b.booking_date, b.total_amount,
               f.airline_name, f.from_location, f.to_location, f.departure, f.arrival,
               p.name AS passenger_name, p.gender, p.age, p.seat_no
        FROM bookings2 b
        JOIN flights2 f ON b.flight_id = f.id
        JOIN passengers p ON b.id = p.booking_id
        WHERE b.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("E-ticket not found.");
}

$data = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>E-Ticket - Booking #<?= $data['booking_id'] ?></title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            padding: 40px;
            background: #f9f9f9;
        }

        .ticket {
            max-width: 700px;
            margin: auto;
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            font-size: 16px;
            border-collapse: collapse;
        }

        td {
            padding: 10px 6px;
            vertical-align: top;
        }

        .section-title {
            background-color: #3498db;
            color: white;
            padding: 8px 10px;
            font-size: 18px;
            margin-top: 20px;
        }

        .download-btn {
            display: block;
            margin: 30px auto 0;
            padding: 12px 24px;
            font-size: 16px;
            background-color: #2ecc71;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        @media print {
            .download-btn {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="ticket" id="ticket">
        <h2>✈️ Flight E-Ticket</h2>

        <div class="section-title">Booking Information</div>
        <table>
            <tr><td>Booking ID:</td><td><?= $data['booking_id'] ?></td></tr>
            <tr><td>Booking Date:</td><td><?= $data['booking_date'] ?></td></tr>
            <tr><td>Total Amount:</td><td>₹<?= number_format($data['total_amount'], 2) ?></td></tr>
        </table>

        <div class="section-title">Flight Details</div>
        <table>
            <tr><td>Airline:</td><td><?= htmlspecialchars($data['airline_name']) ?></td></tr>
            <tr><td>From:</td><td><?= $data['from_location'] ?></td></tr>
            <tr><td>To:</td><td><?= $data['to_location'] ?></td></tr>
            <tr><td>Departure:</td><td><?= $data['departure'] ?></td></tr>
            <tr><td>Arrival:</td><td><?= $data['arrival'] ?></td></tr>
        </table>

        <div class="section-title">Passenger Information</div>
        <table>
            <tr><td>Name:</td><td><?= htmlspecialchars($data['passenger_name']) ?></td></tr>
            <tr><td>Gender:</td><td><?= $data['gender'] ?></td></tr>
            <tr><td>Age:</td><td><?= $data['age'] ?></td></tr>
            <tr><td>Seat No:</td><td><?= $data['seat_no'] ?></td></tr>
        </table>

        <button onclick="window.print()" class="download-btn">🖨️ Download E-Ticket</button>
    </div>
</body>
</html>
