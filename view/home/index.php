<?php 
  include_once('../../vendor/autoload.php');

  if (session_status() == PHP_SESSION_NONE) {
     session_start();
  }
  //if not logged in redirect him
  if (!isset($_SESSION['user_roll'])) {
    header("Location: ../home/login.php");
  }

  $user_role = $_SESSION['user_roll'];

  $settings = new App\settings\Settings();
  $db_student = new App\student\Student();
  $db_course = new App\course\Course();
  $db_user = new App\user\User();
  $db_db = new App\db\Db();
  $db_session = new App\session\Session();

  $css = '<link href="../../assets/css/animate.min.css" rel="stylesheet">';
  // print page header
  $settings->header('Home', $css); 
  $operation = 'home';
  $sidebar_data = array(
      'username'  => $_SESSION['user_name'],
      'user_role' => $user_role,
      'operation' =>  $operation
    );

  // print page sidebar
  $settings->sidebar($sidebar_data); 
  $total_student = $db_student->total();
  $total_course = $db_course->total_distinc_course_number();
  $total_teacher = $db_user->assign(array('user_type' => 'viewer'))->total_user_by_type();
  $total_admin = $db_user->assign(array('user_type' => 'admin'))->total_user_by_type();
  $total_session = $db_session->total();

  $institute = $db_db->assign(array('key' => 'institute'))->get_value();
  $code = $db_db->assign(array('key' => 'college_code'))->get_value();

  $institute = is_array($institute)?$institute['_value']:'';
  $code = is_array($code)?$code['_value']:'';
  ?>

  <!-- page content -->
  <div class="right_col" role="main">
    <div class="row">
      <div class="col-md-12">
        <h1 style="text-align: center;"><?php echo !empty($institute)?ucwords($institute):'<span class="text-danger bg-danger">Please set institute name from settings!</span>'; ?></h1>
        <hr>
      </div>
    </div>
    <!-- first widget -->
    <div class="row top_tiles">
      <div class="animated flipInY col-lg-3 col-md-3 col-md-offset-2 col-sm-6 col-xs-12">
        <div class="tile-stats">
          <div class="icon"><i class="fa fa-graduation-cap"></i></div>
          <div class="count"><?php echo $total_student; ?></div>
          <h3>Total Students</h3>
          <br>
        </div>
      </div>
      <div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <div class="tile-stats">
          <div class="icon"><i class="fa fa-book"></i></div>
          <div class="count"><?php echo $total_course; ?></div>
          <h3>Subjects</h3>
          <br>
        </div>
      </div>
      <div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <div class="tile-stats">
          <div class="icon"><i class="fa fa-check-square-o"></i></div>
          <div class="count"><?php echo $total_session; ?></div>
          <h3>Sessions</h3>
          <br>
        </div>
      </div>
    </div>
    <!-- /first widget -->
    <!-- 2nd widget -->
    <div class="row">
      <div class="animated flipInY col-lg-3 col-md-3 col-md-offset-3 col-sm-6 col-xs-12">
        <div class="tile-stats">
          <div class="icon"><i class="fa fa-users"></i></div>
          <div class="count"><?php echo $total_admin; ?></div>
          <h3>Admins</h3>
          <br>
        </div>
      </div>
      <div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
        <div class="tile-stats">
          <div class="icon"><i class="fa fa-users"></i></div>
          <div class="count"><?php echo $total_teacher; ?></div>
          <h3>Viewer</h3>
          <br>
        </div>
      </div>
    </div>
    <!-- /2nd widget -->

    <div class="row">
      <div class="col-md-12">
      <br><br>
        <h1 >Help</h1>
      </div>
    </div>
    
    <!-- help link -->
    <div class="row">
      <div class="col-md-6">
        <div class="list-group">
          <a href="#" class="list-group-item active">
            Basic
          </a>
          <a href="#" class="list-group-item"><span class="fa fa-angle-double-right"></span>
            How to add a student
          </a>
          <a href="#" class="list-group-item"><span class="fa fa-angle-double-right"></span>
           How to add marks
           </a>
          <a href="#" class="list-group-item"><span class="fa fa-angle-double-right"></span>
          How to add session
          </a>
          <a href="#" class="list-group-item"><span class="fa fa-angle-double-right"></span>
          How to add course
          </a>
        </div>
      </div>
      <div class="col-md-6">
        <div class="list-group">
          <a href="#" class="list-group-item active">
            Advance
          </a>
          <a href="#" class="list-group-item"><span class="fa fa-angle-double-right"></span> Software Flow</a>
          <a href="#" class="list-group-item"><span class="fa fa-angle-double-right"></span> Add new course for a new session</a>
          <a href="#" class="list-group-item"><span class="fa fa-angle-double-right"></span> Delete a old course for new session</a>
          <a href="#" class="list-group-item"><span class="fa fa-angle-double-right"></span> Trouble shooting</a>
        </div>

      </div>
    </div>
    <!-- /help link -->



  </div>
  <!-- /page content -->

  <!-- page footer -->
  <?php $settings->footer(); ?>
