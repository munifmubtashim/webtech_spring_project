<?php
 
session_start();
require_once 'database.php';
require_once 'models/RoomModel.php';
 
 
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: index.php?action=login');
    exit;
}
 
$connection = connection();
$action     = $_GET['action'] ?? '';
 
 
if ($action == 'rooms') {
    $roomTypes = [];
    $result    = getAllRoomTypes($connection);
    while ($row = $result->fetch_assoc()) {
        $row['amenities'] = json_decode($row['amenities'], true);
        $roomTypes[] = $row;
    }
 
    $rooms  = [];
    $result = getAllRooms($connection);
    while ($row = $result->fetch_assoc()) {
        $rooms[] = $row;
    }
 
    include 'views/rooms.php';
} else if ($action == 'create_room_type') {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $name          = $_POST['name']           ?? '';
        $description   = $_POST['description']    ?? '';
        $pricePerNight = $_POST['price_per_night'] ?? 0;
        $maxCapacity   = $_POST['max_capacity']   ?? 1;
        $amenities     = $_POST['amenities']      ?? [];
        $thumbnailPath = '';
 
        // encode amenities as JSON
        $amenitiesJson = json_encode($amenities);
 
 
        if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] == 0) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
            $fileType     = $_FILES['thumbnail']['type'];
 
            if (in_array($fileType, $allowedTypes)) {
                $fileName      = time() . '_' . basename($_FILES['thumbnail']['name']);
                $uploadPath    = 'uploads/' . $fileName;
 
                if (move_uploaded_file($_FILES['thumbnail']['tmp_name'], $uploadPath)) {
                    $thumbnailPath = $uploadPath;
                }
            }
        }
 
        $result = createRoomType($connection, $name, $description, $pricePerNight, $maxCapacity, $amenitiesJson, $thumbnailPath);
 
        if ($result) {
            header('Location: index.php?action=rooms&success=Room type created');
            exit;
        } else {
            $error = "Failed to create room type";
        }
    }
 
    include 'views/rooms.php';
} else if ($action == 'update_room_type') {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $id            = $_POST['id']             ?? '';
        $name          = $_POST['name']           ?? '';
        $description   = $_POST['description']    ?? '';
        $pricePerNight = $_POST['price_per_night'] ?? 0;
        $maxCapacity   = $_POST['max_capacity']   ?? 1;
        $amenities     = $_POST['amenities']      ?? [];
        $amenitiesJson = json_encode($amenities);
 
        $existingResult = getRoomTypeById($connection, $id);
        $existing       = $existingResult->fetch_assoc();
        $thumbnailPath  = $existing['thumbnail_path'] ?? '';
 
 
        if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] == 0) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
            $fileType     = $_FILES['thumbnail']['type'];
 
            if (in_array($fileType, $allowedTypes)) {
                $fileName   = time() . '_' . basename($_FILES['thumbnail']['name']);
                $uploadPath = 'uploads/' . $fileName;
 
                if (move_uploaded_file($_FILES['thumbnail']['tmp_name'], $uploadPath)) {
                    $thumbnailPath = $uploadPath;
                }
            }
        }
 
        $result = updateRoomType($connection, $id, $name, $description, $pricePerNight, $maxCapacity, $amenitiesJson, $thumbnailPath);
 
        if ($result) {
            header('Location: index.php?action=rooms&success=Room type updated');
            exit;
        }
    }
} else if ($action == 'create_room') {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $roomTypeId  = $_POST['room_type_id'] ?? '';
        $roomNumber  = $_POST['room_number']  ?? '';
        $floor       = $_POST['floor']        ?? 1;
        $status      = $_POST['Status'] ?? '';
 
        $result = createRoom($connection, $roomTypeId, $roomNumber, $floor, $status);
 
        if ($result) {
            header('Location: index.php?action=rooms&success=Room created');
            exit;
        } else {
            $error = "Failed to create room. Room number may already exist.";
        }
    }
 
    include 'views/rooms.php';
} else if ($action == 'update_room') {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $id         = $_POST['id']           ?? '';
        $roomTypeId = $_POST['room_type_id'] ?? '';
        $roomNumber = $_POST['room_number']  ?? '';
        $floor      = $_POST['floor']        ?? 1;
        $status      = $_POST['Status'] ?? '';
        $result = updateRoom($connection, $id, $roomTypeId, $roomNumber, $floor, $status);
 
        if ($result) {
            header('Location: index.php?action=rooms&success=Room updated');
            exit;
        }
    }
} else if ($action == 'toggle_status') {
    $roomId    = $_POST['room_id']   ?? '';
    $newStatus = $_POST['new_status'] ?? '';
 
 
    if (!in_array($newStatus, ['available', 'maintenance'])) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Invalid status']);
        exit;
    }
 
    $result = toggleRoomStatus($connection, $roomId, $newStatus);
 
    header('Content-Type: application/json');
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Status Updated', 'new_status' => $newStatus]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Update Failed']);
    }
    exit;
}