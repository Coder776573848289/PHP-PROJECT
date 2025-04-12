<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect if not logged in
    exit();
}

require_once 'includes/db.php';

// Mock payment success logic
if (isset($_POST['make_payment'])) {
    // Process payment (Here, we mock it)
    $payment_status = "success"; // This should be based on a real payment gateway

    if ($payment_status == "success") {
        // Insert into orders table or update payment status
        $user_id = $_SESSION['user_id'];
        $order_id = "ORD" . time(); // Generate unique order ID

        $query = "INSERT INTO orders (user_id, order_id, payment_status) 
                  VALUES ('$user_id', '$order_id', 'Paid')";

        if (mysqli_query($conn, $query)) {
            echo "✅ Payment successful! Your booking is confirmed.";
            header("Location: ticket.php?order_id=$order_id"); // Redirect to ticket page
            exit();
        } else {
            echo "❌ Error in processing payment: " . mysqli_error($conn);
        }
    } else {
        echo "❌ Payment failed. Please try again.";
    }
}
?>

<form method="POST">
    <h3>Payment Details</h3>
    <label>Card Number:</label><br>
    <input type="text" name="card_number" required><br>
    <label>Expiry Date:</label><br>
    <input type="text" name="expiry_date" required><br>
    <label>CVV:</label><br>
    <input type="text" name="cvv" required><br>
    <input type="submit" name="make_payment" value="Pay Now">
</form>
