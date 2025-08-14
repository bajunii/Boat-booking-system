<?php 
session_start();
include('includes/config.php');

if(!isset($_GET['tid'])) {
    header('location: index.php');
    exit();
}

$transaction_id = $_GET['tid'];

// Get payment details
$query = mysqli_query($con, "SELECT p.*, bk.BookingNumber, bk.FullName, b.BoatName 
                           FROM tblpayments p 
                           JOIN tblbookings bk ON p.BookingID = bk.ID 
                           JOIN tblboat b ON bk.BoatID = b.ID 
                           WHERE p.TransactionID = '$transaction_id'");

if(mysqli_num_rows($query) == 0) {
    header('location: index.php');
    exit();
}

$payment = mysqli_fetch_array($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Payment Success | Boat Booking System</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    
    <link href="https://fonts.googleapis.com/css?family=Oswald:400,700|Dancing+Script:400,700|Muli:300,400" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/custom-enhancements.css">
    
    <style>
        .success-icon {
            font-size: 4rem;
            color: #28a745;
            margin-bottom: 20px;
        }
        
        .success-card {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            padding: 40px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="site-wrap">
        <?php include_once("includes/navbar.php");?>
        
        <div class="site-section">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="success-card">
                            <i class="fas fa-check-circle success-icon"></i>
                            <h2 class="text-success mb-3">Payment Successful!</h2>
                            <p class="lead mb-4">Thank you for your payment. Your booking has been confirmed.</p>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5>Payment Details</h5>
                                        </div>
                                        <div class="card-body text-left">
                                            <p><strong>Transaction ID:</strong> <?php echo $payment['TransactionID']; ?></p>
                                            <p><strong>Amount Paid:</strong> Kshs <?php echo number_format($payment['Amount'], 2); ?></p>
                                            <p><strong>Payment Method:</strong> <?php echo ucfirst(str_replace('_', ' ', $payment['PaymentMethod'])); ?></p>
                                            <p><strong>Payment Date:</strong> <?php echo date('M d, Y H:i', strtotime($payment['PaymentDate'])); ?></p>
                                            <p><strong>Status:</strong> <span class="badge badge-success">Completed</span></p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5>Booking Details</h5>
                                        </div>
                                        <div class="card-body text-left">
                                            <p><strong>Booking Number:</strong> <?php echo $payment['BookingNumber']; ?></p>
                                            <p><strong>Customer:</strong> <?php echo $payment['FullName']; ?></p>
                                            <p><strong>Boat:</strong> <?php echo $payment['BoatName']; ?></p>
                                            <p><strong>Status:</strong> <span class="badge badge-success">Confirmed</span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-4">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    A confirmation email has been sent to your registered email address with all the booking details.
                                </div>
                            </div>
                            
                            <div class="mt-4">
                                <a href="index.php" class="btn btn-primary btn-lg mr-3">
                                    <i class="fas fa-home mr-2"></i>Back to Home
                                </a>
                                <a href="booking-details.php?bid=<?php echo $payment['BookingNumber']; ?>" class="btn btn-outline-primary btn-lg">
                                    <i class="fas fa-file-alt mr-2"></i>View Booking Details
                                </a>
                            </div>
                            
                            <div class="mt-4">
                                <button onclick="window.print()" class="btn btn-secondary">
                                    <i class="fas fa-print mr-2"></i>Print Receipt
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <?php include_once("includes/footer.php");?>
    </div>
    
    <script src="js/jquery-3.3.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
</body>
</html>
