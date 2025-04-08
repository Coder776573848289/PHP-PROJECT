<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect if not logged in
    exit();
}

require_once 'includes/db.php';

if (isset($_GET['order_id'])) {
    $order_id = mysqli_real_escape_string($conn, $_GET['order_id']);

    // Fetch ticket details from the database (order details)
    $result = mysqli_query($conn, "SELECT * FROM orders WHERE order_id = '$order_id' AND user_id = '" . $_SESSION['user_id'] . "'");

    if (mysqli_num_rows($result) > 0) {
        $ticket = mysqli_fetch_assoc($result);
        echo "<h2>Booking Confirmation</h2>";
        echo "Order ID: " . $ticket['order_id'] . "<br>";
        echo "Payment Status: " . $ticket['payment_status'] . "<br>";

        // Additional flight and passenger details can be displayed here

        echo "<a href='generate_ticket_pdf.php?order_id=$order_id' target='_blank'>Download Ticket (PDF)</a>";
    } else {
        echo "❌ No booking found.";
    }
}
?>
