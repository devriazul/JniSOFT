<?php
	namespace App\Settings;
	/**
	* Settings class used for
	*
	*	-- Control debugging
	*	-- Control user role of admin
	*	-- Dynamically generating header,footer and sidebar menu
	*
	* @copyright 2017 Lopa IT
	*/
	class Settings
	{
		/**
	    * set debug mood
	    *
	    * @var boolean true or false
	    */
		private $en_debug = true;

		/**
	    * store all debug message
	    *
	    * @var array of message
	    */
		private $debug_msg = array();

		/**
	    * all permission is stored here
	    *
	    * @var array permissions data for all users
	    */
		private $permissions;


		function __construct()
		{
			$this->set_user_permission();
		}

		function __destruct()
		{
			if ($this->en_debug && !empty($this->debug_msg)) {
				$this->log_print($this->debug_msg);
			}
		}

		/**
		*	Set all user permission
		*	Such as	student-add,student-edit etc
		*/
		public function set_user_permission()
		{
			$this->permissions = array(
				'viewer'=> array(
					'student',
					'student-view', 
					'admit-print',

					'course',
					'course-view',

					'marks',
					'marks-view',
					'marks-print',
					'tabulation-print',
					'testimonial-print',

					'session',
					'session-view',

					),
				'admin' => array(
					'student',
					'student-view', 
					'student-add',
					'student-edit', 
					'admit-print',
					'seat-plan-print',

					'course',
					'course-view',
					'course-add',
					'course-edit',

					'marks',
					'marks-view',
					'marks-add',
					'marks-edit',
					

					'session',
					'session-view',
					'session-add',
					'session-edit',

					),
				'super-admin' => array(
					'student',
					'student-view', 
					'student-add',
					'student-edit', 
					'student-delete',
					'admit-print',
					'seat-plan-print',

					'course',
					'course-view',
					'course-add',
					'course-edit',
					'course-delete',

					'marks',
					'marks-view',
					'marks-add',
					'marks-edit',
					'marks-delete',
					'marks-print',
					'tabulation-print',
					'testimonial-print',

					'session',
					'session-view',
					'session-add',
					'session-edit',
					'session-delete',

					'user',
					'user-view',
					'user-add',
					'user-edit',
					'user-delete',

					'backup'
					),
				);
		}

		/**
		*	Add debug message to array if debug mood is enable	
		*	
		*	@param $data array or variable 
		*/
		public function log($data)
		{
			if ($this->en_debug) {
				$this->debug_msg[] = $data;
			}
		}

		/**
		*	print log message if debug mood is enable	
		*	
		*	@param $data array or variable 
		*/
		private function log_print($data)
		{
			if (is_array($data) || is_object($data)) {
				echo '<pre>';
					print_r($data);
				echo '<pre>';
			}else {
				echo '<pre>';
					echo 'debug var: '.$data.'<br>';
				echo '<pre>';
			}
		}

		/**
		*	Get user permission
		*	check if any user role has permission to do any specific operation
		*
		*	@param $user_role is the role of users
		*	@param $operation is every task that can possible to perform such as student-add,student-edit
		*
		*	@return true or false 
		*/
		public function get_user_permission($user_role, $operation)
		{
			$found = false;
			foreach ($this->permissions as $user_roles => $operations) {
				if($user_role == $user_roles) {
					if (in_array($operation, $operations))
					{
						$found = true;
					}
				}
			}
			return $found;
		}

		/**
		*	Header of admin panel	
		*	
		*	@param $title page title
		*/
		public function header($title, $extra_style = '')
		{
			?>
			<!DOCTYPE html>
			<html lang="en">
			  <head>
			    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
			    <!-- Meta, title, CSS, favicons, etc. -->
			    <meta charset="utf-8">
			    <meta http-equiv="X-UA-Compatible" content="IE=edge">
			    <meta name="viewport" content="width=device-width, initial-scale=1">

			    <title><?php echo $title; ?></title>

			    <!-- Bootstrap -->
			    <link href="../../assets/css/bootstrap.min.css" rel="stylesheet">
			    <!-- Font Awesome -->
			    <link href="../../assets/css/font-awesome.min.css" rel="stylesheet">
			    <!-- NProgress -->
			    <link href="../../assets/css/nprogress.css" rel="stylesheet">
			    <!-- iCheck -->
			    <link href="../../assets/css/green.css" rel="stylesheet">
			    <!-- PNotifu -->
			    <link href="../../assets/css/pnotify.custom.min.css" media="all" rel="stylesheet" type="text/css" />
				
			    <!-- bootstrap-progressbar -->
			    <link href="../../assets/css/bootstrap-progressbar-3.3.4.min.css" rel="stylesheet">
			    <!-- JQVMap -->
			    <link href="../../assets/css/jqvmap.min.css" rel="stylesheet" />
			    <!-- bootstrap-daterangepicker -->
			    <link href="../../assets/css/daterangepicker.css" rel="stylesheet">

			    <!-- Custom Theme Style -->
			    <link href="../../assets/css/custom.min.css" rel="stylesheet">

			    <?php echo $extra_style; ?>

			  </head>
			<?php
		}

		/**
		*	Sidebar menu of admin panel	
		*
		*	@param $data is array of information that need to show sidebar
		*			array keys: username is the name of user,
		*						user_role is the role of user, 
		*						operation is the specific task such as student-delete
		*/
		public function sidebar($data)
		{
			//$this->log($data);
			?>  
			<body class="nav-md">
			    <div class="container body">
			      <div class="main_container">
			        <div class="col-md-3 left_col">
			          <div class="left_col scroll-view">
			            <div class="navbar nav_title" style="border: 0;">
			              <a href="../home/index.php" class="site_title"><i class="fa fa-user-md"></i> <span>Saic Group</span></a>
			            </div>

			            <div class="clearfix"></div>

			            <!-- menu profile quick info -->
			            <div class="profile clearfix">
			              <!-- <div class="profile_pic">
			                <img src="../../assets/images/img.jpg" alt="..." class="img-circle profile_img">
			              </div> -->
			              <div class="profile_info">
			                <!-- <span>Salesman,</span> -->
			                <h2><?php echo $data['username']; ?></h2>
			              </div>
			            </div>
			            <!-- /menu profile quick info -->

			            <br />

			            <!-- sidebar menu -->
			            <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
			              <div class="menu_section">
			                <h3>General</h3>
			                <ul class="nav side-menu">
			                  <li>
			                    <a href="../home/index.php"><i class="fa fa-home"></i> Home </a>
			                  </li>
			                <?php 
			                	//Marks menu
			                	if ($this->get_user_permission($data['user_role'], 'marks')) {
			                		echo '<li><a><i class="fa fa-line-chart"></i> Marks <span class="fa fa-chevron-down"></span></a>
			                    			<ul class="nav child_menu">';

			                		if ($this->get_user_permission($data['user_role'], 'marks-view')) {
			                			echo '<li><a href="../marks/index.php">Search Marks</a></li>';
				                	}

				                	if ($this->get_user_permission($data['user_role'], 'marks-view')) {
			                			echo '<li><a href="../marks/marks-by-session.php">View Marks</a></li>';
				                	}

				                	if ($this->get_user_permission($data['user_role'], 'marks-add')) {
				                		echo '<li><a href="../marks/marks-add.php">Add Marks</a></li>';
				                	}
				                	if ($this->get_user_permission($data['user_role'], 'marks-add')) {
				                		echo '<li><a href="../marks/marks-edit.php">Edit Marks</a></li>';
				                	}
				                	if ($this->get_user_permission($data['user_role'], 'tabulation-print')) {
				                		echo '<li><a href="../marks/tabulation.php">Print Tabulation</a></li>';
				                	}
				                	if ($this->get_user_permission($data['user_role'], 'testimonial-print')) {
				                		echo '<li><a href="../testimonial/index.php">Print Testimonial</a></li>';
				                	}
				                	echo '</ul>
			                  			</li>';
			                	}

			                	//Student menu
			                	if ($this->get_user_permission($data['user_role'], 'student')) {
			                		echo '<li><a><i class="fa fa-graduation-cap"></i> Student <span class="fa fa-chevron-down"></span></a>
			                    			<ul class="nav child_menu">';

			                		if ($this->get_user_permission($data['user_role'], 'student-view')) {
			                			echo '<li><a href="../student/index.php">View Student</a></li>';
				                	}

				                	if ($this->get_user_permission($data['user_role'], 'student-add')) {
				                		echo '<li><a href="../student/student-add.php">Add Student</a></li>';
				                	}

				                	if ($this->get_user_permission($data['user_role'], 'admit-print')) {
				                		echo '<li><a href="../admit/index.php">Admit Card</a></li>';
				                	}

				                	if ($this->get_user_permission($data['user_role'], 'seat-plan-print')) {
				                		echo '<li><a href="../admit/seat.php">Seat Plan</a></li>';
				                	}

				                	echo '</ul>
			                  			</li>';
			                	}

			                	//Course Menu
			                	if ($this->get_user_permission($data['user_role'], 'course')) {
			                		echo '<li><a><i class="fa fa-book"></i> Subject <span class="fa fa-chevron-down"></span></a>
			                    			<ul class="nav child_menu">';

			                		if ($this->get_user_permission($data['user_role'], 'course-view')) {
			                			echo '<li><a href="../course/index.php">View Subject</a></li>';
				                	}

				                	if ($this->get_user_permission($data['user_role'], 'course-add')) {
				                		echo '<li><a href="../course/course-add.php">Add Subject</a></li>';
				                	}
				                	echo '</ul>
			                  			</li>';
			                	}

			                	//Session Menu
			                	if ($this->get_user_permission($data['user_role'], 'session')) {
			                		echo '<li><a><i class="fa fa-rocket"></i> Session <span class="fa fa-chevron-down"></span></a>
			                    			<ul class="nav child_menu">';

			                		if ($this->get_user_permission($data['user_role'], 'session-view')) {
			                			echo '<li><a href="../session/index.php">View Session</a></li>';
				                	}

				                	if ($this->get_user_permission($data['user_role'], 'session-add')) {
				                		echo '<li><a href="../session/session-add.php">New Session</a></li>';
				                	}
				                	echo '</ul>
			                  			</li>';
			                	}

			                	//User Menu
			                	if ($this->get_user_permission($data['user_role'], 'user')) {
			                		echo '<li><a><i class="fa fa-users"></i> Users <span class="fa fa-chevron-down"></span></a>
			                    			<ul class="nav child_menu">';

			                		if ($this->get_user_permission($data['user_role'], 'user-view')) {
			                			echo '<li><a href="../user/index.php">View User</a></li>';
				                	}

				                	if ($this->get_user_permission($data['user_role'], 'user-add')) {
				                		echo '<li><a href="../user/user-add.php">New User</a></li>';
				                	}
				                	echo '</ul>
			                  			</li>';
			                	}
			                ?>
			                  <li>
			                    <a href="../settings/index.php"><i class="fa fa-cog"></i> Settings </a>
			                  </li>
			                  <li>
			                    <a href="../home/logout.php"><i class="fa fa-sign-out"></i> Logout </a>
			                  </li>
			                  </ul>
			              </div>
			            </div>
			            <!-- /sidebar menu -->
			          </div>
			        </div>

			        <!-- top navigation -->
			        <div class="top_nav">
			          <div class="nav_menu">
			            <nav>
			              <div class="nav toggle">
			                <a id="menu_toggle"><i class="fa fa-bars"></i></a>
			              </div>

			              <ul class="nav navbar-nav navbar-right">
			                <li class="">
			                  <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
			                    <!-- <img src="../../assets/images/img.jpg" alt=""> -->
			                    <span class=" fa fa-user"></span> <?php echo $data['username']; ?>
			                    <span class=" fa fa-angle-down"></span>
			                  </a>
			                  <ul class="dropdown-menu dropdown-usermenu pull-right">
			                    <li><a href="../settings/index.php">Settings</a></li>
			                    <li><a href="../home/logout.php"><i class="fa fa-sign-out pull-right"></i> Log Out</a></li>
			                  </ul>
			                </li>

			              </ul>
			            </nav>
			          </div>
			        </div>
			        <!-- /top navigation -->
			        <?php
		}

		/**
		*	Footer of admin panel	
		*	
		*	@param $extend if we want to add our custom javascript pass through it
		*/
		public function footer($extend = '')
		{
			?>
			 <!-- footer content -->
			        <footer>
			          <div class="pull-right">
			          Developed by <a style="margin-right: 0px; text-decoration:none;" target="_new"  href="http://it.lopagroup.org/"><i class="fa fa-line-chart"> </i> Lopa IT</a>, High Performance. Delivered.
			          </div>
			          <div class="clearfix"></div>
			        </footer>
			        <!-- /footer content -->
			      </div>
			    </div>

			    <!-- jQuery -->
			    <script src="../../assets/js/jquery.min.js"></script>
			    <!-- Bootstrap -->
			    <script src="../../assets/js/bootstrap.min.js"></script>
			    <!-- FastClick -->
			    <script src="../../assets/js/fastclick.js"></script>
			    <!-- NProgress -->
			    <script src="../../assets/js/nprogress.js"></script>
			    <!-- Chart.js -->
			    <script src="../../assets/js/Chart.min.js"></script>
			    <!-- gauge.js -->
			    <script src="../../assets/js/gauge.min.js"></script>
			    <!-- bootstrap-progressbar -->
			    <script src="../../assets/js/bootstrap-progressbar.min.js"></script>
			    <!-- iCheck -->
			    <script src="../../assets/js/icheck.min.js"></script>
			    <!-- Skycons -->
			    <script src="../../assets/js/skycons.js"></script>
			    <!-- Flot -->
			    <script src="../../assets/js/jquery.flot.js"></script>
			    <script src="../../assets/js/jquery.flot.pie.js"></script>
			    <script src="../../assets/js/jquery.flot.time.js"></script>
			    <script src="../../assets/js/jquery.flot.stack.js"></script>
			    <script src="../../assets/js/jquery.flot.resize.js"></script>
			    <!-- Flot plugins -->
			    <script src="../../assets/js/jquery.flot.orderBars.js"></script>
			    <script src="../../assets/js/jquery.flot.spline.min.js"></script>
			    <script src="../../assets/js/curvedLines.js"></script>
			    <!-- DateJS -->
			    <script src="../../assets/js/date.js"></script>
			    <!-- JQVMap -->
			    <script src="../../assets/js/jquery.vmap.min.js"></script>
			    <script src="../../assets/js/jquery.vmap.world.js"></script>
			    <script src="../../assets/js/jquery.vmap.sampledata.js"></script>
			    <!-- bootstrap-daterangepicker -->
			    <script src="../../assets/js/moment.min.js"></script>
			    <script src="../../assets/js/daterangepicker.js"></script>
			    <script src="../../assets/js/jquery.validate.min.js"></script>
			    <!-- pnotify -->
			    <script type="text/javascript" src="../../assets/js/pnotify.custom.min.js"></script>

			    <!-- Custom Theme Scripts -->
			    <script src="../../assets/js/custom.js"></script>
				<?php echo $extend; ?>
			  </body>
			</html>
			<?php
		}

	public function get_validator_script($jquery)
		{
			$js = '<script type="text/javascript">';
          	foreach($jquery['methods'] as $method_name => $method_function):
            $js .= 'jQuery.validator.addMethod("'.$method_name.'", '.$method_function.');';
          	endforeach;
		    $js .= '$("#myform").validate({
		            errorClass: "text-danger",

		              highlight: function(element, errorClass) {
		                $(element).fadeOut(100,function() {
		                  $(element).fadeIn(100);
		                });
		              },

		            rules: '.json_encode($jquery['rules']).',
		            messages: '.json_encode($jquery['messages']).'

		          });';
		      $js .= '</script>';

		      return $js;
		}
	}

?>