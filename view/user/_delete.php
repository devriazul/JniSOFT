<?php 
	include_once ('../../vendor/autoload.php');
	if (session_status() == PHP_SESSION_NONE) {
		session_start();
	}
	//if not logged in redirect him
	if (!isset($_SESSION['user_roll'])) {
		header("Location: ../home/login.php");
	}

	$db = new App\user\User();
	$settings = new App\settings\Settings();

	//TODO: get user role from session or DB
	$user_role = $_SESSION['user_roll'];
	$operation = 'user-delete';

	//check access permission for this user
	if (!$settings->get_user_permission($user_role, $operation)) {
	   header("Location: ../home/403.php");
	   die();
	}

	$user_id = $_GET['user'];

	if ($db->assign(array('user_id' => $user_id))->check()) {
		$db->assign(array('user_id' => $user_id))->delete();
	}else{
		$_SESSION['error_msg'] = 'Users is not found';
		header('Location: ../../view/user/index.php');
	}
 ?>