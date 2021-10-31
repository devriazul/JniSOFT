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
  $operation = 'session-add';
  $page_title = 'Add New Session';

  $settings = new App\settings\Settings();
  $db_course = new App\course\Course();

  //check access permission for this user
  if (!$settings->get_user_permission($user_role, $operation)) {
    header("Location: ../home/403.php");
    die();
  }

   $style = '<style type="text/css">
                .checkbox {
                  float: left;
                  margin-right: 10px;
                }

              </style>';

  // print page header
  $settings->header($page_title, $style); 
  
  $sidebar_data = array(
      'username'  => $_SESSION['user_name'],
      'user_role' => $user_role,
      'operation' =>  $operation
    );

      //semister map
  $semester_map = array(
    '1-1' =>  '1st Year 1st Semester',
    '1-2' =>  '1st Year 2nd Semester',
    '2-1' =>  '2nd Year 1st Semester',
    '2-2' =>  '2nd Year 2nd Semester',
    '3-1' =>  '3rd Year 1st Semester',
    '3-2' =>  '3rd Year 2nd Semester'
    );
  // print page sidebar
  $settings->sidebar($sidebar_data); 
  
  //generate form valdation code
  $filter = new App\filter\Filter();
  $validator = $filter->session_create();
  $jquery_validator = new HybridLogic\Validation\ClientSide\jQueryValidator($validator);
  $jquery = $jquery_validator->generate();

  $courses = $db_course->all_distinct_course();

  ?>

  <!-- page content -->
  <div class="right_col" role="main">
    <!-- <h1>Add course</h1> -->
    <div class="row">
      <div class="col-md-12">
          <div class="x_panel">
            <div class="x_title">
              <h2>Add Session </h2>
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


              <h2>Subject List:</h2>
              <div class="form-group">
                <table class="table">
                  <!-- <tr>
                    <th>Option</th>
                    <th>Subject Code</th>
                    <th>Subject Name</th>
                    <th>Subject Credit</th>
                  </tr> -->
                <?php
                  $flag = true;
                  $current = '1-1';
                  if (is_array($courses)) {
                    foreach ($courses as $course) {

                      if($flag) {
                        echo '<tr>
                                <th colspan="4" style="text-align:center; font-size: 25px;">'.$semester_map[$course['semester']].'</th>
                              </tr>
                              <tr>
                                <th>Option</th>
                                <th>Subject Code</th>
                                <th>Subject Name</th>
                                <th>Subject Credit</th>
                              </tr>';
                        $flag = false;
                        $current = $course['semester'];
                      }

                      if($current != $course['semester']) {
                        $flag = true;
                      }

                        echo '<tr>
                                <td><input type="checkbox" checked="checked" name="course[]" value="'.$course['course_id'].'"></td>
                                <td>'.$course['course_code'].'</td>
                                <td>'.$course['course_name'].'</td>
                                <td>'.$course['credit'].'</td>
                              </tr>';

                        // echo '<div class="checkbox">
                        //   <label><input type="checkbox" checked="checked" name="course[]" value="'.$course['course_id'].'"> '.$course['course_code'].': '.$course['course_name'].' ('.$course['credit'].') ['.$semester_map[$course['semester']].' ]</label>
                        // </div>';
                    }
                  }
                ?>
                </table>
                </div>

                <div class="form-group">
                  <label class="control-label col-md-3">Session Name</label>
                  <div class="col-md-4 col-sm-9 col-xs-12">
                    <input name="session_name" type="text" class="form-control" placeholder="">
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
    $js = '<script type="text/javascript">';
          foreach($jquery['methods'] as $method_name => $method_function):
            $js .= 'jQuery.validator.addMethod("'.$method_name.'", '.$method_function.');';
          endforeach;
    $js .= '$("#myform").validate({
            errorClass: "text-danger",

              highlight: function(element, errorClass) {
                $(element).fadeOut(100,function() {
                  $(element).fadeIn(100);
                });
              },

            rules: '.json_encode($jquery['rules']).',
            messages: '.json_encode($jquery['messages']).'

          });';
      $js .= '</script>';


  $settings->footer($js); 
  ?>
