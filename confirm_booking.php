<?php
require_once 'includes/db.php'; // Your DB connection file

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Grab form values from passenger_details.php
    $flight_id = intval($_POST['flight_id']);
    $name = trim($_POST['name']);
    $gender = $_POST['gender'];
    $age = intval($_POST['age']);
    $seat_no = trim($_POST['seat_no']);

    // Retrieve flight details
    $sql = "SELECT * FROM flights2 WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $flight_id);
    $stmt->execute();
    $flight = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$flight) {
        die("Invalid Flight Selection");
    }
} else {
    die("Invalid access method.");
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Confirm Booking</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f4f4;
            padding: 30px;
        }

        .container {
            max-width: 750px;
            margin: auto;
            background-color: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #2c3e50;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        th, td {
            text-align: left;
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        .btn-group {
            display: flex;
            justify-content: space-between;
        }

        .btn {
            padding: 12px 24px;
            font-size: 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        .btn-edit {
            background-color: #f39c12;
            color: white;
        }

        .btn-edit:hover {
            background-color: #e67e22;
        }

        .btn-confirm {
            background-color: #27ae60;
            color: white;
        }

        .btn-confirm:hover {
            background-color: #219150;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Review & Confirm Your Booking</h2>

        <h3>Flight Details</h3>
        <table>
            <tr><th>Flight</th><td><?= htmlspecialchars($flight['airline_name']) ?></td></tr>
            <tr><th>From</th><td><?= $flight['from_location'] ?></td></tr>
            <tr><th>To</th><td><?= $flight['to_location'] ?></td></tr>
            <tr><th>Departure</th><td><?= $flight['departure'] ?></td></tr>
            <tr><th>Arrival</th><td><?= $flight['arrival'] ?></td></tr>
            <tr><th>Status</th><td><?= $flight['status'] ?></td></tr>
        </table>

        <h3>Passenger Details</h3>
        <table>
            <tr><th>Name</th><td><?= htmlspecialchars($name) ?></td></tr>
            <tr><th>Gender</th><td><?= $gender ?></td></tr>
            <tr><th>Age</th><td><?= $age ?></td></tr>
            <tr><th>Seat No</th><td><?= htmlspecialchars($seat_no) ?></td></tr>
        </table>

        <form action="payment.php" method="POST">
            <!-- Pass all data to payment page -->
            <input type="hidden" name="flight_id" value="<?= $flight_id ?>">
            <input type="hidden" name="name" value="<?= htmlspecialchars($name) ?>">
            <input type="hidden" name="gender" value="<?= $gender ?>">
            <input type="hidden" name="age" value="<?= $age ?>">
             <input type="hidden" name="seat_no" value="<?= htmlspecialchars($seat_no) ?>">

             <div class="btn-group">
                 <button type="button" class="btn btn-edit" onclick="history.back();">Edit Details</button>
                 <button type="submit" class="btn btn-confirm">Confirm & Proceed to Payment</button>
             </div>
         </form>
     </div>
 </body>
 </html>
