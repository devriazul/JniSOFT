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
  $operation = 'student-edit';
  $page_title = 'Update Student Information';

  $settings = new App\settings\Settings();
  $db = new App\student\Student();
  $db_session = new App\session\Session();

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

  $student = $db->assign(array('student_id' => $_GET['student']))->single();
  $profile = $db->assign(array('student_id' => $_GET['student']))->single_profile();
  $sessions = $db_session->all_full();  

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
              <h2><i class="fa fa-pencil"> </i> Update Student Information</h2>
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
                  <li><a href="#image" data-toggle="tab">Image</a>
                  </li>
                </ul>
              </div>

              <div class="col-xs-9">
                <!-- Tab panes -->
                <div class="tab-content">
                  <div class="tab-pane active" id="home">
                    <p class="lead">Student Name: <b><?php echo $student['student_name']; ?></b></p>
                                        
                    <!-- basic infor form -->
                    <form id="myform" action="_update.php" method="post" class="form-horizontal form-label-left">
                      <input type="hidden" name="student_id" value="<?php echo $student['student_id']; ?>">
                      <span class="section">Personal Info</span>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3" for="first-name">Student Full Name <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6">
                          <input type="text" value="<?php echo $student['student_name']; ?>" name="student_name" required="required" class="form-control col-md-7 col-xs-12">
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3" for="last-name">Roll <span class="required">*</span>
                        </label>
                        <div class="col-md-6 col-sm-6">
                          <input disabled="disabled" type="text" value="<?php echo $student['roll']; ?>" id="last-name2" name="roll" required="required" class="form-control col-md-7 col-xs-12">
                        </div>
                      </div>
                      <div class="form-group">
                        <label for="middle-name" class="control-label col-md-3 col-sm-3">BNMC Reg. No </label>
                        <div class="col-md-6 col-sm-6">
                          <input class="form-control col-md-7 col-xs-12" value="<?php echo $student['bnc_roll']; ?>" type="text" name="bnc_roll">
                        </div>
                      </div>
                      <div class="form-group">
                        <label for="middle-name" class="control-label col-md-3 col-sm-3">Session <span class="required">*</span></label>
                        <div class="col-md-6 col-sm-6">
                        <?php 
                          if (is_array($sessions)) {
                            echo '<select name="session" class="form-control form-group">';
                            echo '<option>Choose Session</option>';
                            foreach ($sessions as $session) {
                              $select = $session['session_name'] == $student['session']? 'selected':'';
                              echo '<option '.$select.'  value="'.$session['session_name'].'">'.$session['session_name'].'</option>';
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
                        <label class="control-label col-md-3">Semester</label>
                        <div class="col-md-6">
                          <select name="semester" required="required" class="form-control">
                            <option value="">Choose a Semester</option>
                            <option <?php echo $student['semester'] == '1-1'?'selected':''; ?> value="1-1">1st Year 1st Semester</option>
                            <option <?php echo $student['semester'] == '1-2'?'selected':''; ?> value="1-2">1st Year 2nd Semester</option>
                            <option <?php echo $student['semester'] == '2-1'?'selected':''; ?> value="2-1">2nd Year 1st Semester</option>
                            <option <?php echo $student['semester'] == '2-2'?'selected':''; ?> value="2-2">2nd Year 2nd Semester</option>
                            <option <?php echo $student['semester'] == '3-1'?'selected':''; ?> value="3-1">3rd Year 1st Semester</option>
                            <option <?php echo $student['semester'] == '3-2'?'selected':''; ?> value="3-2">3rd Year 2nd Semester</option>
                          </select>
                        </div>
                      </div>
					  <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3" for="last-name">Start Date 
                        </label>
                        <div class="col-md-6 col-sm-6">
                          <input type="text" value="<?php echo $student['start_date']; ?>" name="start_date" class="form-control col-md-7 col-xs-12">
                        </div>
                      </div>
					  <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3" for="last-name">End Date 
                        </label>
                        <div class="col-md-6 col-sm-6">
                          <input type="text" value="<?php echo $student['end_date']; ?>" name="end_date" class="form-control col-md-7 col-xs-12">
                        </div>
                      </div>
					  <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3" for="last-name">Final Exam Date 
                        </label>
                        <div class="col-md-6 col-sm-6">
                          <input  type="text" value="<?php echo $student['final_exam_date']; ?>" name="final_exam_date" class="form-control col-md-7 col-xs-12">
                        </div>
                      </div>

                    <a href="../student/index.php" class="btn btn-dark">Back</a>
                    <a href="#profile" data-toggle="tab" class="btn btn-primary">Profile</a>
                      <button type="submit" class="btn btn-success" name="home" value="1">Update</button>
                    </form>

                  </div>
                  <!-- /basic tab -->
                  <div class="tab-pane" id="profile">
                    <?php 
                      //if (!isset($student) || !is_array($student)) {
                        //student is not added first add a student
                        //echo '<h2 class="lead text-danger">Please Add a <a href="#home"  data-toggle="tab"><b>Student</b></a> before adding his/her profile</h2>';
                      //}else {
                        //student added now add student info
                      
                    ?>


                    <p class="lead">Student Name: <b><?php echo $student['student_name']; ?></b></p>
                    <form id="myform2" action="_update.php" method="post" class="form-horizontal form-label-left">
                      <span class="section">Details Info</span>
                      <!-- student_id -->
                      <input type="hidden" name="student_id" value="<?php echo $student['student_id']; ?>">
                      
                     <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3" for="first-name">Institute Name
                        </label>
                        <div class="col-md-6 col-sm-6">
                          <input type="text" disabled="disabled" value="<?php echo $profile['institute_name']; ?>" name="institute_name" class="form-control col-md-7 col-xs-12">
                        </div>
                      </div>

                      <div class="form-group">
                        <label class="control-label col-md-3 col-sm-3" for="last-name">College Code 
                        </label>
                        <div class="col-md-6 col-sm-6">
                          <input type="text" disabled="disabled" id="last-name2" value="<?php echo $profile['college_code']; ?>" name="college_code" class="form-control col-md-7 col-xs-12">
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="middle-name" class="control-label col-md-3 col-sm-3">Father's Name</label>
                        <div class="col-md-6 col-sm-6">
                          <input class="form-control col-md-7 col-xs-12" type="text" value="<?php echo $profile['father_name']; ?>" name="father_name">
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="middle-name" class="control-label col-md-3 col-sm-3">Mother's Name</label>
                        <div class="col-md-6 col-sm-6">
                          <input class="form-control col-md-7 col-xs-12" type="text" value="<?php echo $profile['mother_name']; ?>" name="mother_name">
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="middle-name" class="control-label col-md-3 col-sm-3">Guardian Name</label>
                        <div class="col-md-6 col-sm-6">
                          <input class="form-control col-md-7 col-xs-12" type="text" value="<?php echo $profile['guardian_name']; ?>" name="guardian_name">
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="middle-name" class="control-label col-md-3 col-sm-3">Relation to guardian</label>
                        <div class="col-md-6 col-sm-6">
                          <input class="form-control col-md-7 col-xs-12" type="text" value="<?php echo $profile['relation_to_guardian']; ?>" name="relation_to_guardian">
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="middle-name" class="control-label col-md-3 col-sm-3">Date of Birth</label>
                        <div class="col-md-6 col-sm-6">
                          <fieldset>
                            <div class="control-group">
                              <div class="controls">
                                <div class="col-md-12 xdisplay_inputx form-group has-feedback">
                                  <input type="text" value="<?php echo date('m/d/Y', strtotime($profile['date_of_birth'])); ?>" name="date_of_birth" class="form-control has-feedback-left" id="single_cal1" aria-describedby="inputSuccess2Status">
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
                          <input class="form-control col-md-7 col-xs-12" type="text" value="<?php echo $profile['nid']; ?>" name="nid">
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="middle-name" class="control-label col-md-3 col-sm-3">Contact Number</label>
                        <div class="col-md-6 col-sm-6">
                          <input class="form-control col-md-7 col-xs-12" type="text" value="<?php echo $profile['contact_number']; ?>" name="contact_number">
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="middle-name" class="control-label col-md-3 col-sm-3">Nationality</label>
                        <div class="col-md-6 col-sm-6">
                          <input class="form-control col-md-7 col-xs-12" type="text" value="<?php echo $profile['nationality']; ?>" name="nationality">
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="middle-name" class="control-label col-md-3 col-sm-3">Marital Status</label>
                        <div class="col-md-6 col-sm-6">
                          <select name="marital_status" class="form-control">
                            <option <?php echo $profile['marital_status'] == 1?'selected':''; ?> value="1">Married</option>
                            <option <?php echo $profile['marital_status'] == 0?'selected':''; ?> value="0">Unmarried</option>
                          </select>
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="middle-name" class="control-label col-md-3 col-sm-3">Gender</label>
                        <div class="col-md-6 col-sm-6">
                          Male <input type="radio"<?php echo $profile['gender'] == 'male'?'checked':''; ?> value="male" name="gender">
                          Female <input type="radio" <?php echo $profile['gender'] == 'female'?'checked':''; ?> value="female" name="gender">
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="middle-name" class="control-label col-md-3 col-sm-3">Religion</label>
                        <div class="col-md-6 col-sm-6">
                          <input class="form-control col-md-7 col-xs-12" type="text" value="<?php echo $profile['religion']; ?>" name="religion">
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="middle-name" class="control-label col-md-3 col-sm-3">Service Type</label>
                        <div class="col-md-6 col-sm-6">
                          <input class="form-control col-md-7 col-xs-12" type="text" value="<?php echo $profile['service_type']; ?>" name="service_type">
                        </div>
                      </div>

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

                      <div class="form-group">
                        <label for="middle-name" class="control-label col-md-3 col-sm-3">Permanent Address</label>
                        <div class="col-md-6 col-sm-6">
                        <textarea class="form-control" name="permanent_address"><?php echo $permanent_address; ?></textarea>
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="middle-name" class="control-label col-md-3 col-sm-3">Post Office</label>
                        <div class="col-md-6 col-sm-6">
                          <input class="form-control col-md-7 col-xs-12" value="<?php echo $po; ?>" type="text" name="po">
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="middle-name" class="control-label col-md-3 col-sm-3">PS</label>
                        <div class="col-md-6 col-sm-6">
                          <input class="form-control col-md-7 col-xs-12" value="<?php echo $ps; ?>" type="text" name="ps">
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="middle-name" class="control-label col-md-3 col-sm-3">District</label>
                        <div class="col-md-6 col-sm-6">
                          <input class="form-control col-md-7 col-xs-12" value="<?php echo $district; ?>" type="text" name="district">
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="middle-name" class="control-label col-md-3 col-sm-3">Current Address</label>
                        <div class="col-md-6 col-sm-6">
                        <textarea class="form-control" name="current_address"><?php echo $profile['current_address']; ?></textarea>
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
                          <input class="form-control col-md-7 col-xs-12" type="text"value="<?php echo $profile['school_name']; ?>" name="school_name">
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="middle-name" class="control-label col-md-3 col-sm-3">College Name</label>
                        <div class="col-md-6 col-sm-6">
                          <input class="form-control col-md-7 col-xs-12" type="text" value="<?php echo $profile['college_name']; ?>" name="college_name">
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="middle-name" class="control-label col-md-3 col-sm-3">SSC GPA</label>
                        <div class="col-md-6 col-sm-6">
                          <input class="form-control col-md-7 col-xs-12" type="text" value="<?php echo $profile['ssc_gpa']; ?>" name="ssc_gpa">
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="middle-name" class="control-label col-md-3 col-sm-3">HSC GPA</label>
                        <div class="col-md-6 col-sm-6">
                          <input class="form-control col-md-7 col-xs-12" type="text" value="<?php echo $profile['hsc_gpa']; ?>" name="hsc_gpa">
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="middle-name" class="control-label col-md-3 col-sm-3">SSC Passing Year</label>
                        <div class="col-md-6 col-sm-6">
                          <input class="form-control col-md-7 col-xs-12" type="text" value="<?php echo $profile['ssc_passing_year']; ?>" name="ssc_passing_year">
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="middle-name" class="control-label col-md-3 col-sm-3">HSC Passing Year</label>
                        <div class="col-md-6 col-sm-6">
                          <input class="form-control col-md-7 col-xs-12" type="text" value="<?php echo $profile['hsc_passing_year']; ?>" name="hsc_passing_year">
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="middle-name" class="control-label col-md-3 col-sm-3">Original SSC Doc</label>
                        <div class="col-md-6 col-sm-6">
                          <input class="form-control col-md-7 col-xs-12" type="text" value="<?php echo $profile['original_ssc_doc']; ?>" name="original_ssc_doc">
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="middle-name" class="control-label col-md-3 col-sm-3">Original HSC Doc</label>
                        <div class="col-md-6 col-sm-6">
                          <input class="form-control col-md-7 col-xs-12" type="text" value="<?php echo $profile['original_hsc_doc']; ?>" name="original_hsc_doc">
                        </div>
                      </div>
                      <a href="../student/index.php" class="btn btn-dark">Back</a>
                      <a href="#home" data-toggle="tab" class="btn btn-primary">Basic Info</a>
                      <button type="submit" class="btn btn-success" name="profile" value="1">Update</button>
                    </form>

                    <?php 
                      //} //else condion
                     ?>

                  </div>
                  <!-- /profile -->
                  <!-- /basic tab -->
                  <div class="tab-pane" id="image">
                    <p class="lead">Upload Image</p>
                    <?php 
                      if (!empty($profile['photo'])) {
                        echo '<img class="img img-responsive" style="height:150px;" src="../../assets/images/students/'.$student['session'].'/'.$profile['photo'].'" alt="profile image missing">';
                      }else {
                        echo '<img class="img-responsive" src="../../assets/images/user.png">';
                      }
                    ?>
                    <br>
                    <form id="dZUpload" class="dropzone">
                      <input type="hidden" name="student_id" value="<?php echo $student['student_id']; ?>" />
                      <input type="hidden" name="session" value="<?php echo $student['session']; ?>">
                        <div class="dz-default dz-message">Drag an image or click here to upload</div>
                    </form>

                  </div>
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


          $(document).ready(function () {
              Dropzone.autoDiscover = false;
              $("#dZUpload").dropzone({
                  url: "_store.php",
                  uploadMultiple: false,
                   maxFiles: 1,
                  acceptedFiles: ".png,.jpg,.gif,.jpeg",
                  addRemoveLinks: true,
                  success: function (file, response) {
                      file.previewElement.classList.add("dz-success");
                      

                      message("Success", response);
                  },
                  error: function (file, response) {
                      file.previewElement.classList.add("dz-error");
                      //console.log(response);
                      message("Error", response, "error");
                  }
              });
          });     

          //select tab
          function activaTab(tab){
            $(\'.nav-tabs a[href="#\' + tab + \'"]\').tab(\'show\');
          };';

          if (isset($_SESSION['student_id'])) {
            $js .= 'activaTab(\'profile\');';
            unset($_SESSION['student_id']);
          }

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
            //delete confirmation message
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
   