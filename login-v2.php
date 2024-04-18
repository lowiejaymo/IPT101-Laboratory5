<?php
require_once 'googleconfig.php';
require_once ('facebookconfig.php');


$redirectTo = "http://localhost/IPT101-Laboratory5/callback.php";
$data = ['email'];
$fullURL = $handler->getLoginUrl($redirectTo, $data);

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>AdminLTE 3 | Log in</title>

  <link rel="icon" type="image/ico" href="favicon.ico">

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet"
    href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="AdminLTE-3.2.0/plugins/fontawesome-free/css/all.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="AdminLTE-3.2.0/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="AdminLTE-3.2.0/dist/css/adminlte.min.css">
  <style>
    .btn {
      box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
      /* Adjust values as needed */
    }
  </style>
</head>

<body class="hold-transition login-page">
  <div class="login-box">
    <!-- /.login-logo -->
    <div class="card card-outline card-primary">
      <div class="card-header text-center">
        <a href="https://adminlte.io/" class="h1"><b>Admin</b>LTE</a>
      </div>
      <div class="card-body">
        <p class="login-box-msg">Sign in to start your session</p>

        <!-- This error message will appear when there is error with user's input -->
        <?php if (isset ($_GET['error'])) { ?>
          <!-- alert alert-danger, used to make the error message color into red -->
          <div class="alert alert-danger">
            <?php echo $_GET['error']; ?>
          </div>
        <?php } ?>

        <?php if (isset ($_GET['success'])) { ?>
          <!-- alert alert-danger, used to make the error message color into red -->
          <div class="alert alert-success">
            <?php echo $_GET['success']; ?>
          </div>
        <?php } ?>
        <!-- This form will be submitted to login-be.php -->
        <form action="indexes/login-be.php" method="post">
          <!-- Email Address Input -->
          <div class="input-group mb-3">
            <input type="email" class="form-control" name="email" placeholder="Email">
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-envelope"></span>
              </div>
            </div>
          </div>
          <!-- Password Input -->
          <div class="input-group mb-3">
            <input type="password" class="form-control" name="password" placeholder="Password">
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-lock"></span>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-8">
              <div class="icheck-primary">
                <input type="checkbox" id="remember">
                <label for="remember">
                  Remember Me
                </label>
              </div>
            </div>
            <!-- submit button -->
            <div class="col-4">
              <button type="submit" class="btn btn-primary btn-block" value="Login" name="login">Sign In</button>
            </div>
            <!-- /.col -->
          </div>
        </form>
        <hr>

        <div class="social-auth-links text-center mt-2 mb-3">


          <a class="btn btn-block btn-primary d-flex align-items-center justify-content-center" onclick="window.location = '<?php echo $fullURL ?>'">
            <i class="fab fa-brands fa-facebook-f mr-2"></i> Sign in using Facebook
          </a>

          
          <div class="social-auth-links text-center mt-2 mb-3">
            <a href="<?php echo $client->createAuthUrl(); ?>"
              class="btn btn-block btn-light d-flex align-items-center justify-content-center">
              <img src="icons/google_icon.png" alt="Google Icon" class="mr-2" style="height: 1em;">
              <span class="align-middle">Sign in using Google</span>
            </a>
          </div>


        </div>
        <!-- /.social-auth-links -->

        <!-- <p class="mb-1">
          <a href="#">I forgot my password</a>
        </p> -->
        <p class="mb-0">
          <a href="register-v2.php" class="text-center">Create a new account</a>
        </p>
      </div>
      <!-- /.card-body -->
    </div>
    <!-- /.card -->
  </div>
  <!-- /.login-box -->

  <!-- jQuery -->
  <script src="AdminLTE-3.2.0/plugins/jquery/jquery.min.js"></script>
  <!-- Bootstrap 4 -->
  <script src="AdminLTE-3.2.0/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- AdminLTE App -->
  <script src="AdminLTE-3.2.0/dist/js/adminlte.min.js"></script>
</body>

</html>