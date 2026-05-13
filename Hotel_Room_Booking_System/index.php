<?php

$action = $_GET['action'] ?? '';


 if ($action == 'home' || $action == '')
{
    require_once 'controllers/BookingController.php';
}
else if ($action == 'results')
{
    require_once 'controllers/BookingController.php';
}
else if ($action == 'search')
{
    require_once 'controllers/BookingController.php';
}
else if ($action == 'book')
{
    require_once 'controllers/BookingController.php';
}
else if ($action == 'confirm')
{
    require_once 'controllers/BookingController.php';
}
else if ($action == 'confirmation')
{
    require_once 'controllers/BookingController.php';
}
else if ($action == 'my_bookings')
{
    require_once 'controllers/BookingController.php';
}
else if ($action == 'cancel')
{
    require_once 'controllers/BookingController.php';
}

?>

