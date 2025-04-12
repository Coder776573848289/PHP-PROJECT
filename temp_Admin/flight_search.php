
<?php
session_start();
include 'includes/header.php';
require_once 'includes/db.php'; // adjust path as needed

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Search Flights</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            margin: 0;
            padding: 20px;<?php
session_start();
include 'includes/header.php';
require_once 'includes/db.php';
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Search Flights</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
        }

        form {
            max-width: 700px;
            margin: 0 auto;
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }

        input, select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        input[type="submit"] {
            background-color: #007bff;
            color: white;
            border: none;
            margin-top: 20px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }

        .form-row {
            display: flex;
            gap: 20px;
        }

        .form-row > div {
            flex: 1;
        }

        @media (max-width: 600px) {
            .form-row {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>

<h2>Search Flights</h2>
<form method="POST">
    <div class="form-row">
        <div>
            <label>From:</label>
            <select id="source" name="source">
                <option value="">-- Select Source ---</option>
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
        </div>
        <div>
            <label>To:</label>
            <select id="destination" name="destination">
                <option value="">-- Select Destination ---</option>
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
        </div>
    </div>

    <div class="form-row">
        <div>
            <label>Departure:</label>
            <input type="datetime-local" name="departure">
        </div>
        <div>
            <label>Arrival:</label>
            <input type="datetime-local" name="arrival">
        </div>
    </div>

    <div class="form-row">
        <div>
            <label>Min Price:</label>
            <input type="number" name="min_price" min="0">
        </div>
        <div>
            <label>Max Price:</label>
            <input type="number" name="max_price" min="0">
        </div>
    </div>

    <div class="form-row">
        <div>
            <label>Class:</label>
            <select name="class">
                <option value="">Any</option>
                <option value="economy">Economy</option>
                <option value="business">Business</option>
                <option value="first">First Class</option>
            </select>
        </div>
        <div>
            <label>Flight Duration (hours):</label>
            <input type="number" name="min_duration" min="0">
        </div>
    </div>

    <input type="submit" name="search" value="Search Flights">
</form>
</body>
</html>

<?php
if (isset($_POST['search'])) {
    $source = mysqli_real_escape_string($conn, $_POST['source']);
    $destination = mysqli_real_escape_string($conn, $_POST['destination']);
    $departure = mysqli_real_escape_string($conn, $_POST['departure']);
    $arrival = mysqli_real_escape_string($conn, $_POST['arrival']);
    $min_price = $_POST['min_price'];
    $max_price = $_POST['max_price'];
    $class = mysqli_real_escape_string($conn, $_POST['class']);
    $min_duration = $_POST['min_duration'];

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

    if (!$result) {
        die("Query failed: " . mysqli_error($conn));
    }

    if (mysqli_num_rows($result) > 0) {
        echo "<h3>Available Flights</h3>";
        echo "<table border='1' cellpadding='10' cellspacing='0'>
                <tr>
                    <th>Flight</th>
                    <th>From</th>
                    <th>To</th>
                    <th>Departure</th>
                    <th>Arrival</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>";

        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>
                    <td>" . ($row['flight_name'] ?? 'N/A') . "</td>
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
        echo "<p style='text-align:center; color:red;'>❌ No flights found for selected criteria.</p>";
    }
}
?>

<?php include 'includes/footer.php'; ?>

        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
        }

        form {
            max-width: 700px;
            margin: 0 auto;
            background: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }

        input, select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        input[type="submit"] {
            background-color: #007bff;
            color: white;
            border: none;
            margin-top: 20px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }

        .form-row {
            display: flex;
            gap: 20px;
        }

        .form-row > div {
            flex: 1;
        }

        @media (max-width: 600px) {
            .form-row {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>


<h2>Search Flights</h2>
<form method="POST">
<div class="form-row">
<div>
    <label>From:</label>
    <select id="source" name="source">
                <option value="">-- Select Source ---</option>
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
        </div>
        <div>
    <label>To:</label>
    <select id="destination" name="destination">
                <option value="">-- Select Destination ---</option>
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
        </div>
    </div>

    <div class="form-row">
        <div>
        <label>Departure:</label>
        <input type="datetime-local" name="departure"><br>
        </div>
        <div>
        <label>Arrival :</label>
        <input type="datetime-local" name="arrival"><br>
        </div>
    </div>
    <class="form-row">
        <div>
    <label>Min Price:</label>
    <input type="number" name="min_price" min="0"><br>
    </div>
    </div>
    <label>Max Price:</label>
    <input type="number" name="max_price" min="0"><br>
    </div>
    </div>

    <class="form-row">
        <div>
    <label>Class:</label>
    <select name="class">
        <option value="">Any</option>
        <option value="economy">Economy</option>
        <option value="business">Business</option>
        <option value="first">First Class</option>
    </select><br>
    </div>
</div>
    <label>Flight Duration (hours):</label>
    <input type="number" name="min_duration" min="0"><br>
    </div>
    </div>
    <input type="submit" name="search" value="Search Flights">
</form>
</BODY>
</html>

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