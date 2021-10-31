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
	$db_db = new App\db\Db();

	//TODO: get user role from session or DB
	$user_role = $_SESSION['user_roll'];
	$operation = 'student-add';

	$institute = $db_db->assign(array('key' => 'institute'))->get_value();
  	$code = $db_db->assign(array('key' => 'college_code'))->get_value();

  	$institute = is_array($institute)?$institute['_value']:'';
  	$code = is_array($code)?$code['_value']:'';
	//check access permission for this user
	if (!$settings->get_user_permission($user_role, $operation)) {
	   header("Location: ../home/403.php");
	   die();
	}
	if(isset($_REQUEST)){
		$errors = array();

		if(!empty($_POST['home'])) {
			//first form
			$filter = new App\filter\Filter();
  			$validator = $filter->student_basic_update();

			if($validator->is_valid($_REQUEST)) {
				$data['student_id'] = $_REQUEST['student_id'];
				$data['student_name'] = $_REQUEST['student_name'];
				$data['bnc_roll'] = $_REQUEST['bnc_roll'];
				$data['session'] = $_REQUEST['session'];			//disabled
				$data['semester'] = $_REQUEST['semester'];
				
				$data['start_date'] = $_REQUEST['start_date'];
				$data['end_date'] = $_REQUEST['end_date'];
				$data['final_exam_date'] = $_REQUEST['final_exam_date'];

				$db->assign($data)->update();
			}else{
				//print_r($validator->get_errors());
				$_SESSION['error_msg'] = 'Oops! That wasn\'t supposed to happen.';
				header('Location: ../student/student-edit.php?student='.$data['student_id']);
			}
		}

		if(!empty($_POST['profile'])) {
			//Details form
			$data['student_id'] 		= $_POST['student_id'];
			$data['institute_name'] 	= $institute;
			$data['college_code'] 		= $code;
		    $data['father_name'] 		= $_POST['father_name'];
		    $data['mother_name'] 		= $_POST['mother_name'];
		    $data['guardian_name'] 		= $_POST['guardian_name'];
		    $data['relation_to_guardian'] = $_POST['relation_to_guardian'];
		    $data['date_of_birth'] 		= $_POST['date_of_birth'];
		    $data['nid'] 				= $_POST['nid'];
		    $data['contact_number'] 	= $_POST['contact_number'];
		    $data['nationality'] 		= $_POST['nationality'];
		    $data['marital_status'] 	= $_POST['marital_status'];
		    $data['gender'] 			= isset($_POST['gender'])?$_POST['gender']: null;
		    $data['religion'] 			= $_POST['religion'];
		    $data['service_type'] 		= $_POST['service_type'];
		    $data['permanent_address'] 	= $_POST['permanent_address'];
		    $data['current_address'] 	= $_POST['current_address'];
		    $data['school_name'] 		= $_POST['school_name'];
		    $data['college_name'] 		= $_POST['college_name'];
		    $data['ssc_gpa'] 			= $_POST['ssc_gpa'];
		    $data['hsc_gpa'] 			= $_POST['hsc_gpa'];
		    $data['ssc_passing_year'] 	= $_POST['ssc_passing_year'];
		    $data['hsc_passing_year'] 	= $_POST['hsc_passing_year'];
		    $data['original_ssc_doc'] 	= $_POST['original_ssc_doc'];
		    $data['original_hsc_doc'] 	= $_POST['original_hsc_doc'];

		    /* Updated Dec 2017*/
			$permanent_address = array(
		    	'permanent_address' => $data['permanent_address'],
		    	'ps' => $_POST['ps'],
		    	'po' => $_POST['po'],
		    	'district' => $_POST['district'],
		    );
		    $data['permanent_address'] = json_encode($permanent_address);

		    if($db->assign(array('student_id' => $data['student_id']))->check_profile()) {
		    	$db->assign($data)->update_profile();
		    }else{
		    	$db->assign($data)->store_profile(1);
		    }
		    
			// echo '<pre>';
			// print_r($data);
			// echo '<pre>';
		}
	}

?>