<?php
session_start();
$flight_id = isset($_GET['flight_id']) ? intval($_GET['flight_id']) : 0;
if ($flight_id <= 0) {
    die("Invalid Flight Selection");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Passenger Details</title>
    <style>
        /* Your existing styling */
    </style>
    <script>
        function addPassengerFields(count) {
            const container = document.getElementById('passenger-fields');
            container.innerHTML = '';
            for (let i = 1; i <= count; i++) {
                container.innerHTML += `
                    <fieldset>
                        <legend>Passenger ${i}</legend>
                        <label>Full Name:</label>
                        <input type="text" name="passengers[${i}][name]" required>

                        <label>Gender:</label>
                        <select name="passengers[${i}][gender]" required>
                            <option value="">-- Select --</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>

                        <label>Age:</label>
                        <input type="number" name="passengers[${i}][age]" min="1" required>

                        <label>Seat Number:</label>
                        <select name="passengers[${i}][seat_no]" required>
                            <option value="">-- Select Seat --</option>
                            <option>Standard Window Seat</option>
                            <option>Exit Row Window Seat</option>
                            <option>Bulkhead Window Seat</option>
                            <option>Window Seat Over the Wing</option>
                            <option>Rearmost Window Seat</option>
                        </select>
                    </fieldset>
                `;
            }
        }
    </script>
</head>
<body>

<div class="container">
    <h2>Passenger Details</h2>
    <form method="POST" action="payment.php">
        <input type="hidden" name="flight_id" value="<?= $flight_id ?>">
        
        <label>No. of Passengers:</label>
        <select name="num_passengers" onchange="addPassengerFields(this.value)" required>
            <option value="">-- Select --</option>
            <?php for ($i = 1; $i <= 6; $i++): ?>
                <option value="<?= $i ?>"><?= $i ?></option>
            <?php endfor; ?>
        </select>

        <div id="passenger-fields"></div>

        <label>Seat Class:</label>
        <select name="class_type" required>
            <option value="">-- Select --</option>
            <option value="economy">Economy</option>
            <option value="business">Business</option>
            <option value="first">First</option>
        </select>

        <button type="submit">Confirm Booking</button>
    </form>
</div>

</body>
</html>
