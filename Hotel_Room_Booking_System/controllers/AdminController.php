<?php
// controllers/AdminController.php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require_once 'database.php';
require_once 'models/BookingModel.php';

// only admin can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin')
{
    header('Location: index.php?action=login');
    exit;
}

$connection = connection();
$action     = $_GET['action'] ?? '';

// ==================== BOOKINGS LIST ====================
if ($action == 'bookings')
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
else if ($action == 'checkin')
{
    $bookingId = $_POST['booking_id'] ?? '';

    $result = checkInBooking($connection, $bookingId);

    header('Content-Type: application/json');
    if ($result)
    {
        echo json_encode(['success' => true,  'message' => 'Checked In Successfully']);
    }
    else
    {
        echo json_encode(['success' => false, 'message' => 'Check In Failed']);
    }
    exit;
}

// ==================== CHECKOUT (AJAX) ====================
else if ($action == 'checkout')
{
    $bookingId = $_POST['booking_id'] ?? '';

    $result = checkOutBooking($connection, $bookingId);

    header('Content-Type: application/json');
    if ($result)
    {
        echo json_encode(['success' => true,  'message' => 'Checked Out Successfully']);
    }
    else
    {
        echo json_encode(['success' => false, 'message' => 'Check Out Failed']);
    }
    exit;
}

// ==================== DASHBOARD ====================
else if ($action == 'dashboard')
{
    // total bookings
    $totalBookings = getTotalBookings($connection);

    // today's checkins
    $todayCheckins = getTodayCheckins($connection);

    // occupancy — rooms booked today
    $occupiedRooms  = getOccupiedRoomsCount($connection);
    $totalRooms     = getTotalRoomsCount($connection);

    // revenue data for chart (last 8 weeks)
    $revenueData = getWeeklyRevenue($connection);

    include 'views/dashboard.php';
}

// ==================== REVENUE (AJAX for chart) ====================
else if ($action == 'revenue')
{
    $result = getWeeklyRevenue($connection);

    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
}