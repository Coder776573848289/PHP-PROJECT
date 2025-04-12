<?php
session_start();
require_once 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['booking']) || !isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }

    $booking = $_SESSION['booking'];
    $user_id = $_SESSION['user_id'];

    $flight_id = $booking['flight_id'];
    $return_flight_id = isset($booking['return_flight_id']) ? $booking['return_flight_id'] : null;
    $class_type = $booking['class_type'];
    $total_amount = $booking['total_amount'];
    $passengers = $booking['passengers'];
    $payment_mode = $_POST['payment_mode'];
    $transaction_id = 'TXN' . strtoupper(uniqid());

    // Insert booking
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

    // Clear booking session
    unset($_SESSION['booking']);

    // Redirect to dashboard with success message
    header("Location: user_dashboard.php?success=1");
    exit;
} else {
    header("Location: payment.php");
    exit;
}
