<?php
// Example: get the flight_id from URL
$flight_id = isset($_GET['flight_id']) ? intval($_GET['flight_id']) : 0;

if ($flight_id <= 0) {
    die("Invalid Flight Selection");
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Passenger Details</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }

        h2 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: 500;
            color: #34495e;
        }

        input, select {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
        }

        .submit-btn {
            width: 100%;
            background-color: #3498db;
            color: white;
            padding: 12px;
            font-size: 16px;
            border: none;
            border-radius: 6px;
            margin-top: 25px;
            cursor: pointer;
        }

        .submit-btn:hover {
            background-color: #2980b9;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #3498db;
            text-decoration: none;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Enter Passenger Details</h2>

        <form action="confirm_booking.php" method="POST">
            <input type="hidden" name="flight_id" value="<?= $flight_id ?>">

            <label for="name">Full Name:</label>
            <input type="text" name="name" id="name" required>

            <label for="gender">Gender:</label>
            <select name="gender" id="gender" required>
                <option value="">-- Select Gender --</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
            </select>

            <label for="age">Age:</label>
            <input type="number" name="age" id="age" min="1" required>

            <label for="seat_no">Preferred Seat No (e.g., A12):</label>
            <input type="text" name="seat_no" id="seat_no" required>

            <button class="submit-btn" type="submit">Proceed to Payment</button>
        </form>

        <a href="flight_results.php" class="back-link">← Back to Flight Results</a>
    </div>

</body>
</html>
