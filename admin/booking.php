<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Get filter values
$user_search = $_GET['user_id'] ?? '';
$flight_search = $_GET['flight_id'] ?? '';
$name_search = $_GET['passenger_name'] ?? '';
$gender = $_GET['gender'] ?? '';
$payment_status = $_GET['payment_status'] ?? '';
$min_age = $_GET['min_age'] ?? '';
$max_age = $_GET['max_age'] ?? '';
$booking_date = $_GET['booking_date'] ?? '';

// Build SQL with filters
$sql = "SELECT * FROM bookings WHERE 1=1";

if (!empty($user_search)) {
    $user_search = $conn->real_escape_string($user_search);
    $sql .= " AND user_id LIKE '%$user_search%'";
}

if (!empty($flight_search)) {
    $flight_search = $conn->real_escape_string($flight_search);
    $sql .= " AND flight_id LIKE '%$flight_search%'";
}

if (!empty($name_search)) {
    $name_search = $conn->real_escape_string($name_search);
    $sql .= " AND passenger_name LIKE '%$name_search%'";
}

if (!empty($gender)) {
    $sql .= " AND gender = '" . $conn->real_escape_string($gender) . "'";
}

if (!empty($payment_status)) {
    $sql .= " AND payment_status = '" . $conn->real_escape_string($payment_status) . "'";
}

if (!empty($min_age)) {
    $sql .= " AND age >= " . intval($min_age);
}

if (!empty($max_age)) {
    $sql .= " AND age <= " . intval($max_age);
}

if (!empty($booking_date)) {
    $sql .= " AND DATE(booking_time) = '" . $conn->real_escape_string($booking_date) . "'";
}

$sql .= " ORDER BY booking_time DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>All Bookings</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        h2 {
            text-align: center;
            margin-top: 20px;
        }

        form.filter-form {
            width: 90%;
            margin: 10px auto;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
        }

        form.filter-form input, select {
            padding: 6px 10px;
            font-size: 14px;
        }

        form.filter-form input[type="submit"],
        form.filter-form a.reset-btn {
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border: none;
            padding: 6px 12px;
            font-size: 14px;
            cursor: pointer;
            display: inline-block;
        }

        table {
            width: 90%;
            margin: 20px auto;
            border-collapse: collapse;
        }

        th, td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
        }

        th {
            background-color: #343a40;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .status-paid {
            color: green;
            font-weight: bold;
        }

        .status-unpaid {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>

<h2>All Bookings</h2>

<form method="GET" class="filter-form">
    <input type="text" name="user_id" placeholder="Search User ID" value="<?= htmlspecialchars($user_search) ?>">
    <input type="text" name="flight_id" placeholder="Search Flight ID" value="<?= htmlspecialchars($flight_search) ?>">
    <input type="text" name="passenger_name" placeholder="Search Name" value="<?= htmlspecialchars($name_search) ?>">

    <select name="gender">
        <option value="">All Genders</option>
        <option value="male" <?= $gender === 'male' ? 'selected' : '' ?>>Male</option>
        <option value="female" <?= $gender === 'female' ? 'selected' : '' ?>>Female</option>
        <option value="other" <?= $gender === 'other' ? 'selected' : '' ?>>Other</option>
    </select>

    <select name="payment_status">
        <option value="">All Payments</option>
        <option value="paid" <?= $payment_status === 'paid' ? 'selected' : '' ?>>Paid</option>
        <option value="Pending" <?= $payment_status === 'Pending' ? 'selected' : '' ?>>Pending</option>
    </select>

    <input type="number" name="min_age" placeholder="Min Age" value="<?= htmlspecialchars($min_age) ?>" min="0">
    <input type="number" name="max_age" placeholder="Max Age" value="<?= htmlspecialchars($max_age) ?>" min="0">

    <input type="date" name="booking_date" value="<?= htmlspecialchars($booking_date) ?>">
    
    <input type="submit" value="Filter">
    <a href="view_bookings.php" class="reset-btn">Reset</a>
</form>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>User ID</th>
            <th>Flight ID</th>
            <th>Passenger Name</th>
            <th>Gender</th>
            <th>Age</th>
            <th>Seat No</th>
            <th>Payment Status</th>
            <th>Booking Time</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['id']) ?></td>
                    <td><?= htmlspecialchars($row['user_id']) ?></td>
                    <td><?= htmlspecialchars($row['flight_id']) ?></td>
                    <td><?= htmlspecialchars($row['passenger_name']) ?></td>
                    <td><?= htmlspecialchars($row['gender']) ?></td>
                    <td><?= htmlspecialchars($row['age']) ?></td>
                    <td><?= htmlspecialchars($row['seat_no']) ?></td>
                    <td class="<?= strtolower($row['payment_status']) === 'paid' ? 'status-paid' : 'status-unpaid' ?>">
                        <?= htmlspecialchars(ucfirst($row['payment_status'])) ?>
                    </td>
                    <td><?= htmlspecialchars($row['booking_time']) ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="9">No bookings found.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<div align="center"><a href="dashbord.php">← Back to Dashboard</a></div>

</body>
</html>
