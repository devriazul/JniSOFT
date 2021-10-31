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
  $operation = 'user-view';
  $page_title = 'User List';

  $db = new App\user\User();
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
  
  //selected session from the form
  $post_session = isset($_GET['user'])?$_GET['user']:''; 

  //if no session selected show all student
  if (empty($post_session)) {
    //all student
    $urlPattern = '../user/index.php?page=(:num)';
    $totalItems = $db->total();
  }else{
    //student by session
    $urlPattern = '../user/index.php?user='.$post_session.'&page=(:num)';
    $totalItems = $db->assign(array('user_type' => $post_session))->total_user_by_type();
  }

  $itemsPerPage = 5;
  $currentPage = isset($_GET['page'])?$_GET['page']:1;
  $offset = $itemsPerPage * ($currentPage-1);
  $paginator = new Paginator($totalItems, $itemsPerPage, $currentPage, $urlPattern);

  //session name
  //$users = $db->all($itemsPerPage, $offset);
  ?>

  <!-- page content -->
  <div class="right_col" role="main">

    <div class="page-title">
      <div class="title_left">
        <h3>Users List </h3>
      </div>
    </div>

    <div class="row">
      <div class="col-md-12">
        <div class="x_panel">
          <div class="x_title">
          <form action="index.php" method="GET" class="col-md-4">
              <select name="user"  class="form-control form-group" onchange="this.form.submit()">
                <option value="">Choose a Type</option>
                <option value="admin">Admin</option>
                <option value="viewer">Viewer</option>
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
            <table class="table  table-bordered table-hover">
              <thead>
                <tr>
                  <th>User Name</th>
                  <th>Email</th>
                  <th>User Type</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php 
                  //print all student name into table
                  if(isset($_GET['user'])){
                    $users = $db->assign(array('user_type' => $_GET['user']))->all_by_type($itemsPerPage, $offset);
                  }else{
                    $users = $db->all($itemsPerPage, $offset);
                  }
                  if(is_array($users)){
                    foreach ($users as $user) {
                      if ($user['user_type'] == 'super-admin') {
                        continue;
                      }
                      echo '<tr>
                              <th scope="row">'.$user['user_name'].'</th>
                              <td>'.$user['email'].'</td>
                              <td>'.$user['user_type'].'</td>
                              <td>';

                        //check edit permission for this user
                        // if ($settings->get_user_permission($user_role, 'user-edit')) {
                        //   echo '<a href="user-edit.php?user='.$user['user_id'].'" class="btn btn-primary btn-xs">Edit</a>';
                        // }
                        //check delete permission for this user
                        if ($settings->get_user_permission($user_role, 'user-delete')) {
                          echo '<a href="_delete.php?user='.$user['user_id'].'"  class="btn btn-danger btn-xs">Delete</a>';
                        }
                        echo '</td>
                            </tr>';
                    }
                  }else{
                    echo '<tr>
                            <td>No user found</td>
                            <td></td>
                            <td></td>
                            <td></td>
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
        //if (!isset($_GET['user'])) {
          echo $paginator;  
        //}
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
