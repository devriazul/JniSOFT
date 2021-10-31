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
  
  $operation = 'student-view';
  $page_title = 'Student Profile';

  $db = new App\student\Student();
  $settings = new App\settings\Settings();

  //check access permission for this user
  if (!$settings->get_user_permission($user_role, $operation)) {
    header("Location: ../home/403.php");
    die();
  }

  //if url is not valid
  if(!isset($_GET['student'])) {
    header("Location: ../home/404.php");
    die();
  }

  //if course id is not in our database
  if(!$db->assign(array('student_id'=> $_GET['student']))->check() ) {
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
  $grade_map_r = array(
    '4' => 'A', 
    '3' => 'B', 
    '2' => 'C', 
    '1' => 'D', 
    '0' => 'F' 
    );

  // print page sidebar
  $settings->sidebar($sidebar_data); 

  //grab the course data
  $student = $db->assign(array('student_id'=> $_GET['student']))->single();
  $profile = $db->assign(array('student_id'=> $_GET['student']))->single_profile();
  ?>

  <!-- page content -->
  <div class="right_col" role="main">
    <div class="">
      <div class="page-title">
        <div class="title_left">
        </div>
      </div>
      
      <div class="clearfix"></div>

      <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
          <div class="x_panel">
            <div class="x_title">
              <h2>User Profile</h2>
              <ul class="nav navbar-right panel_toolbox">
                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                </li>
                <li class="dropdown">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                </li>
                <li><a class="close-link"><i class="fa fa-close"></i></a>
                </li>
              </ul>
              <div class="clearfix"></div>
            </div>
            <div class="x_content">
              <div class="col-md-3 col-sm-3 col-xs-12 profile_left">
                <div class="profile_img">
                  <div id="crop-avatar">
                    <!-- Current avatar -->
                    <?php 
                      if (!empty($profile['photo'])) {
                        echo '<img class="img-responsive avatar-view" src="../../assets/images/students/'.$student['session'].'/'.$profile['photo'].'" alt="profile image missing">';
                      }else {
                        echo '<img class="img-responsive avatar-view" src="../../assets/images/user.png">';
                      }
                    ?>
                  </div>
                </div>
                <h3><?php echo $student['student_name']; ?></h3>

                <ul class="list-unstyled user_data">
                  <li><i class="fa fa-map-marker user-profile-icon"></i> <?php echo isset($profile['current_address'])?$profile['current_address']: 'N/A'; ?>
                  </li>

                  <li>
                    <i class="fa fa-graduation-cap user-profile-icon"></i> <?php echo $semester_map[$student['semester']]; ?>
                  </li>
                </ul>

                <a href="../student/index.php" class="btn btn-dark"><i class="fa fa-angle-double-left m-right-xs"></i> Back</a>
                
                <?php 
                	 //check access permission for this user
          			  if ($settings->get_user_permission($user_role, 'student-edit')) {
          			  	echo '<a href="../student/student-edit.php?student='.$student['student_id'] .'" class="btn btn-primary"><i class="fa fa-edit m-right-xs"></i> Edit Profile</a>';
          			  }
                ?>
                
                <br />

              </div>
              <div class="col-md-9 col-sm-9 col-xs-12">
                <!-- start of user-activity-graph -->
                <!-- end of user-activity-graph -->

                <div class="" role="tabpanel" data-example-id="togglable-tabs">
                  <ul id="myTab" class="nav nav-tabs bar_tabs" role="tablist">
                    <li role="presentation" class="active"><a href="#tab_content1" id="home-tab" role="tab" data-toggle="tab" aria-expanded="true">Basic Information</a>
                    </li>
                    <li role="presentation" class=""><a href="#tab_content2" role="tab" id="profile-tab" data-toggle="tab" aria-expanded="false">Details</a>
                    </li>
                    <li role="presentation" class=""><a href="#tab_content3" role="tab" id="profile-tab2" data-toggle="tab" aria-expanded="false">Marks</a>
                    </li>
                    <li role="presentation" class=""><a href="#tab_content4" role="tab" id="profile-tab2" data-toggle="tab" aria-expanded="false">Admit Card</a>
                    </li>
                  </ul>
                  <div id="myTabContent" class="tab-content">
                    <div role="tabpanel" class="tab-pane fade active in" id="tab_content1" aria-labelledby="home-tab">
                      <!-- //tab content 1 -->


                      <!-- start  Basic Info-->
                      <table class="data table  table-bordered table-hover">
                        <tbody>
                          <tr>
                            <th>Name</th>
                            <td><?php echo $student['student_name']; ?></td>
                          </tr>
                          <tr>
                            <th>Roll</th>
                            <td><?php echo $student['roll']; ?></td>
                          </tr>
                          <tr>
                            <th>BNMC Reg No.</th>
                            <td><?php echo isset($student['bnc_roll'])?$student['bnc_roll']: 'N/A'; ?></td>
                          </tr>
                          <tr>
                            <th>Session</th>
                            <td><?php echo $student['session']; ?></td>
                          </tr>
                          <tr>
                            <th>Semester</th>
                            <td><?php echo $student['semester']; ?></td>
                          </tr>
						  <tr>
                            <th>Start Date</th>
                            <td><?php echo $student['start_date']; ?></td>
                          </tr>
						  <tr>
                            <th>End Date</th>
                            <td><?php echo $student['end_date']; ?></td>
                          </tr>
						  <tr>
                            <th>Final Exam Date</th>
                            <td><?php echo $student['final_exam_date']; ?></td>
                          </tr>
                        </tbody>
                      </table>
                      <!-- end Basic Info -->

                    
                    </div>
                    <div role="tabpanel" class="tab-pane fade" id="tab_content2" aria-labelledby="profile-tab">
                    <h2>Personal Information</h2>
                      <!-- start  Details Info-->
                      <table class="data table  table-bordered table-hover">
                        <tbody>
                          <tr>
                            <th>Father's Name</th>
                            <td><?php echo isset($profile['father_name'])?$profile['father_name']: 'N/A'; ?></td>
                          </tr>
                          <tr>
                            <th>Mother's Name</th>
                            <td><?php echo isset($profile['mother_name'])?$profile['mother_name']: 'N/A'; ?></td>
                          </tr>
                          <tr>
                            <th>Guardian Name</th>
                            <td><?php echo isset($profile['guardian_name'])?$profile['guardian_name']: 'N/A'; ?></td>
                          </tr>
                          <tr>
                            <th>Relation to Guardian</th>
                            <td><?php echo isset($profile['relation_to_guardian'])?$profile['relation_to_guardian']: 'N/A'; ?></td>
                          </tr>
                          <tr>
                            <th>Date of Birth</th>
                            <td><?php echo isset($profile['date_of_birth'])?date('d F Y', strtotime($profile['date_of_birth'])): 'N/A'; ?></td>
                          </tr>
                          <tr>
                            <th>National Id</th>
                            <td><?php echo isset($profile['nid'])?$profile['nid']: 'N/A'; ?></td>
                          </tr>
                          <tr>
                            <th>Contact Number</th>
                            <td><?php echo isset($profile['contact_number'])?$profile['contact_number']: 'N/A'; ?></td>
                          </tr>
                          <tr>
                            <th>Nationality</th>
                            <td><?php echo isset($profile['nationality'])?$profile['nationality']: 'N/A'; ?></td>
                          </tr>
                          <tr>
                            <th>Marital Status</th>
                            <td><?php 
                                 if(isset($profile['marital_status'])) {
                                 	if($profile['marital_status'] == 1 ) {
                                 		echo 'Married';
                                 	}else {
                                 		echo 'Unmarried';
                                 	}
                                 }else {
                                 	echo 'N/A';
                                 }
                                                        
                            ?></td>
                          </tr>
                          <tr>
                            <th>Gender</th>
                            <td><?php echo isset($profile['gender'])?ucfirst($profile['gender']): 'N/A'; ?></td>
                          </tr>
                          <tr>
                            <th>Religion</th>
                            <td><?php echo isset($profile['religion'])?ucfirst($profile['religion']): 'N/A'; ?></td>
                          </tr>
                          <?php
                            /* Updated Dec 2017*/
                            $all_address = $profile['permanent_address'];
                            $permanent_address = '';
                            $po = '';
                            $ps = '';
                            $district = '';

                            if (!empty($all_address)) {
                              $all_address = json_decode($all_address, true);

                              if (isset($all_address['permanent_address'])) {
                                $permanent_address = $all_address['permanent_address'];
                              }
                              if (isset($all_address['ps'])) {
                                $ps = $all_address['ps'];
                              }
                              if (isset($all_address['po'])) {
                                $po = $all_address['po'];
                              }
                              if (isset($all_address['district'])) {
                                $district = $all_address['district'];
                              }
                            }
                          ?>
                          <tr>
                            <th>Permanent Address</th>
                            <td><?php echo isset($permanent_address)? $permanent_address: 'N/A'; ?></td>
                          </tr>
                          <tr>
                            <th>Post Office</th>
                            <td><?php echo isset($ps)?$ps: 'N/A'; ?></td>
                          </tr>
                          <tr>
                            <th>PS</th>
                            <td><?php echo isset($po)?$po: 'N/A'; ?></td>
                          </tr>
                          <tr>
                            <th>District</th>
                            <td><?php echo isset($district)?$district: 'N/A'; ?></td>
                          </tr>
                          <tr>
                            <th>Current Address</th>
                            <td><?php echo isset($profile['current_address'])?$profile['current_address']: 'N/A'; ?></td>
                          </tr>
                          </tbody>
                          </table>

                          <h2>Educational Information</h2>

                          <table class="table  table-bordered table-hover">
                          <tbody>
                          <tr>
                            <th>School Name</th>
                            <td><?php echo isset($profile['school_name'])?$profile['school_name']: 'N/A'; ?></td>
                          </tr>
                          <tr>
                            <th>SSC GPA</th>
                            <td><?php echo isset($profile['ssc_gpa'])?$profile['ssc_gpa']: 'N/A'; ?></td>
                          </tr>
                          <tr>
                            <th>SSC Passing Year</th>
                            <td><?php echo isset($profile['ssc_passing_year'])?$profile['ssc_passing_year']: 'N/A'; ?></td>
                          </tr>
                          <tr>
                            <th>SSC Original Docs</th>
                            <td><?php echo isset($profile['original_ssc_doc'])?ucfirst($profile['original_ssc_doc']): 'N/A'; ?></td>
                          </tr>
                        </tbody></table>

                          <table class="table table-bordered table-hover">
                          <tr>
                            <th>College Name</th>
                            <td><?php echo isset($profile['college_name'])?$profile['college_name']: 'N/A'; ?></td>
                          </tr>
                          <tr>
                            <th>HSC GPA</th>
                            <td><?php echo isset($profile['hsc_gpa'])?$profile['hsc_gpa']: 'N/A'; ?></td>
                          </tr>
                          <tr>
                            <th>HSC Passing Year</th>
                            <td><?php echo isset($profile['hsc_passing_year'])?$profile['hsc_passing_year']: 'N/A'; ?></td>
                          </tr>
                          <tr>
                            <th>HSC Original Docs</th>
                            <td><?php echo isset($profile['original_hsc_doc'])?ucfirst($profile['original_hsc_doc']): 'N/A'; ?></td>
                          </tr>
                        </tbody>
                      </table>
                      <!-- end Details Info -->

                    </div>
                    <div role="tabpanel" class="tab-pane fade" id="tab_content3" aria-labelledby="profile-tab">
                      <!-- //marks TAb -->
                      <?php
                          $db_marks = new App\marks\Marks();
                        //if any student is available search his result
                        if (true ) {
                           echo '<div class="col-md-5">';
                            echo '</div>';
                            echo '<div class="col-md-7">';
                           echo '<form action="../marks/_print_full.php" method="post">
                                   <input type="hidden" name="student_id" value="'.$student['student_id'].'">
                                   <button type="submit" class="btn btn-success pull-right" >
                                   <span class="fa fa-print"></span> 
                                    Print Full Result</button>
                                </form>';
                            echo '<form action="../testimonial/_print_testimonial.php" method="post">
                                   <input type="hidden" name="student_id" value="'.$student['student_id'].'">
                                   <button type="submit" class="btn btn-success pull-right" >
                                   <span class="fa fa-print"></span> 
                                    Print Testimonial</button>
                                </form>';
                            echo '</div> <br>';

                          //show all result
                          //$sem_array = explode("-", "1-1");
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

                              //if ($x == $sem_array['0'] && $y == $sem_array['1']) {
                              //  break;
                              //}
                              
                              

                              if ($y == 2) {
                                //display summary of year
                                echo '<div class="text-center">';
                                echo '<h2><u> Year '.($x).' summary </u></h2>';
                                echo 'Total point earned: '. $total_point .'<br>';
                                echo 'Total Credit earned: '. $total_credit .'<br>';
                                
                                if($total_credit>0) {
                                	echo 'Year Grade: '. round($total_point/$total_credit, 2) .'<br>';
                                }else{
                                	echo 'Year Grade: N/A <br>';
                                }
                                
                                if($total_credit>0) {
                                	echo 'Letter Grade: '. $grade_map_r[floor($total_point/$total_credit)] .'<br>';
                                }else{
                                	echo 'Letter Grade: N/A<br>';
                                }
                                //echo '<hr></div>';

                                echo '<form action="../marks/_print_year.php" method="post">
                                      <input type="hidden" name="year" value="'.$x.'">
                                      <input type="hidden" name="session" value="'.$student['session'].'">
                                      <input type="hidden" name="student_id" value="'.$student['student_id'].'">
                                      <button type="submit" class="btn btn-success"> <span class="fa fa-print"><span> Print</button>
                                      </form>';
                               echo '<hr></div>';

                                if ($x == $sem_array['0'] && $y == $sem_array['1']) {
                                  break;
                                }

                                $y = 1;
                                $x++;
                              }else{
                                if ($x == $sem_array['0'] && $y == $sem_array['1']) {
                                  break;
                                }
                                $y++;
                              }
                            }
                          }

                        }else{
                          echo '<div class="text-center">';
                          echo '<b class="">No Grade is found for this student. </b></br></br>';
                          echo '</div>';
                        }

                      ?>
                    </div>
                    <div role="tabpanel" class="tab-pane fade" id="tab_content4" aria-labelledby="profile-tab2">
                      <form class="form-horizontal" action="_print_admit.php" method="post">
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
                        <div class="form-group">
                          <label class="control-label col-md-3 col-sm-3 col-xs-12">Date</label>
                          <div class="col-md-6">
                            <input type="text" name="date" class="form-control">
                            <small>Format: 1st May 2017</small>
                          </div>
                        </div>
                        <input type="hidden" name="student_id" value="<?php echo $student['student_id']; ?>">
                        <button class="btn btn-success pull-right" type="submit"> <span class="fa fa-print"></span> Print Admit</button>
                      </form>
                    </div> <!-- content-panel 4 -->
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- /page content -->

  <!-- page footer -->
  <?php 
    $settings->footer(); 

     function show_result( $student, $semester, $grade_map, $semester_map, $grade_map_r) {
        $db = new App\marks\Marks();
        //if any student is available search his result
        if ($db->assign(array('session' => $student['session'], 'semester' => $semester))->check_all() ) {
          $results = $db->assign(array('session' => $student['session'], 'semester' => $semester))->all_by_session_semester();

          if(is_array($results)) {
            echo '<h2 class="text-center">'.$semester_map[$semester].'</h2>';
            echo '<form action="../marks/_print.php" method="post">
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
                    <td>'.$result['course_name'].' </td>
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
              echo '<tr>
                    <td><b>Didn\'t attend any exam</b></td>
                    <td>&nbsp</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                  </tr>';
            }
            echo '</table>';

            return array(
              'total_credit' => $total_credit, 
              'total_point' =>$total_point
              );

          }
        }
          
      }
  ?>
