<?php
require_once 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $flight_id = intval($_POST['flight_id']);
    $name = trim($_POST['name']);
    $gender = $_POST['gender'];
    $age = intval($_POST['age']);
    $seat_no = trim($_POST['seat_no']);

    // Fetch flight details
    $stmt = $conn->prepare("SELECT * FROM flights2 WHERE id = ?");
    $stmt->bind_param("i", $flight_id);
    $stmt->execute();
    $flight = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$flight) {
        die("Flight not found.");
    }

    // Get price (you can expand to handle class-wise pricing)
    $total_price = $flight['economy_price']; // Assume economy for now

    // Insert into bookings2 table
    $booking_sql = "INSERT INTO bookings2 (user_id, flight_id, booking_date, total_amount)
                    VALUES (?, ?, NOW(), ?)";
    $stmt = $conn->prepare($booking_sql);
    $user_id = 1; // use actual user session if logged in
    $stmt->bind_param("iid", $user_id, $flight_id, $total_price);
    $stmt->execute();
    $booking_id = $stmt->insert_id;
    $stmt->close();

    // Insert passenger
    $passenger_sql = "INSERT INTO passengers (booking_id, name, gender, age, seat_no)
                      VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($passenger_sql);
    $stmt->bind_param("issis", $booking_id, $name, $gender, $age, $seat_no);
    $stmt->execute();
    $stmt->close();

    // Redirect to confirmation page
    header("Location: booking_success.php?booking_id=$booking_id");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Payment</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f0f2f5;
            padding: 40px;
        }

        .container {
            max-width: 600px;
            margin: auto;
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin: 15px 0 5px;
        }

        select, input[type="submit"] {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            margin-top: 8px;
        }

        input[type="submit"] {
            background-color: #27ae60;
            color: white;
            border: none;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #219150;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Choose Payment Method</h2>
        <form method="POST">
            <!-- Hidden values -->
            <input type="hidden" name="flight_id" value="<?= htmlspecialchars($_POST['flight_id']) ?>">
            <input type="hidden" name="name" value="<?= htmlspecialchars($_POST['name']) ?>">
            <input type="hidden" name="gender" value="<?= htmlspecialchars($_POST['gender']) ?>">
            <input type="hidden" name="age" value="<?= htmlspecialchars($_POST['age']) ?>">
            <input type="hidden" name="seat_no" value="<?= htmlspecialchars($_POST['seat_no']) ?>">

            <label for="payment_mode">Select Payment Mode:</label>
            <select id="payment_mode" name="payment_mode" required>
                <option value="">--Choose--</option>
                <option value="UPI">UPI</option>
                <option value="Credit Card">Credit Card</option>
                <option value="Net Banking">Net Banking</option>
            </select>

            <input type="submit" value="Pay & Book">
        </form>
    </div>
</body>
</html>
