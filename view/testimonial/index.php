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
  $operation = 'testimonial-print';


  $settings = new App\settings\Settings();
  $db_user = new App\user\User();
  $db_student = new App\student\Student();

  $db_session = new App\session\Session();
  $sessions = $db_session->all(100, 0);

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

  ?>

  <!-- page content -->
  <div class="right_col" role="main">
    <!-- institute setting -->
    <div class="row">
      <div class="col-md-12">
          <div class="x_panel">
            <div class="x_title">
              <h2> Print Testimonial</h2>
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
                if (!empty($_POST['session']) ) {
                    $students = $db_student->assign(array('session' => $_POST['session']))->all_student_session_full();
                    
                    //echo '<form class="form-horizontal" action="_print_admit.php" method="post">';
                    foreach ($students as $student) {
                      echo '<div class="row">
                                <div class="col-md-4">
                                  Roll: '.$student['roll'].' - Name: '.$student['student_name'].' 
                                ';
                      
                          echo  '</div>
                                <div class="col-md-8">';
                                  
                          echo '<form action="../testimonial/_print_testimonial.php" method="post">
                                   <input type="hidden" name="student_id" value="'.$student['student_id'].'">
                                   <button type="submit" class="btn btn-success" >
                                   <span class="fa fa-print"></span> 
                                    Print Testimonial</button>
                                </form>';

                          echo      '</div>
                              </div> <hr>';
                    }
                    //echo '<br><button class="btn btn-success" type="submit"> <span class="fa fa-print"></span> Print Admit</button>';
                    //echo '</form>';
                  

              

                }else{
              ?>
              
                <form class="form-horizontal" action="index.php" method="post">
                  <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12">Session</label>
                    <div class="col-md-6">

                      <?php 
                        if (is_array($sessions)) {
                          echo '<select name="session" required="required" class="form-control form-group">
                                <option value="">Choose a Session</option>';
                          foreach ($sessions as $session) {
                            echo '<option value="'.$session['session_name'].'">'.$session['session_name'].'</option>';
                          }
                          echo '</select>';
                        }else{
                          //if no session
                          echo '<h2 class="text-danger">Please Add some session.</h2>';
                        }
                      ?>
                    </div>
                  </div>

                  <button class="btn btn-success" type="submit"> <span class="fa fa-print"></span> Print Testimonial</button>
                </form>

              <?php } ?>


                    
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
              
      $js .='</script>';

      $settings->footer($js); 

  ?>
