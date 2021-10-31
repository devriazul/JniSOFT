<?php 
  include_once('../../vendor/autoload.php');
  if (session_status() == PHP_SESSION_NONE) {
    session_start();
  }

  if (!isset($_SESSION['user_roll'])) {
    header("Location: ../home/login.php");
  }

  //if already logged in redirect him
  if (isset($_SESSION['user_roll'])) {
    unset($_SESSION['user_roll']);
    unset($_SESSION['user_id']);
    unset($_SESSION['user_name']);
  }

	header('Location:login.php');
  ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Login Page </title>

    <!-- Bootstrap -->
    <link href="../../assets/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="../../assets/css/font-awesome.min.css" rel="stylesheet">
    <!-- NProgress -->
    <link href="../../assets/css/nprogress.css" rel="stylesheet">
    <!-- Animate.css -->
    <link href="../../assets/css/animate.min.css" rel="stylesheet">

    <!-- Custom Theme Style -->
    <link href="../../assets/css/custom.min.css" rel="stylesheet">
  </head>

  <body class="login"  style="background: url('../../assets/images/logout.jpg') no-repeat 30% -20% #cdaa94;">
    <div>
      <a class="hiddenanchor" id="signup"></a>
      <a class="hiddenanchor" id="signin"></a>

      <div class="login_wrapper">
        <div class="animate form login_form">
          <section class="login_content">
          <!-- <img src="../../assets/images/logout.jpg"> -->
          	<i class="fa fa-books"></i>
            <h1 class="text-primary">Successfully logged out</h1>
            <a href="../home/login.php" class="btn btn-dark btn-lg">Again Login ?</a>
          </section>
        </div>
      </div>
    </div>
  </body>
</html>

