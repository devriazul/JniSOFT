<?php 
	include_once ('../../vendor/autoload.php');
	$db = new App\session\Session();
	$db_course = new App\course\Course();
	$settings = new App\settings\Settings();

	if (session_status() == PHP_SESSION_NONE) {
	 	session_start();
	}
	//if not logged in redirect him
	if (!isset($_SESSION['user_roll'])) {
		header("Location: ../home/login.php");
	}

	//TODO: get user role from session or DB
	$user_role = $_SESSION['user_roll'];
	$operation = 'session-add';

	//check access permission for this user
	if (!$settings->get_user_permission($user_role, $operation)) {
	   header("Location: ../home/403.php");
	   die();
	}

	if(isset($_REQUEST)){
		$filter = new App\filter\Filter();
  		$validator = $filter->session_create();

		if($validator->is_valid($_POST)) {
			if ($db->assign(array('session_name' => $_REQUEST['session_name']))->check_name()) {
				$_SESSION['error_msg'] = 'This session name already exists.';
				header('Location: ../session/session-add.php');
			}else{
				$data['session_id'] = uniqid('', true).''.rand().''.rand();
				$data['session_name'] = $_REQUEST['session_name'];

				$db->assign($data)->store();

				foreach ($_REQUEST['course'] as $course_id) {
					$single =$db_course->assign(array('course_id' => $course_id))->single_course();

					$data2['course_id'] = uniqid('', true).''.rand().''.rand();
					$data2['course_name'] = $single['course_name'];
					$data2['course_code'] = $single['course_code'];
					$data2['credit'] = $single['credit'];
					$data2['session'] = $_REQUEST['session_name'];
					$data2['semester'] = $single['semester'];

					$db_course->assign($data2)->store_single();
				}
			}
			
		}else{
			$_SESSION['error_msg'] = 'Oops! That wasn\'t supposed to happen.';
			 //print_r($validator->get_errors());
			header('Location: ../session/session-add.php');
		}
	}
?>