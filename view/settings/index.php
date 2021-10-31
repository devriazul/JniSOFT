<?php 
  include_once('../../vendor/autoload.php');
  if (session_status() == PHP_SESSION_NONE) {
    session_start();
  }

  //TODO: get user role from session or DB
  if (!isset($_SESSION['user_roll'])) {
    header("Location: ../home/login.php");
  }
  $user_role = $_SESSION['user_roll'];
  $operation = 'settings';


  $settings = new App\settings\Settings();
  $db_user = new App\user\User();
  $db_db = new App\db\Db();
  $user = $db_user->assign(array('user_id' => $_SESSION['user_id']))->single();

  // print page header
  $settings->header('Settings'); 
  
  $sidebar_data = array(
      'username'  => $_SESSION['user_name'],
      'user_role' => $user_role,
      'operation' =>  $operation
    );

  // print page sidebar
  $settings->sidebar($sidebar_data); 

  //generate form valdation code
  $filter = new App\filter\Filter();
  $validator1 = $filter->settings_account();
  $jquery_validator = new HybridLogic\Validation\ClientSide\jQueryValidator($validator1);
  $jquery = $jquery_validator->generate();

  $validator2 = $filter->settings_password();
  $jquery_validator2 = new HybridLogic\Validation\ClientSide\jQueryValidator($validator2);
  $jquery2 = $jquery_validator2->generate();

  $institute = $db_db->assign(array('key' => 'institute'))->get_value();
  $code = $db_db->assign(array('key' => 'college_code'))->get_value();
  $address = $db_db->assign(array('key' => 'college_address'))->get_value();
  $subject = $db_db->assign(array('key' => 'college_subject'))->get_value();
  $bookNo = $db_db->assign(array('key' => 'book_no'))->get_value();
  $final_exam_year = $db_db->assign(array('key' => 'final_exam_year'))->get_value();

  ?>

  <!-- page content -->
  <div class="right_col" role="main">
    <!-- institute setting -->
    <div class="row">
      <div class="col-md-12">
          <div class="x_panel">
            <div class="x_title">
              <h2> College Name </h2>
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
              <?php 
                  if ($settings->get_user_permission($user_role, 'backup')) {
                    echo '<a href="backup.php" class="btn btn-success">Create Backup</a>';
                  }
              ?>
              



              <form id="account" action="_update.php" method="POST" class="form-horizontal form-label-left">
                
                <div class="form-group">
                  <label class="control-label col-md-3 col-sm-3 col-xs-12">College Name</label>
                  <div class="col-md-6">
                    <input name="institute" value="<?php echo is_array($institute)?$institute['_value']:''; ?>" type="text" class="form-control" placeholder="Institute Name">
                  </div>
                </div>

                <div class="form-group">
                  <label class="control-label col-md-3 col-sm-3 col-xs-12">College Code</label>
                  <div class="col-md-6">
                    <input name="college_code" value="<?php echo is_array($code)?$code['_value']:''; ?>" type="text" class="form-control" placeholder="College Code">
                  </div>
                </div>

                <div class="form-group">
                  <label class="control-label col-md-3 col-sm-3 col-xs-12">College Address</label>
                  <div class="col-md-6">
                    <input name="college_address" value="<?php echo is_array($address)?$address['_value']:''; ?>" type="text" class="form-control" placeholder="College Address">
                  </div>
                </div>

                <div class="form-group">
                  <label class="control-label col-md-3 col-sm-3 col-xs-12">College Subject</label>
                  <div class="col-md-6">
                    <input name="college_subject" value="<?php echo is_array($subject)?$subject['_value']:''; ?>" type="text" class="form-control" placeholder="College Subject">
                  </div>
                </div>
                
                 <div class="form-group">
                  <label class="control-label col-md-3 col-sm-3 col-xs-12">Book No</label>
                  <div class="col-md-6">
                    <input name="book_no" value="<?php echo is_array($bookNo)?$bookNo['_value']:''; ?>" type="text" class="form-control" placeholder="Book No">
                  </div>
                </div>
                
                <div class="form-group">
                  <label class="control-label col-md-3 col-sm-3 col-xs-12">Final Exam Year</label>
                  <div class="col-md-6">
                    <input name="final_exam_year" value="<?php echo is_array($final_exam_year)?$final_exam_year['_value']:''; ?>" type="text" class="form-control" placeholder="Final Examination Year">
                  </div>
                </div>

                <div class="ln_solid"></div>
                <div class="form-group">
                  <div class="col-md-9 col-sm-9 col-xs-12 col-md-offset-3">
                    <button type="submit" class="btn btn-success">Update</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div> <!-- /col -->
      </div>
      <!-- /row -->

    <!-- /institute setting -->
    <!-- Account section -->
    <div class="row">
      <div class="col-md-12">
          <div class="x_panel">
            <div class="x_title">
              <h2>My Account </h2>
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
              <form id="account" action="_update.php" method="POST" class="form-horizontal form-label-left">
                
                <div class="form-group">
                  <label class="control-label col-md-3 col-sm-3 col-xs-12">Name</label>
                  <div class="col-md-6">
                    <input name="user_name" value="<?php echo $user['user_name']; ?>" type="text" class="form-control" placeholder="">
                  </div>
                </div>

                <div class="form-group">
                  <label class="control-label col-md-3 col-sm-3 col-xs-12">Email</label>
                  <div class="col-md-6">
                    <input name="email" disabled="disabled" value="<?php echo $user['email']; ?>" type="email" class="form-control" placeholder="">
                  </div>
                </div>

                <div class="ln_solid"></div>
                <div class="form-group">
                  <div class="col-md-9 col-sm-9 col-xs-12 col-md-offset-3">
                    <button type="submit" class="btn btn-success">Update</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div> <!-- /col -->
      </div>
      <!-- /row -->

      <!-- change password section -->
          <div class="row">
      <div class="col-md-12">
          <div class="x_panel">
            <div class="x_title">
              <h2>Change Password </h2>
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
              <form id="change-password" action="_update.php" method="post" class="form-horizontal form-label-left">
                  <input type="hidden" name="email" value="<?php echo $user['email'];?>">
              <div class="form-group">
                  <label class="control-label col-md-3">Old Password</label>
                  <div class="col-md-6">
                    <input type="password" name="old_password" class="form-control" placeholder="Old Password">
                  </div>
                </div>

                <div class="form-group">
                  <label class="control-label col-md-3 col-sm-3 col-xs-12">New Password</label>
                  <div class="col-md-6">
                    <input type="password" name="new_password" class="form-control" placeholder="New Password">
                  </div>
                </div>
                <div class="ln_solid"></div>
                <div class="form-group">
                  <div class="col-md-9 col-sm-9 col-xs-12 col-md-offset-3">
                    <button type="submit" class="btn btn-success">Update</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div> <!-- /col -->
      </div>
      <!-- /row -->
  </div>
  <!-- /page content -->

  <!-- page footer -->
  <?php 
    $js = '<script type="text/javascript">';
          foreach($jquery['methods'] as $method_name => $method_function):
            $js .= 'jQuery.validator.addMethod("'.$method_name.'", '.$method_function.');';
          endforeach;

  $js .= '$("#account").validate({
            errorClass: "text-danger",

              highlight: function(element, errorClass) {
                $(element).fadeOut(100,function() {
                  $(element).fadeIn(100);
                });
              },

            rules: '.json_encode($jquery['rules']).',
            messages: '.json_encode($jquery['messages']).'

          });';

  $js .= '$("#change-password").validate({
            errorClass: "text-danger",

              highlight: function(element, errorClass) {
                $(element).fadeOut(100,function() {
                  $(element).fadeIn(100);
                });
              },

            rules: '.json_encode($jquery2['rules']).',
            messages: '.json_encode($jquery2['messages']).'

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
