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
 
 
 
// ==================== GET ALL BOOKINGS (Admin) ====================
function getAllBookings($connection, $status = '', $date = '')
{
    $sql = "SELECT b.*, u.name AS guest_name, u.email AS guest_email,
                   rt.name AS room_type_name, r.room_number
            FROM bookings b
            JOIN users u       ON b.user_id      = u.id
            JOIN rooms r       ON b.room_id       = r.id
            JOIN room_types rt ON r.room_type_id  = rt.id
            WHERE 1=1";
 
    $params = [];
    $types  = '';
 
    if (!empty($status))
    {
        $sql    .= " AND b.status = ?";
        $params[] = $status;
        $types  .= 's';
    }
 
    if (!empty($date))
    {
        $sql    .= " AND b.checkin_date = ?";
        $params[] = $date;
        $types  .= 's';
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
 
// ==================== CHECKIN ====================
function checkInBooking($connection, $bookingId)
{
    $sql = "UPDATE bookings
            SET status = 'Checked-In', actual_checkin = NOW()
            WHERE id = ?
            AND status = 'Confirmed'";
 
    $statement = $connection->prepare($sql);
    $statement->bind_param("i", $bookingId);
    $statement->execute();
 
    return $connection->affected_rows > 0;
}
 
// ==================== CHECKOUT ====================
function checkOutBooking($connection, $bookingId)
{
    $sql = "UPDATE bookings
            SET status = 'Checked-Out'
            WHERE id = ?
            AND status = 'Checked-In'";
 
    $statement = $connection->prepare($sql);
    $statement->bind_param("i", $bookingId);
    $statement->execute();
 
    return $connection->affected_rows > 0;
}
 
// ==================== TOTAL BOOKINGS ====================
function getTotalBookings($connection)
{
    $sql       = "SELECT COUNT(*) AS total FROM bookings";
    $statement = $connection->prepare($sql);
    $statement->execute();
    $result    = $statement->get_result();
    $row       = $result->fetch_assoc();
    return $row['total'];
}
 
// ==================== TODAY CHECKINS ====================
function getTodayCheckins($connection)
{
    $today = date('Y-m-d'); 

    // ✅ FIXED: Added 'Checked-In' to the status criteria
    $sql = "SELECT COUNT(*) as total 
            FROM bookings 
            WHERE DATE(checkin_date) = ? 
            AND (status = 'Pending' OR status = 'Confirmed' OR status = 'Checked-In')";

    $statement = $connection->prepare($sql);
    if (!$statement) {
        return 0;
    }

    $statement->bind_param("s", $today);
    $statement->execute();
    
    $result = $statement->get_result();
    $row = $result->fetch_assoc();
    
    $statement->close();
    
    return $row['total'] ?? 0;
}
 
// ==================== OCCUPIED ROOMS COUNT ====================
function getOccupiedRoomsCount($connection)
{
    $sql       = "SELECT COUNT(DISTINCT room_id) AS total
                  FROM bookings
                  WHERE status = 'Checked-In'";
    $statement = $connection->prepare($sql);
    $statement->execute();
    $result    = $statement->get_result();
    $row       = $result->fetch_assoc();
    return $row['total'];
}
 
// ==================== TOTAL ROOMS COUNT ====================
function getTotalRoomsCount($connection)
{
    $sql       = "SELECT COUNT(*) AS total FROM rooms";
    $statement = $connection->prepare($sql);
    $statement->execute();
    $result    = $statement->get_result();
    $row       = $result->fetch_assoc();
    return $row['total'];
}
 
// ==================== WEEKLY REVENUE ====================
function getWeeklyRevenue($connection)
{
    $sql = "SELECT
                YEAR(checkin_date)                  AS year,
                WEEK(checkin_date)                  AS week,
                SUM(total_price)                    AS revenue,
                MIN(checkin_date)                   AS week_start
            FROM bookings
            WHERE status IN ('Confirmed','Checked-In','Checked-Out')
            GROUP BY YEAR(checkin_date), WEEK(checkin_date)
            ORDER BY year DESC, week DESC
            LIMIT 8";
 
    $statement = $connection->prepare($sql);
    $statement->execute();
    $result = $statement->get_result();
 
    $data = [];
    while ($row = $result->fetch_assoc())
    {
       
        array_unshift($data, $row);
    }
    return $data;
}
function confirmBooking($connection, $bookingId)
{
    $sql = "UPDATE bookings SET status = 'Confirmed' WHERE id = ?";
    $statement = $connection->prepare($sql);
    if (!$statement) {
        return false;
    }
    $statement->bind_param("i", $bookingId);
    $result = $statement->execute();
    $statement->close();
    return $result;
}