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
        $price = floatval($_POST['price']);
        $seats = intval($_POST['seats']);
        $status = $_POST['status'];
    
        // Extra validation
        if ($from_location === $to_location) {
            die("From and To locations cannot be the same.");
        }
    
        $sql = "INSERT INTO flights (airline_name, from_location, to_location, departure, arrival, price, seats, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
    
        if ($stmt) {
            $stmt->bind_param("sssssiis", $airline_name, $from_location, $to_location, $departure, $arrival, $price, $seats, $status);
    
            if ($stmt->execute()) {
                header("Location: manage_flight.php");
                exit();
            } else {
                echo "Failed to add new flight. Error: " . $stmt->error;
            }
        } else {
            echo "SQL preparation error: " . $conn->error;
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
        <select id="from_location" name="from_location">
        <option value=""> -- Select Source -- </option>
        <option value="DEL">Indira Gandhi International Airport (DEL), New Delhi</option>
        <option value="BOM">Chhatrapati Shivaji Maharaj International Airport (BOM), Mumbai</option>
        <option value="BLR">Kempegowda International Airport (BLR), Bengaluru</option>
        <option value="HYD">Rajiv Gandhi International Airport (HYD), Hyderabad</option>
        <option value="MAA">Chennai International Airport (MAA), Chennai</option>
        <option value="CCU">Netaji Subhas Chandra Bose International Airport (CCU), Kolkata</option>
        <option value="AMD">Sardar Vallabhbhai Patel International Airport (AMD), Ahmedabad</option>
        <option value="COK">Cochin International Airport (COK), Kochi</option>
        <option value="PNQ">Pune Airport (PNQ), Pune</option>
        <option value="GOI">Dabolim Airport (GOI), Goa</option>
        </select>

        <label>To Location:</label>
        <select id="to_location" name="to_location">
        <option value=""> -- Select Destination -- </option>
        <option value="DEL">Indira Gandhi International Airport (DEL), New Delhi</option>
        <option value="BOM">Chhatrapati Shivaji Maharaj International Airport (BOM), Mumbai</option>
        <option value="BLR">Kempegowda International Airport (BLR), Bengaluru</option>
        <option value="HYD">Rajiv Gandhi International Airport (HYD), Hyderabad</option>
        <option value="MAA">Chennai International Airport (MAA), Chennai</option>
        <option value="CCU">Netaji Subhas Chandra Bose International Airport (CCU), Kolkata</option>
        <option value="AMD">Sardar Vallabhbhai Patel International Airport (AMD), Ahmedabad</option>
        <option value="COK">Cochin International Airport (COK), Kochi</option>
        <option value="PNQ">Pune Airport (PNQ), Pune</option>
        <option value="GOI">Dabolim Airport (GOI), Goa</option>
        </select>

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

        <div align="center"><a href="manage_flight.php">Go Back</a></div>
</body>
</html>
