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
  $operation = 'seat-plan-print';


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
              <h2> Seat Plan</h2>
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
                if (!empty($_POST['session']) && !empty($_POST['semester']) && !empty($_POST['term']) ) {
                    $students = $db_student->assign(array('session' => $_POST['session']))->all_student_session_full();
                    
                    echo '<form class="form-horizontal" action="_print_seat.php" method="post">';
                    echo '<input type="hidden" name="session" value="'.$_POST['session'].'">';
                    echo '<input type="hidden" name="semester" value="'.$_POST['semester'].'">';
                    echo '<input type="hidden" name="term" value="'.$_POST['term'].'">';
                    foreach ($students as $student) {
                      echo '<div class="form-group">
                                <label class="control-label col-md-1">
                                  <input type="checkbox" checked="checked" class="form-control" name="selected_students[]" value="'.$student['student_id'].'">  
                                  </label>
                                <div class="col-md-11"> <br>
                                  Roll: '.$student['roll'].' - Name: '.$student['student_name'].' 
                                </div>
                              </div>';
                    }
                    echo '<br><button class="btn btn-success" type="submit"> <span class="fa fa-print"></span> Print Seat Plan</button>';
                    echo '</form>';
                  

              

                }else{
              ?>
              
                <form class="form-horizontal" action="seat.php" method="post">
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

                  <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12">Semester</label>
                    <div class="col-md-6">
                      <select name="semester" required="required" class="form-control">
                        <option value="">Choose a Semester</option>
                        <option value="1st Year 1st Semester">1st Year 1st Semester</option>
                        <option value="1st Year 2nd Semester">1st Year 2nd Semester</option>
                        <option value="2nd Year 1st Semester">2nd Year 1st Semester</option>
                        <option value="2nd Year 2nd Semester">2nd Year 2nd Semester</option>
                        <option value="3rd Year 1st Semester">3rd Year 1st Semester</option>
                        <option value="3rd Year 2nd Semester">3rd Year 2nd Semester</option>
                      </select>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12">Exam Type</label>
                    <div class="col-md-6">
                      <select name="term" required="required" class="form-control">
                        <option value="">Choose an Exam Type</option>
                        <option value="Midterm">Midterm</option>
                        <option value="Final">Final</option>
                      </select>
                    </div>
                  </div>
                  <input type="hidden" name="student_id" value="<?php echo $student['student_id']; ?>">
                  <button class="btn btn-success" type="submit"> <span class="fa fa-graduation-cap"></span> Print Seat Plan</button>
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
