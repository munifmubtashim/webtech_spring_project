<?php



function getAllRoomTypes($connection)
{
    $sql       = "SELECT * FROM room_types";
    $statement = $connection->prepare($sql);
    $statement->execute();
    $result = $statement->get_result();
    return $result;
}

function getRoomTypeById($connection, $roomTypeId)
{
    $sql       = "SELECT * FROM room_types WHERE id = ?";
    $statement = $connection->prepare($sql);
    $statement->bind_param("i", $roomTypeId);
    $statement->execute();
    $result = $statement->get_result();
    return $result;
}

function createRoomType($connection, $name, $description, $price_per_night, $max_capacity, $thumbnail_path, $amenities)
{
    $sql       = "INSERT INTO room_types (name, description, price_per_night, max_capacity, thumbnail_path, amenities)
                  VALUES (?, ?, ?, ?, ?, ?)";
    $statement = $connection->prepare($sql);
    $statement->bind_param("ssdiss", $name, $description, $price_per_night, $max_capacity, $thumbnail_path, $amenities);
    $result    = $statement->execute();

    if ($result && $connection->affected_rows > 0)
    {
        return "Room Type Created Successfully";
    }
    else
    {
        return false;
    }
}

function updateRoomType($connection, $id, $name, $description, $price_per_night, $max_capacity, $thumbnail_path, $amenities)
{
    $sql       = "UPDATE room_types SET name = ?, description = ?, price_per_night = ?, max_capacity = ?,
                  thumbnail_path = ?, amenities = ? WHERE id = ?";
    $statement = $connection->prepare($sql);
    $statement->bind_param("ssdissi", $name, $description, $price_per_night, $max_capacity, $thumbnail_path, $amenities, $id);
    $result    = $statement->execute();

    if ($result && $connection->affected_rows >= 0)
    {
        return "Room Type Updated Successfully";
    }
    else
    {
        return false;
    }
}

function updateRoomTypeNoImage($connection, $id, $name, $description, $price_per_night, $max_capacity, $amenities)
{
    $sql       = "UPDATE room_types SET name = ?, description = ?, price_per_night = ?, max_capacity = ?,
                  amenities = ? WHERE id = ?";
    $statement = $connection->prepare($sql);
    $statement->bind_param("ssdisi", $name, $description, $price_per_night, $max_capacity, $amenities, $id);
    $result    = $statement->execute();

    if ($result && $connection->affected_rows >= 0)
    {
        return "Room Type Updated Successfully";
    }
    else
    {
        return false;
    }
}


function deleteRoomType($connection, $id)
{
    $sql       = "DELETE FROM room_types WHERE id = ?";
    $statement = $connection->prepare($sql);
    $statement->bind_param("i", $id);
    $result    = $statement->execute();

    if ($result && $connection->affected_rows > 0)
    {
        return "Room Type Deleted Successfully";
    }
    else
    {
        return false;
    }
}

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


function getAllRoomsWithOccupancy($connection)
{
    $sql       = "SELECT r.*, rt.name AS room_type_name, rt.price_per_night,
                  CASE
                      WHEN r.status = 'maintenance' THEN 'Maintenance'
                      WHEN b.id IS NOT NULL THEN 'Booked'
                      ELSE 'Available'
                  END AS occupancy_status
                  FROM rooms r
                  JOIN room_types rt ON r.room_type_id = rt.id
                  LEFT JOIN bookings b ON b.room_id = r.id
                      AND b.status IN ('Confirmed', 'Checked-In')
                      AND b.checkin_date >= CURDATE()
                  GROUP BY r.id
                  ORDER BY r.room_number ASC";
    $statement = $connection->prepare($sql);
    $statement->execute();
    $result = $statement->get_result();
    return $result;
}


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


function getRoomsByType($connection, $roomTypeId)
{
    $sql       = "SELECT * FROM rooms WHERE room_type_id = ?";
    $statement = $connection->prepare($sql);
    $statement->bind_param("i", $roomTypeId);
    $statement->execute();
    $result = $statement->get_result();
    return $result;
}


function roomNumberExists($connection, $room_number, $exclude_id = null)
{
    if ($exclude_id)
    {
        $sql       = "SELECT id FROM rooms WHERE room_number = ? AND id != ?";
        $statement = $connection->prepare($sql);
        $statement->bind_param("si", $room_number, $exclude_id);
    }
    else
    {
        $sql       = "SELECT id FROM rooms WHERE room_number = ?";
        $statement = $connection->prepare($sql);
        $statement->bind_param("s", $room_number);
    }
    $statement->execute();
    $result = $statement->get_result();
    return $result->num_rows > 0;
}


function hasFutureBookings($connection, $roomId)
{
    $sql       = "SELECT id FROM bookings
                  WHERE room_id = ?
                    AND status IN ('Confirmed', 'Checked-In')
                    AND checkin_date >= CURDATE()
                  LIMIT 1";
    $statement = $connection->prepare($sql);
    $statement->bind_param("i", $roomId);
    $statement->execute();
    $result = $statement->get_result();
    return $result->num_rows > 0;
}


function createRoom($connection, $room_number, $floor, $room_type_id, $status)
{
    $sql       = "INSERT INTO rooms (room_number, floor, room_type_id, status) VALUES (?, ?, ?, ?)";
    $statement = $connection->prepare($sql);
    $statement->bind_param("siis", $room_number, $floor, $room_type_id, $status);
    $result    = $statement->execute();

    if ($result && $connection->affected_rows > 0)
    {
        return "Room Created Successfully";
    }
    else
    {
        return false;
    }
}

function updateRoom($connection, $id, $room_number, $floor, $room_type_id, $status)
{
    $sql       = "UPDATE rooms SET room_number = ?, floor = ?, room_type_id = ?, status = ? WHERE id = ?";
    $statement = $connection->prepare($sql);
    $statement->bind_param("siisi", $room_number, $floor, $room_type_id, $status, $id);
    $result    = $statement->execute();

    if ($result && $connection->affected_rows >= 0)
    {
        return "Room Updated Successfully";
    }
    else
    {
        return false;
    }
}


function deleteRoom($connection, $roomId)
{
    $sql       = "DELETE FROM rooms WHERE id = ?";
    $statement = $connection->prepare($sql);
    $statement->bind_param("i", $roomId);
    $result    = $statement->execute();

    if ($result && $connection->affected_rows > 0)
    {
        return "Room Deleted Successfully";
    }
    else
    {
        return false;
    }
}

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