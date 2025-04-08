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
    $price = $_POST['price'];
    $seats = $_POST['seats'];
    $status = $_POST['status'];

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
    $stmt->bind_param("sssssdiss", $airline_name, $from_location, $to_location, $departure, $arrival, $price, $seats, $status, $flight_id);
    
    if ($stmt->execute()) {
        header("Location: manage_flight.php");
        exit();
    } else {
        echo "Failed to update flight.";
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
        <input type="text" name="from_location" value="<?php echo htmlspecialchars($flight['from_location']); ?>" required>

        <label>To Location:</label>
        <input type="text" name="to_location" value="<?php echo htmlspecialchars($flight['to_location']); ?>" required>

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
