<!-- user's booking -->

<?php
session_start();
include 'includes/header.php';
require_once 'includes/db.php';

// 👉 Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "Please login to view your bookings.";
    exit();
}

$user_id = $_SESSION['user_id'];

// 🔍 Fetch bookings from database
$query = "SELECT b.*, f.flight_name, f.from_location, f.to_location, f.departure_time, f.arrival_time
          FROM bookings b
          JOIN flights f ON b.flight_id = f.id
          WHERE b.user_id = $user_id
          ORDER BY b.booking_time DESC";

$result = mysqli_query($conn, $query);
?>

<h2>🧾 My Bookings</h2>

<?php if (mysqli_num_rows($result) > 0): ?>
    <table border="1" cellpadding="10">
        <tr>
            <th>Flight</th>
            <th>From</th>
            <th>To</th>
            <th>Departure</th>
            <th>Arrival</th>
            <th>Passenger</th>
            <th>Age</th>
            <th>Gender</th>
            <th>Seat</th>
            <th>Status</th>
            <th>Booked On</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?= $row['flight_name'] ?></td>
                <td><?= $row['from_location'] ?></td>
                <td><?= $row['to_location'] ?></td>
                <td><?= $row['departure_time'] ?></td>
                <td><?= $row['arrival_time'] ?></td>
                <td><?= $row['passenger_name'] ?></td>
                <td><?= $row['age'] ?></td>
                <td><?= $row['gender'] ?></td>
                <td><?= $row['seat_no'] ?></td>
                <td><?= $row['payment_status'] ?></td>
                <td><?= $row['booking_time'] ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
<?php else: ?>
    <p>❌ No bookings found.</p>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
