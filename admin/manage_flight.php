<?php
session_start();
require_once '../includes/db.php'; // Adjust path as needed

// Check if admin is logged in
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Fetch flight data
$sql = "SELECT * FROM flights";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Manage Flights</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        .top-bar {
            margin-bottom: 20px;
        }
        .top-bar a {
            text-decoration: none;
            background-color: #28a745;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th {
            background-color: #f4f4f4;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        .edit-btn {
            background-color: #007bff;
            color: white;
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 4px;
        }
        .edit-btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

    <div class="top-bar">
        <a href="add_flight.php">+ Add New Flight</a>
    </div>

    <h2>Manage Flights</h2>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Airline Name</th>
                <th>From</th>
                <th>To</th>
                <th>Departure</th>
                <th>Arrival</th>
                <th>Price</th>
                <th>Seats</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['airline_name']; ?></td>
                        <td><?php echo $row['from_location']; ?></td>
                        <td><?php echo $row['to_location']; ?></td>
                        <td><?php echo $row['departure']; ?></td>
                        <td><?php echo $row['arrival']; ?></td>
                        <td><?php echo $row['price']; ?></td>
                        <td><?php echo $row['seats']; ?></td>
                        <td><?php echo ucfirst($row['status']); ?></td>
                        <td>
                            <a class="edit-btn" href="edit_flight.php?id=<?php echo $row['id']; ?>">Edit</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="10">No flights available.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <a href="dashbord.php">← Back to Dashboard</a>

</body>
</html>
