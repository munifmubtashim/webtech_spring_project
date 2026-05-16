<?php
// Ensure $booking is an array even if the query fails, to prevent undefined variable errors
$b = $booking ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmed</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, sans-serif; }
        body { background-color: #f4f4f4; }
        nav { background-color: #2c3e50; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; }
        nav .logo { color: white; font-size: 22px; font-weight: bold; }
        nav a { color: white; text-decoration: none; margin-left: 20px; font-size: 15px; }
        nav a:hover { color: #f39c12; }
        .container { max-width: 650px; margin: 40px auto; padding: 0 20px; }
        .success-box { background-color: white; border-radius: 8px; padding: 30px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08); text-align: center; margin-bottom: 20px; }
        .success-icon { font-size: 60px; margin-bottom: 15px; }
        .success-box h2 { color: #27ae60; font-size: 26px; margin-bottom: 8px; }
        .success-box p { color: #777; font-size: 15px; }
        .details-box { background-color: white; border-radius: 8px; padding: 25px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08); margin-bottom: 20px; }
        .details-box h3 { color: #2c3e50; font-size: 18px; margin-bottom: 15px; border-bottom: 2px solid #f39c12; padding-bottom: 8px; }
        .detail-row { display: flex; justify-content: space-between; padding: 10px 0; font-size: 15px; color: #555; border-bottom: 1px solid #f4f4f4; }
        .detail-row:last-child { border-bottom: none; }
        .detail-row span:last-child { font-weight: bold; color: #2c3e50; }
        .total-row { display: flex; justify-content: space-between; padding: 12px 0; font-size: 18px; font-weight: bold; color: #2c3e50; margin-top: 5px; }
        .total-row span:last-child { color: #f39c12; }
        .status-badge { display: inline-block; padding: 4px 14px; border-radius: 20px; font-size: 13px; font-weight: bold; background-color: #fef9e7; color: #f39c12; border: 1px solid #f39c12; }
        .btn-group { display: flex; gap: 15px; }
        .btn-primary { flex: 1; padding: 12px; background-color: #2c3e50; color: white; border: none; border-radius: 5px; font-size: 15px; cursor: pointer; text-align: center; text-decoration: none; display: inline-block; }
        .btn-primary:hover { background-color: #f39c12; }
        .btn-secondary { flex: 1; padding: 12px; background-color: white; color: #2c3e50; border: 2px solid #2c3e50; border-radius: 5px; font-size: 15px; cursor: pointer; text-align: center; text-decoration: none; display: inline-block; }
        .btn-secondary:hover { background-color: #f4f4f4; }
    </style>
</head>
<body>

<nav>
    <div class="logo">🏨 Hotel Booking</div>
    <div>
        <?php if (isset($_SESSION['user_id'])): ?>
            <?php if ($_SESSION['role'] == 'admin'): ?>
                <a href="index.php?action=rooms">Manage Rooms</a>
                <a href="index.php?action=bookings">Bookings</a>
                <a href="index.php?action=dashboard">Dashboard</a>
            <?php else: ?>
                <a href="index.php?action=home">Search Rooms</a>
                <a href="index.php?action=my_bookings">My Bookings</a>
                <a href="index.php?action=profile">Profile</a>
            <?php endif; ?>
            <a href="index.php?action=logout">Logout</a>
        <?php else: ?>
            <a href="index.php?action=login">Login</a>
            <a href="index.php?action=register">Register</a>
        <?php endif; ?>
    </div>
</nav>

<div class="container">

    <div class="success-box">
        <div class="success-icon">✅</div>
        <h2>Booking Confirmed!</h2>
        <p>Your room has been successfully booked. See details below.</p>
    </div>

    <?php if (!empty($b)): ?>
        <div class="details-box">
            <h3>Booking Details</h3>

            <div class="detail-row">
                <span>Booking ID</span>
                <span>#<?php echo htmlspecialchars($b['id'] ?? '-'); ?></span>
            </div>
            <div class="detail-row">
                <span>Room Type</span>
                <span><?php echo htmlspecialchars($b['room_type_name'] ?? 'Standard'); ?></span>
            </div>
            <div class="detail-row">
                <span>Room Number</span>
                <span><?php echo htmlspecialchars($b['room_number'] ?? '-'); ?></span>
            </div>
            <div class="detail-row">
                <span>Check-in</span>
                <span><?php echo htmlspecialchars($b['checkin_date'] ?? '-'); ?></span>
            </div>
            <div class="detail-row">
                <span>Check-out</span>
                <span><?php echo htmlspecialchars($b['checkout_date'] ?? '-'); ?></span>
            </div>
            <div class="detail-row">
                <span>Guests</span>
                <span><?php echo htmlspecialchars($b['guests'] ?? '1'); ?></span>
            </div>
            <div class="detail-row">
                <span>Status</span>
                <span><span class="status-badge"><?php echo htmlspecialchars($b['status'] ?? 'Confirmed'); ?></span></span>
            </div>
            <div class="total-row">
                <span>Total Price</span>
                <span>$<?php echo number_format($b['total_price'] ?? 0, 2); ?></span>
            </div>
        </div>
    <?php else: ?>
        <p style="color:red; text-align:center; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
            ⚠️ Booking process finished successfully, but your itemized receipt details could not be pulled from the database records at this time.
        </p>
    <?php endif; ?>

    <div class="btn-group">
        <a href="index.php?action=my_bookings" class="btn-primary">View My Bookings</a>
        <a href="index.php?action=home" class="btn-secondary">Back to Home</a>
    </div>

</div>

</body>
</html>