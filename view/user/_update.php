<?php 
	include_once ('../../vendor/autoload.php');
	if (session_status() == PHP_SESSION_NONE) {
		session_start();
	}
	//if not logged in redirect him
	if (!isset($_SESSION['user_roll'])) {
		header("Location: ../home/login.php");
	}
	
	$db = new App\session\Session();
	$settings = new App\settings\Settings();

	//TODO: get user role from session or DB
	$user_role = $_SESSION['user_roll'];
	$operation = 'session-edit';

	//check access permission for this user
	if (!$settings->get_user_permission($user_role, $operation)) {
	   header("Location: ../home/403.php");
	   die();
	}

	if(isset($_REQUEST)){
		$errors = array();

		//TODO: Filtering data
		$data['session_id'] = $_REQUEST['id'];
		$data['session_name'] = $_REQUEST['session_name'];

		//$db->assign($data)->update();
	}
?>