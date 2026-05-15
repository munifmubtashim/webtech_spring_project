<?php
// index.php
$action = $_GET['action'] ?? '';

// ==================== STUDENT 1 ====================
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

// ==================== STUDENT 2 ====================
else if ($action == 'rooms'            ||
         $action == 'create_room_type' ||
         $action == 'update_room_type' ||
         $action == 'create_room'      ||
         $action == 'update_room'      ||
         $action == 'toggle_status')
{
    require_once 'controllers/RoomController.php';
}

// ==================== STUDENT 3 ====================
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

// ==================== STUDENT 4 ====================
else if ($action == 'bookings'  ||
         $action == 'dashboard' ||
         $action == 'checkin'   ||
         $action == 'checkout'  ||
         $action == 'revenue')
{
    require_once 'controllers/AdminController.php';
}

// ==================== 404 ====================
else
{
    echo "<h2>404 — Page Not Found</h2>";
}
?>