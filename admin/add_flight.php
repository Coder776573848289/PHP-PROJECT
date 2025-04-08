<?php
session_start();
require_once '../includes/db.php'; // adjust path as needed

// Check admin login
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

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

    $sql = "INSERT INTO flights (airline_name, from_location, to_location, departure, arrival, price, seats, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssdiss", $airline_name, $from_location, $to_location, $departure, $arrival, $price, $seats, $status);

    if ($stmt->execute()) {
        header("Location: manage_flight.php");
        exit();
    } else {
        echo "Failed to add new flight.";
    }
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
        const price = parseFloat(document.getElementById('price').value);
        const seats = parseInt(document.getElementById('seats').value);

        if (departure <= now) {
            alert("Departure time must be in the future.");
            return false;
        }

        if (arrival <= departure) {
            alert("Arrival time must be after departure.");
            return false;
        }

        if (isNaN(price) || price < 1) {
            alert("Price must be at least ₹1.");
            return false;
        }

        if (isNaN(seats) || seats < 1 || !Number.isInteger(seats)) {
            alert("Seats must be a positive whole number.");
            return false;
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
        <input type="text" name="from_location" required>

        <label>To Location:</label>
        <input type="text" name="to_location" required>

        <label>Departure Time:</label>
        <input type="datetime-local" name="departure" id="departure" required>

        <label>Arrival Time:</label>
        <input type="datetime-local" name="arrival" id="arrival" required>

        <label>Price:</label>
        <input type="number" name="price" id="price" step="0.01" required>

        <label>Seats:</label>
        <input type="number" name="seats" id="seats" required>

        <label>Status:</label>
        <select name="status" required>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
        </select>

        <input type="submit" value="Add Flight">
    </form>

</body>
</html>
