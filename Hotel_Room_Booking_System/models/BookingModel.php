
<?php

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
    $sql = "SELECT r.id FROM rooms r WHERE r.room_type_id = ? AND r.status = 'available' AND r.id NOT IN
            (SELECT b.room_id FROM bookings b WHERE b.status IN ('Confirmed', 'Checked-In') AND b.checkin_date < ?
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

function getBookingById($connection, $bookingId)
{
    $sql = "SELECT b.*, rt.name AS room_type_name, r.room_number
    FROM bookings b
    JOIN rooms r ON b.room_id = r.id
    JOIN room_types rt ON r.room_type_id = rt.id
    WHERE b.id = ?";

    $statement = $connection->prepare($sql);
    $statement->bind_param("i", $bookingId);
    $statement->execute();
    return $statement->get_result();
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



function getAllBookingsFiltered($connection, $statusFilter, $dateFrom, $dateTo)
{
    $sql = "SELECT b.*, u.name AS guest_name, r.room_number, rt.name AS room_type_name
            FROM bookings b
            JOIN users u ON b.user_id = u.id
            JOIN rooms r ON b.room_id = r.id
            JOIN room_types rt ON r.room_type_id = rt.id
            WHERE 1=1";

    $params = [];
    $types  = '';

    if (!empty($statusFilter))
    {
        $sql     .= " AND b.status = ?";
        $types   .= 's';
        $params[] = $statusFilter;
    }
    if (!empty($dateFrom))
    {
        $sql     .= " AND b.checkin_date >= ?";
        $types   .= 's';
        $params[] = $dateFrom;
    }
    if (!empty($dateTo))
    {
        $sql     .= " AND b.checkout_date <= ?";
        $types   .= 's';
        $params[] = $dateTo;
    }

    $sql .= " ORDER BY b.created_at DESC";

    $statement = $connection->prepare($sql);
    if (!empty($params))
    {
        $statement->bind_param($types, ...$params);
    }
    $statement->execute();
    return $statement->get_result();
}

function checkInBooking($connection, $bookingId)
{
    $sql = "UPDATE bookings
            SET status = 'Checked-In', actual_checkin = NOW()
            WHERE id = ?
            AND status = 'Confirmed'
            AND checkin_date = CURDATE()";

    $statement = $connection->prepare($sql);
    $statement->bind_param("i", $bookingId);
    $statement->execute();

    if ($connection->affected_rows > 0) {
        return true;
    }

    return false;
}

function checkOutBooking($connection, $bookingId)
{
    $sql = "UPDATE bookings
            SET status = 'Checked-Out'
            WHERE id = ?
            AND status = 'Checked-In'";

    $statement = $connection->prepare($sql);
    $statement->bind_param("i", $bookingId);
    $statement->execute();

    if ($connection->affected_rows > 0)
    {
        $sqlRoom = "UPDATE rooms r
                    JOIN bookings b ON r.id = b.room_id
                    SET r.status = 'available'
                    WHERE b.id = ?";
        $stmtRoom = $connection->prepare($sqlRoom);
        $stmtRoom->bind_param("i", $bookingId);
        $stmtRoom->execute();
        return true;
    }

    return false;
}

function getTodayArrivals($connection)
{
    $sql = "SELECT b.*, u.name AS guest_name, r.room_number, rt.name AS room_type_name
            FROM bookings b
            JOIN users u ON b.user_id = u.id
            JOIN rooms r ON b.room_id = r.id
            JOIN room_types rt ON r.room_type_id = rt.id
            WHERE b.checkin_date = CURDATE()
            AND b.status = 'Confirmed'";

    $statement = $connection->prepare($sql);
    $statement->execute();
    return $statement->get_result();
}

function getTodayDepartures($connection)
{
    $sql = "SELECT b.*, u.name AS guest_name, r.room_number, rt.name AS room_type_name
            FROM bookings b
            JOIN users u ON b.user_id = u.id
            JOIN rooms r ON b.room_id = r.id
            JOIN room_types rt ON r.room_type_id = rt.id
            WHERE b.checkout_date = CURDATE()
            AND b.status = 'Checked-In'";

    $statement = $connection->prepare($sql);
    $statement->execute();
    return $statement->get_result();
}

function getRoomSummaryCounts($connection)
{
    $sql = "SELECT
                (SELECT COUNT(*) FROM rooms) AS total_rooms,
                (SELECT COUNT(*) FROM bookings WHERE status = 'Checked-In') AS occupied_rooms,
                (SELECT COUNT(*) FROM rooms WHERE status = 'available') AS available_rooms,
                (SELECT COUNT(*) FROM rooms WHERE status = 'maintenance') AS maintenance_rooms";

    $statement = $connection->prepare($sql);
    $statement->execute();
    return $statement->get_result();
}

function getWeeklyRevenue($connection)
{
    $sql = "SELECT WEEK(checkin_date) AS week_number,
                   YEAR(checkin_date) AS year,
                   SUM(total_price)   AS total_revenue,
                   MIN(checkin_date)  AS week_start
            FROM bookings
            WHERE status IN ('Confirmed', 'Checked-In', 'Checked-Out')
            AND checkin_date >= DATE_SUB(CURDATE(), INTERVAL 8 WEEK)
            GROUP BY YEAR(checkin_date), WEEK(checkin_date)
            ORDER BY year ASC, week_number ASC";

    $statement = $connection->prepare($sql);
    $statement->execute();
    return $statement->get_result();
}

?>
