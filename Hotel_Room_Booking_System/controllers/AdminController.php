<?php
// controllers/AdminController.php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require_once 'database.php';
require_once 'models/BookingModel.php';

// Only admin can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin')
{
    header('Location: index.php?action=login');
    exit;
}

$connection = connection();
$action     = $_GET['action'] ?? '';

// ==================== BOOKINGS LIST ====================
if ($action === 'bookings')
{
    $status = $_GET['status'] ?? '';
    $date   = $_GET['date']   ?? '';

    $bookings = [];
    $result   = getAllBookings($connection, $status, $date);
    while ($row = $result->fetch_assoc())
    {
        $bookings[] = $row;
    }

    include 'views/bookings.php';
}

// ==================== CHECKIN (AJAX) ====================
else if ($action === 'checkin')
{
    $bookingId = $_POST['booking_id'] ?? '';
    $result = checkInBooking($connection, $bookingId);

    header('Content-Type: application/json');
    echo json_encode(['success' => (bool)$result, 'message' => $result ? 'Checked In Successfully' : 'Check In Failed']);
    exit;
}

// ==================== CHECKOUT (AJAX) ====================
else if ($action === 'checkout')
{
    $bookingId = $_POST['booking_id'] ?? '';
    $result = checkOutBooking($connection, $bookingId);

    header('Content-Type: application/json');
    echo json_encode(['success' => (bool)$result, 'message' => $result ? 'Checked Out Successfully' : 'Check Out Failed']);
    exit;
}

// ==================== ADMIN CONFIRM (AJAX) ====================
else if ($action === 'admin_confirm')
{
    $bookingId = $_POST['booking_id'] ?? '';
    $result = confirmBooking($connection, $bookingId);

    header('Content-Type: application/json');
    echo json_encode(['success' => (bool)$result, 'message' => $result ? 'Booking Confirmed Successfully' : 'Confirmation Failed']);
    exit;
}

// ==================== DASHBOARD ====================
else if ($action === 'dashboard')
{
    $totalBookings = getTotalBookings($connection);
    $todayCheckins = getTodayCheckins($connection);
    $occupiedRooms  = getOccupiedRoomsCount($connection);
    $totalRooms     = getTotalRoomsCount($connection);
    $revenueData = getWeeklyRevenue($connection);

    include 'views/dashboard.php';
}

// ==================== REVENUE (AJAX) ====================
else if ($action === 'revenue')
{
    $result = getWeeklyRevenue($connection);
    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
}

// ==================== MANAGE ROOMS LAYOUT ====================
else if ($action === 'rooms')
{
    $roomTypes = [];
    $rooms     = [];

    $roomTypesResult = getAllRoomTypes($connection);
    if ($roomTypesResult instanceof mysqli_result) {
        while ($row = $roomTypesResult->fetch_assoc()) {
            $row['amenities'] = !empty($row['amenities']) ? json_decode($row['amenities'], true) : [];
            $roomTypes[] = $row;
        }
    }

    $roomsResult = getAllRooms($connection);
    if ($roomsResult instanceof mysqli_result) {
        while ($row = $roomsResult->fetch_assoc()) {
            $rooms[] = $row;
        }
    }

    include 'views/rooms.php';
}

// ==================== CREATE ROOM TYPE ACTION ====================
else if ($action === 'create_room_type')
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name            = $_POST['name'] ?? '';
        $price_per_night = $_POST['price_per_night'] ?? 0;
        $max_capacity    = $_POST['max_capacity'] ?? 1;
        $description     = $_POST['description'] ?? '';
        $amenitiesArr    = $_POST['amenities'] ?? [];
        
        $amenitiesJson   = json_encode($amenitiesArr);
        
        $thumbnailPath = null;
        if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $fileName = time() . '_' . basename($_FILES['thumbnail']['name']);
            $targetPath = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['thumbnail']['tmp_name'], $targetPath)) {
                $thumbnailPath = $targetPath;
            }
        }

        $result = createRoomType($connection, $name, $description, $price_per_night, $max_capacity, $thumbnailPath, $amenitiesJson);
        
        header('Location: index.php?action=rooms&' . ($result ? 'success=Room+Type+Added' : 'error=Failed+to+add+room+type'));
        exit;
    }
}

// ==================== CREATE ROOM ACTION ====================
else if ($action === 'create_room')
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $room_type_id = $_POST['room_type_id'] ?? '';
        $room_number  = $_POST['room_number'] ?? '';
        $floor        = $_POST['floor'] ?? 1;
        
        $result = createRoom($connection, $room_type_id, $room_number, $floor);
        
        header('Location: index.php?action=rooms&' . ($result ? 'success=Room+Added' : 'error=Failed+to+add+room'));
        exit;
    }
}