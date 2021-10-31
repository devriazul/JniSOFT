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
  $operation = 'user-add';
  $page_title = "New User";
  $settings = new App\settings\Settings();

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
  $validator = $filter->user_create();
  $jquery_validator = new HybridLogic\Validation\ClientSide\jQueryValidator($validator);
  $jquery = $jquery_validator->generate();

  ?>

  <!-- page content -->
  <div class="right_col" role="main">
    <div class="row">
      <div class="col-md-12">
          <div class="x_panel">
            <div class="x_title">
              <h2>Create User </h2>
              <ul class="nav navbar-right panel_toolbox">
                <li><a class="close-link"></a></li>
                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                <li><a class="close-link"><i class="fa fa-close"></i></a>
                </li>
              </ul>
              <div class="clearfix"></div>
            </div>
            <div class="x_content">

              <br />
              <form id="create-user" action="_store.php" method="POST" class="form-horizontal form-label-left">
                
                <div class="form-group">
                  <label class="control-label col-md-3 col-sm-3 col-xs-12">Name</label>
                  <div class="col-md-6">
                    <input name="user_name" required type="text" class="form-control" placeholder="">
                  </div>
                </div>

                <div class="form-group">
                  <label class="control-label col-md-3 col-sm-3 col-xs-12">Email</label>
                  <div class="col-md-6">
                    <input name="email" required type="email" class="form-control" placeholder="">
                  </div>
                </div>

                <div class="form-group">
                  <label class="control-label col-md-3 col-sm-3 col-xs-12">Password</label>
                  <div class="col-md-6">
                    <input name="password" required type="Password" class="form-control" placeholder="">
                  </div>
                </div>

                <div class="form-group">
                  <label class="control-label col-md-3 col-sm-3 col-xs-12">User Type</label>
                  <div class="col-md-6">
                    <select name="user_type" class="form-control">
                      <option value="">Choose a user Type</option>
                      <option value="admin">Admin</option>
                      <option value="viewer">Viewer</option>
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
        </div>  <!-- /col -->
      </div> <!-- /row -->
  </div>
  <!-- /page content -->

  <!-- page footer -->
  <?php 
      $js = '<script type="text/javascript">';

      $js .= '$("#create-user").validate({
            errorClass: "text-danger",

              highlight: function(element, errorClass) {
                $(element).fadeOut(100,function() {
                  $(element).fadeIn(100);
                });
              },

            rules: '.json_encode($jquery['rules']).',
            messages: '.json_encode($jquery['messages']).'

          });';
              //display message from session
              //success message
              if(isset($_SESSION["success_msg"])) {
                $js .= 'message("Success","'.$_SESSION['success_msg'].'");';
                unset($_SESSION['success_msg']);
              }
              //error message
              if(isset($_SESSION['error_msg'])) {
                $js .= 'message("Error","'.$_SESSION['error_msg'].'", "error");';
                unset($_SESSION['error_msg']);
              }
              
      $js .='</script>';

      $settings->footer($js); 
  ?>
