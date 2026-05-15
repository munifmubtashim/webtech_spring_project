<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin — Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

        .summary-cards { display: flex; gap: 20px; flex-wrap: wrap; margin-bottom: 30px; }
        .card {
            background: white;
            padding: 25px 35px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            flex: 1;
            min-width: 160px;
            text-align: center;
            border-top: 4px solid #f39c12;
        }
        .card .number { font-size: 38px; font-weight: bold; color: #2c3e50; }
        .card .label  { font-size: 13px; color: #777; margin-top: 6px; }
        .card.green  { border-top-color: #27ae60; }
        .card.blue   { border-top-color: #2980b9; }
        .card.orange { border-top-color: #f39c12; }
        .card.red    { border-top-color: #e74c3c; }

        .section {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 25px;
        }
        .section h3 { color: #2c3e50; font-size: 17px; margin-bottom: 15px; border-bottom: 2px solid #f39c12; padding-bottom: 8px; }

        table { width: 100%; border-collapse: collapse; }
        th { background: #2c3e50; color: white; padding: 11px 14px; text-align: left; font-size: 13px; }
        td { padding: 11px 14px; font-size: 13px; border-bottom: 1px solid #eee; color: #333; }
        tr:hover td { background: #fafafa; }
        .no-data { color: #888; padding: 15px 0; font-size: 14px; }

        #revenueChart { max-height: 300px; }
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

<div class="page-header">📊 Occupancy Dashboard</div>

<div class="container">

    <div class="summary-cards">
        <div class="card blue">
            <div class="number"><?= $counts['total_rooms'] ?></div>
            <div class="label">Total Rooms</div>
        </div>
        <div class="card green">
            <div class="number"><?= $counts['occupied_rooms'] ?></div>
            <div class="label">Occupied</div>
        </div>
        <div class="card orange">
            <div class="number"><?= $counts['available_rooms'] ?></div>
            <div class="label">Available</div>
        </div>
        <div class="card red">
            <div class="number"><?= $counts['maintenance_rooms'] ?></div>
            <div class="label">Maintenance</div>
        </div>
    </div>

    <div class="section">
        <h3>📥 Today's Arrivals</h3>
        <?php if (empty($arrivals)): ?>
            <p class="no-data">No arrivals today.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr><th>Booking ID</th><th>Guest</th><th>Room</th><th>Type</th><th>Check-In</th></tr>
                </thead>
                <tbody>
                <?php foreach ($arrivals as $row): ?>
                    <tr>
                        <td>#<?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['guest_name']) ?></td>
                        <td><?= htmlspecialchars($row['room_number']) ?></td>
                        <td><?= htmlspecialchars($row['room_type_name']) ?></td>
                        <td><?= $row['checkin_date'] ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <div class="section">
        <h3>📤 Today's Departures</h3>
        <?php if (empty($departures)): ?>
            <p class="no-data">No departures today.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr><th>Booking ID</th><th>Guest</th><th>Room</th><th>Type</th><th>Check-Out</th></tr>
                </thead>
                <tbody>
                <?php foreach ($departures as $row): ?>
                    <tr>
                        <td>#<?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['guest_name']) ?></td>
                        <td><?= htmlspecialchars($row['room_number']) ?></td>
                        <td><?= htmlspecialchars($row['room_type_name']) ?></td>
                        <td><?= $row['checkout_date'] ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <div class="section">
        <h3>💰 Weekly Revenue — Last 8 Weeks</h3>
        <canvas id="revenueChart"></canvas>
    </div>

</div>

<script>
fetch('index.php?action=revenue')
    .then(res => res.json())
    .then(data => {
        const labels = data.map(d => 'Week ' + d.week_number + ' (' + d.week_start + ')');
        const values = data.map(d => parseFloat(d.total_revenue));
        const ctx    = document.getElementById('revenueChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Revenue ($)',
                    data: values,
                    backgroundColor: 'rgba(243, 156, 18, 0.7)',
                    borderColor: '#e67e22',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });
    });
</script>

</body>
</html>