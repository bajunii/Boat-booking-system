<?php
session_start();
include('includes/config.php');
//Validating Session
if(!isset($_SESSION['aid']) || strlen($_SESSION['aid'])==0)
{ 
    header('location:index.php');
    exit();
}

// Handle approve/reject actions
if(isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $review_id = $_GET['id'];
    
    if($action == 'approve') {
        $query = mysqli_query($con, "UPDATE tblreviews SET IsApproved=1 WHERE ID='$review_id'");
        if($query) {
            echo "<script>alert('Review approved successfully');</script>";
        }
    } elseif($action == 'reject') {
        $query = mysqli_query($con, "UPDATE tblreviews SET IsApproved=0 WHERE ID='$review_id'");
        if($query) {
            echo "<script>alert('Review rejected successfully');</script>";
        }
    } elseif($action == 'delete') {
        $query = mysqli_query($con, "DELETE FROM tblreviews WHERE ID='$review_id'");
        if($query) {
            echo "<script>alert('Review deleted successfully');</script>";
        }
    }
}

// Handle admin response
if(isset($_POST['submit_response'])) {
    $review_id = $_POST['review_id'];
    $admin_response = $_POST['admin_response'];
    
    $query = mysqli_query($con, "UPDATE tblreviews SET AdminResponse='$admin_response' WHERE ID='$review_id'");
    if($query) {
        echo "<script>alert('Response added successfully');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Manage Reviews | Boat Booking System</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <!-- DataTables -->
  <link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  
  <style>
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
    
    .modal-content {
      border-radius: 10px;
    }
    
    .badge {
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
            <h1 class="m-0">Manage Reviews</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
              <li class="breadcrumb-item active">Manage Reviews</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Customer Reviews</h3>
              </div>
              <div class="card-body">
                <table id="reviewsTable" class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th>S.No</th>
                      <th>Date</th>
                      <th>Customer</th>
                      <th>Boat</th>
                      <th>Rating</th>
                      <th>Review</th>
                      <th>Status</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $query = mysqli_query($con, "SELECT r.*, b.BoatName 
                                                FROM tblreviews r 
                                                JOIN tblboat b ON r.BoatID = b.ID 
                                                ORDER BY r.ReviewDate DESC");
                    $cnt = 1;
                    while($row = mysqli_fetch_array($query)) {
                    ?>
                    <tr>
                      <td><?php echo $cnt; ?></td>
                      <td><?php echo date('M d, Y', strtotime($row['ReviewDate'])); ?></td>
                      <td>
                        <strong><?php echo htmlspecialchars($row['CustomerName']); ?></strong><br>
                        <small class="text-muted"><?php echo htmlspecialchars($row['CustomerEmail']); ?></small>
                      </td>
                      <td><?php echo htmlspecialchars($row['BoatName']); ?></td>
                      <td>
                        <?php 
                        for($i = 1; $i <= 5; $i++) {
                          if($i <= $row['Rating']) {
                            echo '<i class="fas fa-star text-warning"></i>';
                          } else {
                            echo '<i class="far fa-star text-muted"></i>';
                          }
                        }
                        echo ' (' . $row['Rating'] . '/5)';
                        ?>
                      </td>
                      <td>
                        <?php if($row['ReviewTitle']): ?>
                          <strong><?php echo htmlspecialchars($row['ReviewTitle']); ?></strong><br>
                        <?php endif; ?>
                        <?php echo substr(htmlspecialchars($row['ReviewText']), 0, 100); ?>
                        <?php if(strlen($row['ReviewText']) > 100): ?>...<?php endif; ?>
                      </td>
                      <td>
                        <?php if($row['IsApproved']): ?>
                          <span class="badge badge-success">Approved</span>
                        <?php else: ?>
                          <span class="badge badge-warning">Pending</span>
                        <?php endif; ?>
                      </td>
                      <td>
                        <div class="btn-group" role="group">
                          <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#viewModal<?php echo $row['ID']; ?>">
                            <i class="fas fa-eye"></i>
                          </button>
                          
                          <?php if(!$row['IsApproved']): ?>
                          <a href="?action=approve&id=<?php echo $row['ID']; ?>" class="btn btn-sm btn-success" onclick="return confirm('Approve this review?')">
                            <i class="fas fa-check"></i>
                          </a>
                          <?php else: ?>
                          <a href="?action=reject&id=<?php echo $row['ID']; ?>" class="btn btn-sm btn-warning" onclick="return confirm('Reject this review?')">
                            <i class="fas fa-times"></i>
                          </a>
                          <?php endif; ?>
                          
                          <a href="?action=delete&id=<?php echo $row['ID']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this review permanently?')">
                            <i class="fas fa-trash"></i>
                          </a>
                        </div>
                      </td>
                    </tr>

                    <!-- View Modal -->
                    <div class="modal fade" id="viewModal<?php echo $row['ID']; ?>" tabindex="-1" role="dialog">
                      <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title">Review Details</h5>
                            <button type="button" class="close" data-dismiss="modal">
                              <span>&times;</span>
                            </button>
                          </div>
                          <div class="modal-body">
                            <div class="row">
                              <div class="col-md-6">
                                <p><strong>Customer:</strong> <?php echo htmlspecialchars($row['CustomerName']); ?></p>
                                <p><strong>Email:</strong> <?php echo htmlspecialchars($row['CustomerEmail']); ?></p>
                                <p><strong>Boat:</strong> <?php echo htmlspecialchars($row['BoatName']); ?></p>
                                <p><strong>Date:</strong> <?php echo date('M d, Y H:i', strtotime($row['ReviewDate'])); ?></p>
                              </div>
                              <div class="col-md-6">
                                <p><strong>Rating:</strong> 
                                  <?php 
                                  for($i = 1; $i <= 5; $i++) {
                                    if($i <= $row['Rating']) {
                                      echo '<i class="fas fa-star text-warning"></i>';
                                    } else {
                                      echo '<i class="far fa-star text-muted"></i>';
                                    }
                                  }
                                  echo ' (' . $row['Rating'] . '/5)';
                                  ?>
                                </p>
                                <p><strong>Status:</strong> 
                                  <?php if($row['IsApproved']): ?>
                                    <span class="badge badge-success">Approved</span>
                                  <?php else: ?>
                                    <span class="badge badge-warning">Pending</span>
                                  <?php endif; ?>
                                </p>
                              </div>
                            </div>
                            
                            <?php if($row['ReviewTitle']): ?>
                            <div class="mt-3">
                              <strong>Review Title:</strong>
                              <p><?php echo htmlspecialchars($row['ReviewTitle']); ?></p>
                            </div>
                            <?php endif; ?>
                            
                            <?php if($row['ReviewText']): ?>
                            <div class="mt-3">
                              <strong>Review Text:</strong>
                              <p><?php echo nl2br(htmlspecialchars($row['ReviewText'])); ?></p>
                            </div>
                            <?php endif; ?>
                            
                            <!-- Admin Response Section -->
                            <div class="mt-4">
                              <strong>Admin Response:</strong>
                              <?php if($row['AdminResponse']): ?>
                                <div class="alert alert-info mt-2">
                                  <?php echo nl2br(htmlspecialchars($row['AdminResponse'])); ?>
                                </div>
                              <?php endif; ?>
                              
                              <form method="post" class="mt-2">
                                <input type="hidden" name="review_id" value="<?php echo $row['ID']; ?>">
                                <div class="form-group">
                                  <textarea name="admin_response" class="form-control" rows="3" placeholder="Add your response..."><?php echo htmlspecialchars($row['AdminResponse']); ?></textarea>
                                </div>
                                <button type="submit" name="submit_response" class="btn btn-primary">
                                  <?php echo $row['AdminResponse'] ? 'Update Response' : 'Add Response'; ?>
                                </button>
                              </form>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>

                    <?php 
                    $cnt++;
                    } 
                    ?>
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

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- DataTables -->
<script src="plugins/datatables/jquery.dataTables.min.js"></script>
<script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.js"></script>

<script>
$(function () {
  $("#reviewsTable").DataTable({
    "responsive": true,
    "lengthChange": false,
    "autoWidth": false,
    "order": [[ 1, "desc" ]]
  });
});
</script>
</body>
</html>
