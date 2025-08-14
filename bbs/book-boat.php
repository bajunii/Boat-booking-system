<?php session_start();
// Database Connection
include('includes/config.php');

if(isset($_POST['submit'])){

$boatid=$_GET['bid'];
$fname=$_POST['fname'];
$emailid=$_POST['emailid'];
$phonenumber=$_POST['phonenumber'];
$bookingdatefrom=$_POST['bookingdatefrom'];
$bookingdateto=$_POST['bookingdateto'];
$bookingtime=$_POST['bookingtime'];
$nopeople=$_POST['nopeople'];
$notes=$_POST['notes'];
$bno=mt_rand(100000000,9999999999);

// Basic validation
$errors = [];

// Check if dates are valid
if(strtotime($bookingdatefrom) < strtotime(date('Y-m-d'))) {
    $errors[] = 'Cannot book for past dates';
}

if(strtotime($bookingdatefrom) > strtotime($bookingdateto)) {
    $errors[] = 'End date cannot be before start date';
}

// Check boat capacity
$capacity_query = mysqli_query($con, "SELECT Capacity FROM tblboat WHERE ID = '$boatid'");
if($capacity_row = mysqli_fetch_assoc($capacity_query)) {
    // Extract numeric capacity (e.g., "1-20" -> 20)
    $capacity_parts = explode('-', $capacity_row['Capacity']);
    $max_capacity = end($capacity_parts);
    if($nopeople > $max_capacity) {
        $errors[] = 'Number of people exceeds boat capacity of ' . $max_capacity;
    }
}

// Check for existing bookings
$booking_check = mysqli_query($con, "
    SELECT COUNT(*) as booking_count 
    FROM tblbookings 
    WHERE BoatID = '$boatid' 
    AND BookingStatus IN ('Confirmed', 'Pending')
    AND (
        (BookingDateFrom <= '$bookingdatefrom' AND BookingDateTo >= '$bookingdatefrom') OR
        (BookingDateFrom <= '$bookingdateto' AND BookingDateTo >= '$bookingdateto') OR
        (BookingDateFrom >= '$bookingdatefrom' AND BookingDateTo <= '$bookingdateto')
    )
");

$booking_result = mysqli_fetch_assoc($booking_check);

if($booking_result['booking_count'] > 0) {
    $errors[] = 'Boat is not available for the selected dates';
}

if(empty($errors)) {
    //Code for Insertion with 'Pending' status until payment
    $query=mysqli_query($con,"insert into tblbookings(BoatID,BookingNumber,FullName,EmailId,PhoneNumber,BookingDateFrom,BookingDateTo,BookingTime,NumnerofPeople,Notes,BookingStatus) values('$boatid','$bno','$fname','$emailid','$phonenumber','$bookingdatefrom','$bookingdateto','$bookingtime','$nopeople','$notes','Pending')");
    
    if($query){
        // Get the inserted booking ID
        $booking_id = mysqli_insert_id($con);
        
        // Store booking info in session
        $_SESSION['booking_id'] = $booking_id;
        
        echo '<script>alert("Booking validated successfully! Booking number: '.$bno.'. Please complete payment to confirm your booking.");</script>';
        echo "<script type='text/javascript'> document.location = 'payment.php?booking_id=".$booking_id."'; </script>";
    } else {
        echo "<script>alert('Something went wrong during booking. Please try again.');</script>";
    }
} else {
    // Show validation errors
    $error_message = implode("\\n", $errors);
    $error_message = str_replace("'", "\\'", $error_message);
    echo "<script>alert('Booking validation failed:\\n" . $error_message . "');</script>";
}

}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <title>Boat Booking System || Booking Page</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <link href="https://fonts.googleapis.com/css?family=Oswald:400,700|Dancing+Script:400,700|Muli:300,400" rel="stylesheet">
  <link rel="stylesheet" href="fonts/icomoon/style.css">

  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="css/jquery-ui.css">
  <link rel="stylesheet" href="css/owl.carousel.min.css">
  <link rel="stylesheet" href="css/owl.theme.default.min.css">
  <link rel="stylesheet" href="css/owl.theme.default.min.css">

  <link rel="stylesheet" href="css/jquery.fancybox.min.css">

  <link rel="stylesheet" href="css/bootstrap-datepicker.css">

  <link rel="stylesheet" href="fonts/flaticon/font/flaticon.css">

  <link rel="stylesheet" href="css/aos.css">
  <link href="css/jquery.mb.YTPlayer.min.css" media="all" rel="stylesheet" type="text/css">

  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/custom-enhancements.css">

</head>

<body data-spy="scroll" data-target=".site-navbar-target" data-offset="300">

  <div class="site-wrap">

    <?php include_once("includes/navbar.php");?>
    
    <div class="intro-section" style="background-image: url('images/hero_2.jpg');">
        <div class="container">
          <div class="row align-items-center">
            <div class="col-lg-7 mx-auto text-center" data-aos="fade-up">
              <h1>Boat Booking</h1>
              <p><a href="contact.php" class="btn btn-primary py-3 px-5">Contact</a></p>
            </div>
          </div>
        </div>
      </div>
    
    <div class="site-section">
      <div class="container">
        <div class="row">
          <div class="col-md-6">
            <p><img src="images/hero_1.jpg" alt="Image" class="img-fluid"></p>
          </div>
          <div class="col-md-6">
            <span class="text-serif text-primary">Book Now</span>
            <h3 class="heading-92913 text-black">Book A Boat</h3>
            
            <!-- Availability Info Alert -->
            <div class="alert alert-info mb-4">
              <i class="fas fa-info-circle mr-2"></i>
              <strong>Booking Information:</strong> Please select your preferred dates and time. We'll check availability in real-time before processing your payment.
            </div>
            
            <form action="#" class="row" method="post" id="bookingForm">
              <div class="form-group col-md-6">
                <label for="input-1">Full Name:</label>
                <input type="text" class="form-control" name="fname" required="true">
              </div>
              <div class="form-group col-md-6">
                <label for="input-2">Number of People:</label>
                <input type="number" class="form-control" name="nopeople" min="1" required="true">
                <small class="text-muted">Check boat capacity limits</small>
              </div>

              <div class="form-group col-md-6">
                <label for="input-3">Date From:</label>
                <input type="text" class="form-control datepicker" name="bookingdatefrom" required="true" readonly>
              </div>
              <div class="form-group col-md-6">
                <label for="input-4">Date To:</label>
                <input type="text" class="form-control datepicker" name="bookingdateto" required="true" readonly>
              </div>

             <div class="form-group col-md-6">
                <label for="input-4">Time:</label>
                <input type="time" class="form-control timepicker" name="bookingtime" required="true">
              </div>

              <div class="form-group col-md-6">
                <label for="input-6">Email Address</label>
                <input type="email" class="form-control" name="emailid" required="true">
              </div>

              <div class="form-group col-md-6">
                <label for="input-7">Phone Number</label>
                <input type="text" class="form-control" name="phonenumber" maxlength="10" pattern="[0-9]+" required="true"> 
              </div>

              <div class="form-group col-md-12">
                <label for="input-8">Notes</label>
                <textarea cols="30" rows="5" class="form-control" name="notes" placeholder="Any special requirements or notes..."></textarea>
              </div>

              <!-- Real-time Availability Check Button -->
              <div class="form-group col-md-12">
                <button type="button" class="btn btn-info mr-3" onclick="checkAvailability()" id="checkBtn">
                  <i class="fas fa-search mr-2"></i>Check Availability
                </button>
                <div id="availabilityResult" class="mt-2"></div>
              </div>

              <div class="form-group col-md-12">
                <input type="submit" name="submit" class="btn btn-primary py-3 px-5" value="Proceed to Payment" id="submitBtn">
                <small class="text-muted d-block mt-2">
                  <i class="fas fa-shield-alt mr-1"></i>
                  Your booking will be validated before payment processing
                </small>
              </div>

            </form>
          </div>
        </div>
      </div>
    </div>
    
    <div class="site-section bg-image overlay" style="background-image: url('images/hero_2.jpg');">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-md-7 text-center">
            <h2 class="text-white">Get In Touch With Us</h2>
            <p class="lead text-white">Lorem ipsum dolor sit amet, consectetur adipisicing elit.</p>
            <p class="mb-0"><a href="contact.php" class="btn btn-warning py-3 px-5 text-white">Contact Us</a></p>
          </div>
        </div>
      </div>
    </div>

    <?php include_once("includes/footer.php");?>

  </div>
  <!-- .site-wrap -->

  <!-- loader -->
  <div id="loader" class="show fullscreen"><svg class="circular" width="48px" height="48px"><circle class="path-bg" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke="#eeeeee"/><circle class="path" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke-miterlimit="10" stroke="#ff5e15"/></svg></div>

  <script src="js/jquery-3.3.1.min.js"></script>
  <script src="js/jquery-migrate-3.0.1.min.js"></script>
  <script src="js/jquery-ui.js"></script>
  <script src="js/popper.min.js"></script>
  <script src="js/bootstrap.min.js"></script>
  <script src="js/owl.carousel.min.js"></script>
  <script src="js/jquery.stellar.min.js"></script>
  <script src="js/jquery.countdown.min.js"></script>
  <script src="js/bootstrap-datepicker.min.js"></script>
  <script src="js/jquery.easing.1.3.js"></script>
  <script src="js/aos.js"></script>
  <script src="js/jquery.fancybox.min.js"></script>
  <script src="js/jquery.sticky.js"></script>
  <script src="js/jquery.mb.YTPlayer.min.js"></script>
  <script src="js/main.js"></script>

  <script type="text/javascript">
    $(".datepicker").datepicker({
        format: "yyyy-mm-dd",
        startDate: new Date(),
        autoclose: true
    });

    // Real-time availability checker
    function checkAvailability() {
        const boatId = <?php echo isset($_GET['bid']) ? $_GET['bid'] : 'null'; ?>;
        const dateFrom = $('input[name="bookingdatefrom"]').val();
        const dateTo = $('input[name="bookingdateto"]').val();
        const time = $('input[name="bookingtime"]').val();
        const people = $('input[name="nopeople"]').val();
        
        if (!dateFrom || !dateTo || !time || !people) {
            $('#availabilityResult').html('<div class="alert alert-warning"><i class="fas fa-exclamation-triangle mr-2"></i>Please fill in all booking details first.</div>');
            return;
        }
        
        $('#checkBtn').html('<i class="fas fa-spinner fa-spin mr-2"></i>Checking...');
        $('#availabilityResult').html('');
        
        $.ajax({
            url: 'check-availability.php',
            method: 'POST',
            data: {
                boat_id: boatId,
                date_from: dateFrom,
                date_to: dateTo,
                time: time,
                people_count: people
            },
            dataType: 'json',
            success: function(response) {
                $('#checkBtn').html('<i class="fas fa-search mr-2"></i>Check Availability');
                
                if (response.available) {
                    $('#availabilityResult').html('<div class="alert alert-success"><i class="fas fa-check-circle mr-2"></i>' + response.message + '</div>');
                } else {
                    $('#availabilityResult').html('<div class="alert alert-danger"><i class="fas fa-times-circle mr-2"></i>' + response.message + '</div>');
                }
            },
            error: function() {
                $('#checkBtn').html('<i class="fas fa-search mr-2"></i>Check Availability');
                $('#availabilityResult').html('<div class="alert alert-danger"><i class="fas fa-exclamation-triangle mr-2"></i>Error checking availability. Please try again.</div>');
            }
        });
    }

    // Form validation
    $('#bookingForm').on('submit', function(e) {
        const dateFrom = $('input[name="bookingdatefrom"]').val();
        const dateTo = $('input[name="bookingdateto"]').val();
        
        if (new Date(dateFrom) > new Date(dateTo)) {
            e.preventDefault();
            alert('End date cannot be before start date.');
            return false;
        }
        
        // Show loading state
        $('#submitBtn').html('<i class="fas fa-spinner fa-spin mr-2"></i>Processing...');
    });
  </script>

</body>
</html>