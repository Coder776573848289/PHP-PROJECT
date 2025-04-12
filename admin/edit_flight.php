<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: manage_flight.php");
    exit();
}

$id = $_GET['id']; // Changed from $flight_id

// Fetch flight details for editing
$sql = "SELECT * FROM flights2 WHERE id = ?"; // Changed WHERE clause
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id); // Changed variable
$stmt->execute();
$result = $stmt->get_result();
$flight = $result->fetch_assoc();
$stmt->close();

if (!$flight) {
    header("Location: manage_flight.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $airline_name = trim($_POST['airline_name']);
    $from_location = $_POST['from_location'];
    $to_location = $_POST['to_location'];
    $departure = $_POST['departure'];
    $arrival = $_POST['arrival'];
    $economy_seats = intval($_POST['economy_seats']);
    $business_seats = intval($_POST['business_seats']);
    $first_class_seats = isset($_POST['first_class_seats']) ? intval($_POST['first_class_seats']) : 0;
    $economy_price = floatval($_POST['economy_price']);
    $business_price = floatval($_POST['business_price']);
    $first_class_price = isset($_POST['first_class_price']) ? floatval($_POST['first_class_price']) : 0.00;
    $duration = intval($_POST['duration']);
    $status = $_POST['status'];

    // Validate airport
    if ($from_location === $to_location) {
        $error = "From and To locations cannot be the same.";
    }

    if (!isset($error)) {
        $update_sql = "UPDATE flights2 SET
            airline_name = ?,
            from_location = ?,
            to_location = ?,
            departure = ?,
            arrival = ?,
            duration = ?,
            status = ?,
            economy_seats = ?,
            economy_price = ?,
            business_seats = ?,
            business_price = ?,
            first_class_seats = ?,
            first_class_price = ?
            WHERE id = ?"; // Changed WHERE clause

        $stmt = $conn->prepare($update_sql);
        if ($stmt) {
            $stmt->bind_param("sssssisidiidii",
                $airline_name,
                $from_location,
                $to_location,
                $departure,
                $arrival,
                $duration,
                $status,
                $economy_seats,
                $economy_price,
                $business_seats,
                $business_price,
                $first_class_seats,
                $first_class_price,
                $id // Changed from $flight_id
            );

            if ($stmt->execute()) {
                header("Location: manage_flight.php?updated=true");
                exit();
            } else {
                $error = "Update Error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error = "Prepare Failed: " . $conn->error;
        }
    }
}

$conn->close();
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
        .error {
            color: red;
            margin-top: 10px;
        }
    </style>
    <script>
        function validateForm() {
            const departure = new Date(document.getElementById('departure').value);
            const arrival = new Date(document.getElementById('arrival').value);
            const now = new Date();
            const economy_price = parseFloat(document.getElementById('economy_price').value);
            const economy_seats = parseInt(document.getElementById('economy_seats').value);
            const business_price = parseFloat(document.getElementById('business_price').value);
            const business_seats = parseInt(document.getElementById('business_seats').value);
            const first_class_price = parseFloat(document.getElementById('first_class_price').value);
            const first_class_seats = parseInt(document.getElementById('first_class_seats').value);
            const durationInput = document.querySelector('input[name="duration"]');

            if (departure <= now) {
                alert("Departure time must be in the future.");
                return false;
            }

            if (arrival <= departure) {
                alert("Arrival time must be after departure.");
                return false;
            }

            if (isNaN(economy_price) || economy_price < 0) {
                alert("Economy price must be a non-negative number.");
                return false;
            }

            if (isNaN(economy_seats) || economy_seats < 0 || !Number.isInteger(economy_seats)) {
                alert("Economy seats must be a non-negative whole number.");
                return false;
            }

            if (isNaN(business_price) || business_price < 0) {
                alert("Business price must be a non-negative number.");
                return false;
            }

            if (isNaN(business_seats) || business_seats < 0 || !Number.isInteger(business_seats)) {
                alert("Business seats must be a non-negative whole number.");
                return false;
            }

            if (isNaN(first_class_price) || first_class_price < 0) {
                alert("First Class price must be a non-negative number.");
                return false;
            }

            if (isNaN(first_class_seats) || first_class_seats < 0 || !Number.isInteger(first_class_seats)) {
                alert("First Class seats must be a non-negative whole number.");
                return false;
            }

            if (durationInput) {
                const durationValue = parseInt(durationInput.value);
                if (isNaN(durationValue) || durationValue <= 0 || !Number.isInteger(durationValue)) {
                    alert("Duration must be a positive whole number.");
                    return false;
                }
            }

            return true;
        }
    </script>
</head>
<body>

    <form method="POST" onsubmit="return validateForm();">
        <h2>Edit Flight</h2>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>

        <input type="hidden" name="flight_id" value="<?php echo $flight['id']; ?>"> <label>Airline Name:</label>
        <input type="text" name="airline_name" value="<?php echo htmlspecialchars($flight['airline_name']); ?>" required>

        <label>From Location:</label>
        <select name="from_location" required>
            <option value="">-- Select Source --</option>
            <option value="DEL" <?php if ($flight['from_location'] === 'DEL') echo 'selected'; ?>>Delhi</option>
            <option value="BOM" <?php if ($flight['from_location'] === 'BOM') echo 'selected'; ?>>Mumbai</option>
            </select>

        <label>To Location:</label>
        <select name="to_location" required>
            <option value="">-- Select Destination --</option>
            <option value="DEL" <?php if ($flight['to_location'] === 'DEL') echo 'selected'; ?>>Delhi</option>
            <option value="BOM" <?php if ($flight['to_location'] === 'BOM') echo 'selected'; ?>>Mumbai</option>
            </select>

        <label>Departure Time:</label>
        <input type="datetime-local" id="departure" name="departure" value="<?php echo str_replace(' ', 'T', $flight['departure']); ?>" required>

        <label>Arrival Time:</label>
        <input type="datetime-local" id="arrival" name="arrival" value="<?php echo str_replace(' ', 'T', $flight['arrival']); ?>" required>

        <label>Economy Seats:</label>
        <input type="number" id="economy_seats" name="economy_seats" value="<?php echo $flight['economy_seats']; ?>" required>

        <label>Business Seats:</label>
        <input type="number" id="business_seats" name="business_seats" value="<?php echo $flight['business_seats']; ?>" required>

        <label>First Class Seats:</label>
        <input type="number" id="first_class_seats" name="first_class_seats" value="<?php echo $flight['first_class_seats']; ?>" required>

        <label>Economy Price (₹):</label>
        <input type="number" id="economy_price" step="0.01" name="economy_price" value="<?php echo $flight['economy_price']; ?>" required>

        <label>Business Price (₹):</label>
        <input type="number" id="business_price" step="0.01" name="business_price" value="<?php echo $flight['business_price']; ?>" required>

        <label>First Class Price (₹):</label>
        <input type="number" id="first_class_price" step="0.01" name="first_class_price" value="<?php echo $flight['first_class_price']; ?>" required>
<!-- 
        <label>Duration (in minutes):</label>
        <input type="number" name="duration" value="<?php echo $flight['duration']; ?>" required> -->

        <label>Status:</label>
        <select name="status" required>
            <option value="On Time" <?php if ($flight['status'] === 'On Time') echo 'selected'; ?>>On Time</option>
            <option value="Delayed" <?php if ($flight['status'] === 'Delayed') echo 'selected'; ?>>Delayed</option>
            <option value="Cancelled" <?php if ($flight['status'] === 'Cancelled') echo 'selected'; ?>>Cancelled</option>
        </select>

        <input type="submit" value="Update Flight">
    </form>

    <div align="center"><a href="manage_flight.php">Go Back</a></div>
</body>
</html>