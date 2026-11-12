<?php 
require_once('../config.php');

$db_status_ok = ($conn && $conn instanceof mysqli && !$conn->connect_error);
$db_error_msg = (!$db_status_ok) ? (($conn && $conn->connect_error) ? $conn->connect_error : ($db->conn_error ?? 'Unable to connect to database server')) : '';

$login_error = '';
// Handle direct/traditional POST fallback
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username']) && isset($_POST['password'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (!$db_status_ok) {
        $login_error = 'Database is not connected! ' . $db_error_msg;
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
        if ($stmt) {
            $pw = md5($password);
            $stmt->bind_param('ss', $username, $pw);
            $stmt->execute();
            $qry = $stmt->get_result();
            if ($qry && $qry->num_rows > 0) {
                $res = $qry->fetch_array();
                foreach ($res as $k => $v) {
                    if (!is_numeric($k) && $k != 'password') {
                        $_settings->set_userdata($k, $v);
                    }
                }
                $_settings->set_userdata('login_type', 1);
                header("Location: " . base_url . "admin/index.php");
                exit;
            } else {
                $login_error = 'Incorrect username or password.';
            }
        } else {
            $login_error = 'Query failed: ' . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="" style="height: auto;">
 <?php require_once('inc/header.php') ?>
<body class="hold-transition login-page">
  <script>
    start_loader()
  </script>
  <style>
    body{
      background-image: url("<?php echo validate_image($_settings->info('cover')) ?>");
      background-size:cover;
      background-repeat:no-repeat;
      backdrop-filter: contrast(1);
    }
    #page-title{
      text-shadow: 6px 4px 7px black;
      font-size: 3em;
      color: #fff4f4 !important;
      background: #8080801c;
    }
  </style>
  <h1 class="text-center text-white px-4 py-4" id="page-title"><b><?php echo $_settings->info('name') ?: 'Real Estate Portal System' ?></b></h1>
<div class="login-box">
  <?php if (!$db_status_ok): ?>
    <div class="alert alert-warning shadow-sm mb-3">
      <h5><i class="fas fa-exclamation-triangle mr-2"></i> Database Offline</h5>
      <p class="small mb-2"><?php echo htmlspecialchars($db_error_msg); ?></p>
      <a href="<?php echo base_url ?>db_setup.php" class="btn btn-dark btn-sm btn-block"><i class="fas fa-tools mr-1"></i> Open Database Setup Tool</a>
    </div>
  <?php endif; ?>

  <?php if (!empty($login_error)): ?>
    <div class="alert alert-danger shadow-sm mb-3">
      <i class="fas fa-exclamation-circle mr-2"></i> <?php echo htmlspecialchars($login_error); ?>
    </div>
  <?php endif; ?>

  <div class="card card-primary my-2 shadow">
    <div class="card-body">
      <p class="login-box-msg font-weight-bold">Administrator Sign In</p>
      <form id="login-frm" action="" method="post">
        <div class="input-group mb-3">
          <input type="text" class="form-control" name="username" autofocus placeholder="Username" value="admin" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-user"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="password" class="form-control" name="password" placeholder="Password" value="admin123" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-7">
            <a href="<?php echo base_url ?>">Go to Website</a>
          </div>
          <!-- /.col -->
          <div class="col-5">
            <button type="submit" class="btn btn-primary btn-block">Sign In</button>
          </div>
          <!-- /.col -->
        </div>
      </form>
      
      <div class="text-center mt-3 pt-2 border-top">
        <a href="<?php echo base_url ?>db_setup.php" class="text-muted small"><i class="fas fa-database mr-1"></i> Database Health & Setup</a>
      </div>
    </div>
    <!-- /.card-body -->
  </div>
  <!-- /.card -->
</div>
<!-- /.login-box -->

<!-- jQuery -->
<script src="<?php echo base_url ?>plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="<?php echo base_url ?>plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="<?php echo base_url ?>dist/js/adminlte.min.js"></script>

<script>
  $(document).ready(function(){
    end_loader();
  })
</script>
</body>
</html>
