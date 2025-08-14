<?php
session_start();
include('includes/config.php');

header('Content-Type: application/json');

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $boat_id = $_POST['boat_id'];
    $date_from = $_POST['date_from'];
    $date_to = $_POST['date_to'];
    $time = $_POST['time'];
    $people_count = $_POST['people_count'];
    
    // Basic validation
    if(empty($boat_id) || empty($date_from) || empty($date_to) || empty($time) || empty($people_count)) {
        echo json_encode(['available' => false, 'message' => 'All fields are required']);
        exit;
    }
    
    // Check if dates are valid
    if(strtotime($date_from) < strtotime(date('Y-m-d'))) {
        echo json_encode(['available' => false, 'message' => 'Cannot book for past dates']);
        exit;
    }
    
    if(strtotime($date_from) > strtotime($date_to)) {
        echo json_encode(['available' => false, 'message' => 'End date cannot be before start date']);
        exit;
    }
    
    // Check boat capacity
    $capacity_query = mysqli_query($con, "SELECT Capacity FROM tblboat WHERE ID = '$boat_id'");
    if($capacity_row = mysqli_fetch_assoc($capacity_query)) {
        // Extract numeric capacity (e.g., "1-20" -> 20)
        $capacity_parts = explode('-', $capacity_row['Capacity']);
        $max_capacity = end($capacity_parts);
        if($people_count > $max_capacity) {
            echo json_encode(['available' => false, 'message' => 'Number of people exceeds boat capacity of ' . $max_capacity]);
            exit;
        }
    }
    
    // Check for existing bookings
    $booking_check = mysqli_query($con, "
        SELECT COUNT(*) as booking_count 
        FROM tblbookings 
        WHERE BoatID = '$boat_id' 
        AND BookingStatus IN ('Confirmed', 'Pending')
        AND (
            (BookingDateFrom <= '$date_from' AND BookingDateTo >= '$date_from') OR
            (BookingDateFrom <= '$date_to' AND BookingDateTo >= '$date_to') OR
            (BookingDateFrom >= '$date_from' AND BookingDateTo <= '$date_to')
        )
    ");
    
    $booking_result = mysqli_fetch_assoc($booking_check);
    
    if($booking_result['booking_count'] > 0) {
        echo json_encode(['available' => false, 'message' => 'Boat is not available for the selected dates']);
    } else {
        echo json_encode(['available' => true, 'message' => 'Boat is available for booking!']);
    }
} else {
    echo json_encode(['available' => false, 'message' => 'Invalid request method']);
}
?>
