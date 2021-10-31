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

	$user_role = $_SESSION['user_roll'];
	$operation = 'session-edit';

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
				header('Location: ../session/session-edit.php?session='. $_POST['id']);
			}else{
				$data['session_id'] = $_REQUEST['id'];
				$data['session_name'] = $_REQUEST['session_name'];
				$db->assign($data)->update();
			}			
		}else{
			$_SESSION['error_msg'] = 'Oops! That wasn\'t supposed to happen.';
			header('Location: ../session/index.php');
		}
	}
?>