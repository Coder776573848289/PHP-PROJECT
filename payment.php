<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['booking']) || !isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$booking = $_SESSION['booking'];
$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $flight_id = $booking['flight_id'];
    $return_flight_id = isset($booking['return_flight_id']) ? $booking['return_flight_id'] : null;
    $class_type = $booking['class_type'];
    $total_amount = $booking['total_amount'];
    $passengers = $booking['passengers'];
    $payment_mode = $_POST['payment_mode'];
    $transaction_id = 'TXN' . strtoupper(uniqid());

    // Insert into bookings2 table
    $stmt = $conn->prepare("INSERT INTO bookings2 (user_id, flight_id, return_flight_id, class_type, total_amount, payment_status) VALUES (?, ?, ?, ?, ?, 'Paid')");
    $stmt->bind_param("iiisd", $user_id, $flight_id, $return_flight_id, $class_type, $total_amount);
    $stmt->execute();
    $booking_id = $stmt->insert_id;

    // Insert passengers
    $pass_stmt = $conn->prepare("INSERT INTO passengers (booking_id, name, gender, age, seat_no) VALUES (?, ?, ?, ?, ?)");
    foreach ($passengers as $p) {
        $pass_stmt->bind_param("issis", $booking_id, $p['name'], $p['gender'], $p['age'], $p['seat_no']);
        $pass_stmt->execute();
    }

    // Insert payment
    $pay_stmt = $conn->prepare("INSERT INTO payments (booking_id, payment_mode, transaction_id, amount) VALUES (?, ?, ?, ?)");
    $pay_stmt->bind_param("issd", $booking_id, $payment_mode, $transaction_id, $total_amount);
    $pay_stmt->execute();

    unset($_SESSION['booking']);

    // Redirect to dashboard with success flag
    header("Location: user_dashboard.php?success=1");
    exit;
}
?>

<!-- Payment Form -->
<!DOCTYPE html>
<html>
<head>
    <title>Payment</title>
    <style>
        body { font-family: Arial; background: #f8f9fa; padding: 30px; }
        .payment-box {
            max-width: 500px;
            margin: auto;
            background: #fff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 { text-align: center; }
        .info {
            margin-bottom: 20px;
            background: #f1f1f1;
            padding: 15px;
            border-radius: 5px;
        }
        select, button {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            border-radius: 5px;
        }
        button {
            background: #28a745;
            color: white;
            border: none;
        }
        button:hover { background: #218838; }
    </style>
</head>
<body>
<div class="payment-box">
    <h2>Payment</h2>
    <div class="info">
        <p><strong>Total Amount:</strong> ₹<?= number_format($booking['total_amount'], 2) ?></p>
        <p><strong>Seat Class:</strong> <?= ucfirst($booking['class_type']) ?></p>
        <p>Select a payment method to complete your booking:</p>
    </div>
    <form method="POST">
        <label>Payment Mode:</label>
        <select name="payment_mode" required>
            <option value="UPI">UPI</option>
            <option value="Credit Card">Credit Card</option>
            <option value="Debit Card">Debit Card</option>
        </select>
        <br><br>
        <button type="submit">Pay & Confirm Booking</button>
    </form>
</div>
</body>
</html>