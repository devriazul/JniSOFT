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
	$operation = 'user-add';

	//check access permission for this user
	if (!$settings->get_user_permission($user_role, $operation)) {
	   header("Location: ../home/403.php");
	   die();
	}


	if(isset($_REQUEST)){
		$filter = new App\filter\Filter();
  		$validator = $filter->user_create();

		if($validator->is_valid($_POST)) {
			if ($db->assign(array('email' => $_REQUEST['email']))->check_email()) {
				$_SESSION['error_msg'] = 'This email address already exists.';
				header('Location: ../user/user-add.php');
			}else{
				$data['user_id'] = uniqid('', true).''.rand().''.rand();
				$data['user_name'] = $_REQUEST['user_name'];
				$data['email'] = $_REQUEST['email'];
				$data['password'] = md5($_REQUEST['password']);
				$data['user_type'] = $_REQUEST['user_type'];
				$db->assign($data)->store();
			}
			
		}else{
			$_SESSION['error_msg'] = 'Oops! That wasn\'t supposed to happen.';
			header('Location: ../user/user-add.php');
		}
	}
?>