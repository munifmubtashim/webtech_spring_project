<?php

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Rooms</title>
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
            max-width: 900px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .search-summary {
            background-color: white;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            color: #2c3e50;
            font-size: 15px;
        }

        .search-summary span {
            font-weight: bold;
            color: #f39c12;
        }


        #roomResults {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
        }

        .room-card {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .room-info h3 {
            font-size: 20px;
            color: #2c3e50;
            margin-bottom: 8px;
        }

        .room-info p {
            font-size: 14px;
            color: #777;
            margin-bottom: 6px;
        }

        .amenities {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-top: 8px;
        }

        .amenity-tag {
            background-color: #eaf0fb;
            color: #2c3e50;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 12px;
        }

        .room-action {
            text-align: right;
            min-width: 140px;
        }

        .room-action .price {
            font-size: 22px;
            font-weight: bold;
            color: #f39c12;
            margin-bottom: 10px;
        }

        .room-action .price span {
            font-size: 13px;
            color: #999;
            font-weight: normal;
        }

        .btn-book {
            background-color: #2c3e50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
        }

        .btn-book:hover {
            background-color: #f39c12;
        }


        #loading {
            text-align: center;
            padding: 40px;
            font-size: 16px;
            color: #777;
        }

        #noResults {
            text-align: center;
            padding: 40px;
            font-size: 16px;
            color: #e74c3c;
            display: none;
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

<div class="container">

    <a href="index.php?action=home" class="back-link">← Back to Search</a>


    <div class="search-summary">
        Showing rooms for
        <span><?php echo htmlspecialchars($checkin); ?></span> →
        <span><?php echo htmlspecialchars($checkout); ?></span>
        for <span><?php echo htmlspecialchars($guests); ?> guest(s)</span>
    </div>


    <div id="loading">🔍 Searching available rooms...</div>


    <div id="noResults">
        😔 No rooms available for your selected dates.
        <a href="index.php?action=home">Try different dates</a>
    </div>

    <div id="roomResults"></div>

</div>

<script>

    const checkin  = "<?php echo htmlspecialchars($checkin); ?>";
    const checkout = "<?php echo htmlspecialchars($checkout); ?>";
    const guests   = "<?php echo htmlspecialchars($guests); ?>";


    window.addEventListener('load', function() {
        searchRooms();
    });

    function searchRooms() {
        const formData = new FormData();
        formData.append('checkin',  checkin);
        formData.append('checkout', checkout);
        formData.append('guests',   guests);

        fetch('index.php?action=search', {
            method: 'POST',
            body:   formData
        })
        .then(res => res.json())
        .then(rooms => {
            document.getElementById('loading').style.display = 'none';

            if (rooms.length === 0) {
                document.getElementById('noResults').style.display = 'block';
                return;
            }


            const container = document.getElementById('roomResults');
            rooms.forEach(room => {

                let amenityHTML = '';
                if (room.amenities) {
                    room.amenities.forEach(a => {
                        amenityHTML += `<span class="amenity-tag">${a}</span>`;
                    });
                }

                container.innerHTML += `
                    <div class="room-card">
                        <div class="room-info">
                            <h3>${room.name}</h3>
                            <p>${room.description}</p>
                            <p>👥 Max Capacity: ${room.max_capacity} guests</p>
                            <div class="amenities">${amenityHTML}</div>
                        </div>
                        <div class="room-action">
                            <div class="price">
                                $${room.price_per_night}
                                <span>/ night</span>
                            </div>
                            <a href="index.php?action=book&room_type_id=${room.id}&checkin=${checkin}&checkout=${checkout}&guests=${guests}&price=${room.price_per_night}"
                               class="btn-book">
                                Book Now
                            </a>
                        </div>
                    </div>
                `;
            });
        })
        .catch(err => {
            document.getElementById('loading').style.display = 'none';
            document.getElementById('noResults').style.display = 'block';
            console.error('Search error:', err);
        });
    }
</script>

</body>
</html>