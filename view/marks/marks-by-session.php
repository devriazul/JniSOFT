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
  $operation = 'marks-view';
  $page_title = 'View Marks';

  $db = new App\marks\Marks();
  $db_course = new App\course\Course();
  $settings = new App\settings\Settings();
  $db_session = new App\session\Session();
  $sessions = $db_session->all(100, 0);

  //check access permission for this user
  if (!$settings->get_user_permission($user_role, $operation)) {
    header("Location: ../home/403.php");
    die();
  }

  //check parameter of pagination
  if(isset($_GET['page']) && !is_numeric($_GET['page'])) {
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
  //semister map
  $semester_map = array(
    '1-1' => '1st Year 1st Semester',
    '1-2' => '1st Year 2nd Semester',
    '2-1' => '2nd Year 1st Semester',
    '2-2' => '2nd Year 2nd Semester',
    '3-1' => '3rd Year 1st Semester',
    '3-2' => '3rd Year 2nd Semester'
    );

  //result map
  $grade_map = array(
    'A' => 4, 
    'B' => 3, 
    'C' => 2, 
    'D' => 1, 
    'F' => 0 
    );
  $grade_map_r = array(
    '4' => 'A', 
    '3' => 'B', 
    '2' => 'C', 
    '1' => 'D', 
    '0' => 'F' 
    );
  use JasonGrimes\Paginator;
  // print page sidebar
  $settings->sidebar($sidebar_data); 

  //generate form valdation code
  $filter = new App\filter\Filter();
  $validator = $filter->marks_search();
  $jquery_validator = new HybridLogic\Validation\ClientSide\jQueryValidator($validator);
  $jquery = $jquery_validator->generate();
  ?>

  <!-- page content -->
  <div class="right_col" role="main">
    <div class="row">
      <div class="col-md-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>View Marks </h2>
            <ul class="nav navbar-right panel_toolbox">
              <li><a class="collapse-link"></a>
              </li>
              <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
              </li>
              <li><a class="close-link"><i class="fa fa-close"></i></a>
              </li>
            </ul>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">
            
            <form id="myform" action="marks-by-session.php" method="GET" class="form-horizontal">
              <div class="form-group">
                <div class="col-md-5 col-md-offset-3">
                  <?php 
                    if (is_array($sessions)) {
                      echo '<select name="session" class="form-control form-group" onchange="this.form.submit()">';
                      echo '<option>Choose Session</option>';
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
              <!-- <div class="form-group">
                <div class="col-md-5 col-md-offset-5">
                <button type="submit" class="btn btn-primary"> <span class="fa fa-search"></span> Search</button>
                </div>
              </div> -->
            </form>


          </div>
        </div>
    </div>
    </div>

    <?php 

      if (isset($_GET['session']) ) {
            //initiate paginator
          $db_student = new App\student\Student();

          
          $totalItems = $db_student->assign(array('session' =>$_GET['session']))->total_student_session();
          $itemsPerPage = 1;
          $currentPage = isset($_GET['page'])?$_GET['page']:1;
          $offset = $itemsPerPage * ($currentPage-1);
          if (isset($_GET['session'])) {
            $urlPattern = '../marks/marks-by-session.php?session='.$_GET['session'].'&page=(:num)';
          }else{
            $urlPattern = '../marks/marks-by-session.php?page=(:num)';
          }

          $paginator = new Paginator($totalItems, $itemsPerPage, $currentPage, $urlPattern);
  
          $student = $db_student->assign(array('roll' => $_GET['session']))->all_student_session($itemsPerPage, $offset);
          
          $student = $student['0'];
          //$settings->log($student);

          //if student is found show result box
          echo '<div class="row">
                  <div class="col-md-12">
                    <div class="x_panel">
                      
                      <div class="x_content">';

          if(!is_array($student)) {
            echo 'Student is not found';
          }else{
            //if any student is available search his result
            // if ($db->assign(array('session' => $student['session'], 'semester' => $student['semester']))->check_all() ) {
            if (true ) {
              echo $paginator;
              echo '<br><b>Student info: </b></br>';
              echo '<b>Name: </b>'. $student['student_name'] .'</br>';
              echo '<b>Roll: </b>'. $student['roll'].'</br>';
              echo '<b>Session: </b>'. $student['session'].'</br>';
              echo '<b>Semester: </b>'. $semester_map[$student['semester']].'</br><hr>';

              echo '<form action="_print_full.php" method="post">
                       <input type="hidden" name="student_id" value="'.$student['student_id'].'">
                       <button type="submit" class="btn btn-success pull-right" >
                       <span class="fa fa-print"></span> 
                        Print Full Result</button>
                    </form>';
              //die();

              //show all result
              //x = year
              //y = semester
              $sem_array = explode("-", $student['semester']);

              if (is_array($sem_array)) {
                $x = 1;
                $y = 1;

                $total_point = 0;
                $total_credit = 0;

                while($x != $sem_array['0']+1) {
                  $r11 = show_result( $student, "$x-$y", $grade_map, $semester_map, $grade_map_r);

                  if (!empty($r11) && is_array($r11)) {
                    $total_point += $r11['total_point'];
                    $total_credit += $r11['total_credit'];
                  }

                  //change year and semester
                  if ($y == 2) {
                    
                    //display summary of year
                    echo '<div class="text-center">';
                    echo '<h2><u> Year '.($x).' summary </u></h2>';
                    echo 'Total point earned: '. $total_point .'<br>';
                    echo 'Total Credit earned: '. $total_credit .'<br>';
                    if($total_credit >0) {
                    	echo 'Year Grade: '. round($total_point/$total_credit, 2) .'<br>';
                    }else{
                    	echo 'Year Grade: N/A<br>';
                    }
                    
                    if($total_credit >0) {
                    	echo 'Letter Grade: '. $grade_map_r[floor($total_point/$total_credit)] .'<br><br>';
                    }else{
                    	echo 'Letter Grade: N/A <br><br>';
                    }
                    
                    echo '<form action="_print_year.php" method="post">
                            <input type="hidden" name="year" value="'.$x.'">
                            <input type="hidden" name="session" value="'.$student['session'].'">
                            <input type="hidden" name="student_id" value="'.$student['student_id'].'">
                            <button type="submit" class="btn btn-success"> <span class="fa fa-print"><span> Print</button>
                            </form>';
                    echo '<hr></div>';

                    if ($x == $sem_array['0'] && $y == $sem_array['1']) {
                        break;
                    }

                    //if semester = 2 then increase year
                    $y = 1;
                    $x++;
                  }else{
                      //breaking condition to terminate
                      if ($x == $sem_array['0'] && $y == $sem_array['1']) {
                        break;
                      }
                    $y++;
                  }
                }
              }


            }else{
              echo '<div class="text-center">';
              echo '<b class="text-danger">No Grade is found for this student. </b></br></br>';
              echo '</div>';
              echo '<b>Student info: </b></br>';
              echo '<b>Name: </b>'. $student['student_name'] .'</br>';
              echo '<b>Roll: </b>'. $student['roll'].'</br>';
              echo '<b>Session: </b>'. $student['session'].'</br>';
              echo '<b>Semester: </b>'. $semester_map[$student['semester']].'</br>';
            }

            ?>

    <?php 
        }
      } //search result
    ?>

              </div>
        </div>
      </div>
    </div>


      </div>
    </div>
  <!-- /col -->
  <!-- footer link -->
  </div>
  <!-- /page content -->

  <!-- footer content -->
  <?php 
      $settings->footer($settings->get_validator_script($jquery)); 



      function show_result( $student, $semester, $grade_map, $semester_map, $grade_map_r) {
        $db = new App\marks\Marks();
        $db_course = new App\course\Course();
        $col_span = $db_course->assign(array('session' => $student['session'], 'semester' => $student['semester']))->total_by_session_semester();

        //if any student is available search his result
        if ($db->assign(array('session' => $student['session'], 'semester' => $semester))->check_all() ) {
          $results = $db->assign(array('session' => $student['session'], 'semester' => $semester))->all_by_session_semester();

          if(is_array($results)) {
            echo '<h2 class="text-center">'.$semester_map[$semester].' </h2>';
            echo '<form action="_print.php" method="post">
                    <input type="hidden" name="session" value="'.$student['session'].'">
                    <input type="hidden" name="semester" value="'.$semester.'">
                    <input type="hidden" name="student_id" value="'.$student['student_id'].'">
                    <button type="submit" class="btn btn-warning pull-right btn-xs"> 
                      <span class="fa fa-print"></span> Print</button>
                  </form>';
            echo '<table class="table  table-bordered table-hover">
                    <tr>
                      <th>Subject</th>
                      <th>Credit Earned</th>
                      <th>Letter Grade</th>
                      <th>Point</th>
                      <th>Semester Grade</th>
                    </tr>';
            //echo '<pre>';
            //print_r($results);
            //echo '</pre>';
            //return;
            $total_point = 0;
            $total_credit = 0;
            foreach ($results as $result) {
              //grab my result
              $my_result = unserialize($result['result']);

              if (array_key_exists($student['student_id'], $my_result) && !empty($my_result[$student['student_id']]) ) {
                $grade_value =  $my_result[$student['student_id']];
              }else{
                $grade_value = 'N/A';
              }

              if (array_key_exists($student['student_id'], $my_result) && !empty($my_result[$student['student_id']]) ) {
                $point_value =  $result['credit']*$grade_map[$my_result[$student['student_id']]];
                $total_point += $point_value;
                $total_credit += $result['credit'];
              }else{
                $point_value = 'N/A';
              }

              echo '<tr>
                    <td>'.$result['course_name'].'</td>
                    <td>'.$result['credit'].'</td>
                    <td>'.($grade_value != 'F' ?$grade_value:'<a class="text-danger bg-danger" style="padding:10px;" href="../marks/marks-edit.php?session='.$student['session'].'&semester='.$semester.'&course_id='.$result['course_id'].'"><b>F</b></a>').' </td>
                    <td>'.$point_value.'</td>
                    <td>&nbsp;</td>
                  </tr>';
            } //end foreach

            if ($total_credit != 0) {
              echo '<tr>
                    <td><b>Total</b></td>
                    <td><b>'.$total_credit.'</b></td>
                    <td>&nbsp;</td>
                    <td><b>'.$total_point.'</b></td>
                    <td><b>'.round($total_point/$total_credit, 2).' [ '.$grade_map_r[floor($total_point/$total_credit)].' ]</b></td>
                  </tr>';
            }else{
              // echo '<tr>
              //       <td><b></b></td>
              //       <td>&nbsp</td>
              //       <td>&nbsp;</td>
              //       <td>&nbsp;</td>
              //       <td>&nbsp;</td>
              //     </tr>';
            }
            
            echo '</table>';

            return array(
              'total_credit' => $total_credit, 
              'total_point' =>$total_point
              );

          } //has result
        } //student available
      } //end function
  ?>
