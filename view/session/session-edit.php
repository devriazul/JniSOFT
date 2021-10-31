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
  $operation = 'session-edit';
  $page_title = 'Edit Session';

  $db = new App\session\Session();
  $settings = new App\settings\Settings();

  //check access permission for this user
  if (!$settings->get_user_permission($user_role, $operation)) {
    header("Location: ../home/403.php");
    die();
  }

  //if url is not valid
  if(!isset($_GET['session'])) {
    header("Location: ../home/404.php");
    die();
  }

  //if course id is not in our database
  if(!$db->assign(array('session_id'=> $_GET['session']))->check() ) {
     header("Location: ../home/404.php");
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

  //grab the course data
  $session = $db->assign(array('session_id'=> $_GET['session']))->single();

  //generate form valdation code
  $filter = new App\filter\Filter();
  $validator = $filter->session_create();
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
              <h2>Edit Session </h2>
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
              <form id="myform" action="_update.php" method="POST" class="form-horizontal form-label-left">
                <div class="form-group">
                  <label class="control-label col-md-3 col-sm-3 col-xs-12">Session Name</label>
                  <div class="col-md-6">
                    <input name="session_name" value="<?php echo $session['session_name']; ?>" required type="text" class="form-control" placeholder="">
                  </div>
                </div>
                <input type="hidden" name="id" value="<?php echo $_GET['session']; ?>">
                <div class="ln_solid"></div>
                <div class="form-group">
                  <div class="col-md-9 col-sm-9 col-xs-12 col-md-offset-3">
                    <a href="../session/index.php" class="btn btn-primary"><span class="fa fa-angle-double-left"></span> Back</a>
                    <button type="submit" class="btn btn-success">Update</button>
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
