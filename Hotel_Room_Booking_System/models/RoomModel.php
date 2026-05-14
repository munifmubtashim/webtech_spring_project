<?php
// models/RoomModel.php

// ==================== GET ALL ROOM TYPES ====================
function getAllRoomTypes($connection)
{
    $sql       = "SELECT * FROM room_types";
    $statement = $connection->prepare($sql);
    $statement->execute();
    $result = $statement->get_result();
    return $result;
}

// ==================== GET ROOM TYPE BY ID ====================
function getRoomTypeById($connection, $roomTypeId)
{
    $sql       = "SELECT * FROM room_types WHERE id = ?";
    $statement = $connection->prepare($sql);
    $statement->bind_param("i", $roomTypeId);
    $statement->execute();
    $result = $statement->get_result();
    return $result;
}

// ==================== GET ALL ROOMS ====================
function getAllRooms($connection)
{
    $sql       = "SELECT r.*, rt.name AS room_type_name, rt.price_per_night
                  FROM rooms r
                  JOIN room_types rt ON r.room_type_id = rt.id
                  ORDER BY r.room_number";
    $statement = $connection->prepare($sql);
    $statement->execute();
    $result = $statement->get_result();
    return $result;
}

// ==================== GET ROOM BY ID ====================
function getRoomById($connection, $roomId)
{
    $sql       = "SELECT r.*, rt.name AS room_type_name, rt.price_per_night
                  FROM rooms r
                  JOIN room_types rt ON r.room_type_id = rt.id
                  WHERE r.id = ?";
    $statement = $connection->prepare($sql);
    $statement->bind_param("i", $roomId);
    $statement->execute();
    $result = $statement->get_result();
    return $result;
}

// ==================== GET ROOMS BY TYPE ====================
function getRoomsByType($connection, $roomTypeId)
{
    $sql       = "SELECT * FROM rooms WHERE room_type_id = ?";
    $statement = $connection->prepare($sql);
    $statement->bind_param("i", $roomTypeId);
    $statement->execute();
    $result = $statement->get_result();
    return $result;
}

// ==================== UPDATE ROOM STATUS ====================
// Used by Student 2 AJAX toggle
function updateRoomStatus($connection, $roomId, $status)
{
    $sql       = "UPDATE rooms SET status = ? WHERE id = ?";
    $statement = $connection->prepare($sql);
    $statement->bind_param("si", $status, $roomId);
    $result    = $statement->execute();

    if ($result && $connection->affected_rows > 0)
    {
        return "Status Updated Successfully";
    }
    else
    {
        return false;
    }
}