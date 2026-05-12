<?php
include "./Hotel_Room_Booking_System/database.php";

function getAvailableRoomTypes($connection, $checkin, $checkout, $guests)
{
    $sql = "SELECT rt.id, rt.name, rt.description, rt.price_per_night, rt.max_capacity, rt.amenities FROM room_types rt
            WHERE rt.max_capacity >= ? AND EXISTS ( SELECT 1 FROM rooms r WHERE r.room_type_id = rt.id
            AND r.status = 'available' AND r.id NOT IN ( SELECT b.room_id FROM bookings b WHERE b.status IN ('Confirmed', 'Checked-In')
            AND b.checkin_date  < ? AND b.checkout_date > ? )
            )";

    $statement = $connection->prepare($sql);
    $statement->bind_param("iss", $guests, $checkout, $checkin);
    $statement->execute();
    $result = $statement->get_result();
    return $result;
}

function getAvailableRoom($connection, $roomTypeId, $checkin, $checkout)
{
    $sql = "SELECT r.id FROM rooms r WHERE r.room_type_id = ? AND r.status = 'available'AND r.id NOT IN 
            (SELECT b.room_id FROM bookings b WHERE b.status IN ('Confirmed', 'Checked-In') AND b.checkin_date  < ?
            AND b.checkout_date > ?)
            LIMIT 1";

    $statement = $connection->prepare($sql);
    $statement->bind_param("iss", $roomTypeId, $checkout, $checkin);
    $statement->execute();
    $result = $statement->get_result();
    return $result;
}

?>
