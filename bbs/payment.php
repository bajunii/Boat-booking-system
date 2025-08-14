<?php 
session_start();
include('includes/config.php');

if(!isset($_GET['booking_id'])) {
    header('location: index.php');
    exit();
}

$booking_id = $_GET['booking_id'];

// Get booking details
$query = mysqli_query($con, "SELECT bk.*, b.BoatName, b.Price 
                           FROM tblbookings bk 
                           JOIN tblboat b ON bk.BoatID = b.ID 
                           WHERE bk.ID = '$booking_id'");

if(mysqli_num_rows($query) == 0) {
    header('location: index.php');
    exit();
}

$booking = mysqli_fetch_array($query);
$total_amount = $booking['Price'] * $booking['NumnerofPeople'];

// Handle payment submission
if(isset($_POST['process_payment'])) {
    $payment_method = $_POST['payment_method'];
    $payment_gateway = $_POST['payment_gateway'];
    
    // In a real application, you would integrate with actual payment gateways
    // For demonstration, we'll simulate payment processing
    
    // Generate a transaction ID (in real app, this comes from payment gateway)
    $transaction_id = 'TXN_' . time() . '_' . rand(1000, 9999);
    
    // Insert payment record
    $payment_query = mysqli_query($con, "INSERT INTO tblpayments (BookingID, PaymentMethod, PaymentGateway, TransactionID, Amount, PaymentStatus) 
                                        VALUES ('$booking_id', '$payment_method', '$payment_gateway', '$transaction_id', '$total_amount', 'completed')");
    
    if($payment_query) {
        // Update booking status
        mysqli_query($con, "UPDATE tblbookings SET AdminRemark='Payment Completed', BookingStatus='Accepted' WHERE ID='$booking_id'");
        
        echo "<script>alert('Payment successful! Transaction ID: $transaction_id'); window.location.href='payment-success.php?tid=$transaction_id';</script>";
    } else {
        echo "<script>alert('Payment failed. Please try again.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Payment | Boat Booking System</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    
    <link href="https://fonts.googleapis.com/css?family=Oswald:400,700|Dancing+Script:400,700|Muli:300,400" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/custom-enhancements.css">
    
    <style>
        .payment-card {
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            transition: border-color 0.3s;
            cursor: pointer;
        }
        
        .payment-card:hover {
            border-color: #007bff;
        }
        
        .payment-card.selected {
            border-color: #007bff;
            background-color: #f8f9fa;
        }
        
        .payment-method-radio {
            display: none;
        }
        
        .payment-icon {
            font-size: 2rem;
            margin-bottom: 10px;
        }
        
        .order-summary {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
        }
    </style>
</head>

<body>
    <div class="site-wrap">
        <?php include_once("includes/navbar.php");?>
        
        <div class="site-section">
            <div class="container">
                <div class="row">
                    <div class="col-md-8">
                        <h2 class="mb-4">Complete Your Payment</h2>
                        
                        <!-- Booking Summary -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5>Booking Summary</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Booking Number:</strong> <?php echo $booking['BookingNumber']; ?></p>
                                        <p><strong>Customer:</strong> <?php echo $booking['FullName']; ?></p>
                                        <p><strong>Email:</strong> <?php echo $booking['EmailId']; ?></p>
                                        <p><strong>Phone:</strong> <?php echo $booking['PhoneNumber']; ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Boat:</strong> <?php echo $booking['BoatName']; ?></p>
                                        <p><strong>Date:</strong> <?php echo $booking['BookingDateFrom']; ?> to <?php echo $booking['BookingDateTo']; ?></p>
                                        <p><strong>Time:</strong> <?php echo $booking['BookingTime']; ?></p>
                                        <p><strong>People:</strong> <?php echo $booking['NumnerofPeople']; ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Payment Methods -->
                        <form method="post" id="paymentForm">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Select Payment Method</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <!-- Credit Card -->
                                        <div class="col-md-6">
                                            <div class="payment-card" onclick="selectPayment('credit_card', 'stripe')">
                                                <input type="radio" name="payment_method" value="credit_card" class="payment-method-radio" id="credit_card">
                                                <input type="hidden" name="payment_gateway" value="stripe" id="gateway_credit_card">
                                                <div class="text-center">
                                                    <i class="fas fa-credit-card payment-icon text-primary"></i>
                                                    <h6>Credit/Debit Card</h6>
                                                    <p class="text-muted">Visa, MasterCard, American Express</p>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- PayPal -->
                                        <div class="col-md-6">
                                            <div class="payment-card" onclick="selectPayment('paypal', 'paypal')">
                                                <input type="radio" name="payment_method" value="paypal" class="payment-method-radio" id="paypal">
                                                <input type="hidden" name="payment_gateway" value="paypal" id="gateway_paypal">
                                                <div class="text-center">
                                                    <i class="fab fa-paypal payment-icon text-primary"></i>
                                                    <h6>PayPal</h6>
                                                    <p class="text-muted">Pay with your PayPal account</p>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Mobile Money -->
                                        <div class="col-md-6">
                                            <div class="payment-card" onclick="selectPayment('mobile_money', 'mpesa')">
                                                <input type="radio" name="payment_method" value="mobile_money" class="payment-method-radio" id="mobile_money">
                                                <input type="hidden" name="payment_gateway" value="mpesa" id="gateway_mobile_money">
                                                <div class="text-center">
                                                    <i class="fas fa-mobile-alt payment-icon text-success"></i>
                                                    <h6>Mobile Money</h6>
                                                    <p class="text-muted">M-Pesa, Airtel Money</p>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Bank Transfer -->
                                        <div class="col-md-6">
                                            <div class="payment-card" onclick="selectPayment('bank_transfer', 'bank')">
                                                <input type="radio" name="payment_method" value="bank_transfer" class="payment-method-radio" id="bank_transfer">
                                                <input type="hidden" name="payment_gateway" value="bank" id="gateway_bank_transfer">
                                                <div class="text-center">
                                                    <i class="fas fa-university payment-icon text-info"></i>
                                                    <h6>Bank Transfer</h6>
                                                    <p class="text-muted">Direct bank transfer</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Payment Form Fields -->
                                    <div id="creditCardForm" class="payment-form mt-4" style="display: none;">
                                        <h6>Credit Card Details</h6>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Card Number</label>
                                                    <input type="text" class="form-control" placeholder="1234 5678 9012 3456" maxlength="19">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Cardholder Name</label>
                                                    <input type="text" class="form-control" placeholder="John Doe">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Expiry Month</label>
                                                    <select class="form-control">
                                                        <option>01</option>
                                                        <option>02</option>
                                                        <option>03</option>
                                                        <!-- Add all months -->
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Expiry Year</label>
                                                    <select class="form-control">
                                                        <option>2024</option>
                                                        <option>2025</option>
                                                        <option>2026</option>
                                                        <!-- Add more years -->
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>CVV</label>
                                                    <input type="text" class="form-control" placeholder="123" maxlength="4">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div id="mobileMoneyForm" class="payment-form mt-4" style="display: none;">
                                        <h6>Mobile Money Details</h6>
                                        <div class="form-group">
                                            <label>Phone Number</label>
                                            <input type="tel" class="form-control" placeholder="+254 712 345 678">
                                        </div>
                                    </div>
                                    
                                    <div id="bankTransferForm" class="payment-form mt-4" style="display: none;">
                                        <h6>Bank Transfer Instructions</h6>
                                        <div class="alert alert-info">
                                            <strong>Bank Details:</strong><br>
                                            Account Name: Boat Booking System<br>
                                            Account Number: 1234567890<br>
                                            Bank: ABC Bank<br>
                                            Reference: <?php echo $booking['BookingNumber']; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Order Summary Sidebar -->
                    <div class="col-md-4">
                        <div class="order-summary">
                            <h5 class="mb-3">Order Summary</h5>
                            
                            <div class="d-flex justify-content-between mb-2">
                                <span>Price per person:</span>
                                <span>Kshs <?php echo number_format($booking['Price']); ?></span>
                            </div>
                            
                            <div class="d-flex justify-content-between mb-2">
                                <span>Number of people:</span>
                                <span><?php echo $booking['NumnerofPeople']; ?></span>
                            </div>
                            
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal:</span>
                                <span>Kshs <?php echo number_format($total_amount); ?></span>
                            </div>
                            
                            <div class="d-flex justify-content-between mb-2">
                                <span>Service Fee:</span>
                                <span>Kshs 0</span>
                            </div>
                            
                            <hr>
                            
                            <div class="d-flex justify-content-between mb-3">
                                <strong>Total Amount:</strong>
                                <strong>Kshs <?php echo number_format($total_amount); ?></strong>
                            </div>
                            
                            <button type="submit" name="process_payment" form="paymentForm" class="btn btn-primary btn-lg btn-block" id="payButton" disabled>
                                <i class="fas fa-lock mr-2"></i>Pay Now
                            </button>
                            
                            <div class="text-center mt-3">
                                <small class="text-muted">
                                    <i class="fas fa-shield-alt mr-1"></i>
                                    Your payment information is secure
                                </small>
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
    
    <script>
        function selectPayment(method, gateway) {
            // Remove selected class from all cards
            $('.payment-card').removeClass('selected');
            
            // Add selected class to clicked card
            $('#' + method).closest('.payment-card').addClass('selected');
            
            // Check the radio button
            $('#' + method).prop('checked', true);
            
            // Set the gateway value
            $('input[name="payment_gateway"]').val(gateway);
            
            // Hide all payment forms
            $('.payment-form').hide();
            
            // Show relevant payment form
            if(method === 'credit_card') {
                $('#creditCardForm').show();
            } else if(method === 'mobile_money') {
                $('#mobileMoneyForm').show();
            } else if(method === 'bank_transfer') {
                $('#bankTransferForm').show();
            }
            
            // Enable pay button
            $('#payButton').prop('disabled', false);
        }
        
        // Format card number input
        $(document).on('input', 'input[placeholder="1234 5678 9012 3456"]', function() {
            var value = $(this).val().replace(/\D/g, '');
            var formattedValue = value.replace(/(\d{4})(?=\d)/g, '$1 ');
            $(this).val(formattedValue);
        });
    </script>
</body>
</html>
