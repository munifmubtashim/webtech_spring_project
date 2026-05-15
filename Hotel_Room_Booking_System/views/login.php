
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
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
        .container {
            max-width:450px;
            margin:60px auto;
            background:white;
            padding:30px;
            border-radius:8px;
            box-shadow:0 2px 10px rgba(0,0,0,0.1);
        }
        h2 { color:#2c3e50; margin-bottom:20px; text-align:center; }
        .form-group { margin-bottom:15px; }
        .form-group label { display:block; margin-bottom:5px; color:#555; font-size:14px; }
        .form-group input {
            width:100%; padding:10px;
            border:1px solid #ddd;
            border-radius:5px; font-size:14px;
        }
        .remember {
            display:flex;
            align-items:center;
            gap:8px;
            font-size:14px;
            color:#555;
            margin-bottom:10px;
        }
        .btn {
            width:100%; padding:12px;
            background-color:#f39c12;
            color:white; border:none;
            border-radius:5px; font-size:16px;
            cursor:pointer; margin-top:10px;
        }
        .btn:hover { background-color:#e67e22; }
        .error {
            color:red; font-size:13px;
            margin-bottom:15px; text-align:center;
        }
        .link { text-align:center; margin-top:15px; font-size:14px; }
        .link a { color:#f39c12; text-decoration:none; }
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
    <h2>Login</h2>

    <?php if (isset($error)): ?>
        <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email"
                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                   required>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required>
        </div>
        <div class="remember">
            <input type="checkbox" name="remember_me" id="remember_me">
            <label for="remember_me">Remember Me</label>
        </div>
        <button type="submit" class="btn">Login</button>
    </form>

    <div class="link">
        Don't have an account? <a href="index.php?action=register">Register here</a>
    </div>
</div>

</body>
</html>