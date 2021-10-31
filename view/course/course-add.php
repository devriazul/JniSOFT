<?php 
  include_once('../../vendor/autoload.php');

  if (session_status() == PHP_SESSION_NONE) {
    session_start();
  }
  //if not logged in redirect him
  if (!isset($_SESSION['user_roll'])) {
    header("Location: ../home/login.php");
  }

  //TODO: get user role from session or DB
  $user_role = $_SESSION['user_roll'];
  $operation = 'course-add';
  $page_title = 'Add New Subject';

  $settings = new App\settings\Settings();
  /*$db_course = new App\course\Course();*/
  $db_session = new App\session\Session();

  //check access permission for this user
  if (!$settings->get_user_permission($user_role, $operation)) {
    header("Location: ../home/403.php");
    die();
  }
  $sessions = $db_session->all(100, 0);
/*
  include_once('../../vendor/autoload.php');

  if (session_status() == PHP_SESSION_NONE) {
    session_start();
  }
  
  $settings = new App\settings\Settings();
  $db_session = new App\session\session();
  $sessions = $db_session->all(100, 0);
  
  //if not logged in redirect him
  if (!isset($_SESSION['user_roll'])) {
    header("Location: ../home/login.php");
  }

  
  

  //TODO: get user role from session or DB
  $user_role = $_SESSION['user_roll'];
  $operation = 'course-add';
  $page_title = 'Add Subject';

  */

  //check access permission for this user
  if (!$settings->get_user_permission($user_role, $operation)) {
    header("Location: ../home/403.php");
    die();
  }

  // print page header
  $settings->header($page_title); 
  
  $sidebar_data = array(
      'username'  => $_SESSION['user_name'],
      'user_role' => $user_role,
      'operation' =>  $operation
    );

  // print page sidebar
  $settings->sidebar($sidebar_data); 

  //generate form valdation code
  $filter = new App\filter\Filter();
  $validator = $filter->course_add();
  $jquery_validator = new HybridLogic\Validation\ClientSide\jQueryValidator($validator);
  $jquery = $jquery_validator->generate();
  ?>

  <!-- page content -->
  <div class="right_col" role="main">
    <!-- <h1>Add course</h1> -->
    <div class="row">
      <div class="col-md-12">
          <div class="x_panel">
            <div class="x_title">
              <h2>Add Subject </h2>
              <ul class="nav navbar-right panel_toolbox">
                <li><a class="close-link"></a></li>
                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                <li><a class="close-link"><i class="fa fa-close"></i></a>
                </li>
              </ul>
              <div class="clearfix"></div>
            </div>
            <div class="x_content">
              <?php 
                //success message
                if(isset($_SESSION['success_msg'])) {
                  echo ' <h4 class="alert alert-success">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>';
                  echo $_SESSION['success_msg'];
                  unset($_SESSION['success_msg']);
                  echo '</h4>';
                }
                //error message
                if(isset($_SESSION['error_msg'])) {
                  echo ' <h4 class="alert alert-danger">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>';
                  echo $_SESSION['error_msg'];
                  unset($_SESSION['error_msg']);
                  echo '</h4>';
                }

              ?>
              <br />
              <form id="myform" action="_store.php" method="POST" class="form-horizontal form-label-left">
                <div class="form-group">
                  <label class="control-label col-md-3 col-sm-3 col-xs-12">Subject Name</label>
                  <div class="col-md-6">
                    <input name="course_name" required type="text" class="form-control" placeholder="">
                  </div>
                </div>
                <div class="form-group">
                  <label class="control-label col-md-3 col-sm-3 col-xs-12">Subject Code</label>
                  <div class="col-md-6">
                    <input name="course_code" required type="text" class="form-control" placeholder="">
                  </div>
                </div>
                <div class="form-group">
                  <label class="control-label col-md-3 col-sm-3 col-xs-12">Credit</label>
                  <div class="col-md-6">
                    <input name="credit" required type="number" class="form-control" placeholder="">
                  </div>
                </div>
                <div class="form-group">
                  <label class="control-label col-md-3">Session</label>
                  <div class="col-md-6">
                    <select name="session" required="required" class="form-control">
                      <option value="">Choose a Session</option>
                      <?php 
                        foreach ($sessions as $session) {
                          echo '<option value="'.$session['session_name'].'">'.$session['session_name'].'</option>';
                        }
                      ?>
                    </select>
                  </div>
                </div>
                <div class="form-group">
                  <label class="control-label col-md-3 col-sm-3 col-xs-12">Semester</label>
                  <div class="col-md-6">
                    <select name="semester" required="required" class="form-control">
                      <option value="">Choose a Semester</option>
                      <option value="1-1">1st Year 1st Semester</option>
                      <option value="1-2">1st Year 2nd Semester</option>
                      <option value="2-1">2nd Year 1st Semester</option>
                      <option value="2-2">2nd Year 2nd Semester</option>
                      <option value="3-1">3rd Year 1st Semester</option>
                      <option value="3-2">3rd Year 2nd Semester</option>
                    </select>
                  </div>
                </div>
                <div class="ln_solid"></div>
                <div class="form-group">
                  <div class="col-md-9 col-sm-9 col-xs-12 col-md-offset-3">
                    <a href="index.php" class="btn btn-primary"> <span class="fa fa-angle-double-left"></span> Back</a>
                    <button type="submit" class="btn btn-success">Save</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
      <!-- /row -->
  </div>
  <!-- /page content -->

  <!-- page footer -->
  <?php 
    $settings->footer($settings->get_validator_script($jquery)); 
  ?>
