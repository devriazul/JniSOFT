<?php 
	include_once ('../../vendor/autoload.php');
	$db = new App\user\User();
	$db_db = new App\db\Db();
	$settings = new App\settings\Settings();

	if (session_status() == PHP_SESSION_NONE) {
	    session_start();
	}
	//if not logged in redirect him
	if (!isset($_SESSION['user_roll'])) {
		header("Location: ../home/login.php");
	}

	//form 1\\
	$user_role = $_SESSION['user_roll'];
	$operation = 'settings';

	$filter = new App\filter\Filter();
  	$validator = $filter->settings_account();

	if(isset($_POST['user_name'])){
		if($validator->is_valid($_POST)) {
			if ($db->assign(array('email' => $_REQUEST['email']))->check_email()) {
				$_SESSION['error_msg'] = 'This email address already exists.';
				header('Location: ../user/user-add.php');
			}else{
				$data['user_id'] = $_SESSION['user_id'];
				$data['email'] = $_REQUEST['email'];
				$data['user_name'] = $_REQUEST['user_name'];

				$db->assign($data)->update();
			}
			
		}else{
			$_SESSION['error_msg'] = 'Oops! That wasn\'t supposed to happen.';
			header('Location: ../settings/index.php');
		}
	}

	//form 2
	if (isset($_POST['old_password']) && isset($_POST['new_password']) &&  isset($_POST['email'])) {
		$validator2 = $filter->settings_password();
		if($validator2->is_valid($_POST)) {
		    $user = $db->assign(array('email' => $_POST['email']))->single_by_email();
		    if(md5($_POST['old_password']) == $user['password'] ) {
		    	$data['password'] = md5($_REQUEST['new_password']);
		    	$data['user_id'] = $_SESSION['user_id'];
		    	$db->assign($data)->update_password();
		    }else{
		    	$_SESSION['error_msg'] = 'Sorry! Wrong password.';
		    	header('Location: ../settings/index.php');
		    }
		}else{
			$_SESSION['error_msg'] = 'Oops! That wasn\'t supposed to happen.';
			header('Location: ../settings/index.php');
		}
	}

	if (!empty($_POST['institute'])) {
		if (!$db_db->assign(array('key' => 'institute', 'value' => $_POST['institute']))->check()){
			$db_db->assign(array('key' => 'institute', 'value' => $_POST['institute']))->store();
		}else{
			$db_db->assign(array('key' => 'institute', 'value' => $_POST['institute']))->update();
		}
	}

	if (!empty($_POST['college_code'])) {
		if (!$db_db->assign(array('key' => 'college_code', 'value' => $_POST['college_code']))->check()){
			$db_db->assign(array('key' => 'college_code', 'value' => $_POST['college_code']))->store();
		}else{
			$db_db->assign(array('key' => 'college_code', 'value' => $_POST['college_code']))->update();
		}
	}

	if (!empty($_POST['college_address'])) {
		if (!$db_db->assign(array('key' => 'college_address', 'value' => $_POST['college_address']))->check()){
			$db_db->assign(array('key' => 'college_address', 'value' => $_POST['college_address']))->store();
		}else{
			$db_db->assign(array('key' => 'college_address', 'value' => $_POST['college_address']))->update();
		}
	}

	if (!empty($_POST['college_subject'])) {
		if (!$db_db->assign(array('key' => 'college_subject', 'value' => $_POST['college_subject']))->check()){
			$db_db->assign(array('key' => 'college_subject', 'value' => $_POST['college_subject']))->store();
		}else{
			$db_db->assign(array('key' => 'college_subject', 'value' => $_POST['college_subject']))->update();
		}
	}
	
	if (!empty($_POST['book_no'])) {
		if (!$db_db->assign(array('key' => 'book_no', 'value' => $_POST['book_no']))->check()){
			$db_db->assign(array('key' => 'book_no', 'value' => $_POST['book_no']))->store();
		}else{
			$db_db->assign(array('key' => 'book_no', 'value' => $_POST['book_no']))->update();
		}
	}


    if (!empty($_POST['final_exam_year'])) {
		if (!$db_db->assign(array('key' => 'final_exam_year', 'value' => $_POST['final_exam_year']))->check()){
			$db_db->assign(array('key' => 'final_exam_year', 'value' => $_POST['final_exam_year']))->store();
		}else{
			$db_db->assign(array('key' => 'final_exam_year', 'value' => $_POST['final_exam_year']))->update();
		}
	}


	if (!isset($_POST)) {
		header('Location: ../settings/index.php');
	}
?>