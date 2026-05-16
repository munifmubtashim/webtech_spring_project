<?php
// index.php

$action = $_GET['action'] ?? '';

if ($action == 'login'    ||
    $action == 'logout'   ||
    $action == 'register')
{
    require_once 'controllers/AuthController.php';
}
else if ($action == 'profile')
{
    require_once 'controllers/ProfileController.php';
}
else if ($action == 'rooms'            ||
         $action == 'create_room_type' ||
         $action == 'update_room_type' ||
         $action == 'create_room'      ||
         $action == 'update_room'      ||
         $action == 'toggle_status')
{
    require_once 'controllers/RoomController.php';
}
else if ($action == 'home'         ||
         $action == 'results'      ||
         $action == 'search'       ||
         $action == 'book'         ||
         $action == 'confirm'      || 
         $action == 'confirmation' ||
         $action == 'my_bookings'  ||
         $action == 'cancel'       ||
         $action == '')
{
    require_once 'controllers/BookingController.php';
}
else if ($action == 'bookings'      ||
         $action == 'dashboard'     ||
         $action == 'checkin'       ||
         $action == 'checkout'      ||
         $action == 'revenue'       ||
         $action == 'admin_confirm') 
{
    require_once 'controllers/AdminController.php';
}
else
{
    echo "<h2 style='text-align:center; margin-top:50px;'>404 — Page Not Found</h2>";
}
?>