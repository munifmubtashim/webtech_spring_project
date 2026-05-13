<?php


session_start();
require_once  './Hotel_Room_Booking_System/database.php';
require_once  '/../models/BookingModel.php';

$connection = connection();
$action     = $_GET['action'] ?? '';


if ($action == 'search')
{
    $checkin  = $_POST['checkin']  ?? '';
    $checkout = $_POST['checkout'] ?? '';
    $guests   = $_POST['guests']   ?? 1;

    $result = getAvailableRoomTypes($connection, $checkin, $checkout, $guests);

    $rooms = [];
    while ($row = $result->fetch_assoc())
    {
        $row['amenities'] = json_decode($row['amenities'], true);
        $rooms[] = $row;
    }

    header('Content-Type: application/json');
    echo json_encode($rooms);
    exit;
}


else if ($action == 'home' || $action == '')
{
    include  '/../views/home.php';
}


else if ($action == 'results')
{
    $checkin  = $_GET['checkin']  ?? '';
    $checkout = $_GET['checkout'] ?? '';
    $guests   = $_GET['guests']   ?? 1;

    include  '/../views/results.php';
}


else if ($action == 'book')
{
    if (!isset($_SESSION['user_id']))
    {
        header('Location: index.php?action=login');
        exit;
    }

    $roomTypeId = $_GET['room_type_id'] ?? '';
    $checkin    = $_GET['checkin']      ?? '';
    $checkout   = $_GET['checkout']     ?? '';
    $guests     = $_GET['guests']       ?? 1;

    include  '/../views/book.php';
}


else if ($action == 'confirm')
{
    if (!isset($_SESSION['user_id']))
    {
        header('Location: index.php?action=login');
        exit;
    }

    $userId     = $_SESSION['user_id'];
    $roomTypeId = $_POST['room_type_id'] ?? '';
    $checkin    = $_POST['checkin']      ?? '';
    $checkout   = $_POST['checkout']     ?? '';
    $guests     = $_POST['guests']       ?? 1;
    $pricePerNight = $_POST['price_per_night'] ?? 0;

    $nights     = (strtotime($checkout) - strtotime($checkin)) / 86400;
    $totalPrice = $nights * $pricePerNight;


    $roomResult = getAvailableRoom($connection, $roomTypeId, $checkin, $checkout);
    $room       = $roomResult->fetch_assoc();

    if (!$room)
    {

        header('Location: index.php?action=results&checkin=' . $checkin . '&checkout=' . $checkout . '&guests=' . $guests . '&error=noroom');
        exit;
    }

    $bookingId = createBooking($connection, $userId, $room['id'], $checkin, $checkout, $totalPrice);

    if ($bookingId)
    {
        header('Location: index.php?action=confirmation&booking_id=' . $bookingId);
        exit;
    }
    else
    {
        header('Location: index.php?action=book&error=failed');
        exit;
    }
}

else if ($action == 'confirmation')
{
    $bookingId = $_GET['booking_id'] ?? '';

    $result  = getBookingById($connection, $bookingId);
    $booking = $result->fetch_assoc();

    include  '/../views/confirmation.php';
}


else if ($action == 'my_bookings')
{
    if (!isset($_SESSION['user_id']))
    {
        header('Location: index.php?action=login');
        exit;
    }

    $userId = $_SESSION['user_id'];
    $result = getBookingsByUser($connection, $userId);

    $bookings = [];
    while ($row = $result->fetch_assoc())
    {
        $bookings[] = $row;
    }

    include  '/../views/my_bookings.php';
}



else if ($action == 'cancel')
{
    if (!isset($_SESSION['user_id']))
    {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Not logged in']);
        exit;
    }

    $userId    = $_SESSION['user_id'];
    $bookingId = $_POST['booking_id'] ?? '';

    $result = cancelBooking($connection, $bookingId, $userId);

    header('Content-Type: application/json');
    if ($result)
    {
        echo json_encode(['success' => true,  'message' => 'Booking Cancelled Successfully']);
    }
    else
    {
        echo json_encode(['success' => false, 'message' => 'Cancel Failed']);
    }
    exit;
}
?>