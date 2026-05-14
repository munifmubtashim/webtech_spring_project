<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Room</title>
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


        .container {
            max-width: 700px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #2c3e50;
            text-decoration: none;
            font-size: 14px;
        }

        .back-link:hover {
            color: #f39c12;
        }

        .summary-box {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .summary-box h2 {
            color: #2c3e50;
            margin-bottom: 15px;
            font-size: 18px;
            border-bottom: 2px solid #f39c12;
            padding-bottom: 8px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            font-size: 15px;
            color: #555;
            border-bottom: 1px solid #f4f4f4;
        }

        .summary-row span:last-child {
            font-weight: bold;
            color: #2c3e50;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            font-size: 18px;
            color: #2c3e50;
            font-weight: bold;
        }

        .total-row span:last-child {
            color: #f39c12;
        }


        .form-box {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .form-box h2 {
            color: #2c3e50;
            margin-bottom: 15px;
            font-size: 18px;
            border-bottom: 2px solid #f39c12;
            padding-bottom: 8px;
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
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            background-color: #f9f9f9;
        }

        .form-group input[readonly] {
            color: #888;
            cursor: not-allowed;
        }

        .form-group textarea {
            resize: vertical;
            height: 80px;
        }

        .btn-confirm {
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

        .btn-confirm:hover {
            background-color: #e67e22;
        }

        .error {
            color: red;
            font-size: 13px;
            margin-top: 5px;
            display: none;
        }
.checkbox-group {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 10px;
    margin-top: 10px;
}

.checkbox-label {
    margin: 0;
    font-size: 14px;
    color: #555;
    display: flex;
    align-items: center;
    line-height: 1;
}

.checkbox-group input[type="checkbox"] {
    width: 18px;
    height: 18px;
    margin: 0;
    accent-color: #f39c12;
    cursor: pointer;
    position: relative;
    top: -1px;
}
    </style>
</head>

<body>


    <nav>
        <div class="logo">🏨 Hotel Booking</div>
        <div>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="index.php?action=my_bookings">My Bookings</a>
                <a href="index.php?action=profile">Profile</a>
                <a href="index.php?action=logout">Logout</a>
            <?php else: ?>
                <a href="index.php?action=login">Login</a>
                <a href="index.php?action=register">Register</a>
            <?php endif; ?>
        </div>
    </nav>

    <?php
    $checkin = $_GET['checkin'] ?? '';
    $checkout = $_GET['checkout'] ?? '';
    $guests = $_GET['guests'] ?? '';
    $roomTypeId = $_GET['room_type_id'] ?? '';
    ?>

    <div class="container">

        <a href="index.php?action=results&checkin=<?php echo htmlspecialchars($checkin); ?>&checkout=<?php echo htmlspecialchars($checkout); ?>&guests=<?php echo htmlspecialchars($guests); ?>"
            class="back-link">← Back to Results</a>

        <?php

        $nights     = (strtotime($checkout) - strtotime($checkin)) / 86400;
        $pricePerNight = $_GET['price'] ?? 0;
        $totalPrice = $nights * $pricePerNight;
        ?>


        <div class="summary-box">
            <h2>Booking Summary</h2>

            <div class="summary-row">
                <span>Room Type</span>
                <span><?php echo htmlspecialchars($roomTypeId); ?></span>
            </div>
            <div class="summary-row">
                <span>Check-in</span>
                <span><?php echo htmlspecialchars($checkin); ?></span>
            </div>
            <div class="summary-row">
                <span>Check-out</span>
                <span><?php echo htmlspecialchars($checkout); ?></span>
            </div>
            <div class="summary-row">
                <span>Nights</span>
                <span><?php echo $nights; ?></span>
            </div>
            <div class="summary-row">
                <span>Guests</span>
                <span><?php echo htmlspecialchars($guests); ?></span>
            </div>
            <div class="summary-row">
                <span>Price Per Night</span>
                <span>$<?php echo number_format($pricePerNight, 2); ?></span>
            </div>
            <div class="total-row">
                <span>Total Price</span>
                <span>$<?php echo number_format($totalPrice, 2); ?></span>
            </div>
        </div>


        <div class="form-box">
            <h2>Confirm Your Booking</h2>

            <?php if (isset($_GET['error']) && $_GET['error'] == 'failed'): ?>
                <p style="color:red; margin-bottom:15px;">
                    Booking failed. Please try again.
                </p>
            <?php endif; ?>

            <form id="bookingForm" action="index.php?action=confirm" method="POST">


                <input type="hidden" name="room_type_id" value="<?php echo htmlspecialchars($roomTypeId); ?>">
                <input type="hidden" name="checkin" value="<?php echo htmlspecialchars($checkin); ?>">
                <input type="hidden" name="checkout" value="<?php echo htmlspecialchars($checkout); ?>">
                <input type="hidden" name="guests" value="<?php echo htmlspecialchars($guests); ?>">
                <input type="hidden" name="price_per_night" value="<?php echo htmlspecialchars($pricePerNight); ?>">

                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" value="<?php echo isset($_SESSION['name']) ? htmlspecialchars($_SESSION['name']) : ''; ?>" readonly>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="text" value="<?php echo isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : ''; ?>" readonly>
                </div>

                <div class="form-group">
                    <label>Special Requests (Optional)</label>
                    <textarea name="special_requests" placeholder="Any special requests..."></textarea>
                </div>
              <div class="form-group checkbox-group">

    <label for="agreeCheck" class="checkbox-label">
        I agree to the booking terms and conditions
    </label>

    <input type="checkbox" id="agreeCheck">

</div>

                <button type="submit" class="btn-confirm">Confirm Booking</button>

            </form>
        </div>

    </div>

    <script>
        document.getElementById('bookingForm').addEventListener('submit', function(e) {
            const agree = document.getElementById('agreeCheck').checked;

            if (!agree) {
                document.getElementById('agreeError').style.display = 'block';
                e.preventDefault();
            } else {
                document.getElementById('agreeError').style.display = 'none';
            }
        });
    </script>

</body>

</html>