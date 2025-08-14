<?php 
session_start();
include('includes/config.php');

if(isset($_POST['submit'])) {
    $boatid = $_POST['boatid'];
    $customername = $_POST['customername'];
    $customeremail = $_POST['customeremail'];
    $rating = $_POST['rating'];
    $reviewtitle = $_POST['reviewtitle'];
    $reviewtext = $_POST['reviewtext'];
    
    $query = mysqli_query($con, "INSERT INTO tblreviews(BoatID, CustomerName, CustomerEmail, Rating, ReviewTitle, ReviewText) VALUES('$boatid', '$customername', '$customeremail', '$rating', '$reviewtitle', '$reviewtext')");
    
    if($query) {
        echo "<script>alert('Review submitted successfully! It will be visible after admin approval.');</script>";
    } else {
        echo "<script>alert('Something went wrong. Please try again.');</script>";
    }
}

// Get boat details
if(isset($_GET['bid'])) {
    $boatid = $_GET['bid'];
    $query = mysqli_query($con, "SELECT * FROM tblboat WHERE ID='$boatid'");
    $boat = mysqli_fetch_array($query);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Boat Booking System || Leave Review</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    
    <link href="https://fonts.googleapis.com/css?family=Oswald:400,700|Dancing+Script:400,700|Muli:300,400" rel="stylesheet">
    <link rel="stylesheet" href="fonts/icomoon/style.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/custom-enhancements.css">
    
    <style>
        .rating {
            display: flex;
            flex-direction: row-reverse;
            justify-content: center;
            margin: 20px 0;
        }
        
        .rating input {
            display: none;
        }
        
        .rating label {
            cursor: pointer;
            width: 40px;
            height: 40px;
            display: block;
            background: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' width='126.729' height='126.73'%3e%3cpath fill='%23e3e3e3' d='M121.215 44.212l-34.899-3.3c-2.2-.2-4.101-1.6-5-3.7l-12.5-30.3c-2-5-9.101-5-11.101 0l-12.4 30.3c-.8 2.1-2.8 3.5-5 3.7l-34.9 3.3c-5.2.5-7.3 7-3.4 10.5l26.3 23.1c1.7 1.5 2.4 3.7 1.9 5.9l-7.9 32.399c-1.2 5.101 4.3 9.3 8.9 6.601l29.1-17.101c1.9-1.1 4.2-1.1 6.1 0l29.101 17.101c4.6 2.699 10.1-1.4 8.899-6.601l-7.8-32.399c-.5-2.2.2-4.4 1.9-5.9l26.3-23.1c3.8-3.5 1.6-10-3.4-10.5z'/%3e%3c/svg%3e") no-repeat center center;
            background-size: 76%;
            transition: .3s;
        }
        
        .rating label:hover,
        .rating label:hover ~ label,
        .rating input:checked ~ label {
            background: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' width='126.729' height='126.73'%3e%3cpath fill='%23ffc107' d='M121.215 44.212l-34.899-3.3c-2.2-.2-4.101-1.6-5-3.7l-12.5-30.3c-2-5-9.101-5-11.101 0l-12.4 30.3c-.8 2.1-2.8 3.5-5 3.7l-34.9 3.3c-5.2.5-7.3 7-3.4 10.5l26.3 23.1c1.7 1.5 2.4 3.7 1.9 5.9l-7.9 32.399c-1.2 5.101 4.3 9.3 8.9 6.601l29.1-17.101c1.9-1.1 4.2-1.1 6.1 0l29.101 17.101c4.6 2.699 10.1-1.4 8.899-6.601l-7.8-32.399c-.5-2.2.2-4.4 1.9-5.9l26.3-23.1c3.8-3.5 1.6-10-3.4-10.5z'/%3e%3c/svg%3e") no-repeat center center;
            background-size: 76%;
        }
    </style>
</head>

<body>
    <div class="site-wrap">
        <?php include_once("includes/navbar.php");?>
        
        <div class="site-section">
            <div class="container">
                <div class="row">
                    <div class="col-md-8 offset-md-2">
                        <h2 class="text-center mb-4">Leave a Review</h2>
                        
                        <?php if(isset($boat)): ?>
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $boat['BoatName']; ?></h5>
                                <p class="card-text">
                                    <strong>Route:</strong> <?php echo $boat['Source']; ?> → <?php echo $boat['Destination']; ?><br>
                                    <strong>Price:</strong> Kshs <?php echo $boat['Price']; ?>
                                </p>
                            </div>
                        </div>
                        
                        <form method="post">
                            <input type="hidden" name="boatid" value="<?php echo $boat['ID']; ?>">
                            
                            <div class="form-group">
                                <label for="customername">Your Name *</label>
                                <input type="text" class="form-control" name="customername" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="customeremail">Your Email *</label>
                                <input type="email" class="form-control" name="customeremail" required>
                            </div>
                            
                            <div class="form-group text-center">
                                <label>Rating *</label>
                                <div class="rating">
                                    <input type="radio" id="star5" name="rating" value="5" required>
                                    <label for="star5"></label>
                                    <input type="radio" id="star4" name="rating" value="4">
                                    <label for="star4"></label>
                                    <input type="radio" id="star3" name="rating" value="3">
                                    <label for="star3"></label>
                                    <input type="radio" id="star2" name="rating" value="2">
                                    <label for="star2"></label>
                                    <input type="radio" id="star1" name="rating" value="1">
                                    <label for="star1"></label>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="reviewtitle">Review Title</label>
                                <input type="text" class="form-control" name="reviewtitle" placeholder="e.g., Excellent service!">
                            </div>
                            
                            <div class="form-group">
                                <label for="reviewtext">Your Review</label>
                                <textarea class="form-control" name="reviewtext" rows="5" placeholder="Share your experience..."></textarea>
                            </div>
                            
                            <div class="text-center">
                                <button type="submit" name="submit" class="btn btn-primary btn-lg">Submit Review</button>
                                <a href="boat-details.php?bid=<?php echo $boat['ID']; ?>" class="btn btn-secondary btn-lg ml-2">Back to Boat</a>
                            </div>
                        </form>
                        <?php else: ?>
                        <div class="alert alert-danger">
                            <h4>Error!</h4>
                            <p>Boat not found. Please select a valid boat to review.</p>
                            <a href="index.php" class="btn btn-primary">Back to Home</a>
                        </div>
                        <?php endif; ?>
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
