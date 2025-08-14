<?php
// Boat Availability Checker Functions
// This file contains functions to check boat availability and prevent double bookings

/**
 * Check if a boat is available for the given date range and time
 * @param mysqli $con Database connection
 * @param int $boat_id Boat ID
 * @param string $date_from Start date (YYYY-MM-DD)
 * @param string $date_to End date (YYYY-MM-DD)
 * @param string $time Booking time (HH:MM)
 * @param int $exclude_booking_id Booking ID to exclude (for updates)
 * @return array Result with availability status and details
 */
function checkBoatAvailability($con, $boat_id, $date_from, $date_to, $time, $exclude_booking_id = null) {
    $result = [
        'available' => false,
        'message' => '',
        'conflicting_bookings' => []
    ];
    
    // Validate inputs
    if (empty($boat_id) || empty($date_from) || empty($date_to) || empty($time)) {
        $result['message'] = 'Invalid booking parameters provided.';
        return $result;
    }
    
    // Validate date range
    if (strtotime($date_from) > strtotime($date_to)) {
        $result['message'] = 'Start date cannot be after end date.';
        return $result;
    }
    
    // Check if booking dates are in the past
    if (strtotime($date_from) < strtotime(date('Y-m-d'))) {
        $result['message'] = 'Cannot book for past dates.';
        return $result;
    }
    
    // Get boat details first
    $boat_query = mysqli_query($con, "SELECT * FROM tblboat WHERE ID = '$boat_id'");
    if (mysqli_num_rows($boat_query) == 0) {
        $result['message'] = 'Boat not found.';
        return $result;
    }
    
    // Build the availability check query
    $exclude_clause = $exclude_booking_id ? "AND ID != '$exclude_booking_id'" : "";
    
    $availability_query = "
        SELECT bk.*, b.BoatName 
        FROM tblbookings bk 
        JOIN tblboat b ON bk.BoatID = b.ID 
        WHERE bk.BoatID = '$boat_id' 
        AND bk.BookingStatus IN ('Accepted', 'Pending')
        AND (
            (DATE('$date_from') BETWEEN DATE(bk.BookingDateFrom) AND DATE(bk.BookingDateTo))
            OR (DATE('$date_to') BETWEEN DATE(bk.BookingDateFrom) AND DATE(bk.BookingDateTo))
            OR (DATE(bk.BookingDateFrom) BETWEEN DATE('$date_from') AND DATE('$date_to'))
            OR (DATE(bk.BookingDateTo) BETWEEN DATE('$date_from') AND DATE('$date_to'))
        )
        $exclude_clause
        ORDER BY bk.BookingDateFrom ASC
    ";
    
    $availability_result = mysqli_query($con, $availability_query);
    
    if (mysqli_num_rows($availability_result) > 0) {
        // Boat is not available - get conflicting bookings
        while ($conflict = mysqli_fetch_array($availability_result)) {
            $result['conflicting_bookings'][] = [
                'booking_number' => $conflict['BookingNumber'],
                'customer_name' => $conflict['FullName'],
                'date_from' => $conflict['BookingDateFrom'],
                'date_to' => $conflict['BookingDateTo'],
                'time' => $conflict['BookingTime'],
                'status' => $conflict['BookingStatus']
            ];
        }
        
        $result['message'] = 'Boat is not available for the selected dates. Please choose different dates.';
        return $result;
    }
    
    // Check for same-day multiple bookings (optional business rule)
    $same_day_query = "
        SELECT COUNT(*) as same_day_count
        FROM tblbookings 
        WHERE BoatID = '$boat_id' 
        AND DATE(BookingDateFrom) = DATE('$date_from')
        AND BookingStatus IN ('Accepted', 'Pending')
        AND TIME(BookingTime) = TIME('$time')
        $exclude_clause
    ";
    
    $same_day_result = mysqli_query($con, $same_day_query);
    $same_day_data = mysqli_fetch_array($same_day_result);
    
    if ($same_day_data['same_day_count'] > 0) {
        $result['message'] = 'Boat is already booked for this time slot. Please choose a different time.';
        return $result;
    }
    
    // All checks passed - boat is available
    $result['available'] = true;
    $result['message'] = 'Boat is available for the selected dates and time.';
    
    return $result;
}

/**
 * Create a temporary booking lock to prevent race conditions
 * @param mysqli $con Database connection
 * @param int $boat_id Boat ID
 * @param string $date_from Start date
 * @param string $date_to End date
 * @param string $session_id Unique session identifier
 * @return bool Success status
 */
