<?php 
	include_once ('../../vendor/autoload.php');
	$db = new App\session\Session();
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
	$operation = 'session-delete';

	//check access permission for this user
	if (!$settings->get_user_permission($user_role, $operation)) {
	   header("Location: ../home/403.php");
	   die();
	}

	$session_id = $_GET['session'];

	$db->assign(array('session_id' => $session_id))->delete();
	header('Location: ../../view/session/index.php');
 ?>