<?php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

// Get filters
$user_search = $_GET['user_id'] ?? '';
$flight_search = $_GET['flight_id'] ?? '';
$return_flight_search = $_GET['return_flight_id'] ?? '';
$class_type = $_GET['class_type'] ?? '';
$name_search = $_GET['passenger_name'] ?? '';
$gender = $_GET['gender'] ?? '';
$payment_status = $_GET['payment_status'] ?? '';
$min_age = $_GET['min_age'] ?? '';
$max_age = $_GET['max_age'] ?? '';
$booking_date = $_GET['booking_date'] ?? '';
$min_amount = $_GET['min_amount'] ?? '';
$max_amount = $_GET['max_amount'] ?? '';

// Build query
$sql = "
    SELECT b.*, p.name AS passenger_name, p.gender, p.age, p.seat_no
    FROM bookings2 b
    JOIN passengers p ON b.id = p.booking_id
    WHERE 1=1
";

if (!empty($user_search)) {
    $sql .= " AND b.user_id LIKE '%" . $conn->real_escape_string($user_search) . "%'";
}
if (!empty($flight_search)) {
    $sql .= " AND b.flight_id LIKE '%" . $conn->real_escape_string($flight_search) . "%'";
}
if (!empty($return_flight_search)) {
    $sql .= " AND b.return_flight_id LIKE '%" . $conn->real_escape_string($return_flight_search) . "%'";
}
if (!empty($class_type)) {
    $sql .= " AND b.class_type = '" . $conn->real_escape_string($class_type) . "'";
}
if (!empty($name_search)) {
    $sql .= " AND p.name LIKE '%" . $conn->real_escape_string($name_search) . "%'";
}
if (!empty($gender)) {
    $sql .= " AND p.gender = '" . $conn->real_escape_string($gender) . "'";
}
if (!empty($payment_status)) {
    $sql .= " AND b.payment_status = '" . $conn->real_escape_string($payment_status) . "'";
}
if (!empty($min_age)) {
    $sql .= " AND p.age >= " . intval($min_age);
}
if (!empty($max_age)) {
    $sql .= " AND p.age <= " . intval($max_age);
}
if (!empty($min_amount)) {
    $sql .= " AND b.total_amount >= " . floatval($min_amount);
}
if (!empty($max_amount)) {
    $sql .= " AND b.total_amount <= " . floatval($max_amount);
}
if (!empty($booking_date)) {
    $sql .= " AND DATE(b.booking_time) = '" . $conn->real_escape_string($booking_date) . "'";
}

$sql .= " ORDER BY b.booking_time ASC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>All Bookings</title>
    <style>
        body { font-family: Arial, sans-serif; }
        h2 { text-align: center; margin-top: 20px; }

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
        }

        table {
            width: 95%;
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

        .status-paid { color: green; font-weight: bold; }
        .status-unpaid { color: red; font-weight: bold; }
    </style>
</head>
<body>

<h2>All Bookings</h2>

<form method="GET" class="filter-form">
    <input type="text" name="user_id" placeholder="User ID" value="<?= htmlspecialchars($user_search) ?>">
    <input type="text" name="flight_id" placeholder="Flight ID" value="<?= htmlspecialchars($flight_search) ?>">
    <input type="text" name="return_flight_id" placeholder="Return Flight ID" value="<?= htmlspecialchars($return_flight_search) ?>">
    
    <select name="class_type">
        <option value="">All Classes</option>
        <option value="economy" <?= $class_type === 'economy' ? 'selected' : '' ?>>Economy</option>
        <option value="business" <?= $class_type === 'business' ? 'selected' : '' ?>>Business</option>
        <option value="first" <?= $class_type === 'first' ? 'selected' : '' ?>>First</option>
    </select>

    <input type="text" name="passenger_name" placeholder="Passenger Name" value="<?= htmlspecialchars($name_search) ?>">

    <select name="gender">
        <option value="">All Genders</option>
        <option value="Male" <?= $gender === 'Male' ? 'selected' : '' ?>>Male</option>
        <option value="Female" <?= $gender === 'Female' ? 'selected' : '' ?>>Female</option>
        <option value="Other" <?= $gender === 'Other' ? 'selected' : '' ?>>Other</option>
    </select>

    <select name="payment_status">
        <option value="">All Payments</option>
        <option value="Paid" <?= $payment_status === 'Paid' ? 'selected' : '' ?>>Paid</option>
        <option value="Pending" <?= $payment_status === 'Pending' ? 'selected' : '' ?>>Pending</option>
    </select>

    <input type="number" name="min_age" placeholder="Min Age" min="0" value="<?= htmlspecialchars($min_age) ?>">
    <input type="number" name="max_age" placeholder="Max Age" min="0" value="<?= htmlspecialchars($max_age) ?>">

    <input type="number" step="0.01" name="min_amount" placeholder="Min Amount" value="<?= htmlspecialchars($min_amount) ?>">
    <input type="number" step="0.01" name="max_amount" placeholder="Max Amount" value="<?= htmlspecialchars($max_amount) ?>">

    <input type="date" name="booking_date" value="<?= htmlspecialchars($booking_date) ?>">
    
    <input type="submit" value="Filter">
    <a href="booking.php" class="reset-btn">Reset</a>
</form>

<table>
    <thead>
        <tr>
            <th>Booking ID</th>
            <th>User ID</th>
            <th>Flight ID</th>
            <th>Return Flight</th>
            <th>Class Type</th>
            <th>Total Amount</th>
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
                    <td><?= htmlspecialchars($row['return_flight_id'] ?? '-') ?></td>
                    <td><?= ucfirst(htmlspecialchars($row['class_type'])) ?></td>
                    <td>₹<?= number_format($row['total_amount'], 2) ?></td>
                    <td><?= htmlspecialchars($row['passenger_name']) ?></td>
                    <td><?= htmlspecialchars($row['gender']) ?></td>
                    <td><?= htmlspecialchars($row['age']) ?></td>
                    <td><?= htmlspecialchars($row['seat_no']) ?></td>
                    <td class="<?= $row['payment_status'] === 'Paid' ? 'status-paid' : 'status-unpaid' ?>">
                        <?= htmlspecialchars($row['payment_status']) ?>
                    </td>
                    <td><?= htmlspecialchars($row['booking_time']) ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="12">No bookings found.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<div align="center"><a href="dashbord.php">← Back to Dashboard</a></div>

</body>
</html>
