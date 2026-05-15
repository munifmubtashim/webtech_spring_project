<?php

session_start();
require_once 'database.php';
require_once 'models/UserModel.php';
require_once 'models/BookingModel.php';

if (!isset($_SESSION['user_id']))
{
    header('Location: index.php?action=login');
    exit;
}

$connection = connection();
$userId     = $_SESSION['user_id'];


$result = getUserById($connection, $userId);
$user   = $result->fetch_assoc();


$bookingResult = getBookingsByUser($connection, $userId);
$upcomingBooking = null;
while ($row = $bookingResult->fetch_assoc())
{
    if (in_array($row['status'], ['Pending', 'Confirmed']) &&
        strtotime($row['checkin_date']) >= strtotime('today'))
    {
        $upcomingBooking = $row;
        break;
    }
}


if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    $name                  = $_POST['name']                   ?? '';
    $phone                 = $_POST['phone']                  ?? '';
    $nationality           = $_POST['nationality']            ?? '';
    $specialRequests       = $_POST['special_requests']       ?? '';
    $preferredRoomTypeId   = $_POST['preferred_room_type_id'] ?? null;

    $result = updateProfile($connection, $userId, $name, $phone, $nationality, $specialRequests, $preferredRoomTypeId);

    if ($result)
    {
        $_SESSION['name'] = $name;
        $success = "Profile Updated Successfully";

  
        $userResult = getUserById($connection, $userId);
        $user       = $userResult->fetch_assoc();
    }
    else
    {
        $error = "Update failed. Please try again.";
    }
}

include 'views/profile.php';