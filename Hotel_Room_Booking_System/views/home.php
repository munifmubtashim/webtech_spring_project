

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Room Booking</title>
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

        nav {
            background: #2c3e50;
            padding: 11px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 2px solid black;
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


        .hero {
            background-image: url('uploads/homew.png');
            background-size: cover;
            background-position: center;
            color: white;
            text-align: center;
            padding: 60px 40px;
        }

        .hero h1 {
            font-size: 36px;
            margin-bottom: 10px;
        }

        .hero p {
            font-size: 16px;
            color: #d8e5ed;
        }


        .search-box {
            background-color: white;
            max-width: 700px;
            margin: 30px auto;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .search-box h2 {
            margin-bottom: 20px;
            color: #2c3e50;
            font-size: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-size: 14px;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }

        .btn-search {
            width: 100%;
            padding: 12px;
            background-color: #f39c12;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 10px;
        }

        .btn-search:hover {
            background-color: #e67e22;
        }


        .error {
            color: red;
            font-size: 13px;
            margin-top: 5px;
            display: none;
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


    <div class="hero">
        <h1>Find Your Perfect Room</h1>
        <p>Book comfortable rooms at the best prices</p>
    </div>


    <div class="search-box">
        <h2>Search Available Rooms</h2>

        <?php if (isset($_GET['error']) && $_GET['error'] == 'noroom'): ?>
        <p style="color:red; margin-bottom:15px;">
            Sorry, no rooms available for that selection. Please try different dates.
        </p>
        <?php endif; ?>

        <form id="searchForm" action="index.php?action=results" method="GET">
            <input type="hidden" name="action" value="results">

            <div class="form-group">
                <label>Check-in Date</label>
                <input type="date" name="checkin" id="checkin" required>
                <span class="error" id="checkinError">Please select a check-in date</span>
            </div>

            <div class="form-group">
                <label>Check-out Date</label>
                <input type="date" name="checkout" id="checkout" required>
                <span class="error" id="checkoutError">Check-out must be after check-in</span>
            </div>

            <div class="form-group">
                <label>Number of Guests</label>
                <select name="guests" id="guests">
                    <option value="1">1 Guest</option>
                    <option value="2">2 Guests</option>
                    <option value="3">3 Guests</option>
                    <option value="4">4 Guests</option>
                </select>
            </div>

            <button type="submit" class="btn-search">Search Rooms</button>
        </form>
    </div>

    <script>

        const today = new Date().toISOString().split('T')[0];
        document.getElementById('checkin').min = today;
        document.getElementById('checkout').min = today;


        document.getElementById('searchForm').addEventListener('submit', function (e) {
            let valid = true;
            const checkin = document.getElementById('checkin').value;
            const checkout = document.getElementById('checkout').value;

            if (!checkin) {
                document.getElementById('checkinError').style.display = 'block';
                valid = false;
            } else {
                document.getElementById('checkinError').style.display = 'none';
            }

            if (!checkout || checkout <= checkin) {
                document.getElementById('checkoutError').style.display = 'block';
                valid = false;
            } else {
                document.getElementById('checkoutError').style.display = 'none';
            }

            if (!valid) e.preventDefault();
        });


        document.getElementById('checkin').addEventListener('change', function () {
            document.getElementById('checkout').min = this.value;
        });
    </script>

</body>

</html>