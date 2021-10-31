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
  $page_title = 'Student List';

  $db = new App\student\Student();
  $db2 = new App\student\Student();
  $db_session = new App\session\Session();
  $settings = new App\settings\Settings();

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
    '1-1' =>  '1st Year 1st Semester',
    '1-2' =>  '1st Year 2nd Semester',
    '2-1' =>  '2nd Year 1st Semester',
    '2-2' =>  '2nd Year 2nd Semester',
    '3-1' =>  '3rd Year 1st Semester',
    '3-2' =>  '3rd Year 2nd Semester'
    );

  // print page sidebar
  $settings->sidebar($sidebar_data); 

  //initiate paginator
  use JasonGrimes\Paginator;
  
  //selected session from the form
  $post_session = isset($_GET['session'])?$_GET['session']:''; 

  //if no session selected show all student
  if (empty($post_session)) {
    //all student
    $urlPattern = '../student/index.php?page=(:num)';
    $totalItems = $db->total();
  }else{
    if(!empty($_GET['semester'])) {
      //student by session
      $urlPattern = '../student/index.php?session='.$post_session.'&semester='.$_GET['semester'].'&page=(:num)';
      $totalItems = $db->assign(array('session' => $post_session, 'semester' => $_GET['semester']))->total_student_session_semester();
    }else{
      //student by session
      $urlPattern = '../student/index.php?session='.$post_session.'&page=(:num)';
      $totalItems = $db->assign(array('session' => $post_session))->total_student_session($post_session);
    }
  }

  $itemsPerPage = 15;
  $currentPage = isset($_GET['page'])?$_GET['page']:1;
  $offset = $itemsPerPage * ($currentPage-1);
  $paginator = new Paginator($totalItems, $itemsPerPage, $currentPage, $urlPattern);

  //session name
  $sessions = $db_session->all_full();
  ?>

  <!-- page content -->
  <div class="right_col" role="main">

    <div class="page-title">
      <div class="title_left">
        <h3>Student List </h3>
        <!-- <br><br> -->
      </div>

      <div class="title_right">
        <div class="col-md-5 col-sm-5 col-xs-12 form-group pull-right top_search">
          <form action="index.php" method="post">
            <div class="input-group">
              <input type="hidden" name="session" value="<?php echo !empty($_GET['session'])?$_GET['session']:''; ?>">
              <input type="text" name="search" class="form-control" placeholder="Students Name or Roll">
              <span class="input-group-btn">
                <button type="submit" class="btn btn-default" type="button">Go!</button>
              </span>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-12">
        <div class="x_panel">
          <div class="x_title">
          <form action="index.php" method="GET" class="col-md-4">
            <h5>Session</h5>
            <?php 
              if (is_array($sessions)) {
                echo '<select name="session" class="form-control form-group" onchange="this.form.submit()">';
                echo '<option>Choose Session</option>';
                foreach ($sessions as $session) {
                  echo '<option '.(isset($_GET['session']) && $_GET['session'] == $session['session_name']?'selected':'').'  value="'.$session['session_name'].'">'.$session['session_name'].'</option>';
                }
                echo '</select>';
              }else{
                //if no session
                echo '<h2 class="text-danger">Please Add some session.</h2>';
              }
            ?>
              <h5>Semester</h5>
              <select name="semester" class="form-control" onchange="this.form.submit()">
                <option value="">Chose Semester</option>
                <option <?php echo isset($_GET['semester']) && $_GET['semester'] == '1-1'?'selected':''; ?> value="1-1">1st Year 1st Semester</option>
                <option <?php echo isset($_GET['semester']) && $_GET['semester'] == '1-2'?'selected':''; ?> value="1-2">1st Year 2nd Semester</option>
                <option <?php echo isset($_GET['semester']) && $_GET['semester'] == '2-1'?'selected':''; ?> value="2-1">2nd Year 1st Semester</option>
                <option <?php echo isset($_GET['semester']) && $_GET['semester'] == '2-2'?'selected':''; ?> value="2-2">2nd Year 2nd Semester</option>
                <option <?php echo isset($_GET['semester']) && $_GET['semester'] == '3-1'?'selected':''; ?> value="3-1">3rd Year 1st Semester</option>
                <option <?php echo isset($_GET['semester']) && $_GET['semester'] == '3-2'?'selected':''; ?> value="3-2">3rd Year 2nd Semester</option>
              </select>
            </form>
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
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  <th></th>
                  <th>Name</th>
                  <th>Roll</th>
                  <th>BNMC Reg No</th>
                  <th>Session</th>
                  <th>Semester</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php 
                  //print all student name into table
                  if(!empty($_GET['session']) && !empty($_GET['semester'])) {
                     $students = $db->assign(array('session'=> $_GET['session'], 'semester' => $_GET['semester']))->all_student_session_semester($itemsPerPage, $offset);
                  }
                  else if (isset($_GET['session'])) {
                    $students = $db->assign(array('session'=> $post_session))->all_student_session($itemsPerPage, $offset, $post_session);
                  }
                  // else if(isset($_POST['search']) && !empty($_POST['session'])){
                  //   echo '<h1>Roll and session</h1>';
                  //   $students = $db->assign(array('roll' => $_POST['search'], 'session' => $_POST['session']))->search_by_roll_session();
                  // }
                  else if(isset($_POST['search'])){
                    $students = $db->assign(array('roll' => $_POST['search']))->search_by_roll();
                  }else{
                    $students = $db->all($itemsPerPage, $offset);
                  }

                  $i =0;
                  if(is_array($students)){
                    foreach ($students as $student) {
                      echo '<tr>
                              <td style="text-align:center;">'.($offset+1+$i).'</td>
                              <th scope="row">'.$student['student_name'].'</th>
                              <td>'.$student['roll'].'</td>
                              <td>'.$student['bnc_roll'].'</td>
                              <td>'.$student['session'].'</td>
                              <td>'.$semester_map[$student['semester']].'</td>
                              <td>';

                        //check edit permission for this user
                        if ($settings->get_user_permission($user_role, 'student-view')) {
                          echo '<a href="student-profile.php?student='.$student['student_id'].'" class="btn btn-dark btn-xs">Profile</a>';
                        }
                        //check edit permission for this user
                        if ($settings->get_user_permission($user_role, 'student-edit')) {
                          echo '<a href="student-edit.php?student='.$student['student_id'].'" class="btn btn-primary btn-xs">Edit</a>';
                        }
                        //check delete permission for this user
                        if ($settings->get_user_permission($user_role, 'student-delete')) {
                          echo '<a href="_delete.php?student='.$student['student_id'].'"  class="btn btn-danger btn-xs">Delete</a>';
                        }
                        echo '</td>
                            </tr>';
                        $i++;
                    }
                  }else{
                    echo '<tr>
                            <td colspan="6" style="text-align:center;">No student found</td>
                          </tr>';
                  }
                ?>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
    </div>

    </div>
  <!-- /form -->
  <!-- footer link -->
    <div class="row">
      <div class="col-md-12">

      <?php 
        if (!isset($_POST['search'])) {
          echo $paginator;  
        }
      ?>

      </div>
  </div>
  </div>
  <!-- /footer link -->
  <!-- /page content -->

  <!-- footer content -->
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

      $settings->footer($js); 


  ?>
