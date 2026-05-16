<?php
// views/bookings.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Bookings</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; font-family:Arial,sans-serif; }
        body { background-color:#f4f4f4; }
        nav {
            background-color:#2c3e50;
            padding:15px 30px;
            display:flex;
            justify-content:space-between;
            align-items:center;
        }
        nav .logo { color:white; font-size:22px; font-weight:bold; }
        nav a { color:white; text-decoration:none; margin-left:20px; font-size:15px; }
        nav a:hover { color:#f39c12; }
        .container { max-width:1100px; margin:30px auto; padding:0 20px; }
        .page-title { font-size:22px; color:#2c3e50; margin-bottom:20px; font-weight:bold; }
        .card {
            background:white; border-radius:8px;
            padding:25px; margin-bottom:20px;
            box-shadow:0 2px 8px rgba(0,0,0,0.08);
        }

        /* FILTER */
        .filter-form {
            display:flex; gap:15px;
            align-items:center;
            margin-bottom:20px;
            flex-wrap:wrap;
        }
        .filter-form select,
        .filter-form input {
            padding:8px 12px;
            border:1px solid #ddd;
            border-radius:5px;
            font-size:14px;
        }
        .btn-filter {
            padding:8px 20px;
            background-color:#2c3e50;
            color:white; border:none;
            border-radius:5px; font-size:14px;
            cursor:pointer;
        }
        .btn-filter:hover { background-color:#f39c12; }

        /* TABLE */
        table { width:100%; border-collapse:collapse; }
        th {
            background-color:#2c3e50;
            color:white; padding:10px;
            text-align:left; font-size:14px;
        }
        td { padding:10px; border-bottom:1px solid #f4f4f4; font-size:13px; color:#555; }
        tr:hover td { background-color:#fafafa; }

        /* BADGES */
        .badge {
            padding:3px 10px; border-radius:20px;
            font-size:11px; font-weight:bold;
        }
        .badge-pending    { background:#fef9e7; color:#f39c12; border:1px solid #f39c12; }
        .badge-confirmed  { background:#eafaf1; color:#27ae60; border:1px solid #27ae60; }
        .badge-checkedin  { background:#eaf0fb; color:#2980b9; border:1px solid #2980b9; }
        .badge-checkedout { background:#f4f4f4; color:#888;    border:1px solid #ccc;    }
        .badge-cancelled  { background:#fdedec; color:#e74c3c; border:1px solid #e74c3c; }

        /* ACTION BUTTONS */
        .btn-checkin {
            padding:5px 12px;
            background-color:#27ae60;
            color:white; border:none;
            border-radius:4px; font-size:12px;
            cursor:pointer;
        }
        .btn-checkin:hover { background-color:#1e8449; }

        .btn-checkout {
            padding:5px 12px;
            background-color:#2980b9;
            color:white; border:none;
            border-radius:4px; font-size:12px;
            cursor:pointer;
        }
        .btn-checkout:hover { background-color:#1a5276; }

        /* TOAST */
        .toast {
            position:fixed; bottom:30px; right:30px;
            padding:12px 24px; border-radius:6px;
            font-size:14px; color:white; display:none; z-index:999;
        }
        .toast-success { background-color:#27ae60; }
        .toast-error   { background-color:#e74c3c; }
    </style>
</head>
<body>

<nav>
    <div class="logo">🏨 Hotel Booking</div>
    <div>
        <?php if ($_SESSION['role'] == 'admin'): ?>
            <a href="index.php?action=rooms">Manage Rooms</a>
            <a href="index.php?action=bookings">Bookings</a>
            <a href="index.php?action=dashboard">Dashboard</a>
        <?php endif; ?>
        <a href="index.php?action=logout">Logout</a>
    </div>
</nav>

<div class="container">
    <div class="page-title">📋 Booking Management</div>

    <div class="card">

        <!-- FILTER FORM -->
        <form class="filter-form" method="GET" action="index.php">
            <input type="hidden" name="action" value="bookings">
            <select name="status">
                <option value="">All Status</option>
                <option value="Pending"     <?php echo (isset($_GET['status']) && $_GET['status'] == 'Pending')     ? 'selected' : ''; ?>>Pending</option>
                <option value="Confirmed"   <?php echo (isset($_GET['status']) && $_GET['status'] == 'Confirmed')   ? 'selected' : ''; ?>>Confirmed</option>
                <option value="Checked-In"  <?php echo (isset($_GET['status']) && $_GET['status'] == 'Checked-In')  ? 'selected' : ''; ?>>Checked-In</option>
                <option value="Checked-Out" <?php echo (isset($_GET['status']) && $_GET['status'] == 'Checked-Out') ? 'selected' : ''; ?>>Checked-Out</option>
                <option value="Cancelled"   <?php echo (isset($_GET['status']) && $_GET['status'] == 'Cancelled')   ? 'selected' : ''; ?>>Cancelled</option>
            </select>
            <input type="date" name="date" value="<?php echo isset($_GET['date']) ? htmlspecialchars($_GET['date']) : ''; ?>">
            <button type="submit" class="btn-filter">Filter</button>
            <a href="index.php?action=bookings" class="btn-filter" style="text-decoration:none;">Reset</a>
        </form>

        <!-- BOOKINGS TABLE -->
        <table>
            <tr>
                <th>ID</th>
                <th>Guest</th>
                <th>Room</th>
                <th>Check-in</th>
                <th>Check-out</th>
                <th>Total</th>
                <th>Status</th>
                <th>Action</th>
            </tr>

            <?php if (empty($bookings)): ?>
            <tr>
                <td colspan="8" style="text-align:center; padding:30px; color:#999;">
                    No bookings found
                </td>
            </tr>
            <?php else: ?>
                <?php foreach ($bookings as $booking): ?>
                <?php
                    $badgeClass = 'badge-pending';
                    if ($booking['status'] == 'Confirmed')   $badgeClass = 'badge-confirmed';
                    if ($booking['status'] == 'Checked-In')  $badgeClass = 'badge-checkedin';
                    if ($booking['status'] == 'Checked-Out') $badgeClass = 'badge-checkedout';
                    if ($booking['status'] == 'Cancelled')   $badgeClass = 'badge-cancelled';
                ?>
                <tr id="row-<?php echo $booking['id']; ?>">
                    <td>#<?php echo $booking['id']; ?></td>
                    <td>
                        <?php echo htmlspecialchars($booking['guest_name']); ?><br>
                        <small><?php echo htmlspecialchars($booking['guest_email']); ?></small>
                    </td>
                    <td>
                        <?php echo htmlspecialchars($booking['room_type_name']); ?><br>
                        <small>Room <?php echo htmlspecialchars($booking['room_number']); ?></small>
                    </td>
                    <td><?php echo htmlspecialchars($booking['checkin_date']); ?></td>
                    <td><?php echo htmlspecialchars($booking['checkout_date']); ?></td>
                    <td>$<?php echo number_format($booking['total_price'], 2); ?></td>
                    <td>
                        <span class="badge <?php echo $badgeClass; ?>"
                              id="status-<?php echo $booking['id']; ?>">
                            <?php echo htmlspecialchars($booking['status']); ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($booking['status'] == 'Confirmed'): ?>
                            <button class="btn-checkin"
                                    id="btn-<?php echo $booking['id']; ?>"
                                    onclick="checkIn(<?php echo $booking['id']; ?>)">
                                Check In
                            </button>
                        <?php else if ($booking['status'] == 'Checked-In'): ?>
                            <button class="btn-checkout"
                                    id="btn-<?php echo $booking['id']; ?>"
                                    onclick="checkOut(<?php echo $booking['id']; ?>)">
                                Check Out
                            </button>
                        <?php else: ?>
                            —
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </table>
    </div>
</div>

<div class="toast" id="toast"></div>

<script src="js/admin.js"></script>

</body>
</html>