<?php 
	include_once ('../../vendor/autoload.php');
	if (session_status() == PHP_SESSION_NONE) {
		session_start();
	}
	//if not logged in redirect him
	if (!isset($_SESSION['user_roll'])) {
		header("Location: ../home/login.php");
	}

	$db = new App\course\Course();
	$settings = new App\settings\Settings();



	//TODO: get user role from session or DB
	$user_role = $_SESSION['user_roll'];
	$operation = 'course-add';

	//check access permission for this user
	if (!$settings->get_user_permission($user_role, $operation)) {
	   header("Location: ../home/403.php");
	   die();
	}

	if(isset($_REQUEST)){
		$filter = new App\filter\Filter();
  		$validator = $filter->course_add();

		if($validator->is_valid($_POST)) {
			$data['course_id'] = uniqid('', true).''.rand().''.rand();
			$data['course_name'] = $_REQUEST['course_name'];
			$data['course_code'] = $_REQUEST['course_code'];
			$data['credit'] = $_REQUEST['credit'];
			$data['session'] = $_REQUEST['session'];
			$data['semester'] = $_REQUEST['semester'];

			if($db->assign($data)->check_duplicate()) {
				$_SESSION['error_msg'] = 'This course name already exists in this session and semester.';
				header('Location: ../course/course-add.php');
			}else{
				$db->assign($data)->store();
			}
		}else{
			$_SESSION['error_msg'] = 'Oops! That wasn\'t supposed to happen.';
			header('Location: ../course/course-add.php');
		}
	}
?>