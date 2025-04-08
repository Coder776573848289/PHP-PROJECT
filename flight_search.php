<?php
session_start();
include 'includes/header.php';
require_once 'includes/db.php';
?>

<h2>Search Flights</h2>
<form method="POST">
    <label>From:</label>
    <input type="text" name="source" required><br>

    <label>To:</label>
    <input type="text" name="destination" required><br>

    <label>Departure Time:</label>
    <input type="datetime-local" name="departure"><br>

    <label>Arrival Time:</label>
    <input type="datetime-local" name="arrival"><br>

    <label>Min Price:</label>
    <input type="number" name="min_price" min="0"><br>

    <label>Max Price:</label>
    <input type="number" name="max_price" min="0"><br>

    <label>Class:</label>
    <select name="class">
        <option value="">Any</option>
        <option value="economy">Economy</option>
        <option value="business">Business</option>
        <option value="first">First Class</option>
    </select><br>

    <label>Flight Duration (hours):</label>
    <input type="number" name="min_duration" min="0"><br>

    <input type="submit" name="search" value="Search Flights">
</form>


<?php
if (isset($_POST['search'])) {
    $source = mysqli_real_escape_string($conn, $_POST['source']);
    $destination = mysqli_real_escape_string($conn, $_POST['destination']);
    $departure = mysqli_real_escape_string($conn, $_POST['departure']);
    $arrival = mysqli_real_escape_string($conn, $_POST['arrival']);
    $min_price = $_POST['min_price'];
    $max_price = $_POST['max_price'];
    // $status = mysqli_real_escape_string($conn, $_POST['status']);

    // ✅ Build the dynamic query
    $query = "SELECT * FROM flights WHERE from_location='$source' AND to_location='$destination'";

    if (!empty($departure)) {
        $query .= " AND departure='$departure'";
    }

    if (!empty($arrival)) {
        $query .= " AND arrival='$arrival'";
    }

    if (!empty($min_price)) {
        $query .= " AND price >= $min_price";
    }

    if (!empty($max_price)) {
        $query .= " AND price <= $max_price";
    }

    if (!empty($class)) {
        $query .= " AND class='$class'";
    }

    if (!empty($min_duration)) {
        $query .= " AND duration >= $min_duration";
    }

    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        echo "<h3>Available Flights</h3>";
        echo "<table border='1'>
                <tr><th>Flight</th><th>From</th><th>To</th><th>Departure</th><th>Arrival</th><th>Price</th><th>Status</th><th>Action</th></tr>";

        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>
                    <td>{$row['flight_name']}</td>
                    <td>{$row['from_location']}</td>
                    <td>{$row['to_location']}</td>
                    <td>{$row['departure']}</td>
                    <td>{$row['arrival']}</td>
                    <td>₹{$row['price']}</td>
                    <td>{$row['status']}</td>
                    <td><a href='passenger_details.php?flight_id={$row['id']}'>Book Now</a></td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "❌ No flights found for selected criteria.";
    }
}
?>

<?php include 'includes/footer.php'; ?>