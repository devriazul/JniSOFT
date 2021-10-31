<?php 
	include_once ('../../vendor/autoload.php');
	
	if (session_status() == PHP_SESSION_NONE) {
	    session_start();
	}
	//if not logged in redirect him
	if (!isset($_SESSION['user_roll'])) {
		header("Location: ../home/login.php");
	}

	$db = new App\marks\Marks();
	$settings = new App\settings\Settings();

	//TODO: get user role from session or DB
	$user_role = $_SESSION['user_roll'];
	$operation = 'marks-delete';

	//check access permission for this user
	if (!$settings->get_user_permission($user_role, $operation)) {
	   header("Location: ../home/403.php");
	   die();
	}

	$data['student_id'] = $_REQUEST['student_id'];
	$data['course_id'] = $_REQUEST['course_id'];
	$data['session'] = $_REQUEST['session'];
	$data['semester'] = $_REQUEST['semester'];

	$db->assign($data)->delete();
 ?>