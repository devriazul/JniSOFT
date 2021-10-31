<?php 
	include_once ('../../vendor/autoload.php');
	if (session_status() == PHP_SESSION_NONE) {
	    session_start();
	}
	//if not logged in redirect him
	if (!isset($_SESSION['user_roll'])) {
		header("Location: ../home/login.php");
	}

	$db = new App\student\Student();
	$settings = new App\settings\Settings();

	//TODO: get user role from session or DB
	$user_role = $_SESSION['user_roll'];
	$operation = 'student-delete';

	//check access permission for this user
	if (!$settings->get_user_permission($user_role, $operation)) {
	   header("Location: ../home/403.php");
	   die();
	}

	$student_id = $_GET['student'];

	$db->assign(array('student_id' => $student_id))->delete();
	header('Location: ../../view/student/index.php');
 ?>