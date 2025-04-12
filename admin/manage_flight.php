<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Fetch all flight details
$sql = "SELECT * FROM flights2 ORDER BY departure ASC";
$result = $conn->query($sql);
$flights = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $flights[] = $row;
    }
}

// Handle delete functionality
if (isset($_GET['delete_id']) && is_numeric($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $delete_sql = "DELETE FROM flights2 WHERE id = ?";
    $stmt = $conn->prepare($delete_sql);
    if ($stmt) {
        $stmt->bind_param("i", $delete_id);
        if ($stmt->execute()) {
            header("Location: manage_flight.php?deleted=true");
            exit();
        } else {
            $delete_error = "Delete Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $delete_error = "Prepare Failed: " . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Manage Flights</title>
    <style>
        body {
            font-family: sans-serif;
            margin: 20px;
        }
        h2 {
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .actions a {
            margin-right: 10px;
            text-decoration: none;
        }
        .edit-btn {
            background-color: #007bff;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
        }
        .delete-btn {
            background-color: #dc3545;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
        }
        .success-message {
            color: green;
            margin-top: 10px;
            text-align: center;
        }
        .error-message {
            color: red;
            margin-top: 10px;
            text-align: center;
        }
        .add-new {
            display: block;
            margin-top: 20px;
            text-align: center;
        }
        .add-new a {
            text-decoration: none;
            background-color: #28a745;
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <h2>Manage Flights</h2>

    <?php if (isset($_GET['updated']) && $_GET['updated'] == 'true'): ?>
        <p class="success-message">Flight details updated successfully!</p>
    <?php endif; ?>

    <?php if (isset($_GET['deleted']) && $_GET['deleted'] == 'true'): ?>
        <p class="success-message">Flight deleted successfully!</p>
    <?php endif; ?>

    <?php if (isset($delete_error)): ?>
        <p class="error-message"><?php echo $delete_error; ?></p>
    <?php endif; ?>

    <?php if (!empty($flights)): ?>
        <table>
            <thead>
                <tr>
                    <th>Flight ID</th>
                    <th>Airline</th>
                    <th>From</th>
                    <th>To</th>
                    <th>Departure</th>
                    <th>Arrival</th>
                    <th>Duration (min)</th>
                    <th>Status</th>
                    <th>Economy Seats</th>
                    <th>Economy Price (₹)</th>
                    <th>Business Seats</th>
                    <th>Business Price (₹)</th>
                    <th>First Class Seats</th>
                    <th>First Class Price (₹)</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($flights as $flight): ?>
                    <tr>
                        <td><?php echo $flight['id']; ?></td>
                        <td><?php echo htmlspecialchars($flight['airline_name']); ?></td>
                        <td><?php echo htmlspecialchars($flight['from_location']); ?></td>
                        <td><?php echo htmlspecialchars($flight['to_location']); ?></td>
                        <td><?php echo $flight['departure']; ?></td>
                        <td><?php echo $flight['arrival']; ?></td>
                        <td><?php echo $flight['duration']; ?></td>
                        <td><?php echo htmlspecialchars($flight['status']); ?></td>
                        <td><?php echo $flight['economy_seats']; ?></td>
                        <td><?php echo number_format($flight['economy_price'], 2); ?></td>
                        <td><?php echo $flight['business_seats']; ?></td>
                        <td><?php echo number_format($flight['business_price'], 2); ?></td>
                        <td><?php echo $flight['first_class_seats']; ?></td>
                        <td><?php echo number_format($flight['first_class_price'], 2); ?></td>
                        <td><a href="edit_flight.php?id=<?php echo $flight['id']; ?>">Edit</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No flights available.</p>
    <?php endif; ?>

    <div class="add-new">
        <a href="add_flight.php">Add New Flight</a>
    </div>
    <div class="back-link">
    <a href="dashbord.php">← Back to Dashboard</a>
    </div>
</body>
</html>