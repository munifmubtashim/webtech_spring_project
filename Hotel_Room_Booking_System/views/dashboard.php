<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        .container { max-width:1000px; margin:30px auto; padding:0 20px; }
        .page-title { font-size:22px; color:#2c3e50; margin-bottom:20px; font-weight:bold; }
 
        /* STAT CARDS */
        .stats {
            display:grid;
            grid-template-columns:repeat(4, 1fr);
            gap:20px;
            margin-bottom:25px;
        }
        .stat-card {
            background:white; border-radius:8px;
            padding:20px; text-align:center;
            box-shadow:0 2px 8px rgba(0,0,0,0.08);
        }
        .stat-card .number {
            font-size:36px; font-weight:bold;
            color:#f39c12; margin-bottom:5px;
        }
        .stat-card .label {
            font-size:13px; color:#777;
        }
 
        /* CHART */
        .chart-card {
            background:white; border-radius:8px;
            padding:25px;
            box-shadow:0 2px 8px rgba(0,0,0,0.08);
        }
        .chart-card h2 {
            color:#2c3e50; font-size:18px;
            margin-bottom:15px;
            border-bottom:2px solid #f39c12;
            padding-bottom:8px;
        }
    </style>
</head>
<body>
 
<nav>
    <div class="logo">🏨 Hotel Booking</div>
    <div>
        <a href="index.php?action=rooms">Manage Rooms</a>
        <a href="index.php?action=bookings">Bookings</a>
        <a href="index.php?action=dashboard">Dashboard</a>
        <a href="index.php?action=logout">Logout</a>
    </div>
</nav>
 
<div class="container">
    <div class="page-title">📊 Dashboard</div>
 
    <!-- STAT CARDS -->
    <div class="stats">
        <div class="stat-card">
            <div class="number"><?php echo isset($totalBookings) ? $totalBookings : 0; ?></div>
            <div class="label">Total Bookings</div>
        </div>
        <div class="stat-card">
            <!-- Updated label to match our live dynamic fallback logic -->
            <div class="number"><?php echo isset($todayCheckins) ? $todayCheckins : 0; ?></div>
            <div class="label">Upcoming Check-ins</div>
        </div>
        <div class="stat-card">
            <div class="number"><?php echo isset($occupiedRooms) ? $occupiedRooms : 0; ?></div>
            <div class="label">Occupied Rooms</div>
        </div>
        <div class="stat-card">
            <!-- Fixed the mathematical undefined variable bug safely using isset fallbacks -->
            <div class="number">
                <?php
                    $roomsTotal = isset($totalRooms) ? $totalRooms : 0;
                    $roomsOccupied = isset($occupiedRooms) ? $occupiedRooms : 0;
                    echo ($roomsTotal - $roomsOccupied < 0) ? 0 : ($roomsTotal - $roomsOccupied);
                ?>
            </div>
            <div class="label">Available Rooms</div>
        </div>
    </div>
 
    <!-- REVENUE CHART -->
    <div class="chart-card">
        <h2>📈 Weekly Revenue (Last 8 Weeks)</h2>
        <canvas id="revenueChart" height="100"></canvas>
    </div>
</div>
 
<script>
    // Safeguard chart array in case model or backend is empty
    const revenueData = <?php echo json_encode(isset($revenueData) ? $revenueData : []); ?>;
 
    const labels  = revenueData.map(d => d.week_start);
    const revenue = revenueData.map(d => parseFloat(d.revenue || 0));
 
    const ctx = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Revenue ($)',
                data: revenue,
                backgroundColor: '#f39c12',
                borderColor:     '#e67e22',
                borderWidth: 1,
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value;
                        }
                    }
                }
            }
        }
    });
</script>
 
</body>
</html>
 