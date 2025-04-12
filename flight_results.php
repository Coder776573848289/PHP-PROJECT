<?php
// flight_results.php
require_once 'includes/db.php';

// Read form data using POST
$tripType = $_POST['trip_type'] ?? 'oneway';
$from = $_POST['from_location'] ?? '';
$to = $_POST['to_location'] ?? '';
$departure = $_POST['departure'] ?? '';
$return = $_POST['return_date'] ?? '';
$seatClass = $_POST['seat_class'] ?? '';
$minPrice = $_POST['min_price'] ?? '0';
$maxPrice = $_POST['max_price'] ?? '999999';

// Remove commas from price inputs (important)
$minPrice = floatval(str_replace(',', '', $minPrice));
$maxPrice = floatval(str_replace(',', '', $maxPrice));

// Convert dates
$departureDate = !empty($departure) ? date('Y-m-d', strtotime($departure)) : '';
$returnDate = !empty($return) ? date('Y-m-d', strtotime($return)) : '';

// Start SQL and params
$sql = "SELECT * FROM flights2 WHERE from_location = ? AND to_location = ?";
$paramTypes = 'ss';
$params = [$from, $to];

// Add filters
if ($tripType === 'oneway' && $departureDate) {
    $sql .= " AND DATE(departure) = ?";
    $paramTypes .= 's';
    $params[] = $departureDate;
} elseif ($tripType === 'round' && $departureDate && $returnDate) {
    $sql .= " AND DATE(departure) = ? AND DATE(arrival) <= ?";
    $paramTypes .= 'ss';
    $params[] = $departureDate;
    $params[] = $returnDate;
}

// Price filter
if ($seatClass === 'economy') {
    $sql .= " AND economy_price BETWEEN ? AND ?";
    $paramTypes .= 'dd';
    $params[] = $minPrice;
    $params[] = $maxPrice;
} elseif ($seatClass === 'business') {
    $sql .= " AND business_price BETWEEN ? AND ?";
    $paramTypes .= 'dd';
    $params[] = $minPrice;
    $params[] = $maxPrice;
} elseif ($seatClass === 'first') {
    $sql .= " AND first_class_price BETWEEN ? AND ?";
    $paramTypes .= 'dd';
    $params[] = $minPrice;
    $params[] = $maxPrice;
} else {
    $sql .= " AND (
        economy_price BETWEEN ? AND ? OR
        business_price BETWEEN ? AND ? OR
        first_class_price BETWEEN ? AND ?
    )";
    $paramTypes .= 'dddddd';
    $params = array_merge($params, [$minPrice, $maxPrice, $minPrice, $maxPrice, $minPrice, $maxPrice]);
}

// Prepare and execute
$stmt = $conn->prepare($sql);
$stmt->bind_param($paramTypes, ...$params);
$stmt->execute();
$result = $stmt->get_result();
$matchedFlights = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Flight Search Results</title>
    <style>
      .container {
        width: 80%;
        margin: 20px auto;
        padding: 20px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-family: Arial, sans-serif;
      }

      .container h2 {
        text-align: center;
        margin-bottom: 20px;
      }

      .flight-card {
        border: 1px solid #ddd;
        padding: 15px;
        margin-bottom: 15px;
        border-radius: 5px;
        background-color: #f9f9f9;
      }

      .flight-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
      }

      .flight-info {
        width: 30%;
      }

      .flight-info label {
        font-weight: bold;
        display: block;
        margin-bottom: 5px;
      }

      .book-btn {
        display: block;
        width: 150px;
        margin: 10px auto 0 auto;
        padding: 10px;
        background-color: #007BFF;
        color: white;
        text-align: center;
        border-radius: 5px;
        text-decoration: none;
        font-size: 16px;
      }

      .book-btn:hover {
        background-color: #0056b3;
      }

      .no-results {
        text-align: center;
        padding: 10px;
        border: 1px solid #ddd;
        margin-top: 20px;
        border-radius: 5px;
      }
      .back-link {
        display: block;
        text-align: center;
        margin-top: 20px;
        color: #007BFF;
        text-decoration: none;
      }
      .back-link:hover {
        text-decoration: underline;
      }
    </style>
</head>
<body>
    <div class="container">
        <h2>Matched Flights</h2>
        <?php if (!empty($matchedFlights)): ?>
            <?php foreach ($matchedFlights as $flight): ?>
                <div class="flight-card">
                    <div class="flight-row">
                        <div class="flight-info">
                            <label>Airline:</label>
                            <p><?= htmlspecialchars($flight['airline_name']) ?></p>
                            <label>From - To:</label>
                            <p><?= $flight['from_location'] ?> → <?= $flight['to_location'] ?></p>
                            <label>Status:</label>
                            <p><?= $flight['status'] ?></p>
                        </div>
                        <div class="flight-info">
                            <label>Departure:</label>
                            <p><?= date("d M Y, H:i", strtotime($flight['departure'])) ?></p>
                            <label>Arrival:</label>
                            <p><?= date("d M Y, H:i", strtotime($flight['arrival'])) ?></p>
                            <label>Duration:</label>
                            <p><?= $flight['duration'] ?> mins</p>
                        </div>
                        <div class="flight-info">
                            <label>Economy:</label>
                            <p><?= $flight['economy_seats'] ?> seats | ₹<?= number_format($flight['economy_price']) ?></p>
                            <label>Business:</label>
                            <p><?= $flight['business_seats'] ?> seats | ₹<?= number_format($flight['business_price']) ?></p>
                            <label>First Class:</label>
                            <p><?= $flight['first_class_seats'] ?> seats | ₹<?= number_format($flight['first_class_price']) ?></p>
                        </div>
                    </div>
                    <a class="book-btn" href="passenger_details.php?flight_id=<?= $flight['id'] ?>">Book Now</a>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-results">No flights match your search. Please try again.</div>
        <?php endif; ?>
        <a class="back-link" href="search_flights.php">← Back to Search</a>
    </div>
</body>
</html>
