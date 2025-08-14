<?php session_start();


include('includes/config.php');
error_reporting(0);

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <title>Boat Booking System || Home Page</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <link href="https://fonts.googleapis.com/css?family=Oswald:400,700|Dancing+Script:400,700|Muli:300,400" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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

  <style>
    .star-rating {
      color: #ffc107;
      font-size: 18px;
    }
    .star-rating .empty {
      color: #e0e0e0;
    }
    .review-card {
      border: 1px solid #e0e0e0;
      border-radius: 8px;
      padding: 20px;
      margin-bottom: 15px;
      background: #f9f9f9;
    }
    .rating-summary {
      background: #fff;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      margin-bottom: 30px;
    }
  </style>

</head>

<body data-spy="scroll" data-target=".site-navbar-target" data-offset="300">

  <div class="site-wrap">

  


    
    <?php include_once("includes/navbar.php");?>
    
    <div class="intro-section" style="background-image: url('images/hero_2.jpg');">
        <div class="container">
          <div class="row align-items-center">
            <div class="col-lg-7 mx-auto text-center" data-aos="fade-up">
              <h1>Our Services</h1>
              <p><a href="#" class="btn btn-primary py-3 px-5">Contact</a></p>
            </div>
          </div>
        </div>
      </div>
    
    <div class="site-section">
      <div class="container">
        <?php 
$bid=$_GET['bid'];
$query = mysqli_query($con, "SELECT * FROM tblboat WHERE ID='$bid'");
$cnt=1;
while($result=mysqli_fetch_array($query)){
?>
        <div class="row">
          <div class="col-md-7">
            <p><img src="admin/images/<?php echo $result['Image'];?>" alt="Image" class="img-fluid"></p>
          </div>
          <div class="col-md-5">
            
            <h3 class="heading-92913 text-black">Boat Details</h3>
                <p><strong>Boat Name:</Strong>  <?php echo $result['BoatName'];?></p>
                <p><strong>Boat Size:</Strong>  <?php echo $result['Size'];?></p>
                <p><strong>Capacity of Boat:</Strong>  <?php echo $result['Capacity'];?> persons.</p>
              <p><strong>Source:</Strong>  <?php echo $result['Source'];?></p> 
              <p><strong>Destination: <?php echo $result['Destination'];?></strong> </p>
                <p><strong>Route: <?php echo $result['Route'];?></strong> </p>
                <p><strong>Price: <?php echo $result['Price'];?>(per head)</strong> </p>
                <p><strong>Description: <?php echo $result['Description'];?></strong> </p>

              
              <div class="form-group col-md-12">
              
                <a href="book-boat.php?bid=<?php echo $result['ID']; ?>"  class="btn btn-primary py-3 px-5">
                 Book Now</a>
                 
                <a href="leave-review.php?bid=<?php echo $result['ID']; ?>"  class="btn btn-success py-3 px-5 ml-2">
                 Leave Review</a>
              </div>

            
          </div>
        </div>
      </div><?php } ?>
      
      <!-- Reviews Section -->
      <div class="row mt-5">
        <div class="col-md-12">
          <h3>Customer Reviews & Ratings</h3>
          
          <?php
          // Get average rating and review count
          $bid = $_GET['bid'];
          $rating_query = mysqli_query($con, "SELECT AVG(Rating) as avg_rating, COUNT(*) as review_count FROM tblreviews WHERE BoatID='$bid' AND IsApproved=1");
          $rating_data = mysqli_fetch_array($rating_query);
          $avg_rating = round($rating_data['avg_rating'], 1);
          $review_count = $rating_data['review_count'];
          ?>
          
          <!-- Rating Summary -->
          <div class="rating-summary">
            <div class="row">
              <div class="col-md-3 text-center">
                <h2 class="mb-0"><?php echo $avg_rating > 0 ? $avg_rating : 'No ratings'; ?></h2>
                <div class="star-rating mb-2">
                  <?php 
                  for($i = 1; $i <= 5; $i++) {
                    if($i <= $avg_rating) {
                      echo '<i class="fas fa-star"></i>';
                    } else {
                      echo '<i class="fas fa-star empty"></i>';
                    }
                  }
                  ?>
                </div>
                <small class="text-muted"><?php echo $review_count; ?> reviews</small>
              </div>
              <div class="col-md-9">
                <?php
                // Get rating distribution
                for($star = 5; $star >= 1; $star--) {
                  $star_query = mysqli_query($con, "SELECT COUNT(*) as count FROM tblreviews WHERE BoatID='$bid' AND Rating='$star' AND IsApproved=1");
                  $star_data = mysqli_fetch_array($star_query);
                  $star_count = $star_data['count'];
                  $percentage = $review_count > 0 ? ($star_count / $review_count) * 100 : 0;
                ?>
                <div class="d-flex align-items-center mb-2">
                  <span class="mr-2"><?php echo $star; ?> star</span>
                  <div class="progress flex-fill mr-2" style="height: 8px;">
                    <div class="progress-bar bg-warning" style="width: <?php echo $percentage; ?>%"></div>
                  </div>
                  <span class="text-muted"><?php echo $star_count; ?></span>
                </div>
                <?php } ?>
              </div>
            </div>
          </div>
          
          <!-- Individual Reviews -->
          <?php
          $reviews_query = mysqli_query($con, "SELECT * FROM tblreviews WHERE BoatID='$bid' AND IsApproved=1 ORDER BY ReviewDate DESC LIMIT 10");
          
          if(mysqli_num_rows($reviews_query) > 0) {
            while($review = mysqli_fetch_array($reviews_query)) {
          ?>
          <div class="review-card">
            <div class="d-flex justify-content-between align-items-start mb-2">
              <div>
                <h5 class="mb-1"><?php echo htmlspecialchars($review['CustomerName']); ?></h5>
                <div class="star-rating">
                  <?php 
                  for($i = 1; $i <= 5; $i++) {
                    if($i <= $review['Rating']) {
                      echo '<i class="fas fa-star"></i>';
                    } else {
                      echo '<i class="fas fa-star empty"></i>';
                    }
                  }
                  ?>
                </div>
              </div>
              <small class="text-muted"><?php echo date('M d, Y', strtotime($review['ReviewDate'])); ?></small>
            </div>
            
            <?php if($review['ReviewTitle']): ?>
            <h6 class="mb-2"><?php echo htmlspecialchars($review['ReviewTitle']); ?></h6>
            <?php endif; ?>
            
            <?php if($review['ReviewText']): ?>
            <p class="mb-2"><?php echo htmlspecialchars($review['ReviewText']); ?></p>
            <?php endif; ?>
            
            <?php if($review['AdminResponse']): ?>
            <div class="mt-3 p-3 bg-light rounded">
              <strong>Response from Management:</strong>
              <p class="mb-0 mt-1"><?php echo htmlspecialchars($review['AdminResponse']); ?></p>
            </div>
            <?php endif; ?>
          </div>
          <?php 
            }
          } else {
            echo '<div class="alert alert-info">No reviews yet. Be the first to review this boat!</div>';
          }
          ?>
        </div>
      </div>
    </div>
    

    <div class="site-section bg-image overlay" style="background-image: url('images/hero_2.jpg');">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-md-7 text-center">
            <h2 class="text-white">Get In Touch With Us</h2>
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

</body>

</html>