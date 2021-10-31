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
  $operation = 'student-add';
  $page_title = 'New Student';

  $settings = new App\settings\Settings();
  $db = new App\student\Student();
  $db_session = new App\session\Session();

  //check access permission for this user
  if (!$settings->get_user_permission($user_role, $operation)) {
    header("Location: ../home/403.php");
    die();
  }

  $css = '<!-- Dropzone.js -->
          <link href="../../assets/css/dropzone.min.css" rel="stylesheet">';
  // print page header
  $settings->header($page_title, $css); 
  
  $sidebar_data = array(
      'username'  => $_SESSION['user_name'],
      'user_role' => $user_role,
      'operation' =>  $operation
    );

  // print page sidebar
  $settings->sidebar($sidebar_data); 
  $sessions = $db_session->all(100,0);

  //generate form valdation code
  $filter = new App\filter\Filter();
  $validator = $filter->student_basic();
  $jquery_validator = new HybridLogic\Validation\ClientSide\jQueryValidator($validator);
  $jquery = $jquery_validator->generate();

  $validator2 = $filter->student_add_profile();
  $jquery_validator2 = new HybridLogic\Validation\ClientSide\jQueryValidator($validator2);
  $jquery2 = $jquery_validator2->generate();
  ?>

  <!-- page content -->
  <div class="right_col" role="main">
    <div class="row">
      <div class="col-md-12">
          <div class="x_panel">
            <div class="x_title">
              <h2><i class="fa fa-pencil"> </i> Add new Student</h2>
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
              <div class="col-xs-3">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs tabs-left">
                  <li class="active"><a href="#home" data-toggle="tab">Basic Info</a>
                  </li>
                  <li><a href="#profile" data-toggle="tab">Profile</a>
                  </li>
                  <!-- <li><a href="#image" data-toggle="tab">Image</a> -->
                  <!-- </li> -->
                </ul>
              </div>

              <div class="col-xs-9">
                <!-- Tab panes -->
                <div class="tab-content">
                  <div class="tab-pane active" id="home">
                    <!-- <p class="lead"></p> -->
                    
                    <?php 
                      //if student basic info saved the it only display it
                      if (isset($_SESSION['student_id'])) {
                        $student = $db->assign(array('student_id' => $_SESSION['student_id']))->single();
                     ?>
                     <p class="lead text-danger">Add student profile now from <a href="#profile"  data-toggle="tab"><b>Profile</b></a> Tab</p>
                     <table class="table  table-bordered table-hover">
                        <tbody>
                          <tr>
                            <th>Student Name</th>
                            <td><?php echo $student['student_name']; ?></td>
                          </tr>
                          <tr>
                            <th>Roll</th>
                            <td><?php echo $student['roll']; ?></td>
                          </tr>
                          <tr>
                            <th>BNMC Roll</th>
                            <td><?php echo empty($student['bnc_roll'])? 'N/A': $student['bnc_roll']; ?></td>
                          </tr>
                          <tr>
                            <th>Session</th>
                            <td><?php echo $student['session']; ?></td>
                          </tr>
                          <tr>
                            <th>Semester</th>
                            <td><?php echo $student['semester']; ?></td>
                          </tr>
                        </tbody>
                      </table>

                    <?php    
                      }else{
                        //else it will show the input form
                    ?>
                    <!-- basic infor form -->
                    <form id="myform" action="_store.php" method="post" class="form-horizontal form-label-left">
                      <span class="section">Personal Info</span>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3" for="first-name">Student Full Name <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6">
                          <input type="text" name="student_name" required="required" class="form-control col-md-7 col-xs-12">
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3" for="last-name">Roll <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6">
                          <input type="text" id="last-name2" name="roll" required="required" class="form-control col-md-7 col-xs-12">
                        </div>
                      </div>
                      <div class="form-group">
                        <label for="middle-name" class="control-label col-md-3 col-sm-3">BNMC Reg. No </label>
                        <div class="col-md-6 col-sm-6">
                          <input class="form-control col-md-7 col-xs-12" type="text" name="bnc_roll">
                        </div>
                      </div>


                      <div class="form-group">
                        <label for="middle-name" class="control-label col-md-3 col-sm-3">Session <span class="required">*</span></label>
                        <div class="col-md-6 col-sm-6">

                        <?php 
                          if (is_array($sessions)) {
                            echo '<select name="session" required class="form-control form-group">';
                            echo '<option value="">Choose Session</option>';
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
                        <label for="middle-name" class="control-label col-md-3 col-sm-3">Semester <span class="required">*</span></label>
                        <div class="col-md-6 col-sm-6">
                          <!-- <input class="form-control col-md-7 col-xs-12" required="required" type="text" name="semester"> -->
                          <select name="semester" required="required" required="required" class="form-control">
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
					  
					  <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3" for="last-name">Start Date 
                        </label>
                        <div class="col-md-6 col-sm-6">
                          <input type="text" id="last-name2" name="start_date" placeholder="12 Jun 2017" class="form-control col-md-7 col-xs-12">
                        </div>
                      </div>
					  
					  <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3" for="last-name">End Date 
                        </label>
                        <div class="col-md-6 col-sm-6">
                          <input type="text" id="last-name2" name="end_date" placeholder="12 Jun 2020" class="form-control col-md-7 col-xs-12">
                        </div>
                      </div>
					  
					  <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3" for="last-name">Final Exam Date 
                        </label>
                        <div class="col-md-6 col-sm-6">
                          <input type="text" id="last-name2" name="final_exam_date" placeholder="12 May 2020" class="form-control col-md-7 col-xs-12">
                        </div>
                      </div>

                    <!-- <a href="#profile" data-toggle="tab" class="btn btn-primary">Next</a> -->
                      <button type="submit" class="btn btn-success" name="home" value="1">Save</button>
                    </form>

                    <?php } ?>

                  </div>
                  <!-- /basic tab -->
                  <div class="tab-pane" id="profile">
                    <?php 
                      if (!isset($student) || !is_array($student)) {
                        //student is not added first add a student
                        echo '<h2 class="lead text-danger">Please Add a <a href="#home"  data-toggle="tab"><b>Student</b></a> before adding his/her profile</h2>';
                      }else {
                        //student added now add student info
                    ?>


                    <p class="lead">Student Name: <b><?php echo ucfirst($student['student_name']); ?></b></p>
                    <form id="myform2" action="_store.php" method="post" class="form-horizontal form-label-left">
                      <span class="section">Details Info</span>
                      <!-- student_id -->
                      <input type="hidden" name="student_id" value="<?php echo $student['student_id']; ?>">
                      
                      <!-- <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3" for="first-name">Institute Name
                        </label>
                        <div class="col-md-6 col-sm-6">
                          <input type="text" name="institute_name" class="form-control col-md-7 col-xs-12">
                        </div>
                      </div> -->

                      <!-- <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3" for="last-name">College Code 
                        </label>
                        <div class="col-md-6 col-sm-6">
                          <input type="text" id="last-name2" name="college_code" class="form-control col-md-7 col-xs-12">
                        </div>
                      </div> -->

                      <div class="form-group">
                        <label for="middle-name" class="control-label col-md-3 col-sm-3">Father's Name</label>
                        <div class="col-md-6 col-sm-6">
                          <input class="form-control col-md-7 col-xs-12" type="text" name="father_name">
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="middle-name" class="control-label col-md-3 col-sm-3">Mother's Name</label>
                        <div class="col-md-6 col-sm-6">
                          <input class="form-control col-md-7 col-xs-12" type="text" name="mother_name">
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="middle-name" class="control-label col-md-3 col-sm-3">Guardian Name</label>
                        <div class="col-md-6 col-sm-6">
                          <input class="form-control col-md-7 col-xs-12" type="text" name="guardian_name">
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="middle-name" class="control-label col-md-3 col-sm-3">Relation to guardian</label>
                        <div class="col-md-6 col-sm-6">
                          <input class="form-control col-md-7 col-xs-12" type="text" name="relation_to_guardian">
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="middle-name" class="control-label col-md-3 col-sm-3">Date of Birth</label>
                        <div class="col-md-6 col-sm-6">
                          <fieldset>
                            <div class="control-group">
                              <div class="controls">
                                <div class="col-md-12 xdisplay_inputx form-group has-feedback">
                                  <input type="text" name="date_of_birth" class="form-control has-feedback-left" id="single_cal1" aria-describedby="inputSuccess2Status">
                                  <span class="fa fa-calendar-o form-control-feedback left" aria-hidden="true"></span>
                                  <span id="inputSuccess2Status" class="sr-only">(success)</span>
                                </div>
                              </div>
                            </div>
                          </fieldset>
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="middle-name" class="control-label col-md-3 col-sm-3">NID/Passport</label>
                        <div class="col-md-6 col-sm-6">
                          <input class="form-control col-md-7 col-xs-12" type="text" name="nid">
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="middle-name" class="control-label col-md-3 col-sm-3">Contact Number</label>
                        <div class="col-md-6 col-sm-6">
                          <input class="form-control col-md-7 col-xs-12" type="text" name="contact_number">
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="middle-name" class="control-label col-md-3 col-sm-3">Nationality</label>
                        <div class="col-md-6 col-sm-6">
                          <input class="form-control col-md-7 col-xs-12" type="text" name="nationality">
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="middle-name" class="control-label col-md-3 col-sm-3">Marital Status</label>
                        <div class="col-md-6 col-sm-6">
                          <select  name="marital_status" class="form-control">
                            <option value="1">Married</option>
                            <option value="0">Unmarried</option>
                          </select>
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="middle-name" class="control-label col-md-3 col-sm-3">Gender</label>
                        <div class="col-md-6 col-sm-6">
                          Male <input type="radio" value="male" name="gender">
                          Female <input type="radio"  value="female" name="gender">
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="middle-name" class="control-label col-md-3 col-sm-3">Religion</label>
                        <div class="col-md-6 col-sm-6">
                          <input class="form-control col-md-7 col-xs-12" type="text" name="religion">
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="middle-name" class="control-label col-md-3 col-sm-3">Service Type</label>
                        <div class="col-md-6 col-sm-6">
                          <input class="form-control col-md-7 col-xs-12" type="text" name="service_type">
                        </div>
                      </div>
                      
                      <div class="form-group">
                        <label for="middle-name" class="control-label col-md-3 col-sm-3">Permanent Address</label>
                        <div class="col-md-6 col-sm-6">
                        <textarea class="form-control" name="permanent_address"></textarea>
                        </div>
                      </div>
                      
                       <!-- Updated Dec 2017 -->
                       <div class="form-group">
                        <label for="middle-name" class="control-label col-md-3 col-sm-3">Post Office</label>
                        <div class="col-md-6 col-sm-6">
                          <input class="form-control col-md-7 col-xs-12" type="text" name="po">
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="middle-name" class="control-label col-md-3 col-sm-3">PS</label>
                        <div class="col-md-6 col-sm-6">
                          <input class="form-control col-md-7 col-xs-12" type="text" name="ps">
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="middle-name" class="control-label col-md-3 col-sm-3">District</label>
                        <div class="col-md-6 col-sm-6">
                          <input class="form-control col-md-7 col-xs-12" type="text" name="district">
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="middle-name" class="control-label col-md-3 col-sm-3">Current Address</label>
                        <div class="col-md-6 col-sm-6">
                        <textarea class="form-control" name="current_address"></textarea>
                        </div>
                      </div>

                      <!-- <div class="form-group">
                        <label for="middle-name" class="control-label col-md-3 col-sm-3">Photo</label>
                        <div class="col-md-6 col-sm-6">
                          <input type="file" name="photo">
                        </div>
                      </div> -->

                      <div class="form-group">
                        <label for="middle-name" class="control-label col-md-3 col-sm-3">School Name</label>
                        <div class="col-md-6 col-sm-6">
                          <input class="form-control col-md-7 col-xs-12" type="text" name="school_name">
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="middle-name" class="control-label col-md-3 col-sm-3">College Name</label>
                        <div class="col-md-6 col-sm-6">
                          <input class="form-control col-md-7 col-xs-12" type="text" name="college_name">
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="middle-name" class="control-label col-md-3 col-sm-3">SSC GPA</label>
                        <div class="col-md-6 col-sm-6">
                          <input class="form-control col-md-7 col-xs-12" type="text" name="ssc_gpa">
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="middle-name" class="control-label col-md-3 col-sm-3">HSC GPA</label>
                        <div class="col-md-6 col-sm-6">
                          <input class="form-control col-md-7 col-xs-12" type="text" name="hsc_gpa">
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="middle-name" class="control-label col-md-3 col-sm-3">SSC Passing Year</label>
                        <div class="col-md-6 col-sm-6">
                          <input class="form-control col-md-7 col-xs-12" type="text" name="ssc_passing_year">
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="middle-name" class="control-label col-md-3 col-sm-3">HSC Passing Year</label>
                        <div class="col-md-6 col-sm-6">
                          <input class="form-control col-md-7 col-xs-12" type="text" name="hsc_passing_year">
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="middle-name" class="control-label col-md-3 col-sm-3">Original SSC Doc</label>
                        <div class="col-md-6 col-sm-6">
                          <input class="form-control col-md-7 col-xs-12" type="text" name="original_ssc_doc">
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="middle-name" class="control-label col-md-3 col-sm-3">Original HSC Doc</label>
                        <div class="col-md-6 col-sm-6">
                          <input class="form-control col-md-7 col-xs-12" type="text" name="original_hsc_doc">
                        </div>
                      </div>

                    <!-- <a href="#profile" data-toggle="tab" class="btn btn-primary">Next</a> -->
                      <button type="submit" class="btn btn-success" name="profile" value="1">Save</button>
                    </form>

                    <?php 
                      } //else condion
                     ?>

                  </div>
                  <!-- /profile -->
                  <!-- /basic tab -->
                  <!-- <div class="tab-pane" id="image">
                    <p class="lead">Step 3</p>

                    <form action="_store.php" id="my-dropzone" class="dropzone"></form>
                    http://www.dropzonejs.com/bootstrap.html
                  </div> -->
                  <!-- end image -->
                </div>
              </div>

              <div class="clearfix"></div>

            </div>
          </div>
        </div>
        <!-- end tab -->
      </div>
      <!-- /row -->
  </div>
  <!-- /page content -->

  <!-- page footer -->
  <?php 
    $js = '<!-- Dropzone.js -->
          <script src="../../assets/js/dropzone.min.js"></script>

          <script type="text/javascript">
        

          //select tab
          function activaTab(tab){
            $(\'.nav-tabs a[href="#\' + tab + \'"]\').tab(\'show\');
          };';

          if (isset($_SESSION['student_id'])) {
            $js .= 'activaTab(\'profile\');';
            unset($_SESSION['student_id']);
          }
   // $js .= '<script type="text/javascript">';
        foreach($jquery2['methods'] as $method_name => $method_function):
        $js .= 'jQuery.validator.addMethod("'.$method_name.'", '.$method_function.');';
        endforeach;
    $js .= '$("#myform2").validate({
            errorClass: "text-danger",

              highlight: function(element, errorClass) {
                $(element).fadeOut(100,function() {
                  $(element).fadeIn(100);
                });
              },

            rules: '.json_encode($jquery2['rules']).',
            messages: '.json_encode($jquery2['messages']).'

          });';
    $js .= '</script>';

    $js .= '<script type="text/javascript">
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
   