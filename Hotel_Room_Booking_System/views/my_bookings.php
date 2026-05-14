<?php
// views/my_bookings.php
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background-color: #f4f4f4;
        }

        /* ===== NAVBAR ===== */
        nav {
            background-color: #2c3e50;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        nav .logo {
            color: white;
            font-size: 22px;
            font-weight: bold;
        }

        nav a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
            font-size: 15px;
        }

        nav a:hover {
            color: #f39c12;
        }

        /* ===== CONTAINER ===== */
        .container {
            max-width: 900px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .page-title {
            font-size: 22px;
            color: #2c3e50;
            margin-bottom: 20px;
            font-weight: bold;
        }

        /* ===== NO BOOKINGS ===== */
        .no-bookings {
            background-color: white;
            border-radius: 8px;
            padding: 40px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            color: #777;
            font-size: 16px;
        }

        .no-bookings a {
            color: #f39c12;
            text-decoration: none;
            font-weight: bold;
        }

        /* ===== BOOKING CARD ===== */
        .booking-card {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .booking-info h3 {
            font-size: 18px;
            color: #2c3e50;
            margin-bottom: 8px;
        }

        .booking-info p {
            font-size: 14px;
            color: #777;
            margin-bottom: 4px;
        }

        .booking-info p span {
            color: #2c3e50;
            font-weight: bold;
        }

        /* ===== STATUS BADGES ===== */
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            margin-top: 8px;
        }

        .badge-pending    { background-color: #fef9e7; color: #f39c12; border: 1px solid #f39c12; }
        .badge-confirmed  { background-color: #eafaf1; color: #27ae60; border: 1px solid #27ae60; }
        .badge-checkedin  { background-color: #eaf0fb; color: #2980b9; border: 1px solid #2980b9; }
        .badge-checkedout { background-color: #f4f4f4; color: #888;    border: 1px solid #ccc;    }
        .badge-cancelled  { background-color: #fdedec; color: #e74c3c; border: 1px solid #e74c3c; }

        /* ===== BOOKING ACTION ===== */
        .booking-action {
            text-align: right;
            min-width: 150px;
        }

        .booking-action .price {
            font-size: 20px;
            font-weight: bold;
            color: #f39c12;
            margin-bottom: 10px;
        }

        .btn-cancel {
            padding: 8px 18px;
            background-color: #e74c3c;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 13px;
            cursor: pointer;
        }

        .btn-cancel:hover {
            background-color: #c0392b;
        }

        .btn-cancel:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }

        /* ===== TOAST MESSAGE ===== */
        .toast {
            position: fixed;
            bottom: 30px;
            right: 30px;
            padding: 12px 24px;
            border-radius: 6px;
            font-size: 14px;
            color: white;
            display: none;
            z-index: 999;
        }

        .toast-success { background-color: #27ae60; }
        .toast-error   { background-color: #e74c3c; }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav>
    <div class="logo">🏨 Hotel Booking</div>
    <div>
        <a href="index.php?action=my_bookings">My Bookings</a>
        <a href="index.php?action=profile">Profile</a>
        <a href="index.php?action=logout">Logout</a>
    </div>
</nav>

<div class="container">

    <div class="page-title">📋 My Bookings</div>

    <?php if (empty($bookings)): ?>
        <!-- NO BOOKINGS -->
        <div class="no-bookings">
            😔 You have no bookings yet.
            <br><br>
            <a href="index.php?action=home">Search for rooms now</a>
        </div>

    <?php else: ?>
        <!-- BOOKING CARDS -->
        <?php foreach ($bookings as $booking): ?>
            <?php
                // pick badge class based on status
                $badgeClass = 'badge-pending';
                if ($booking['status'] == 'Confirmed')   $badgeClass = 'badge-confirmed';
                if ($booking['status'] == 'Checked-In')  $badgeClass = 'badge-checkedin';
                if ($booking['status'] == 'Checked-Out') $badgeClass = 'badge-checkedout';
                if ($booking['status'] == 'Cancelled')   $badgeClass = 'badge-cancelled';

                // can only cancel Pending or Confirmed
                // and checkin must be more than 1 day away
                $canCancel = in_array($booking['status'], ['Pending', 'Confirmed'])
                             && strtotime($booking['checkin_date']) > strtotime('+1 day');
            ?>
            <div class="booking-card" id="booking-<?php echo $booking['id']; ?>">

                <div class="booking-info">
                    <h3><?php echo htmlspecialchars($booking['room_type_name']); ?></h3>
                    <p>🛏 Room Number: <span><?php echo htmlspecialchars($booking['room_number']); ?></span></p>
                    <p>📅 Check-in:  <span><?php echo htmlspecialchars($booking['checkin_date']); ?></span></p>
                    <p>📅 Check-out: <span><?php echo htmlspecialchars($booking['checkout_date']); ?></span></p>
                    <p>🔖 Booking ID: <span>#<?php echo htmlspecialchars($booking['id']); ?></span></p>
                    <span class="badge <?php echo $badgeClass; ?>" id="status-<?php echo $booking['id']; ?>">
                        <?php echo htmlspecialchars($booking['status']); ?>
                    </span>
                </div>

                <div class="booking-action">
                    <div class="price">$<?php echo number_format($booking['total_price'], 2); ?></div>

                    <?php if ($canCancel): ?>
                        <button class="btn-cancel"
                                id="cancel-btn-<?php echo $booking['id']; ?>"
                                onclick="cancelBooking(<?php echo $booking['id']; ?>)">
                            Cancel
                        </button>
                    <?php else: ?>
                        <button class="btn-cancel" disabled>
                            <?php echo $booking['status'] == 'Cancelled' ? 'Cancelled' : 'Cannot Cancel'; ?>
                        </button>
                    <?php endif; ?>
                </div>

            </div>
        <?php endforeach; ?>
    <?php endif; ?>

</div>

<!-- TOAST -->
<div class="toast" id="toast"></div>

<script src="js/booking.js"></script>

</body>
</html>