function createBookingLock($con, $boat_id, $date_from, $date_to, $session_id) {
    // Remove expired locks (older than 10 minutes)
    mysqli_query($con, "DELETE FROM tblbooking_locks WHERE created_at < DATE_SUB(NOW(), INTERVAL 10 MINUTE)");
    
    // Create new lock
    $lock_query = "INSERT INTO tblbooking_locks (boat_id, date_from, date_to, session_id, created_at) 
                   VALUES ('$boat_id', '$date_from', '$date_to', '$session_id', NOW())";
    
    return mysqli_query($con, $lock_query);
}

/**
 * Check if there's a valid booking lock
 * @param mysqli $con Database connection
 * @param int $boat_id Boat ID
 * @param string $session_id Session identifier
 * @return bool Lock status
 */
function hasValidBookingLock($con, $boat_id, $session_id) {
    $lock_query = "SELECT * FROM tblbooking_locks 
                   WHERE boat_id = '$boat_id' 
                   AND session_id = '$session_id' 
                   AND created_at > DATE_SUB(NOW(), INTERVAL 10 MINUTE)";
    
    $result = mysqli_query($con, $lock_query);
    return mysqli_num_rows($result) > 0;
}

/**
 * Release booking lock
 * @param mysqli $con Database connection
 * @param string $session_id Session identifier
 * @return bool Success status
 */
function releaseBookingLock($con, $session_id) {
    $release_query = "DELETE FROM tblbooking_locks WHERE session_id = '$session_id'";
    return mysqli_query($con, $release_query);
}

/**
 * Get boat capacity and check if requested people count is valid
 * @param mysqli $con Database connection
 * @param int $boat_id Boat ID
 * @param int $people_count Number of people
 * @return array Result with capacity check
 */
function checkBoatCapacity($con, $boat_id, $people_count) {
    $result = [
        'valid' => false,
        'message' => '',
        'max_capacity' => 0
    ];
    
    $boat_query = mysqli_query($con, "SELECT Capacity FROM tblboat WHERE ID = '$boat_id'");
    if (mysqli_num_rows($boat_query) == 0) {
        $result['message'] = 'Boat not found.';
        return $result;
    }
    
    $boat_data = mysqli_fetch_array($boat_query);
    $capacity_str = $boat_data['Capacity'];
    
    // Extract maximum capacity from capacity string (e.g., "1-20" -> 20)
    if (preg_match('/(\d+)-(\d+)/', $capacity_str, $matches)) {
        $max_capacity = intval($matches[2]);
    } elseif (preg_match('/(\d+)/', $capacity_str, $matches)) {
        $max_capacity = intval($matches[1]);
    } else {
        $max_capacity = 20; // Default fallback
    }
    
    $result['max_capacity'] = $max_capacity;
    
    if ($people_count <= 0) {
        $result['message'] = 'Number of people must be greater than 0.';
        return $result;
    }
    
    if ($people_count > $max_capacity) {
        $result['message'] = "This boat can accommodate maximum $max_capacity people. You requested $people_count people.";
        return $result;
    }
    
    $result['valid'] = true;
    $result['message'] = 'Capacity check passed.';
    
    return $result;
}

/**
 * Comprehensive booking validation
 * @param mysqli $con Database connection
 * @param array $booking_data Booking details
 * @return array Validation result
 */
function validateBookingRequest($con, $booking_data) {
    $result = [
        'valid' => false,
        'message' => '',
        'errors' => []
    ];
    
    // Extract booking data
    $boat_id = $booking_data['boat_id'] ?? '';
    $date_from = $booking_data['date_from'] ?? '';
    $date_to = $booking_data['date_to'] ?? '';
    $time = $booking_data['time'] ?? '';
    $people_count = intval($booking_data['people_count'] ?? 0);
    $session_id = $booking_data['session_id'] ?? '';
    
    // Check capacity
    $capacity_check = checkBoatCapacity($con, $boat_id, $people_count);
    if (!$capacity_check['valid']) {
        $result['errors'][] = $capacity_check['message'];
    }
    
    // Check availability
    $availability_check = checkBoatAvailability($con, $boat_id, $date_from, $date_to, $time);
    if (!$availability_check['available']) {
        $result['errors'][] = $availability_check['message'];
    }
    
    // If all checks pass
    if (empty($result['errors'])) {
        $result['valid'] = true;
        $result['message'] = 'Booking request is valid.';
    } else {
        $result['message'] = 'Booking validation failed.';
    }
    
    return $result;
}
?>
