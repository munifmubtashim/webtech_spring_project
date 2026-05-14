<?php
session_start();
require_once 'database.php';
require_once 'models/BookingModel.php';

$connection = connection();
$action     = $_GET['action'] ?? '';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin')
{
    header('Location: index.php?action=login');
    exit;
}

if ($action == 'dashboard')
{
    $todayArrivals   = getTodayArrivals($connection);
    $arrivals        = [];
    while ($row = $todayArrivals->fetch_assoc())
    {
        $arrivals[] = $row;
    }

    $todayDepartures = getTodayDepartures($connection);
    $departures      = [];
    while ($row = $todayDepartures->fetch_assoc())
    {
        $departures[] = $row;
    }

    $counts = getRoomSummaryCounts($connection)->fetch_assoc();

    include 'views/dashboard.php';
}
else if ($action == 'bookings')
{
    $statusFilter = $_GET['status']     ?? '';
    $dateFrom     = $_GET['date_from']  ?? '';
    $dateTo       = $_GET['date_to']    ?? '';

    $result   = getAllBookingsFiltered($connection, $statusFilter, $dateFrom, $dateTo);
    $bookings = [];
    while ($row = $result->fetch_assoc())
    {
        $bookings[] = $row;
    }

    include 'views/bookings.php';
}
else if ($action == 'checkin')
{
    $bookingId = $_POST['booking_id'] ?? '';
    $result    = checkInBooking($connection, $bookingId);

    header('Content-Type: application/json');
    if ($result)
    {
        echo json_encode(['success' => true,  'message' => 'Checked In Successfully']);
    }
    else
    {
        echo json_encode(['success' => false, 'message' => 'Check-In Failed']);
    }
    exit;
}
else if ($action == 'checkout')
{
    $bookingId = $_POST['booking_id'] ?? '';
    $result    = checkOutBooking($connection, $bookingId);

    header('Content-Type: application/json');
    if ($result)
    {
        echo json_encode(['success' => true,  'message' => 'Checked Out Successfully']);
    }
    else
    {
        echo json_encode(['success' => false, 'message' => 'Check-Out Failed']);
    }
    exit;
}
else if ($action == 'revenue')
{
    $result  = getWeeklyRevenue($connection);
    $revenue = [];
    while ($row = $result->fetch_assoc())
    {
        $revenue[] = $row;
    }

    header('Content-Type: application/json');
    echo json_encode($revenue);
    exit;
}