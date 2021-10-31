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
  $operation = 'session-view';
  $page_title = 'Session List';

  $db = new App\session\Session();
  $db_student = new App\student\Student();
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

  // print page sidebar
  $settings->sidebar($sidebar_data); 

  //initiate paginator
  use JasonGrimes\Paginator;
  $totalItems = $db->total();
  $itemsPerPage = 15;
  $currentPage = isset($_GET['page'])?$_GET['page']:1;
  $offset = $itemsPerPage * ($currentPage-1);
  $urlPattern = '../session/index.php?page=(:num)';

  $paginator = new Paginator($totalItems, $itemsPerPage, $currentPage, $urlPattern);

  ?>

  <!-- page content -->
  <div class="right_col" role="main">
    <div class="col-md-12">
        <div class="x_panel">
          <div class="x_title">
            <h2>Session List </h2>
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
            <table class="table  table-bordered table-hover">
              <thead>
                <tr>
                  <th></th>
                  <th>Session Name</th>
                  <th>Number Of Student</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php 
                  //print all session name into table
                  $sessions = $db->all($itemsPerPage, $offset);
                  
                  $i = 0;
                  if(is_array($sessions)){
                    foreach ($sessions as $session) {
                      $student_num = $db_student->assign(array('session' => $session['session_name']))->total_student_session();
                      echo '<tr>
                              <td style="text-align:center;">'.($offset+1+$i).'</td>
                              <th scope="row">'.$session['session_name'].'</th>
                              <td>'.$student_num.'</td>
                              <td>';

                        //check edit permission for this user
                        if ($settings->get_user_permission($user_role, 'session-edit')) {
                          echo '<a href="session-edit.php?session='.$session['session_id'].'" class="btn btn-primary btn-xs">Edit</a>';
                        }
                        //check delete permission for this user
                        if ($settings->get_user_permission($user_role, 'session-delete')) {
                          echo '<a href="_delete.php?session='.$session['session_id'].'"  class="btn btn-danger btn-xs">Delete</a>';
                        }
                        echo '</td>
                            </tr>';
                        $i++;
                    }
                  }else{
                    echo '<tr><td>No session found</td><td></tr>';
                  }
                ?>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
    </div>
  <!-- /form -->
  <!-- footer link -->
    <div class="row">
      <div class="col-md-12">

      <?php echo $paginator;  ?>

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
