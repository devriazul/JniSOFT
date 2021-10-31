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
  $operation = 'marks-add';
  $page_title = 'Add Marks';

  $settings = new App\settings\Settings();
  $db = new App\marks\Marks();
  $db_session = new App\session\Session();
  $db_course = new App\course\Course();

  //check access permission for this user
  if (!$settings->get_user_permission($user_role, $operation)) {
    header("Location: ../home/403.php");
    die();
  }

  //initiate paginator
  use JasonGrimes\Paginator;

  $sessions = $db_session->all(100, 0);



  // print page header
  $settings->header($page_title); 
  
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
  
  //result map
  $grade_map = array(
    'A' => 4, 
    'B' => 3, 
    'C' => 2, 
    'D' => 1, 
    'F' => 0 
    );

  // print page sidebar
  $settings->sidebar($sidebar_data); 

  //generate form valdation code
  $filter = new App\filter\Filter();
  $validator = $filter->marks_add();
  $jquery_validator = new HybridLogic\Validation\ClientSide\jQueryValidator($validator);
  $jquery = $jquery_validator->generate();

  ?>

  <!-- page content -->
  <div class="right_col" role="main">
    
    <!-- select session & semister & subject -->
    <div class="row">
      <div class="col-md-12">
          <div class="x_panel">
            <div class="x_title">
              <h2>Add Marks </h2>
              <ul class="nav navbar-right panel_toolbox">
                <li><a class="close-link"></a></li>
                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                <li><a class="close-link"><i class="fa fa-close"></i></a>
                </li>
              </ul>
              <div class="clearfix"></div>
            </div>
            <div class="x_content">
              <!-- <br /> -->

              <?php 
                //if anyone select session and semester
                //show them course list from session and semester
                if (isset($_GET['session']) && isset($_GET['semester'])) {
                  //print_r($_GET);
                  $courses = $db_course->assign(array('session' => $_GET['session'], 'semester' => $_GET['semester']))->all_course_by_session_semester();
                }

              ?>
              
              <?php 
                //if session, semester and course is set
                //hide form and show marks input box
                if (isset($_GET['session']) && isset($_GET['semester']) && isset($_GET['course_id'])) {
              ?>
              <!-- marks input -->
              <!-- title -->
              <div class="row">
                <div class="col-md-4">
                    <h2>Subject: <small>
                    <?php 
                      //course data
                      $course = $db_course->assign(array('course_id' => $_GET['course_id']))->single_course();
                      echo $course['course_name'] . ' [ Credit: '.$course['credit'].']';
                     ?></small></h2>
                </div>
                <div class="col-md-4">
                    <h2>Semester: <small><?php echo $semester_map[$_GET['semester']]; ?></small></h2>
                </div>
                <div class="col-md-4">
                    <h2>Session: <small><?php echo $_GET['session']; ?></small></h2>
                </div>
              </div>
              <br>
              <!-- /title -->

              <!-- result input box -->
              <div class="row">
                <div class="col-md-12">
                  <table class="table  table-bordered table-hover">
                  <?php 
                    $db_student = new App\student\Student();
                    //init pagination
                    $itemsPerPage = 15;
                    $currentPage = isset($_GET['page'])?$_GET['page']:1;
                    $offset = $itemsPerPage * ($currentPage-1);
                    $urlPattern = '../marks/marks-add.php?session='.$_GET['session'].'&semester='.$_GET['semester'].'&course_id='.$_GET['course_id'].'&page=(:num)';
                    $totalItems = $db_student->assign(array('session' => $_GET['session'], 'semester' => $_GET['semester'] ))->total_student_session_semester();
                    $paginator = new Paginator($totalItems, $itemsPerPage, $currentPage, $urlPattern);

                    //student data
                    $students = $db_student->assign(array('session' => $_GET['session'], 'semester' => $_GET['semester']))->all_student_session_semester($itemsPerPage, $offset);


                  ?>

                    <tr>
                      <th></th>
                      <th>Student Name</th>
                      <th>Roll</th>
                      <th>Letter Grade</th>
                      <th>Point</th>
                    </tr>

                    <?php
                      if (is_array($students)) {
                        //check if result is already saved
                        $data['course_id'] = $_GET['course_id'];
                        $data['session'] = $_GET['session'];
                        $data['semester'] = $_GET['semester'];

                        if($db->assign($data)->check()) {
                          $result = $db->assign($data)->single();
                          $result = unserialize($result['result']);
                        }else{
                          $result = array();
                        }
                        //print_r($result);
                        $settings->log($result);
                        echo '<form id="myform" action="_store.php" method="post">
                              <input type="hidden" name="session" value="'.$_GET['session'].'" >
                              <input type="hidden" name="semester" value="'.$_GET['semester'].'" >
                              <input type="hidden" name="course_id" value="'.$_GET['course_id'].'" >';
                        echo '<button type="submit" class="btn btn-success btn-xl pull-right"> <span class="fa fa-save"></span> Save</button>';
                        $i = 0;
                        foreach ($students as $student) {

                          echo '<tr>
                                      <td style="text-align:center;">'.($offset+1+$i).'</td>
                                      <td>'.$student['student_name'].'</td>
                                      <td>'.$student['roll'].'</td>
                                      <td>';

                          //set result if available
                          $old_result = isset($result[$student['student_id']])?$result[$student['student_id']]:'';
                          
                          if (is_array($student) && is_array($result) && array_key_exists($student['student_id'], $result) && !empty($result[$student['student_id']]) ) {
                            $point_value =  $course['credit']*$grade_map[$result[$student['student_id']]];
                          }else{
                            $point_value = 'N/A';
                          }
                            
                          echo  '<div class="col-md-5"><input type="text" value="'.$old_result.'" name="result['.$student['student_id'].'][]" class="form-control"></div>
                                    </td>';
                          echo  '<td> '.$point_value.' </td>';

                          echo    '</tr>';
                          $i++;
                        }
                        echo '<table>';
                        echo '<button type="submit" class="btn btn-success btn-xl pull-right"> <span class="fa fa-save"></span> Save</button>';
                        echo '</form>';
                      }
                    ?>

                  </table>

                  <?php echo $paginator; ?>
                </div> 
              </div>  

              <!-- /marks input -->
              <?php
                }else {
                  //show form
              ?>

              <!-- form row -->
              <!-- <div class="row"> -->
                <form action="marks-add.php" method="get" class="form-horizontal form-label-left">
                  <div class="form-group">
                    <label class="control-label col-md-4">Session</label>
                    <div class="col-md-4">
                      <select name="session" required="required" class="form-control">
                        <option value="">Choose a Session</option>
                        <?php 
                          foreach ($sessions as $session) {
                            $selected = $session['session_name'] == $_GET['session']?'Selected':'';
                            echo '<option '.$selected.' value="'.$session['session_name'].'">'.$session['session_name'].'</option>';
                          }
                        ?>
                      </select>
                    </div>
                  </div>

                  <div class="form-group">
                  <label class="control-label col-md-4">Semester</label>
                  <div class="col-md-4">
                    <select name="semester" required="required" class="form-control">
                      <option  value="">Choose a Semester</option>
                      <option <?php echo isset($_GET['semester']) && $_GET['semester'] == '1-1'?'Selected':''; ?> value="1-1">1st Year 1st Semester</option>
                      <option <?php echo isset($_GET['semester']) && $_GET['semester'] == '1-2'?'Selected':''; ?> value="1-2">1st Year 2nd Semester</option>
                      <option <?php echo isset($_GET['semester']) && $_GET['semester'] == '2-1'?'Selected':''; ?> value="2-1">2nd Year 1st Semester</option>
                      <option <?php echo isset($_GET['semester']) && $_GET['semester'] == '2-2'?'Selected':''; ?> value="2-2">2nd Year 2nd Semester</option>
                      <option <?php echo isset($_GET['semester']) && $_GET['semester'] == '3-1'?'Selected':''; ?> value="3-1">3rd Year 1st Semester</option>
                      <option <?php echo isset($_GET['semester']) && $_GET['semester'] == '3-2'?'Selected':''; ?> value="3-2">3rd Year 2nd Semester</option>
                    </select>
                  </div>
                </div>

                <?php 
                  if (isset($courses) && is_array($courses)) {

                    echo '<div class="form-group">
                          <label class="control-label col-md-4">Course</label>
                          <div class="col-md-4">
                            <select name="course_id" required="required" class="form-control">
                            option value="">Choose a course</option>';
                         
                           foreach ($courses as $course) {
                             echo '<option value="'.$course['course_id'].'">'.$course['course_name'].'</option>';
                           }
                    echo '  </select>
                            </div>
                          </div>';
                  }
                ?>
                  <div class="ln_solid"></div>
                  <div class="form-group">
                    <div class="col-md-2 col-md-offset-4">
                      <button type="submit" class="btn btn-success">Start</button>
                    </div>
                  </div>
                </form>
              <!-- </div> -->
              <!-- /form row -->

              <?php 
                } //hide the for 
              ?>
                <br>
                <a href="../marks/marks-add.php" class="btn btn-dark"> Back</a>
                <a href="../marks/marks-add.php" class="btn btn-primary"> Add another Mark</a>

            </div>
            <!-- /x content -->
          </div>
          <!-- /x panel -->
        </div>
        <!-- /col -->
      </div>
      <!-- /row -->
      <!-- select session & semister & subject -->
  </div>
  <!-- /page content -->

  <!-- page footer -->
  <?php 
      $js = '<script type="text/javascript">
            $(".btn-danger").on("click", function (e) {
                e.preventDefault();
                confirm($(this).attr("href"));
            });
            </script>
            <script type="text/javascript">';
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

      $js .= $settings->get_validator_script($jquery); 

      $settings->footer($js); 
  ?>
