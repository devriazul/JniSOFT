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
  $operation = 'tabulation-print';

  $settings = new App\settings\Settings();

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

  ?>

  <!-- page content -->
  <div class="right_col" role="main">
    <!-- institute setting -->
    <div class="row">
      <div class="col-md-12">
          <div class="x_panel">
            <div class="x_title">
              <h2> Print Tabulation Sheet </h2>
              <ul class="nav navbar-right panel_toolbox">
                <li><a class="close-link"></a></li>
                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a></li>
                <li><a class="close-link"><i class="fa fa-close"></i></a>
                </li>
              </ul>
              <div class="clearfix"></div>
            </div>
            <div class="x_content">
                            
                <form class="form-horizontal" action="_print_tabulation.php" method="post">
                  <div class="form-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12">Session</label>
                    <div class="col-md-4">

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
                  <label class="control-label col-md-3">Semester</label>
                  <div class="col-md-4">
                    <select name="semester" required="required" class="form-control">
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
                  <button class="btn btn-success" type="submit"> <span class="fa fa-print"></span> Print Tabulation</button>
                </form>

                  
            </div>
          </div>
        </div> <!-- /col -->
      </div>
      <!-- /row -->
  </div>
  <!-- /page content -->

  <!-- page footer -->
  <?php 

      $settings->footer(); 

  ?>
