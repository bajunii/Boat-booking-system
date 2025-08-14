<?php
session_start();
include('includes/config.php');
//Validating Session
if(!isset($_SESSION['aid']) || strlen($_SESSION['aid'])==0)
{ 
    header('location:index.php');
    exit();
}

// Get date range from URL or set defaults
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Analytics Dashboard | Boat Booking System</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  
  <style>
    .small-box {
      border-radius: 10px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      transition: transform 0.3s ease;
    }
    
    .small-box:hover {
      transform: translateY(-2px);
    }
    
    .card {
      border-radius: 10px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .table {
      background: #fff;
      border-radius: 8px;
    }
    
    .table thead th {
      background-color: #f8f9fa;
      font-weight: 600;
    }
    
    .btn {
      border-radius: 6px;
    }
  </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <!-- Navbar -->
  <?php include_once('includes/navbar.php');?>

  <!-- Main Sidebar Container -->
  <?php include_once('includes/sidebar.php');?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Analytics Dashboard</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
              <li class="breadcrumb-item active">Analytics</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        
        <!-- Date Range Filter -->
        <div class="row mb-3">
          <div class="col-12">
            <div class="card">
              <div class="card-body">
                <form method="GET" class="row">
                  <div class="col-md-4">
                    <label>Start Date:</label>
                    <input type="date" name="start_date" class="form-control" value="<?php echo $start_date; ?>">
                  </div>
                  <div class="col-md-4">
                    <label>End Date:</label>
                    <input type="date" name="end_date" class="form-control" value="<?php echo $end_date; ?>">
                  </div>
                  <div class="col-md-4">
                    <label>&nbsp;</label><br>
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="analytics-dashboard.php" class="btn btn-secondary">Reset</a>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>

        <!-- Revenue Analytics -->
        <div class="row">
          <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
              <div class="inner">
                <?php 
                $revenue_query = mysqli_query($con, "SELECT SUM(b.Price * bk.NumnerofPeople) as total_revenue 
                                                    FROM tblbookings bk 
                                                    JOIN tblboat b ON bk.BoatID = b.ID 
                                                    WHERE bk.BookingStatus = 'Accepted' 
                                                    AND DATE(bk.postingDate) BETWEEN '$start_date' AND '$end_date'");
                $revenue_data = mysqli_fetch_array($revenue_query);
                $total_revenue = $revenue_data['total_revenue'] ?? 0;
                ?>
                <h3>Kshs <?php echo number_format($total_revenue); ?></h3>
                <p>Total Revenue</p>
              </div>
              <div class="icon">
                <i class="ion ion-cash"></i>
              </div>
            </div>
          </div>

          <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
              <div class="inner">
                <?php 
                $bookings_query = mysqli_query($con, "SELECT COUNT(*) as total_bookings 
                                                     FROM tblbookings 
                                                     WHERE BookingStatus = 'Accepted' 
                                                     AND DATE(postingDate) BETWEEN '$start_date' AND '$end_date'");
                $bookings_data = mysqli_fetch_array($bookings_query);
                $total_bookings = $bookings_data['total_bookings'];
                ?>
                <h3><?php echo $total_bookings; ?></h3>
                <p>Completed Bookings</p>
              </div>
              <div class="icon">
                <i class="ion ion-checkmark-circled"></i>
              </div>
            </div>
          </div>

          <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
              <div class="inner">
                <?php 
                $avg_rating_query = mysqli_query($con, "SELECT AVG(Rating) as avg_rating FROM tblreviews WHERE IsApproved = 1");
                $avg_rating_data = mysqli_fetch_array($avg_rating_query);
                $avg_rating = round($avg_rating_data['avg_rating'], 1);
                ?>
                <h3><?php echo $avg_rating ?? 'N/A'; ?></h3>
                <p>Average Rating</p>
              </div>
              <div class="icon">
                <i class="ion ion-star"></i>
              </div>
            </div>
          </div>

          <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
              <div class="inner">
                <?php 
                $avg_booking_value = $total_bookings > 0 ? $total_revenue / $total_bookings : 0;
                ?>
                <h3>Kshs <?php echo number_format($avg_booking_value); ?></h3>
                <p>Avg Booking Value</p>
              </div>
              <div class="icon">
                <i class="ion ion-calculator"></i>
              </div>
            </div>
          </div>
        </div>

        <!-- Charts Row -->
        <div class="row">
          <!-- Revenue Chart -->
          <div class="col-md-6">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Monthly Revenue Trend</h3>
              </div>
              <div class="card-body">
                <canvas id="revenueChart" width="400" height="200"></canvas>
              </div>
            </div>
          </div>

          <!-- Bookings Chart -->
          <div class="col-md-6">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Monthly Bookings</h3>
              </div>
              <div class="card-body">
                <canvas id="bookingsChart" width="400" height="200"></canvas>
              </div>
            </div>
          </div>
        </div>

        <!-- Popular Routes & Top Boats -->
        <div class="row">
          <!-- Popular Routes -->
          <div class="col-md-6">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Popular Routes</h3>
              </div>
              <div class="card-body">
                <table class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th>Route</th>
                      <th>Bookings</th>
                      <th>Revenue</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $routes_query = mysqli_query($con, "SELECT 
                                                        CONCAT(b.Source, ' → ', b.Destination) as route,
                                                        COUNT(bk.ID) as booking_count,
                                                        SUM(b.Price * bk.NumnerofPeople) as route_revenue
                                                        FROM tblbookings bk 
                                                        JOIN tblboat b ON bk.BoatID = b.ID 
                                                        WHERE bk.BookingStatus = 'Accepted'
                                                        AND DATE(bk.postingDate) BETWEEN '$start_date' AND '$end_date'
                                                        GROUP BY b.Source, b.Destination 
                                                        ORDER BY booking_count DESC 
                                                        LIMIT 10");
                    
                    while($route = mysqli_fetch_array($routes_query)) {
                    ?>
                    <tr>
                      <td><?php echo htmlspecialchars($route['route']); ?></td>
                      <td><?php echo $route['booking_count']; ?></td>
                      <td>Kshs <?php echo number_format($route['route_revenue']); ?></td>
                    </tr>
                    <?php } ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- Top Performing Boats -->
          <div class="col-md-6">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Top Performing Boats</h3>
              </div>
              <div class="card-body">
                <table class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th>Boat Name</th>
                      <th>Bookings</th>
                      <th>Revenue</th>
                      <th>Avg Rating</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $boats_query = mysqli_query($con, "SELECT 
                                                      b.BoatName,
                                                      COUNT(bk.ID) as booking_count,
                                                      SUM(b.Price * bk.NumnerofPeople) as boat_revenue,
                                                      COALESCE(AVG(r.Rating), 0) as avg_rating
                                                      FROM tblboat b
                                                      LEFT JOIN tblbookings bk ON b.ID = bk.BoatID AND bk.BookingStatus = 'Accepted'
                                                      LEFT JOIN tblreviews r ON b.ID = r.BoatID AND r.IsApproved = 1
                                                      WHERE DATE(bk.postingDate) BETWEEN '$start_date' AND '$end_date'
                                                      GROUP BY b.ID, b.BoatName
                                                      ORDER BY booking_count DESC 
                                                      LIMIT 10");
                    
                    while($boat = mysqli_fetch_array($boats_query)) {
                    ?>
                    <tr>
                      <td><?php echo htmlspecialchars($boat['BoatName']); ?></td>
                      <td><?php echo $boat['booking_count']; ?></td>
                      <td>Kshs <?php echo number_format($boat['boat_revenue']); ?></td>
                      <td>
                        <?php echo round($boat['avg_rating'], 1); ?>
                        <i class="fas fa-star text-warning"></i>
                      </td>
                    </tr>
                    <?php } ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Reviews -->
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Recent Customer Reviews</h3>
              </div>
              <div class="card-body">
                <table class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th>Date</th>
                      <th>Customer</th>
                      <th>Boat</th>
                      <th>Rating</th>
                      <th>Review</th>
                      <th>Status</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $reviews_query = mysqli_query($con, "SELECT r.*, b.BoatName 
                                                        FROM tblreviews r 
                                                        JOIN tblboat b ON r.BoatID = b.ID 
                                                        ORDER BY r.ReviewDate DESC 
                                                        LIMIT 10");
                    
                    while($review = mysqli_fetch_array($reviews_query)) {
                    ?>
                    <tr>
                      <td><?php echo date('M d, Y', strtotime($review['ReviewDate'])); ?></td>
                      <td><?php echo htmlspecialchars($review['CustomerName']); ?></td>
                      <td><?php echo htmlspecialchars($review['BoatName']); ?></td>
                      <td>
                        <?php 
                        for($i = 1; $i <= 5; $i++) {
                          if($i <= $review['Rating']) {
                            echo '<i class="fas fa-star text-warning"></i>';
                          } else {
                            echo '<i class="far fa-star text-muted"></i>';
                          }
                        }
                        ?>
                      </td>
                      <td><?php echo substr(htmlspecialchars($review['ReviewText']), 0, 50) . '...'; ?></td>
                      <td>
                        <?php if($review['IsApproved']): ?>
                          <span class="badge badge-success">Approved</span>
                        <?php else: ?>
                          <span class="badge badge-warning">Pending</span>
                        <?php endif; ?>
                      </td>
                      <td>
                        <a href="manage-reviews.php?action=view&id=<?php echo $review['ID']; ?>" class="btn btn-sm btn-info">View</a>
                      </td>
                    </tr>
                    <?php } ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

      </div>
    </section>
  </div>

  <?php include_once('includes/footer.php');?>
</div>

<!-- Chart.js Scripts -->
<script>
// Revenue Chart
<?php
$revenue_chart_query = mysqli_query($con, "SELECT 
                                          DATE_FORMAT(bk.postingDate, '%Y-%m') as month,
                                          SUM(b.Price * bk.NumnerofPeople) as monthly_revenue
                                          FROM tblbookings bk 
                                          JOIN tblboat b ON bk.BoatID = b.ID 
                                          WHERE bk.BookingStatus = 'Accepted'
                                          AND bk.postingDate >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
                                          GROUP BY DATE_FORMAT(bk.postingDate, '%Y-%m')
                                          ORDER BY month");

$revenue_labels = [];
$revenue_data = [];
while($revenue_row = mysqli_fetch_array($revenue_chart_query)) {
    $revenue_labels[] = $revenue_row['month'];
    $revenue_data[] = $revenue_row['monthly_revenue'];
}
?>

const revenueCtx = document.getElementById('revenueChart').getContext('2d');
const revenueChart = new Chart(revenueCtx, {
    type: 'line',
    data: {
        labels: <?php echo json_encode($revenue_labels); ?>,
        datasets: [{
            label: 'Revenue (Kshs)',
            data: <?php echo json_encode($revenue_data); ?>,
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Bookings Chart
<?php
$bookings_chart_query = mysqli_query($con, "SELECT 
                                           DATE_FORMAT(postingDate, '%Y-%m') as month,
                                           COUNT(*) as monthly_bookings
                                           FROM tblbookings 
                                           WHERE BookingStatus = 'Accepted'
                                           AND postingDate >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
                                           GROUP BY DATE_FORMAT(postingDate, '%Y-%m')
                                           ORDER BY month");

$bookings_labels = [];
$bookings_data = [];
while($bookings_row = mysqli_fetch_array($bookings_chart_query)) {
    $bookings_labels[] = $bookings_row['month'];
    $bookings_data[] = $bookings_row['monthly_bookings'];
}
?>

const bookingsCtx = document.getElementById('bookingsChart').getContext('2d');
const bookingsChart = new Chart(bookingsCtx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($bookings_labels); ?>,
        datasets: [{
            label: 'Bookings',
            data: <?php echo json_encode($bookings_data); ?>,
            backgroundColor: 'rgba(54, 162, 235, 0.2)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.js"></script>
</body>
</html>
