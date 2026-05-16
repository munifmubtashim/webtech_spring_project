<?php
// Ensure session details are running on this layout file
if (!isset($_SESSION)) {
    session_start();
}
 
// Fallback arrays to silence structural engine exceptions completely
$roomTypes = $roomTypes ?? [];
$rooms     = $rooms ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Rooms</title>
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
        .card {
            background:white; border-radius:8px;
            padding:25px; margin-bottom:25px;
            box-shadow:0 2px 8px rgba(0,0,0,0.08);
        }
        .card h2 {
            color:#2c3e50; font-size:18px;
            margin-bottom:15px;
            border-bottom:2px solid #f39c12;
            padding-bottom:8px;
        }
        .form-row {
            display:grid;
            grid-template-columns:1fr 1fr;
            gap:15px;
        }
        .form-group { margin-bottom:15px; }
        .form-group label { display:block; margin-bottom:5px; color:#555; font-size:14px; }
        .form-group input,
        .form-group select,
        .form-group textarea {
            width:100%; padding:10px;
            border:1px solid #ddd;
            border-radius:5px; font-size:14px;
        }
        .form-group textarea { height:70px; resize:vertical; }
        .amenity-group { display:flex; flex-wrap:wrap; gap:10px; margin-top:5px; }
        .amenity-group label { display:flex; align-items:center; gap:5px; font-size:14px; color:#555; }
        .btn {
            padding:10px 20px;
            background-color:#f39c12;
            color:white; border:none;
            border-radius:5px; font-size:14px;
            cursor:pointer;
        }
        .btn:hover { background-color:#e67e22; }
        .success { color:green; font-size:13px; margin-bottom:15px; }
        .error   { color:red;   font-size:13px; margin-bottom:15px; }
 
     
        table { width:100%; border-collapse:collapse; margin-top:10px; }
        th { background-color:#2c3e50; color:white; padding:10px; text-align:left; font-size:14px; }
        td { padding:10px; border-bottom:1px solid #f4f4f4; font-size:14px; color:#555; }
        tr:hover td { background-color:#fafafa; }
 
       
        .badge {
            padding:4px 12px;
            border-radius:20px;
            font-size:12px;
            font-weight:bold;
            cursor:pointer;
        }
        .badge-available    { background:#eafaf1; color:#27ae60; border:1px solid #27ae60; }
        .badge-maintenance  { background:#fdedec; color:#e74c3c; border:1px solid #e74c3c; }
 
        .thumbnail { width:60px; height:40px; object-fit:cover; border-radius:4px; }
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
    <div class="page-title">🏨 Room Management</div>
 
    <?php if (isset($_GET['success'])): ?>
        <p class="success">✅ <?php echo htmlspecialchars($_GET['success']); ?></p>
    <?php endif; ?>
 
    <?php if (isset($error)): ?>
        <p class="error">❌ <?php echo $error; ?></p>
    <?php endif; ?>
 
    <!-- ADD ROOM TYPE FORM -->
    <div class="card">
        <h2>Add Room Type</h2>
        <form action="index.php?action=create_room_type" method="POST" enctype="multipart/form-data">
            <div class="form-row">
                <div class="form-group">
                    <label>Name</label>
                    <select name="name" required>
                        <option value="">Select Type</option>
                        <option value="Standard">Standard</option>
                        <option value="Deluxe">Deluxe</option>
                        <option value="Suite">Suite</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Price Per Night ($)</label>
                    <input type="number" name="price_per_night" step="0.01" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Max Capacity</label>
                    <input type="number" name="max_capacity" min="1" required>
                </div>
                <div class="form-group">
                    <label>Thumbnail Image</label>
                    <input type="file" name="thumbnail" accept="image/*">
                </div>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description"></textarea>
            </div>
            <div class="form-group">
                <label>Amenities</label>
                <div class="amenity-group">
                    <label><input type="checkbox" name="amenities[]" value="WiFi"> WiFi</label>
                    <label><input type="checkbox" name="amenities[]" value="AC"> AC</label>
                    <label><input type="checkbox" name="amenities[]" value="TV"> TV</label>
                    <label><input type="checkbox" name="amenities[]" value="Mini-bar"> Mini-bar</label>
                    <label><input type="checkbox" name="amenities[]" value="Safe"> Safe</label>
                    <label><input type="checkbox" name="amenities[]" value="Bathtub"> Bathtub</label>
                    <label><input type="checkbox" name="amenities[]" value="Balcony"> Balcony</label>
                </div>
            </div>
            <button type="submit" class="btn">Add Room Type</button>
        </form>
    </div>
 
  
    <div class="card">
        <h2>Room Types</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Image</th>
                <th>Name</th>
                <th>Price/Night</th>
                <th>Capacity</th>
                <th>Amenities</th>
            </tr>
            <?php foreach ($roomTypes as $rt): ?>
            <tr>
                <td><?php echo $rt['id']; ?></td>
                <td>
                    <?php if ($rt['thumbnail_path']): ?>
                        <img src="<?php echo htmlspecialchars($rt['thumbnail_path']); ?>" class="thumbnail">
                    <?php else: ?>
                        —
                    <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($rt['name']); ?></td>
                <td>$<?php echo number_format($rt['price_per_night'], 2); ?></td>
                <td><?php echo $rt['max_capacity']; ?></td>
                <td>
                    <?php if ($rt['amenities']): ?>
                        <?php echo implode(', ', $rt['amenities']); ?>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
 
    
    <div class="card">
        <h2>Add Room</h2>
        <form action="index.php?action=create_room" method="POST">
            <div class="form-row">
                <div class="form-group">
                    <label>Room Type</label>
                    <select name="room_type_id" required>
                        <option value="">Select Type</option>
                        <?php foreach ($roomTypes as $rt): ?>
                            <option value="<?php echo $rt['id']; ?>">
                                <?php echo htmlspecialchars($rt['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Room Number</label>
                    <input type="text" name="room_number" required>
                </div>
            </div>
            <div class="form-group">
                <label>Floor</label>
                <input type="number" name="floor" min="1" required>
            </div>
            <button type="submit" class="btn">Add Room</button>
        </form>
    </div>
 
   
    <div class="card">
        <h2>Rooms</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Room Number</th>
                <th>Type</th>
                <th>Floor</th>
                <th>Status</th>
            </tr>
            <?php foreach ($rooms as $room): ?>
            <tr>
                <td><?php echo $room['id']; ?></td>
                <td><?php echo htmlspecialchars($room['room_number']); ?></td>
                <td><?php echo htmlspecialchars($room['room_type_name']); ?></td>
                <td><?php echo $room['floor']; ?></td>
                <td>
                    <?php
                        $badgeClass = $room['status'] == 'available'
                            ? 'badge-available'
                            : 'badge-maintenance';
                        $newStatus  = $room['status'] == 'available'
                            ? 'maintenance'
                            : 'available';
                    ?>
                    <span class="badge <?php echo $badgeClass; ?>"
                          id="badge-<?php echo $room['id']; ?>"
                          onclick="toggleStatus(<?php echo $room['id']; ?>, '<?php echo $newStatus; ?>')">
                        <?php echo ucfirst($room['status']); ?>
                    </span>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
 
</div>
 
<script src="js/toggle_status.js"></script>
 
</body>
</html>