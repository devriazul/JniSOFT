<?php 
  include_once('../../vendor/autoload.php');

  if (session_status() == PHP_SESSION_NONE) {
    session_start();
  }

  //if already logged in redirect him
  if (isset($_SESSION['user_roll'])) {
    header("Location: ../home/index.php");
  }

  //check login
  if (isset($_POST['email']) && isset($_POST['password'])) {
    $db_user = new App\user\User();
    $user = $db_user->assign(array('email' => $_POST['email']))->single_by_email();

    if(md5($_POST['password']) == $user['password'] ) {
      $_SESSION['user_roll'] = $user['user_type'];
      $_SESSION['user_id']  = $user['user_id'];
      $_SESSION['user_name']  = $user['user_name'];

      header("Location: ../home/index.php");
    }else{
      $_SESSION['error_msg'] = 'Email and Password didn\'t match';
    }
  }

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

  <body class="login">
    <div>
      <a class="hiddenanchor" id="signup"></a>
      <a class="hiddenanchor" id="signin"></a>

      <div class="login_wrapper">
        <div class="animate form login_form">
          <section class="login_content">
            <form action="login.php" method="post">
              <h1>Login Form</h1>
              <?php 
                if (isset($_SESSION['error_msg'])) {
                  echo '<h2 class="text-danger">'.$_SESSION['error_msg'].'</h2>';
                  unset($_SESSION['error_msg']);
                }

              ?>
                
              <div>
                <input type="text" name="email" class="form-control" placeholder="Email" required="" />
              </div>
              <div>
                <input type="password" name="password" class="form-control" placeholder="Password" required="" />
              </div>
              <div>
                <button type="submit" class="btn btn-default submit">Log in</button>
                <!-- <a class="reset_pass" href="#signup">Lost your password?</a>-->
              </div>

              <div class="clearfix"></div>

              <div class="separator">
                <div class="clearfix"></div>
                <br />

                <div>
                  Developed by <a style="margin-right: 0px; text-decoration:none;" target="_new"  href="http://it.lopagroup.org/"><i class="fa fa-line-chart"> </i> Lopa IT </a>,
                  A Sister Concern of <a target="_new" style="margin-right: 0px;" href="http://lopagroup.org/">Lopa Group</a>
                </div>
              </div>
            </form>
          </section>
        </div>
<!--
        <div id="register" class="animate form registration_form">
          <section class="login_content">
            <form>
              <h1> Reset Password  </h1>
              <div>
                <input type="email" class="form-control" placeholder="Email" required="" />
              </div>
              <div>
                <a class="btn btn-default submit" href="index.html">Reset Password</a>
              </div>

              <div class="clearfix"></div>

              <div class="separator">
                <p class="change_link">Already a member ?
                  <a href="#signin" class="to_register"> Log in </a>
                </p>

                <div class="clearfix"></div>
                <br />

                <div>
                  <a style="margin-right: 0px; text-decoration:none;" target="_new"  href="http://it.lopagroup.org/"><h1><i class="fa fa-line-chart"> </i> Lopa IT</h1></a>
                  <p>©<?php echo date('Y'); ?> All Rights Reserved. <a target="_new" style="margin-right: 0px;" href="http://it.lopagroup.org/">Lopa IT</a>, High Performance. Delivered.</p>
                </div>
              </div>
              -->
            </form>
          </section>
        </div>
      </div>
    </div>
  </body>
</html>

