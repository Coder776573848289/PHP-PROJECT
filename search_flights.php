<?php
// search_flights.php
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Search Flights</title>
    <style>
        .search-container {
            width: 80%;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-family: Arial, sans-serif;
        }
        .search-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .form-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }
        .form-row div {
            width: 48%;
        }
        .form-row label {
            display: block;
            margin-bottom: 5px;
        }
        .form-row input, .form-row select {
            width: 100%;
            padding: 8px 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .trip-type {
            margin-bottom: 15px;
        }
        .trip-type label {
            margin-right: 10px;
        }
        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            display: block;
            margin: 0 auto;
            width: 200px;
            font-size: 16px;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        .error-message {
            color: red;
            margin-top: 10px;
            text-align: center;
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