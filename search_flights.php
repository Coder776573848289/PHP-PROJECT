<?php
include 'includes/header.php';
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Search Flights</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f2f2f2;
            padding: 20px;
        }

        .search-container {
            max-width: 800px;
            margin: auto;
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }

        input[type="text"], input[type="date"], input[type="number"],
        select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .form-row {
            display: flex;
            gap: 20px;
        }

        .form-row > div {
            flex: 1;
        }

        input[type="submit"] {
            background-color: #007bff;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 5px;
            margin-top: 25px;
            cursor: pointer;
            width: 100%;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }

        .trip-type {
            display: flex;
            gap: 20px;
            margin-top: 10px;
        }
    </style>

    <script>
        function toggleReturnDate() {
            const roundTrip = document.querySelector('input[value="round"]').checked;
            const returnDateInput = document.querySelector('input[name="return_date"]');
            returnDateInput.disabled = !roundTrip;
        }

        function validateForm() {
            const from = document.forms[0]["from_location"].value;
            const to = document.forms[0]["to_location"].value;
            const departure = document.forms[0]["departure"].value;
            const returnDate = document.forms[0]["return_date"].value;
            const tripType = document.querySelector('input[name="trip_type"]:checked').value;

            if (from === to) {
                alert("From and To locations must be different.");
                return false;
            }

            if (!departure) {
                alert("Please select a departure date.");
                return false;
            }

            if (tripType === "round") {
                if (!returnDate) {
                    alert("Please select a return date.");
                    return false;
                }

                if (returnDate < departure) {
                    alert("Return date must be after departure date.");
                    return false;
                }
            }

            return true;
        }

        window.onload = function () {
            toggleReturnDate();
            document.querySelectorAll('input[name="trip_type"]').forEach(input => {
                input.addEventListener("change", toggleReturnDate);
            });
        };
    </script>
</head>
<body>

<div class="search-container">
    <h2>Search Flights</h2>
    <form method="POST" action="flight_results.php" onsubmit="return validateForm()">

        <label>Trip Type:</label>
        <div class="trip-type">
            <label><input type="radio" name="trip_type" value="oneway" checked> One Way</label>
            <label><input type="radio" name="trip_type" value="round"> Round Trip</label>
        </div>

        <div class="form-row">
            <div>
                <label>From:</label>
                <select name="from_location" required>
                    <option value="">-- Select --</option>
                    <option value="DEL">Delhi</option>
                    <option value="BOM">Mumbai</option>
                    <option value="BLR">Bangalore</option>
                    <option value="HYD">Hyderabad</option>
                    <option value="MAA">Chennai</option>
                    <option value="CCU">Kolkata</option>
                </select>
            </div>

            <div>
                <label>To:</label>
                <select name="to_location" required>
                    <option value="">-- Select --</option>
                    <option value="DEL">Delhi</option>
                    <option value="BOM">Mumbai</option>
                    <option value="BLR">Bangalore</option>
                    <option value="HYD">Hyderabad</option>
                    <option value="MAA">Chennai</option>
                    <option value="CCU">Kolkata</option>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div>
                <label>Departure Date:</label>
                <input type="date" name="departure" required>
            </div>
            <div>
                <label>Return Date:</label>
                <input type="date" name="return_date" disabled>
            </div>
        </div>

        <label>Seat Class:</label>
        <select name="seat_class">
            <option value="">Any</option>
            <option value="economy">Economy</option>
            <option value="business">Business</option>
            <option value="first">First Class</option>
        </select>

        <div class="form-row">
            <div>
                <label>Min Price (₹):</label>
                <input type="number" name="min_price" min="0">
            </div>
            <div>
                <label>Max Price (₹):</label>
                <input type="number" name="max_price" min="0">
            </div>
        </div>

        <input type="submit" value="Search Flights">
    </form>
</div>

</body>
</html>
