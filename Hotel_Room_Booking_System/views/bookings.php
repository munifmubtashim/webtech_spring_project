<?php
$statusOptions = ['Pending', 'Confirmed', 'Checked-In', 'Checked-Out', 'Cancelled'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin — All Bookings</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, sans-serif; }
        body { background-color: #f4f4f4; }

        nav {
            background: #2c3e50;
            padding: 11px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 2px solid black;
        }
        nav .logo { color: white; font-size: 22px; font-weight: bold; }
        nav a { color: white; text-decoration: none; margin-left: 20px; font-size: 15px; }
        nav a:hover { color: #f39c12; }

        .page-header {
            background: #2c3e50;
            color: white;
            padding: 25px 40px;
            font-size: 24px;
            font-weight: bold;
        }

        .container { max-width: 1100px; margin: 30px auto; padding: 0 20px; }

        .filter-box {
            background: white;
            padding: 20px 25px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 25px;
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            align-items: flex-end;
        }
        .filter-box .form-group { display: flex; flex-direction: column; gap: 5px; }
        .filter-box label { font-size: 13px; color: #555; }
        .filter-box input, .filter-box select {
            padding: 9px 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        .btn-filter {
            padding: 9px 22px;
            background: #f39c12;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
        }
        .btn-filter:hover { background: #e67e22; }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        th {
            background: #2c3e50;
            color: white;
            padding: 13px 15px;
            text-align: left;
            font-size: 14px;
        }
        td { padding: 12px 15px; font-size: 14px; border-bottom: 1px solid #eee; color: #333; }
        tr:hover td { background: #fafafa; }
        .no-data { text-align: center; padding: 30px; color: #888; }

        .badge { padding: 4px 11px; border-radius: 12px; font-size: 12px; font-weight: 600; }
        .badge-pending    { background: #fef3c7; color: #92400e; }
        .badge-confirmed  { background: #dbeafe; color: #1e40af; }
        .badge-checkedin  { background: #d1fae5; color: #065f46; }
        .badge-checkedout { background: #e5e7eb; color: #374151; }
        .badge-cancelled  { background: #fee2e2; color: #991b1b; }

        .btn-action {
            padding: 6px 14px;
            border: none;
            border-radius: 5px;
            font-size: 13px;
            cursor: pointer;
            color: white;
        }
        .btn-checkin  { background: #27ae60; }
        .btn-checkin:hover  { background: #1e8449; }
        .btn-checkout { background: #e67e22; }
        .btn-checkout:hover { background: #ca6f1e; }
    </style>
</head>
<body>

<nav>
    <div class="logo">🏨 Hotel Booking</div>
    <div>
        <a href="index.php?action=dashboard">Dashboard</a>
        <a href="index.php?action=bookings">All Bookings</a>
        <a href="index.php?action=rooms">Rooms</a>
        <a href="index.php?action=logout">Logout</a>
    </div>
</nav>

<div class="page-header">📋 All Bookings</div>

<div class="container">

    <form method="GET" action="index.php">
        <input type="hidden" name="action" value="bookings">
        <div class="filter-box">
            <div class="form-group">
                <label>Status</label>
                <select name="status">
                    <option value="">All Statuses</option>
                    <?php foreach ($statusOptions as $opt): ?>
                        <option value="<?= $opt ?>" <?= (($_GET['status'] ?? '') == $opt) ? 'selected' : '' ?>>
                            <?= $opt ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Check-in From</label>
                <input type="date" name="date_from" value="<?= htmlspecialchars($_GET['date_from'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Check-in To</label>
                <input type="date" name="date_to" value="<?= htmlspecialchars($_GET['date_to'] ?? '') ?>">
            </div>
            <button type="submit" class="btn-filter">Filter</button>
        </div>
    </form>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Guest</th>
                <th>Room</th>
                <th>Type</th>
                <th>Check-In</th>
                <th>Check-Out</th>
                <th>Total Price</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($bookings)): ?>
            <tr><td colspan="9" class="no-data">No bookings found.</td></tr>
        <?php else: ?>
            <?php foreach ($bookings as $booking): ?>
                <?php
                    $badgeClass = match($booking['status']) {
                        'Pending'     => 'badge-pending',
                        'Confirmed'   => 'badge-confirmed',
                        'Checked-In'  => 'badge-checkedin',
                        'Checked-Out' => 'badge-checkedout',
                        'Cancelled'   => 'badge-cancelled',
                        default       => ''
                    };
                ?>
                <tr>
                    <td>#<?= $booking['id'] ?></td>
                    <td><?= htmlspecialchars($booking['guest_name']) ?></td>
                    <td><?= htmlspecialchars($booking['room_number']) ?></td>
                    <td><?= htmlspecialchars($booking['room_type_name']) ?></td>
                    <td><?= $booking['checkin_date'] ?></td>
                    <td><?= $booking['checkout_date'] ?></td>
                    <td>$<?= number_format($booking['total_price'], 2) ?></td>
                    <td><span class="badge <?= $badgeClass ?>"><?= $booking['status'] ?></span></td>
                    <td>
                        <?php if ($booking['status'] == 'Confirmed' && $booking['checkin_date'] == date('Y-m-d')): ?>
                            <button class="btn-action btn-checkin" data-id="<?= $booking['id'] ?>">Check In</button>
                        <?php elseif ($booking['status'] == 'Checked-In'): ?>
                            <button class="btn-action btn-checkout" data-id="<?= $booking['id'] ?>">Check Out</button>
                        <?php else: ?>
                            —
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>

</div>

<script src="js/admin.js"></script>
</body>
</html>