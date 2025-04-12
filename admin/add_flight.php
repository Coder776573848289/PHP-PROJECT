<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
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

    // Calculate duration in minutes
    $departure_dt = new DateTime($departure);
    $arrival_dt = new DateTime($arrival);
    $duration = $departure_dt->diff($arrival_dt)->h * 60 + $departure_dt->diff($arrival_dt)->i;

    // Status is now taken from the form submission
    $status = $_POST['status'];

    // Validate airport
    if ($from_location === $to_location) {
        die("From and To locations cannot be the same.");
    }

    $insert_sql = "INSERT INTO flights2
    (airline_name, from_location, to_location, departure, arrival, duration, status,
     economy_seats, economy_price, business_seats, business_price, first_class_seats, first_class_price)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($insert_sql);
    if ($stmt) {
        $stmt->bind_param("sssssisidiidi",
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
            $first_class_price
        );

        if ($stmt->execute()) {
            header("Location: manage_flight.php");
            exit();
        } else {
            echo "Insert Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Prepare Failed: " . $conn->error;
    }
    $conn->close();
}

?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Add New Flight</title>
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
            background-color: #28a745;
            color: white;
            border: none;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #218838;
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

        const durationInput = document.querySelector('input[name="duration"]');
        if (durationInput) {
        const durationValue = parseInt(durationInput.value);
        if (isNaN(durationValue) || durationValue <= 0 || !Number.isInteger(durationValue)) {
        alert("Duration must be a positive whole number.");
        return false;
        }
}
}

        return true;
    }
    </script>
</head>
<body>

    <form method="POST" onsubmit="return validateForm();">
    <h2>Add New Flight</h2>

<label>Airline Name:</label>
<input type="text" name="airline_name" required>

<label>From Location:</label>
<select name="from_location" required>
    <option value="">-- Select Source --</option>
    <option value="DEL">Delhi</option>
    <option value="BOM">Mumbai</option>
    <option value="BLR">Bangalore</option>
    <option value="HYD">Hyderabad</option>
    <option value="MAA">Chennai</option>
    <option value="CCU">Kolkata</option>
    </select>

<label>To Location:</label>
<select name="to_location" required>
    <option value="">-- Select Destination --</option>
        <option value="DEL">Delhi</option>
        <option value="BOM">Mumbai</option>
        <option value="BLR">Bangalore</option>
        <option value="HYD">Hyderabad</option>
        <option value="MAA">Chennai</option>
        <option value="CCU">Kolkata</option>
    </select>

<label>Departure Time:</label>
<input type="datetime-local" id="departure" name="departure" required>

<label>Arrival Time:</label>
<input type="datetime-local" id="arrival" name="arrival" required>

<label>Economy Seats:</label>
<input type="number" id="economy_seats" name="economy_seats" required>

<label>Business Seats:</label>
<input type="number" id="business_seats" name="business_seats" required>

<label>First Class Seats:</label>
<input type="number" id="first_class_seats" name="first_class_seats" required>

<label>Economy Price (₹):</label>
<input type="number" id="economy_price" step="0.01" name="economy_price" required>

<label>Business Price (₹):</label>
<input type="number" id="business_price" step="0.01" name="business_price" required>

<label>First Class Price (₹):</label>
<input type="number" id="first_class_price" step="0.01" name="first_class_price" required>

<!-- <label>Duration (in minutes):</label>
<input type="number" name="duration" required> -->

<label>Status:</label>
<select name="status" required>
    <option value="On Time">On Time</option>
    <option value="Delayed">Delayed</option>
    <option value="Cancelled">Cancelled</option>
</select>

<input type="submit" value="Add Flight">


        <div align="center"><a href="manage_flight.php">Go Back</a></div>
</body>
</html>