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
function createBooking($connection, $userId, $roomId, $checkin, $checkout, $totalPrice)
{
	$sql = "INSERT INTO bookings (user_id, room_id, checkin_date, checkout_date, total_price, status)
	VALUES (?, ?, ?, ?, ?, 'Pending')";

	$statement = $connection->prepare($sql);
	$statement->bind_param("iissd", $userId, $roomId, $checkin, $checkout, $totalPrice);

	if ($statement->execute()) {
		return $connection->insert_id;
	}

	return false;
}
function getBookingsByUser($connection, $userId)
{
	$sql = "SELECT b.*, rt.name AS room_type_name, r.room_number
	FROM bookings b
	JOIN rooms r ON b.room_id = r.id
	JOIN room_types rt ON r.room_type_id = rt.id
	WHERE b.user_id = ?
	ORDER BY b.created_at DESC";

	$statement = $connection->prepare($sql);
	$statement->bind_param("i", $userId);
	$statement->execute();
	return $statement->get_result();
}

function cancelBooking($connection, $bookingId, $userId)
{
	$sql = "UPDATE bookings
	SET status = 'Cancelled'
	WHERE id = ?
	AND user_id = ?
	AND status IN ('Pending', 'Confirmed')
	AND checkin_date > DATE_ADD(CURDATE(), INTERVAL 1 DAY)";

	$statement = $connection->prepare($sql);
	$statement->bind_param("ii", $bookingId, $userId);
	$statement->execute();

	if ($connection->affected_rows > 0) {
		return "Booking Cancelled Successfully";
	}

	return false;
}
?>
