<?php

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
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
        .container { max-width:700px; margin:30px auto; padding:0 20px; }
        .card {
            background:white; border-radius:8px;
            padding:25px; margin-bottom:20px;
            box-shadow:0 2px 8px rgba(0,0,0,0.08);
        }
        .card h2 {
            color:#2c3e50; font-size:18px;
            margin-bottom:15px;
            border-bottom:2px solid #f39c12;
            padding-bottom:8px;
        }
        .form-group { margin-bottom:15px; }
        .form-group label { display:block; margin-bottom:5px; color:#555; font-size:14px; }
        .form-group input, .form-group select, .form-group textarea {
            width:100%; padding:10px;
            border:1px solid #ddd;
            border-radius:5px; font-size:14px;
        }
        .form-group textarea { height:80px; resize:vertical; }
        .btn {
            padding:10px 25px;
            background-color:#f39c12;
            color:white; border:none;
            border-radius:5px; font-size:15px;
            cursor:pointer;
        }
        .btn:hover { background-color:#e67e22; }
        .success { color:green; font-size:13px; margin-bottom:15px; }
        .error   { color:red;   font-size:13px; margin-bottom:15px; }

      
        .booking-card {
            background:#eaf0fb;
            border-radius:8px;
            padding:15px 20px;
        }
        .booking-card h3 { color:#2c3e50; margin-bottom:10px; }
        .booking-card p  { color:#555; font-size:14px; margin-bottom:5px; }
        .booking-card .status {
            display:inline-block;
            padding:3px 12px;
            border-radius:20px;
            font-size:12px;
            font-weight:bold;
            background:#fef9e7;
            color:#f39c12;
            border:1px solid #f39c12;
            margin-top:8px;
        }
    </style>
</head>
<body>

<nav>
    <div class="logo">🏨 Hotel Booking</div>
    <div>
        <?php if (isset($_SESSION['user_id'])): ?>

            <?php if ($_SESSION['role'] == 'admin'): ?>
                <!-- ADMIN LINKS -->
                <a href="index.php?action=rooms">Manage Rooms</a>
                <a href="index.php?action=bookings">Bookings</a>
                <a href="index.php?action=dashboard">Dashboard</a>
            <?php else: ?>
                <!-- GUEST LINKS -->
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

   
    <?php if ($upcomingBooking): ?>
    <div class="card">
        <h2>📅 Upcoming Booking</h2>
        <div class="booking-card">
            <h3><?php echo htmlspecialchars($upcomingBooking['room_type_name']); ?></h3>
            <p>🛏 Room: <?php echo htmlspecialchars($upcomingBooking['room_number']); ?></p>
            <p>📅 Check-in:  <?php echo htmlspecialchars($upcomingBooking['checkin_date']); ?></p>
            <p>📅 Check-out: <?php echo htmlspecialchars($upcomingBooking['checkout_date']); ?></p>
            <p>💰 Total: $<?php echo number_format($upcomingBooking['total_price'], 2); ?></p>
            <span class="status"><?php echo htmlspecialchars($upcomingBooking['status']); ?></span>
        </div>
    </div>
    <?php endif; ?>

   
    <div class="card">
        <h2>👤 My Profile</h2>

        <?php if (isset($success)): ?>
            <p class="success"><?php echo $success; ?></p>
        <?php endif; ?>

        <?php if (isset($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name"
                       value="<?php echo htmlspecialchars($user['name']); ?>"
                       required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email"
                       value="<?php echo htmlspecialchars($user['email']); ?>"
                       readonly style="background:#f9f9f9; color:#888;">
            </div>
            <div class="form-group">
                <label>Phone</label>
                <input type="text" name="phone"
                       value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>Nationality</label>
                <input type="text" name="nationality"
                       value="<?php echo htmlspecialchars($user['nationality'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>Preferred Room Type</label>
                <select name="preferred_room_type_id">
                    <option value="">No preference</option>
                    <option value="1" <?php echo ($user['preferred_room_type_id'] == 1) ? 'selected' : ''; ?>>Standard</option>
                    <option value="2" <?php echo ($user['preferred_room_type_id'] == 2) ? 'selected' : ''; ?>>Deluxe</option>
                    <option value="3" <?php echo ($user['preferred_room_type_id'] == 3) ? 'selected' : ''; ?>>Suite</option>
                </select>
            </div>
            <div class="form-group">
                <label>Special Requests</label>
                <textarea name="special_requests"><?php echo htmlspecialchars($user['special_requests'] ?? ''); ?></textarea>
            </div>
            <button type="submit" class="btn">Update Profile</button>
        </form>
    </div>

</div>

</body>
</html>