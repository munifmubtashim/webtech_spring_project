<?php
// index.php
$action = $_GET['action'] ?? '';

if ($action == 'register' || $action == 'login' || $action == 'logout') {
    require_once 'controllers/AuthController.php';
} else if ($action == 'profile') {
    require_once 'controllers/ProfileController.php';
} else if ($action == 'rooms' || $action == 'toggle_status') {
    require_once 'controllers/RoomController.php';
} else if (
    $action == 'home'    || $action == 'results'  ||
    $action == 'search'  || $action == 'book'     ||
    $action == 'confirm' || $action == 'confirmation' ||
    $action == 'my_bookings' || $action == 'cancel' || $action == ''
) {
    require_once 'controllers/BookingController.php';
} else if (
    $action == 'bookings'  || $action == 'dashboard' ||
    $action == 'checkin'   || $action == 'revenue'
) {
    require_once 'controllers/AdminController.php';
} else {
    echo "<h2>404 — Page Not Found</h2>";
}
