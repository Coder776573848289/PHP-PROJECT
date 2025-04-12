<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect if not logged in
    exit();
}

require_once 'includes/db.php'; // Assuming db.php handles DB connection

if (isset($_POST['submit_passenger'])) {
    // Assuming you have a 'flights' table with details
    $flight_id = mysqli_real_escape_string($conn, $_POST['flight_id']);
    $passenger_name = mysqli_real_escape_string($conn, $_POST['passenger_name']);
    $contact_number = mysqli_real_escape_string($conn, $_POST['contact_number']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    // Insert passenger details into the database (create a passenger table if necessary)
    $query = "INSERT INTO passengers (user_id, flight_id, name, contact_number, email) 
              VALUES ('" . $_SESSION['user_id'] . "', '$flight_id', '$passenger_name', '$contact_number', '$email')";
    
    if (mysqli_query($conn, $query)) {
        echo "✅ Passenger details saved. Proceed to payment.";
        // Redirect or go to payment page
        header("Location: payment.php");
        exit();
    } else {
        echo "❌ Error: " . mysqli_error($conn);
    }
}
?>

<form method="POST">
    <input type="hidden" name="flight_id" value="<?php echo $flight_id; ?>"> <!-- Set flight ID dynamically -->
    <input type="text" name="passenger_name" placeholder="Passenger Name" required><br>
    <input type="text" name="contact_number" placeholder="Contact Number" required><br>
    <input type="email" name="email" placeholder="Email" required><br>
    <input type="submit" name="submit_passenger" value="Proceed to Payment">
</form>
