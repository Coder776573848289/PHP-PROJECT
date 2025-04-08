<?php
session_start();
require_once '../includes/db.php'; // adjust path as needed

// Check if admin is logged in
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Get the flight ID from URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: manage_flight.php");
    exit();
}

$flight_id = $_GET['id'];

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $airline_name = $_POST['airline_name'];
    $from_location = $_POST['from_location'];
    $to_location = $_POST['to_location'];
    $departure = $_POST['departure'];
    $arrival = $_POST['arrival'];
    $price = floatval($_POST['price']);
    $seats = intval($_POST['seats']);
    $status = $_POST['status'];
    $flight_id = intval($_GET['id']);

    // Prevent from and to being same
    if ($from_location === $to_location) {
        die("From and To locations cannot be the same.");
    }

    $update_sql = "UPDATE flights SET 
        airline_name=?, 
        from_location=?, 
        to_location=?, 
        departure=?, 
        arrival=?, 
        price=?, 
        seats=?, 
        status=? 
        WHERE id=?";

    $stmt = $conn->prepare($update_sql);

    if ($stmt) {
        $stmt->bind_param("sssssdiss", $airline_name, $from_location, $to_location, $departure, $arrival, $price, $seats, $status, $flight_id);

        if ($stmt->execute()) {
            header("Location: manage_flight.php");
            exit();
        } else {
            echo "Update failed: " . $stmt->error;
        }
    } else {
        echo "SQL prepare failed: " . $conn->error;
    }
}

// Fetch flight details for form
$sql = "SELECT * FROM flights WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $flight_id);
$stmt->execute();
$result = $stmt->get_result();
$flight = $result->fetch_assoc();

if (!$flight) {
    echo "Flight not found.";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Edit Flight</title>
    <style>
        form {
            max-width: 600px;
            margin: 30px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        label {
            display: block;
            margin-top: 12px;
        }
        input, select {
            width: 100%;
            padding: 8px;
            margin-top: 4px;
        }
        input[type="submit"] {
            margin-top: 20px;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

    <form method="POST">
        <h2>Edit Flight</h2>

        <label>Airline Name:</label>
        <input type="text" name="airline_name" value="<?php echo htmlspecialchars($flight['airline_name']); ?>" required>

        <label>From Location:</label>
        <select name="from_location" required>
            <option value=""> -- Select Source -- </option>
            <option value="DEL" <?php if ($flight['from_location'] == 'DEL') echo 'selected'; ?>>Indira Gandhi International Airport (DEL), New Delhi</option>
            <option value="BOM" <?php if ($flight['from_location'] == 'BOM') echo 'selected'; ?>>Chhatrapati Shivaji Maharaj International Airport (BOM), Mumbai</option>
            <option value="BLR" <?php if ($flight['from_location'] == 'BLR') echo 'selected'; ?>>Kempegowda International Airport (BLR), Bengaluru</option>
            <option value="HYD" <?php if ($flight['from_location'] == 'HYD') echo 'selected'; ?>>Rajiv Gandhi International Airport (HYD), Hyderabad</option>
            <option value="MAA" <?php if ($flight['from_location'] == 'MAA') echo 'selected'; ?>>Chennai International Airport (MAA), Chennai</option>
            <option value="CCU" <?php if ($flight['from_location'] == 'CCU') echo 'selected'; ?>>Netaji Subhas Chandra Bose International Airport (CCU), Kolkata</option>
            <option value="AMD" <?php if ($flight['from_location'] == 'AMD') echo 'selected'; ?>>Sardar Vallabhbhai Patel International Airport (AMD), Ahmedabad</option>
            <option value="COK" <?php if ($flight['from_location'] == 'COK') echo 'selected'; ?>>Cochin International Airport (COK), Kochi</option>
            <option value="PNQ" <?php if ($flight['from_location'] == 'PNQ') echo 'selected'; ?>>Pune Airport (PNQ), Pune</option>
            <option value="GOI" <?php if ($flight['from_location'] == 'GOI') echo 'selected'; ?>>Dabolim Airport (GOI), Goa</option>
        </select>

        <label>To Location:</label>
        <select name="to_location" required>
            <option value=""> -- Select Destination -- </option>
            <option value="DEL" <?php if ($flight['to_location'] == 'DEL') echo 'selected'; ?>>Indira Gandhi International Airport (DEL), New Delhi</option>
            <option value="BOM" <?php if ($flight['to_location'] == 'BOM') echo 'selected'; ?>>Chhatrapati Shivaji Maharaj International Airport (BOM), Mumbai</option>
            <option value="BLR" <?php if ($flight['to_location'] == 'BLR') echo 'selected'; ?>>Kempegowda International Airport (BLR), Bengaluru</option>
            <option value="HYD" <?php if ($flight['to_location'] == 'HYD') echo 'selected'; ?>>Rajiv Gandhi International Airport (HYD), Hyderabad</option>
            <option value="MAA" <?php if ($flight['to_location'] == 'MAA') echo 'selected'; ?>>Chennai International Airport (MAA), Chennai</option>
            <option value="CCU" <?php if ($flight['to_location'] == 'CCU') echo 'selected'; ?>>Netaji Subhas Chandra Bose International Airport (CCU), Kolkata</option>
            <option value="AMD" <?php if ($flight['to_location'] == 'AMD') echo 'selected'; ?>>Sardar Vallabhbhai Patel International Airport (AMD), Ahmedabad</option>
            <option value="COK" <?php if ($flight['to_location'] == 'COK') echo 'selected'; ?>>Cochin International Airport (COK), Kochi</option>
            <option value="PNQ" <?php if ($flight['to_location'] == 'PNQ') echo 'selected'; ?>>Pune Airport (PNQ), Pune</option>
            <option value="GOI" <?php if ($flight['to_location'] == 'GOI') echo 'selected'; ?>>Dabolim Airport (GOI), Goa</option>
        </select>

        <label>Departure Time:</label>
        <input type="datetime-local" name="departure" value="<?php echo date('Y-m-d\TH:i', strtotime($flight['departure'])); ?>" required>

        <label>Arrival Time:</label>
        <input type="datetime-local" name="arrival" value="<?php echo date('Y-m-d\TH:i', strtotime($flight['arrival'])); ?>" required>

        <label>Price:</label>
        <input type="number" name="price" step="0.01" value="<?php echo $flight['price']; ?>" required>

        <label>Seats:</label>
        <input type="number" name="seats" value="<?php echo $flight['seats']; ?>" required>

        <label>Status:</label>
        <select name="status" required>
            <option value="active" <?php if ($flight['status'] == 'active') echo 'selected'; ?>>Active</option>
            <option value="inactive" <?php if ($flight['status'] == 'inactive') echo 'selected'; ?>>Inactive</option>
        </select>

        <input type="submit" value="Update Flight">
    </form>
    
        <div align="center"><a href="manage_flight.php">Go Back</a></div>
        
</body>
</html>
